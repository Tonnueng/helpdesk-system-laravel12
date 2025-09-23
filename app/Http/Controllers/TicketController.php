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
    public function index(Request $request)
    {
        $query = Ticket::with(['user', 'category', 'priority', 'status']);

        // ค้นหาตามคำค้น
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // กรองตามสถานะ
        if ($request->filled('status')) {
            $query->where('status_id', $request->status);
        }

        // กรองตามหมวดหมู่หลัก
        if ($request->filled('main_category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('parent_category', $request->main_category);
            });
        }

        // กรองตามหมวดหมู่ย่อย
        if ($request->filled('sub_category')) {
            $query->where('category_id', $request->sub_category);
        }

        // กรองตามประเภท (สำหรับ backward compatibility)
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // กรองตามระดับความสำคัญ
        if ($request->filled('priority')) {
            $query->where('priority_id', $request->priority);
        }

        // กรองตามวันที่
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // กำหนดสิทธิ์การดูปัญหาตาม Role ใหม่
        if (Auth::user()->isEmployee()) {
            // พนักงาน: ดูเฉพาะปัญหาที่ตนเองแจ้ง
            $query->where('user_id', Auth::id());
        } elseif (Auth::user()->isLeader()) {
            // หัวหน้าทีม: ดูปัญหาที่มอบหมายให้ + ปัญหาของทีม (ตาม department)
            $query->where(function ($q) {
                $q->where('assigned_to_user_id', Auth::id()) // ปัญหาที่มอบหมายให้
                  ->orWhereHas('user', function ($userQuery) {
                      $userQuery->where('department', Auth::user()->department);
                  }); // ปัญหาของทีม
            });
        } elseif (Auth::user()->isManager() || Auth::user()->isCEO()) {
            // ผู้จัดการ/CEO: ดูปัญหาทั้งหมด
            // กรองตามผู้รับผิดชอบ
            if ($request->filled('assigned_to')) {
                if ($request->assigned_to === 'me') {
                    $query->where('assigned_to_user_id', Auth::id());
                } elseif ($request->assigned_to === 'unassigned') {
                    $query->whereNull('assigned_to_user_id');
                } else {
                    $query->where('assigned_to_user_id', $request->assigned_to);
                }
            }
        }

        // เรียงลำดับ
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $tickets = $query->paginate(10)->withQueryString();

        // โหลดข้อมูลสำหรับ dropdown filters
        $categories = Category::all();
        $priorities = Priority::all();
        $statuses = Status::all();
        $agents = User::whereIn('role', ['leader', 'manager', 'ceo'])->get();


        // กำหนดสถานะที่จะแสดงใน Kanban Board
        $kanbanStatuses = ['New', 'In Progress', 'Pending', 'Resolved'];
        
        // ถ้ามีการเลือกสถานะในตัวกรอง ให้เพิ่มสถานะนั้นเข้าไป
        if ($request->filled('status')) {
            $selectedStatus = Status::find($request->status);
            if ($selectedStatus && in_array($selectedStatus->name, ['Closed', 'Rejected'])) {
                $kanbanStatuses[] = $selectedStatus->name;
            }
        }

        return view('tickets.index', compact('tickets', 'categories', 'priorities', 'statuses', 'agents', 'kanbanStatuses'));
    }

    public function ajaxIndex(Request $request)
    {
        $query = Ticket::with(['user', 'category', 'priority', 'status']);

        // ค้นหาตามคำค้น
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // กรองตามสถานะ
        if ($request->filled('status')) {
            $query->where('status_id', $request->status);
        }

        // กรองตามหมวดหมู่หลัก
        if ($request->filled('main_category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('parent_category', $request->main_category);
            });
        }

        // กรองตามหมวดหมู่ย่อย
        if ($request->filled('sub_category')) {
            $query->where('category_id', $request->sub_category);
        }

        // กรองตามประเภท (สำหรับ backward compatibility)
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // กรองตามระดับความสำคัญ
        if ($request->filled('priority')) {
            $query->where('priority_id', $request->priority);
        }

        // กรองตามวันที่
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // กำหนดสิทธิ์การดูปัญหาตาม Role ใหม่
        if (Auth::user()->isEmployee()) {
            // พนักงาน: ดูเฉพาะปัญหาที่ตนเองแจ้ง
            $query->where('user_id', Auth::id());
        } elseif (Auth::user()->isLeader()) {
            // หัวหน้าทีม: ดูปัญหาที่มอบหมายให้ + ปัญหาของทีม (ตาม department)
            $query->where(function ($q) {
                $q->where('assigned_to_user_id', Auth::id()) // ปัญหาที่มอบหมายให้
                  ->orWhereHas('user', function ($userQuery) {
                      $userQuery->where('department', Auth::user()->department);
                  }); // ปัญหาของทีม
            });
        } elseif (Auth::user()->isManager() || Auth::user()->isCEO()) {
            // ผู้จัดการ/CEO: ดูปัญหาทั้งหมด
            // กรองตามผู้รับผิดชอบ
            if ($request->filled('assigned_to')) {
                if ($request->assigned_to === 'me') {
                    $query->where('assigned_to_user_id', Auth::id());
                } elseif ($request->assigned_to === 'unassigned') {
                    $query->whereNull('assigned_to_user_id');
                } else {
                    $query->where('assigned_to_user_id', $request->assigned_to);
                }
            }
        }

        // เรียงลำดับ
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $tickets = $query->get();

        // จัดกลุ่มตามสถานะ
        $groupedTickets = $tickets->groupBy('status.name');

        // กำหนดสถานะที่จะแสดงใน Kanban Board
        $kanbanStatuses = ['New', 'In Progress', 'Pending', 'Resolved'];
        
        // ถ้ามีการเลือกสถานะในตัวกรอง ให้เพิ่มสถานะนั้นเข้าไป
        if ($request->filled('status')) {
            $selectedStatus = Status::find($request->status);
            if ($selectedStatus && in_array($selectedStatus->name, ['Closed', 'Rejected'])) {
                $kanbanStatuses[] = $selectedStatus->name;
            }
        }

        return response()->json([
            'tickets' => $groupedTickets,
            'kanbanStatuses' => $kanbanStatuses
        ]);
    }

    public function create()
    {
        // โหลดข้อมูลหมวดหมู่หลักและย่อย, Priority, Status เพื่อใช้ใน Dropdown ของฟอร์ม
        $mainCategories = Category::mainCategories()->get();
        $priorities = Priority::all();
        $statuses = Status::all(); // อาจไม่จำเป็นสำหรับฟอร์มสร้าง แต่เตรียมไว้ก่อน

        return view('tickets.create', compact('mainCategories', 'priorities', 'statuses'));
    }

    public function store(Request $request)
    {

        try {
            // 1. ตรวจสอบข้อมูลจากฟอร์ม
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'main_category' => 'required|string',
                'sub_category_id' => 'required|exists:categories,id',
                'priority_id' => 'required|exists:priorities,id',
                'reported_at' => 'nullable|date',
                'phone' => 'nullable|string|max:30',
                'position' => 'nullable|in:หัวหน้า,พนักงานปกติ',
                'department' => 'nullable|in:programer,product,marketing,admin,hr,manager,editor,finance',
            ], [
                'title.required' => 'กรุณากรอกหัวข้อปัญหา',
                'description.required' => 'กรุณากรอกรายละเอียดปัญหา',
                'main_category.required' => 'กรุณาเลือกหมวดหมู่หลัก',
                'sub_category_id.required' => 'กรุณาเลือกหมวดหมู่ย่อย',
                'sub_category_id.exists' => 'หมวดหมู่ย่อยไม่ถูกต้อง',
                'priority_id.required' => 'กรุณาเลือกระดับความสำคัญ',
                'priority_id.exists' => 'ระดับความสำคัญไม่ถูกต้อง',
                'reported_at.date' => 'รูปแบบวันที่และเวลาไม่ถูกต้อง',
                'phone.max' => 'เบอร์โทรศัพท์ยาวเกินไป',
                'position.in' => 'ตำแหน่งไม่ถูกต้อง',
                'department.in' => 'แผนกไม่ถูกต้อง',
            ]);

            // 2. อัปเดตข้อมูลส่วนตัวของผู้ใช้ (phone, position, department)
            $user = Auth::user();
            $user->phone = $validatedData['phone'] ?? $user->phone;
            $user->position = $validatedData['position'] ?? $user->position;
            $user->department = $validatedData['department'] ?? $user->department;
            $user->save();

            // 3. หาหัวหน้าตามหมวดหมู่หลัก
            $mainCategory = $validatedData['main_category'];
            $assignedLeader = $this->findLeaderByMainCategory($mainCategory);

            // 4. สร้าง Ticket ใหม่
            $ticket = new Ticket();
            $ticket->user_id = $user->id; // ผู้แจ้งคือผู้ที่เข้าสู่ระบบปัจจุบัน
            $ticket->category_id = $validatedData['sub_category_id']; // ใช้หมวดหมู่ย่อย
            $ticket->priority_id = $validatedData['priority_id'];
            $ticket->status_id = Status::where('name', 'New')->first()->id; // กำหนดสถานะเริ่มต้นเป็น 'New'
            $ticket->title = $validatedData['title'];
            $ticket->description = $validatedData['description'];
            $ticket->reported_at = $validatedData['reported_at'];
            
            // มอบหมายให้หัวหน้าตามหมวดหมู่หลัก
            if ($assignedLeader) {
                $ticket->assigned_to_user_id = $assignedLeader->id;
            }
            
            $ticket->save();



            // 5. สร้าง Ticket Update (แจ้งว่ามีการสร้าง Ticket ใหม่)
            $comment = 'Ticket created.';
            if ($assignedLeader) {
                $comment .= ' Assigned to ' . $assignedLeader->name . ' (Team Leader for ' . $mainCategory . ').';
            }
            
            $ticket->updates()->create([
                'user_id' => Auth::id(),
                'comment' => $comment,
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
        // ตรวจสอบสิทธิ์การดูปัญหาตาม Role ใหม่
        $canView = false;
        
        if (Auth::user()->isEmployee()) {
            // พนักงาน: ดูเฉพาะปัญหาที่ตนเองแจ้ง
            $canView = ($ticket->user_id === Auth::id());
        } elseif (Auth::user()->isLeader()) {
            // หัวหน้าทีม: ดูปัญหาที่มอบหมายให้ + ปัญหาของทีม (ตาม department)
            $canView = ($ticket->assigned_to_user_id === Auth::id()) || 
                      ($ticket->user->department === Auth::user()->department);
        } elseif (Auth::user()->isManager() || Auth::user()->isCEO()) {
            // ผู้จัดการ/CEO: ดูปัญหาทั้งหมด
            $canView = true;
        }
        
        if (!$canView) {
            abort(403, 'Unauthorized access.');
        }

        // โหลดความสัมพันธ์ที่จำเป็นทั้งหมด
        $ticket->load(['user', 'category', 'priority', 'status', 'assignedTo', 'attachments', 'updates.user', 'updates.status']);

        // โหลดสถานะทั้งหมดสำหรับ Dropdown การเปลี่ยนสถานะ (สำหรับผู้ดูแล)
        $statuses = Status::all();

        // ดึงผู้ใช้งานที่สามารถรับผิดชอบ Ticket ได้
        $user = Auth::user();
        if ($user->isCEO() || $user->isManager()) {
            // CEO และผู้จัดการ: สามารถเลือกหัวหน้าทีม, ผู้จัดการ, และ CEO อื่นๆ
            $agents = \App\Models\User::whereIn('role', ['leader', 'manager', 'ceo'])->get();
        } elseif ($user->isLeader()) {
            // หัวหน้าทีม: สามารถเลือกหัวหน้าทีมคนอื่นและผู้จัดการ
            $agents = \App\Models\User::whereIn('role', ['leader', 'manager'])->get();
        } else {
            // พนักงาน: ไม่สามารถมอบหมายให้ใคร
            $agents = collect();
        }

        return view('tickets.show', compact('ticket', 'statuses', 'agents'));
    }


    public function edit(Ticket $ticket)
    {
        //
    }


    public function update(Request $request, Ticket $ticket)
    {
        // ตรวจสอบสิทธิ์: เฉพาะ Leader, Manager, CEO เท่านั้นที่แก้ไขได้
        if (!Auth::user()->canManageTickets()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // 1. ตรวจสอบข้อมูลจากฟอร์ม
            $validatedData = $request->validate([
                'status_id' => 'required|exists:statuses,id',
                'assigned_to_user_id' => [
                    'nullable',
                    // ตรวจสอบว่าผู้ที่ถูกมอบหมายมี role เป็น manager, leader, หรือ ceo (ถ้ามีการเลือก)
                    Rule::exists('users', 'id')->where(function ($query) {
                        $query->whereIn('role', ['manager', 'leader', 'ceo']);
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

            return redirect()->route('tickets.index')->with('success', 'อัปเดตปัญหาสำเร็จแล้ว!');

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการอัปเดตปัญหา: ' . $e->getMessage())->withInput();
        }
    }


    public function destroy(Ticket $ticket)
    {
        // ตรวจสอบสิทธิ์: เฉพาะ Leader, Manager, CEO เท่านั้นที่ลบได้
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


    private function sendTicketCreatedNotifications(Ticket $ticket)
    {
        // ส่ง notification ให้หัวหน้าทีมที่ได้รับมอบหมาย
        if ($ticket->assignedTo) {
            $ticket->assignedTo->notify(new TicketCreatedNotification($ticket));
        }

        // หาผู้จัดการและ CEO
        $managers = User::whereIn('role', ['manager', 'ceo'])->get();

        // ส่ง notification ให้ผู้จัดการและ CEO
        foreach ($managers as $manager) {
            $manager->notify(new TicketCreatedNotification($ticket));
        }
    }


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



    private function sendTicketAssignedNotification(Ticket $ticket)
    {
        if ($ticket->assigned_to_user_id && $ticket->assigned_to_user_id !== Auth::id()) {
            $ticket->assignedTo->notify(new TicketAssignedNotification($ticket, Auth::user()));
        }
    }

    /**
     * หาหัวหน้าตามหมวดหมู่หลัก
     */
    private function findLeaderByMainCategory($mainCategory)
    {
        // Mapping หมวดหมู่หลักกับหัวหน้าทีม
        $categoryLeaderMapping = [
            'Operation & Production' => 'leader.operation@test.com',
            'Sales & Customer' => 'leader.sales@test.com',
            'Marketing & Ads' => 'leader.marketing@test.com',
            'Finance & Accounting' => 'leader.finance@test.com',
            'People & HR' => 'leader.hr@test.com',
            'IT & Systems' => 'leader.it@test.com',
            'Supplier & Partner' => 'leader.supplier@test.com',
            'Strategy & Management' => 'leader.strategy@test.com',
        ];

        $leaderEmail = $categoryLeaderMapping[$mainCategory] ?? null;
        
        if ($leaderEmail) {
            return User::where('email', $leaderEmail)->where('role', 'leader')->first();
        }

        return null;
    }
}
