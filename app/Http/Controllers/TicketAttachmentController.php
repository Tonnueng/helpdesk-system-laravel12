<?php

namespace App\Http\Controllers;

use App\Models\TicketAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;


class TicketAttachmentController extends Controller
{
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TicketAttachment $attachment)
    {
        // ตรวจสอบสิทธิ์: เฉพาะผู้ดูแล (หรือผู้ที่มีสิทธิ์จัดการ Ticket) เท่านั้นที่ลบไฟล์แนบได้
        if (!Auth::user()->canManageTickets()) {
            abort(403, 'Unauthorized action.');
        }

        // ตรวจสอบว่าไฟล์แนบมีอยู่จริงก่อนลบ
        $filePath = str_replace(Storage::url(''), 'public/', $attachment->filepath); // แปลง URL กลับเป็น storage path (เช่น public/attachments/...)

        try {
            // ลบไฟล์ออกจาก Storage
            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
            }

            // ลบข้อมูลไฟล์แนบออกจากฐานข้อมูล
            $attachment->delete();

            return redirect()->back()->with('success', 'ไฟล์แนบถูกลบเรียบร้อยแล้ว!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการลบไฟล์แนบ: ' . $e->getMessage());
        }
    }
}