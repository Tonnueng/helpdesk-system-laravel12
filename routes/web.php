<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketAttachmentController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use App\Models\Status;
use App\Models\Category;
use App\Models\Priority;
use App\Models\User;

Route::get('/', function () {
    // ถ้าผู้ใช้ล็อกอินแล้ว ให้ไปที่ dashboard
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    // ถ้ายังไม่ล็อกอิน ให้ไปที่หน้า login
    return redirect()->route('login');
});

Route::middleware('auth', 'verified')->group(function () {

    Route::get('/dashboard', function () {        // ดึงข้อมูลสถิติพื้นฐาน
        $totalTickets = Ticket::count();

        // สถิติตามสถานะ
        $statuses = Status::all();
        $ticketsByStatus = [];
        foreach ($statuses as $status) {
            $ticketsByStatus[$status->name] = Ticket::where('status_id', $status->id)->count();
        }

        // สถิติตามประเภท
        $categories = Category::all();
        $ticketsByCategory = [];
        foreach ($categories as $category) {
            $ticketsByCategory[$category->name] = Ticket::where('category_id', $category->id)->count();
        }

        // สถิติตามระดับความสำคัญ
        $priorities = Priority::all();
        $ticketsByPriority = [];
        foreach ($priorities as $priority) {
            $ticketsByPriority[$priority->name] = Ticket::where('priority_id', $priority->id)->count();
        }

        // สถิติตามเดือน (6 เดือนย้อนหลัง)
        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyStats[] = [
                'month' => $date->format('M Y'),
                'count' => Ticket::whereYear('created_at', $date->year)
                                ->whereMonth('created_at', $date->month)
                                ->count()
            ];
        }

        // สถิติการแก้ไขปัญหา (เฉลี่ยเวลาที่ใช้แก้ไข)
        $resolvedTickets = Ticket::whereHas('status', function ($query) {
            $query->whereIn('name', ['Resolved', 'Closed']);
        })->get();

        $avgResolutionTime = 0;
        if ($resolvedTickets->count() > 0) {
            $totalHours = 0;
            foreach ($resolvedTickets as $ticket) {
                $firstUpdate = $ticket->updates()->orderBy('created_at')->first();
                $lastUpdate = $ticket->updates()->orderBy('created_at', 'desc')->first();

                if ($firstUpdate && $lastUpdate) {
                    $hours = $firstUpdate->created_at->diffInHours($lastUpdate->created_at);
                    $totalHours += $hours;
                }
            }
            $avgResolutionTime = round($totalHours / $resolvedTickets->count(), 1);
        }

        // สถิติสำหรับผู้ดูแล
        $assignedTickets = 0;
        $myResolvedTickets = 0;
        $myAvgResolutionTime = 0;

        if (Auth::user()->canManageTickets()) {
            $assignedTickets = Ticket::where('assigned_to_user_id', Auth::id())->count();
            $myResolvedTickets = Ticket::where('assigned_to_user_id', Auth::id())
                                      ->whereHas('status', function ($query) {
                                          $query->whereIn('name', ['Resolved', 'Closed']);
                                      })->count();

            // คำนวณเวลาการแก้ไขเฉลี่ยของตัวเอง
            $myResolvedTicketsList = Ticket::where('assigned_to_user_id', Auth::id())
                                          ->whereHas('status', function ($query) {
                                              $query->whereIn('name', ['Resolved', 'Closed']);
                                          })->get();

            if ($myResolvedTicketsList->count() > 0) {
                $totalMyHours = 0;
                foreach ($myResolvedTicketsList as $ticket) {
                    $firstUpdate = $ticket->updates()->orderBy('created_at')->first();
                    $lastUpdate = $ticket->updates()->orderBy('created_at', 'desc')->first();

                    if ($firstUpdate && $lastUpdate) {
                        $hours = $firstUpdate->created_at->diffInHours($lastUpdate->created_at);
                        $totalMyHours += $hours;
                    }
                }
                $myAvgResolutionTime = round($totalMyHours / $myResolvedTicketsList->count(), 1);
            }
        }

        // สถิติล่าสุด (7 วันย้อนหลัง)
        $recentStats = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $recentStats[] = [
                'date' => $date->format('d/m'),
                'count' => Ticket::whereDate('created_at', $date)->count()
            ];
        }

        // Top Categories (ประเภทที่แจ้งบ่อยที่สุด)
        $topCategories = Category::withCount('tickets')
                                ->orderBy('tickets_count', 'desc')
                                ->take(5)
                                ->get();

        // Top Agents (เจ้าหน้าที่ที่แก้ไขปัญหาได้มากที่สุด)
        $topAgents = User::whereIn('role', ['owner', 'head', 'agent'])
                        ->withCount(['assignedTickets as resolved_count' => function ($query) {
                            $query->whereHas('status', function ($q) {
                                $q->whereIn('name', ['Resolved', 'Closed']);
                            });
                        }])
                        ->orderBy('resolved_count', 'desc')
                        ->take(5)
                        ->get();

        return view('dashboard', compact(
            'totalTickets',
            'ticketsByStatus',
            'ticketsByCategory',
            'ticketsByPriority',
            'monthlyStats',
            'avgResolutionTime',
            'assignedTickets',
            'myResolvedTickets',
            'myAvgResolutionTime',
            'recentStats',
            'topCategories',
            'topAgents'
        ));
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // เส้นทางสำหรับ Tickets (ใช้ middleware 'auth' เพื่อให้เข้าถึงได้เฉพาะผู้ที่เข้าสู่ระบบแล้ว)
    Route::resource('tickets', TicketController::class);
    Route::delete('/attachments/{attachment}', [TicketAttachmentController::class, 'destroy'])->name('attachments.destroy');

    // เส้นทางสำหรับ Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::patch('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications', [NotificationController::class, 'destroyAll'])->name('notifications.destroyAll');

});

require __DIR__.'/auth.php';
