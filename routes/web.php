<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketAttachmentController;
use Illuminate\Support\Facades\Route;
use App\Models\Ticket; // เพิ่มบรรทัดนี้
use Illuminate\Support\Facades\Auth; // เพิ่มบรรทัดนี้
use App\Models\Status; // เพิ่มบรรทัดนี้

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth', 'verified')->group(function () {
    // *** แก้ไขโค้ดในส่วนนี้ ***
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

});

require __DIR__.'/auth.php';