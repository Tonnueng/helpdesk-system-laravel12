<?php

namespace App\Http\Controllers;

use App\Models\Workflow;
use App\Models\WorkflowStep;
use App\Models\TicketWorkflow;
use App\Models\Category;
use App\Models\Priority;
use App\Models\Status;
use App\Models\User;
use App\Models\Ticket;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkflowController extends Controller
{
    protected $workflowService;

    public function __construct(WorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    /**
     * Display a listing of workflows.
     */
    public function index(Request $request)
    {
        // ตรวจสอบสิทธิ์ - เฉพาะ Manager และ CEO
        if (!Auth::user()->canViewDashboard()) {
            abort(403, 'Unauthorized access.');
        }

        $query = Workflow::with(['steps']);

        // ค้นหา
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // กรองตามสถานะ
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $workflows = $query->orderBy('sort_order')->paginate(15);

        return view('workflows.index', compact('workflows'));
    }

    /**
     * Show the form for creating a new workflow.
     */
    public function create()
    {
        // ตรวจสอบสิทธิ์
        if (!Auth::user()->canViewDashboard()) {
            abort(403, 'Unauthorized access.');
        }

        $categories = Category::active()->ordered()->get();
        $priorities = Priority::all();
        $statuses = Status::all();
        $users = User::whereIn('role', ['leader', 'manager', 'ceo'])->get();

        return view('workflows.create', compact('categories', 'priorities', 'statuses', 'users'));
    }

    /**
     * Store a newly created workflow.
     */
    public function store(Request $request)
    {
        // ตรวจสอบสิทธิ์
        if (!Auth::user()->canViewDashboard()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'trigger_type' => 'required|in:auto,manual,category_based,priority_based,status_based',
            'trigger_conditions' => 'nullable|array',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $workflow = Workflow::create([
            'name' => $request->name,
            'description' => $request->description,
            'trigger_type' => $request->trigger_type,
            'trigger_conditions' => $request->trigger_conditions,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('workflows.show', $workflow)
            ->with('success', 'สร้าง Workflow เรียบร้อยแล้ว!');
    }

    /**
     * Display the specified workflow.
     */
    public function show(Workflow $workflow)
    {
        // ตรวจสอบสิทธิ์
        if (!Auth::user()->canViewDashboard()) {
            abort(403, 'Unauthorized access.');
        }

        $workflow->load(['steps' => function ($query) {
            $query->orderBy('step_order');
        }]);

        $ticketWorkflows = TicketWorkflow::where('workflow_id', $workflow->id)
            ->with(['ticket.user', 'ticket.status'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('workflows.show', compact('workflow', 'ticketWorkflows'));
    }

    /**
     * Show the form for editing the specified workflow.
     */
    public function edit(Workflow $workflow)
    {
        // ตรวจสอบสิทธิ์
        if (!Auth::user()->canViewDashboard()) {
            abort(403, 'Unauthorized access.');
        }

        $categories = Category::active()->ordered()->get();
        $priorities = Priority::all();
        $statuses = Status::all();
        $users = User::whereIn('role', ['leader', 'manager', 'ceo'])->get();

        $workflow->load(['steps' => function ($query) {
            $query->orderBy('step_order');
        }]);

        return view('workflows.edit', compact('workflow', 'categories', 'priorities', 'statuses', 'users'));
    }

    /**
     * Update the specified workflow.
     */
    public function update(Request $request, Workflow $workflow)
    {
        // ตรวจสอบสิทธิ์
        if (!Auth::user()->canViewDashboard()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'trigger_type' => 'required|in:auto,manual,category_based,priority_based,status_based',
            'trigger_conditions' => 'nullable|array',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $workflow->update([
            'name' => $request->name,
            'description' => $request->description,
            'trigger_type' => $request->trigger_type,
            'trigger_conditions' => $request->trigger_conditions,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('workflows.show', $workflow)
            ->with('success', 'อัปเดต Workflow เรียบร้อยแล้ว!');
    }

    /**
     * Remove the specified workflow.
     */
    public function destroy(Workflow $workflow)
    {
        // ตรวจสอบสิทธิ์
        if (!Auth::user()->canViewDashboard()) {
            abort(403, 'Unauthorized access.');
        }

        // ตรวจสอบว่ามี workflow กำลังทำงานอยู่หรือไม่
        $runningWorkflows = TicketWorkflow::where('workflow_id', $workflow->id)
            ->where('status', 'running')
            ->count();

        if ($runningWorkflows > 0) {
            return redirect()->back()
                ->with('error', 'ไม่สามารถลบ Workflow นี้ได้ เนื่องจากมี Ticket กำลังใช้ Workflow นี้อยู่');
        }

        $workflow->delete();

        return redirect()->route('workflows.index')
            ->with('success', 'ลบ Workflow เรียบร้อยแล้ว!');
    }

    /**
     * Clone a workflow.
     */
    public function clone(Workflow $workflow)
    {
        // ตรวจสอบสิทธิ์
        if (!Auth::user()->canViewDashboard()) {
            abort(403, 'Unauthorized access.');
        }

        $newWorkflow = $workflow->replicate();
        $newWorkflow->name = $workflow->name . ' (คัดลอก)';
        $newWorkflow->is_active = false; // ปิดไว้ก่อน
        $newWorkflow->save();

        // คัดลอก steps
        foreach ($workflow->steps as $step) {
            $newStep = $step->replicate();
            $newStep->workflow_id = $newWorkflow->id;
            $newStep->save();
        }

        return redirect()->route('workflows.show', $newWorkflow)
            ->with('success', 'คัดลอก Workflow เรียบร้อยแล้ว!');
    }

    /**
     * Toggle workflow status.
     */
    public function toggleStatus(Workflow $workflow)
    {
        // ตรวจสอบสิทธิ์
        if (!Auth::user()->canViewDashboard()) {
            abort(403, 'Unauthorized access.');
        }

        $workflow->update(['is_active' => !$workflow->is_active]);

        $status = $workflow->is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
        
        return redirect()->back()
            ->with('success', "{$status} Workflow เรียบร้อยแล้ว!");
    }

    /**
     * Start workflow for a specific ticket.
     */
    public function startForTicket(Request $request, Ticket $ticket)
    {
        // ตรวจสอบสิทธิ์
        if (!Auth::user()->canManageTickets()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'workflow_id' => 'required|exists:workflows,id',
        ]);

        $workflow = Workflow::findOrFail($request->workflow_id);
        
        if (!$workflow->is_active) {
            return redirect()->back()
                ->with('error', 'Workflow นี้ถูกปิดใช้งานอยู่');
        }

        $result = $this->workflowService->startWorkflow($ticket, $workflow);

        if ($result) {
            return redirect()->back()
                ->with('success', "เริ่มต้น Workflow '{$workflow->name}' สำหรับ Ticket นี้เรียบร้อยแล้ว!");
        } else {
            return redirect()->back()
                ->with('error', 'ไม่สามารถเริ่มต้น Workflow ได้');
        }
    }

    /**
     * Get workflows available for a ticket.
     */
    public function getAvailableWorkflows(Ticket $ticket)
    {
        $workflows = Workflow::active()->get()->filter(function ($workflow) use ($ticket) {
            return $workflow->canTrigger($ticket);
        });

        return response()->json([
            'workflows' => $workflows->map(function ($workflow) {
                return [
                    'id' => $workflow->id,
                    'name' => $workflow->name,
                    'description' => $workflow->description,
                    'trigger_conditions_text' => $workflow->trigger_conditions_text,
                ];
            })
        ]);
    }

    /**
     * Get workflow progress for a ticket.
     */
    public function getTicketWorkflow(Ticket $ticket)
    {
        $ticketWorkflow = TicketWorkflow::where('ticket_id', $ticket->id)
            ->where('status', 'running')
            ->with(['workflow', 'currentStep'])
            ->first();

        if (!$ticketWorkflow) {
            return response()->json(['workflow' => null]);
        }

        return response()->json([
            'workflow' => [
                'id' => $ticketWorkflow->id,
                'name' => $ticketWorkflow->workflow->name,
                'status' => $ticketWorkflow->status,
                'status_text' => $ticketWorkflow->status_text,
                'current_step' => $ticketWorkflow->currentStep ? [
                    'id' => $ticketWorkflow->currentStep->id,
                    'name' => $ticketWorkflow->currentStep->name,
                    'action_type_text' => $ticketWorkflow->currentStep->action_type_text,
                    'action_description' => $ticketWorkflow->currentStep->action_description,
                ] : null,
                'progress_percentage' => $ticketWorkflow->getProgressPercentage(),
                'next_action_at' => $ticketWorkflow->next_action_at,
                'started_at' => $ticketWorkflow->started_at,
            ]
        ]);
    }

    /**
     * Pause workflow.
     */
    public function pause(TicketWorkflow $ticketWorkflow)
    {
        // ตรวจสอบสิทธิ์
        if (!Auth::user()->canManageTickets()) {
            abort(403, 'Unauthorized access.');
        }

        $this->workflowService->pauseWorkflow($ticketWorkflow);

        return redirect()->back()
            ->with('success', 'หยุด Workflow เรียบร้อยแล้ว!');
    }

    /**
     * Resume workflow.
     */
    public function resume(TicketWorkflow $ticketWorkflow)
    {
        // ตรวจสอบสิทธิ์
        if (!Auth::user()->canManageTickets()) {
            abort(403, 'Unauthorized access.');
        }

        $this->workflowService->resumeWorkflow($ticketWorkflow);

        return redirect()->back()
            ->with('success', 'เริ่มต้น Workflow ใหม่เรียบร้อยแล้ว!');
    }

    /**
     * Cancel workflow.
     */
    public function cancel(TicketWorkflow $ticketWorkflow)
    {
        // ตรวจสอบสิทธิ์
        if (!Auth::user()->canManageTickets()) {
            abort(403, 'Unauthorized access.');
        }

        $this->workflowService->cancelWorkflow($ticketWorkflow);

        return redirect()->back()
            ->with('success', 'ยกเลิก Workflow เรียบร้อยแล้ว!');
    }

    /**
     * Process due workflows (for cron job).
     */
    public function processDueWorkflows()
    {
        $this->workflowService->processDueWorkflows();
        
        return response()->json(['message' => 'Processed due workflows']);
    }

    /**
     * Show the form for creating a new workflow step.
     */
    public function createStep(Workflow $workflow)
    {
        // ตรวจสอบสิทธิ์
        if (!Auth::user()->canManageTickets()) {
            abort(403, 'Unauthorized access.');
        }

        $users = User::whereIn('role', ['leader', 'manager', 'ceo'])->get();
        $statuses = Status::all();
        
        return view('workflows.steps.create', compact('workflow', 'users', 'statuses'));
    }

    /**
     * Store a newly created workflow step.
     */
    public function storeStep(Request $request, Workflow $workflow)
    {
        // ตรวจสอบสิทธิ์
        if (!Auth::user()->canManageTickets()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'action_type' => 'required|string',
            'action_config' => 'required|array',
            'step_order' => 'required|integer|min:1',
        ]);

        $step = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'step_order' => $request->step_order,
            'action_type' => $request->action_type,
            'action_config' => $request->action_config,
            'delay_minutes' => $request->delay_minutes ?? null,
        ]);

        return redirect()->route('workflows.edit', $workflow)
            ->with('success', 'ขั้นตอนการทำงานถูกเพิ่มเรียบร้อยแล้ว');
    }

    /**
     * Show the form for editing the specified workflow step.
     */
    public function editStep(Workflow $workflow, WorkflowStep $step)
    {
        // ตรวจสอบสิทธิ์
        if (!Auth::user()->canManageTickets()) {
            abort(403, 'Unauthorized access.');
        }

        $users = User::whereIn('role', ['leader', 'manager', 'ceo'])->get();
        $statuses = Status::all();
        
        return view('workflows.steps.edit', compact('workflow', 'step', 'users', 'statuses'));
    }

    /**
     * Update the specified workflow step.
     */
    public function updateStep(Request $request, Workflow $workflow, WorkflowStep $step)
    {
        // ตรวจสอบสิทธิ์
        if (!Auth::user()->canManageTickets()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'action_type' => 'required|string',
            'action_config' => 'required|array',
            'step_order' => 'required|integer|min:1',
        ]);

        $step->update([
            'step_order' => $request->step_order,
            'action_type' => $request->action_type,
            'action_config' => $request->action_config,
            'delay_minutes' => $request->delay_minutes ?? null,
        ]);

        return redirect()->route('workflows.edit', $workflow)
            ->with('success', 'ขั้นตอนการทำงานถูกอัปเดตเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified workflow step.
     */
    public function destroyStep(Workflow $workflow, WorkflowStep $step)
    {
        // ตรวจสอบสิทธิ์
        if (!Auth::user()->canManageTickets()) {
            abort(403, 'Unauthorized access.');
        }

        $step->delete();

        return redirect()->route('workflows.edit', $workflow)
            ->with('success', 'ขั้นตอนการทำงานถูกลบเรียบร้อยแล้ว');
    }
}