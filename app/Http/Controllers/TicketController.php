<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Category;
use App\Models\Priority;
use App\Models\Status;
use App\Models\User;
use App\Notifications\TicketCreatedNotification;
use App\Notifications\TicketUpdatedNotification;
use App\Notifications\TicketAssignedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // ถ้าผู้ใช้งานมีสิทธิ์จัดการ Ticket (เจ้าของ, หัวหน้า, เจ้าหน้าที่) ให้แสดงปัญหาทั้งหมด
        if (Auth::user()->canManageTickets()) { // ใช้เมธอดใหม่ canManageTickets()
            $tickets = Ticket::with(['user', 'category', 'priority', 'status'])
                              ->orderBy('created_at', 'desc')
                              ->paginate(10);
        } else {
            // ถ้าเป็นผู้ใช้งานทั่วไป (role: 'user') ให้แสดงเฉพาะปัญหาที่ตัวเองแจ้ง
            $tickets = Ticket::where('user_id', Auth::id())
                              ->with(['user', 'category', 'priority', 'status'])
                              ->orderBy('created_at', 'desc')
                              ->paginate(10);
        }

        return view('tickets.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // โหลดข้อมูล Category, Priority, Status เพื่อใช้ใน Dropdown ของฟอร์ม
        $categories = Category::all();
        $priorities = Priority::all();
        $statuses = Status::all(); // อาจไม่จำเป็นสำหรับฟอร์มสร้าง แต่เตรียมไว้ก่อน

        return view('tickets.create', compact('categories', 'priorities', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // 1. ตรวจสอบข้อมูลจากฟอร์ม
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'priority_id' => 'required|exists:priorities,id',
                'reported_at' => 'nullable|date',
                'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:2048', // 2MB per file
            ], [
                'title.required' => 'กรุณากรอกหัวข้อปัญหา',
                'description.required' => 'กรุณากรอกรายละเอียดปัญหา',
                'category_id.required' => 'กรุณาเลือกประเภทปัญหา',
                'category_id.exists' => 'ประเภทปัญหาไม่ถูกต้อง',
                'priority_id.required' => 'กรุณาเลือกระดับความสำคัญ',
                'priority_id.exists' => 'ระดับความสำคัญไม่ถูกต้อง',
                'reported_at.date' => 'รูปแบบวันที่และเวลาไม่ถูกต้อง',
                'attachments.*.mimes' => 'รองรับไฟล์ประเภท jpeg, png, jpg, gif, pdf, doc, docx เท่านั้น',
                'attachments.*.max' => 'ขนาดไฟล์ไม่ควรเกิน 2MB',
            ]);

            // 2. สร้าง Ticket ใหม่
            $ticket = new Ticket();
            $ticket->user_id = Auth::id(); // ผู้แจ้งคือผู้ที่เข้าสู่ระบบปัจจุบัน
            $ticket->category_id = $validatedData['category_id'];
            $ticket->priority_id = $validatedData['priority_id'];
            $ticket->status_id = Status::where('name', 'New')->first()->id; // กำหนดสถานะเริ่มต้นเป็น 'New'
            $ticket->title = $validatedData['title'];
            $ticket->description = $validatedData['description'];
            $ticket->reported_at = $validatedData['reported_at'];
            $ticket->save();

            // 3. จัดการไฟล์แนบ 
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    // เก็บไฟล์ใน storage/app/public/attachments
                    $filepath = $file->storeAs('public/attachments', $filename);

                    // บันทึกข้อมูลไฟล์ลงในตาราง ticket_attachments
                    $ticket->attachments()->create([
                        'filename' => $file->getClientOriginalName(), // ชื่อไฟล์เดิม
                        'filepath' => Storage::url($filepath), // Path ที่สามารถเข้าถึงได้
                        'mime_type' => $file->getClientMimeType(),
                    ]);
                }
            }

            // 4. สร้าง Ticket Update (แจ้งว่ามีการสร้าง Ticket ใหม่)
            $ticket->updates()->create([
                'user_id' => Auth::id(),
                'comment' => 'Ticket created.',
                'status_id' => $ticket->status_id,
            ]);

            // 5. ส่ง In-app Notifications ให้ผู้ดูแล
            $this->sendTicketCreatedNotifications($ticket);

            // 6. Redirect พร้อมข้อความสำเร็จ
            return redirect()->route('tickets.index')->with('success', 'แจ้งปัญหาสำเร็จแล้ว!');

        } catch (ValidationException $e) {
            // หากเกิดข้อผิดพลาดในการตรวจสอบข้อมูล
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // ข้อผิดพลาดอื่นๆ
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการแจ้งปัญหา: ' . $e->getMessage())->withInput();
        }
    }

    
    public function show(Ticket $ticket)
    {
        // ตรวจสอบสิทธิ์: ถ้าไม่ใช่ผู้แจ้งเอง และไม่มีสิทธิ์จัดการ Ticket ให้ปฏิเสธการเข้าถึง
        if ($ticket->user_id !== Auth::id() && !Auth::user()->canManageTickets()) {
            abort(403, 'Unauthorized access.');
        }

        // โหลดความสัมพันธ์ที่จำเป็นทั้งหมด
        $ticket->load(['user', 'category', 'priority', 'status', 'assignedTo', 'attachments', 'updates.user', 'updates.status']);

        // โหลดสถานะทั้งหมดสำหรับ Dropdown การเปลี่ยนสถานะ (สำหรับผู้ดูแล)
        $statuses = Status::all();

        // ดึงผู้ใช้งานที่มี Role เป็น 'owner', 'head' หรือ 'agent' มาแสดงใน Dropdown 'มอบหมายให้'
        // นี่คือผู้ที่สามารถรับผิดชอบ Ticket ได้
        $agents = \App\Models\User::whereIn('role', ['owner', 'head', 'agent'])->get();

        return view('tickets.show', compact('ticket', 'statuses', 'agents'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        // ตรวจสอบสิทธิ์: เฉพาะผู้ที่มีสิทธิ์จัดการ Ticket เท่านั้นที่แก้ไขได้
        if (!Auth::user()->canManageTickets()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // 1. ตรวจสอบข้อมูลจากฟอร์ม
            $validatedData = $request->validate([
                'status_id' => 'required|exists:statuses,id',
                'assigned_to_user_id' => [
                    'nullable',
                    'required',
                    // ตรวจสอบว่าผู้ที่ถูกมอบหมายมี role เป็น owner, head, หรือ agent
                    Rule::exists('users', 'id')->where(function ($query) {
                        $query->whereIn('role', ['owner', 'head', 'agent']);
                    }),
                ],
                'comment' => 'nullable|string|max:1000',
            ], [
                'status_id.required' => 'กรุณาเลือกสถานะ',
                'status_id.exists' => 'สถานะไม่ถูกต้อง',
                'assigned_to_user_id.exists' => 'ผู้รับผิดชอบไม่ถูกต้อง',
                'comment.max' => 'บันทึก/ความคิดเห็นมีความยาวเกินไป',
            ]);

            // บันทึกสถานะเก่าและผู้รับผิดชอบเก่า
            $oldStatusId = $ticket->status_id;
            $oldAssignedToId = $ticket->assigned_to_user_id;

            // 2. อัปเดตข้อมูล Ticket
            $ticket->status_id = $validatedData['status_id'];
            $ticket->assigned_to_user_id = $validatedData['assigned_to_user_id'];
            $ticket->save();

            // 3. เพิ่ม Ticket Update
            $comment = $validatedData['comment'];
            $statusChanged = ($oldStatusId != $validatedData['status_id']);
            $assignedToChanged = ($oldAssignedToId != $validatedData['assigned_to_user_id']);

            // ถ้ามีการเปลี่ยนสถานะหรือผู้รับผิดชอบ หรือมี comment
            if ($statusChanged || $assignedToChanged || $comment) {
                // สร้าง comment อัตโนมัติสำหรับการเปลี่ยนสถานะ/มอบหมายงาน
                $autoComment = '';
                if ($statusChanged) {
                    $newStatus = Status::find($validatedData['status_id'])->name;
                    $autoComment .= "เปลี่ยนสถานะเป็น '{$newStatus}'. ";
                }
                if ($assignedToChanged) {
                    $newAssignedTo = $ticket->assignedTo ? $ticket->assignedTo->name : 'ไม่ได้มอบหมาย';
                    $autoComment .= "มอบหมายให้ '{$newAssignedTo}'. ";
                }

                $ticket->updates()->create([
                    'user_id' => Auth::id(), // ผู้ที่ทำการอัปเดต (ผู้ดูแล)
                    'comment' => trim($comment . ' ' . $autoComment), // รวม comment ที่ผู้ใช้พิมพ์และ comment อัตโนมัติ
                    'status_id' => $validatedData['status_id'], // สถานะใหม่
                ]);
            }

            // 4. ส่ง In-app Notifications
            $this->sendTicketUpdatedNotifications($ticket, $ticket->updates()->latest()->first());

            return redirect()->route('tickets.show', $ticket)->with('success', 'อัปเดตปัญหาสำเร็จแล้ว!');

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการอัปเดตปัญหา: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        // ตรวจสอบสิทธิ์: เฉพาะผู้ที่มีสิทธิ์จัดการ Ticket เท่านั้นที่ลบได้
        if (!Auth::user()->canManageTickets()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $ticket->delete();
            return redirect()->route('tickets.index')->with('success', 'ลบปัญหาสำเร็จแล้ว!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการลบปัญหา: ' . $e->getMessage());
        }
    }

    /**
     * ส่ง In-app Notifications เมื่อมีการสร้าง Ticket ใหม่
     */
    private function sendTicketCreatedNotifications(Ticket $ticket)
    {
        // หาผู้ดูแลทั้งหมด (owner, head, agent)
        $managers = User::whereIn('role', ['owner', 'head', 'agent'])->get();
        
        // ส่ง notification ให้ผู้ดูแลทุกคน
        foreach ($managers as $manager) {
            $manager->notify(new TicketCreatedNotification($ticket));
        }
    }

    /**
     * ส่ง In-app Notifications เมื่อมีการอัปเดต Ticket
     */
    private function sendTicketUpdatedNotifications(Ticket $ticket, $update)
    {
        // ส่ง notification ให้ผู้แจ้งปัญหา
        if ($ticket->user_id !== Auth::id()) {
            $ticket->user->notify(new TicketUpdatedNotification($ticket, $update));
        }

        // ส่ง notification ให้ผู้รับผิดชอบ (ถ้ามี)
        if ($ticket->assigned_to_user_id && $ticket->assigned_to_user_id !== Auth::id()) {
            $ticket->assignedTo->notify(new TicketUpdatedNotification($ticket, $update));
        }

        // ส่ง notification ให้ผู้ดูแลอื่นๆ (ยกเว้นผู้ที่อัปเดต)
        $otherManagers = User::whereIn('role', ['owner', 'head', 'agent'])
                            ->where('id', '!=', Auth::id())
                            ->get();
        
        foreach ($otherManagers as $manager) {
            $manager->notify(new TicketUpdatedNotification($ticket, $update));
        }
    }

    /**
     * ส่ง In-app Notifications เมื่อมีการมอบหมาย Ticket
     */
    private function sendTicketAssignedNotification(Ticket $ticket)
    {
        if ($ticket->assigned_to_user_id && $ticket->assigned_to_user_id !== Auth::id()) {
            $ticket->assignedTo->notify(new TicketAssignedNotification($ticket, Auth::user()));
        }
    }
}