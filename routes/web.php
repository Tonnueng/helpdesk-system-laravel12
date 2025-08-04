<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketAttachmentController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use App\Models\Ticket; 
use Illuminate\Support\Facades\Auth; 
use App\Models\Status; 

Route::get('/', function () {
    // ถ้าผู้ใช้ล็อกอินแล้ว ให้ไปที่ dashboard
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    // ถ้ายังไม่ล็อกอิน ให้ไปที่หน้า login
    return redirect()->route('login');
});

Route::middleware('auth', 'verified')->group(function () {
    
    Route::get('/dashboard', function () {
        // ดึงข้อมูลสถิติ
        $totalTickets = Ticket::count(); // จำนวน Ticket ทั้งหมด

        // ดึงสถานะ ID ของ 'New' และ 'In Progress'
        $openStatusIds = Status::whereIn('name', ['New', 'In Progress'])->pluck('id');
        $openTickets = Ticket::whereIn('status_id', $openStatusIds)->count();

        // ดึงสถานะ ID ของ 'Resolved' และ 'Closed'
        $closedStatusIds = Status::whereIn('name', ['Resolved', 'Closed'])->pluck('id');
        $closedTickets = Ticket::whereIn('status_id', $closedStatusIds)->count();

        // สถิติสำหรับ Ticket ที่มอบหมายให้ผู้ดูแลที่กำลังล็อกอิน (ถ้ามี Role จัดการ)
        $assignedTickets = 0;
        if (Auth::user()->canManageTickets()) {
            $assignedTickets = Ticket::where('assigned_to_user_id', Auth::id())->count();
        }

        return view('dashboard', compact('totalTickets', 'openTickets', 'closedTickets', 'assignedTickets'));
    })->name('dashboard');
    // *** สิ้นสุดการแก้ไขในส่วนนี้ ***

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