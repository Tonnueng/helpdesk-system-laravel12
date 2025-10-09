<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-indigo-700 leading-tight">
                    <i class="fas fa-eye mr-3"></i>
                    {{ __('รายละเอียด Workflow') }}
                </h2>
                <p class="text-gray-600 mt-1">{{ $workflow->name }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('workflows.edit', $workflow) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <i class="fas fa-edit mr-2"></i>
                    แก้ไข
                </a>
                <a href="{{ route('workflows.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    กลับ
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- แจ้งเตือนสำเร็จ --}}
            @if (session('success'))
                <div class="mb-6 flex items-start bg-green-50 border border-green-300 rounded-xl p-4" role="alert">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="font-semibold text-green-700">{{ __('สำเร็จ!') }}</p>
                        <p class="text-green-600">{{ session('success') }}</p>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            {{-- แจ้งเตือนข้อผิดพลาด --}}
            @if (session('error'))
                <div class="mb-6 flex items-start bg-red-50 border border-red-300 rounded-xl p-4" role="alert">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="font-semibold text-red-700">{{ __('ผิดพลาด!') }}</p>
                        <p class="text-red-600">{{ session('error') }}</p>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- ข้อมูล Workflow --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-info-circle mr-2"></i>
                                ข้อมูล Workflow
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อ Workflow</label>
                                    <p class="text-gray-900">{{ $workflow->name }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">สถานะ</label>
                                    @if($workflow->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>เปิดใช้งาน
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>ปิดใช้งาน
                                        </span>
                                    @endif
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">ประเภทการเรียกใช้</label>
                                    @switch($workflow->trigger_type)
                                        @case('auto')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="fas fa-bolt mr-1"></i>อัตโนมัติ
                                            </span>
                                            @break
                                        @case('manual')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <i class="fas fa-hand-paper mr-1"></i>ด้วยตนเอง
                                            </span>
                                            @break
                                        @case('category_based')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-tags mr-1"></i>ตามประเภท
                                            </span>
                                            @break
                                        @case('priority_based')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>ตามความสำคัญ
                                            </span>
                                            @break
                                        @case('status_based')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                <i class="fas fa-info-circle mr-1"></i>ตามสถานะ
                                            </span>
                                            @break
                                    @endswitch
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">วันที่สร้าง</label>
                                    <p class="text-gray-900">{{ $workflow->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            
                            @if($workflow->description)
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">คำอธิบาย</label>
                                    <p class="text-gray-900">{{ $workflow->description }}</p>
                                </div>
                            @endif
                            
                            @if($workflow->trigger_conditions_text)
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">เงื่อนไขการเรียกใช้</label>
                                    <p class="text-gray-900">{{ $workflow->trigger_conditions_text }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- ขั้นตอนการทำงาน --}}
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-list-ol mr-2"></i>
                                ขั้นตอนการทำงาน ({{ $workflow->steps->count() }} ขั้นตอน)
                            </h3>
                            
                            @if($workflow->steps->count() > 0)
                                <div class="space-y-4">
                                    @foreach($workflow->steps as $index => $step)
                                        <div class="border border-gray-200 rounded-lg p-4 {{ $index % 2 === 0 ? 'bg-gray-50' : 'bg-white' }}">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center mb-2">
                                                        <span class="inline-flex items-center justify-center w-8 h-8 bg-indigo-100 text-indigo-800 text-sm font-medium rounded-full mr-3">
                                                            {{ $step->step_order }}
                                                        </span>
                                                        <h4 class="text-lg font-medium text-gray-900">{{ $step->name }}</h4>
                                                        @if($step->is_required)
                                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                                <i class="fas fa-asterisk mr-1"></i>บังคับ
                                                            </span>
                                                        @endif
                                                    </div>
                                                    
                                                    @if($step->description)
                                                        <p class="text-gray-600 mb-2">{{ $step->description }}</p>
                                                    @endif
                                                    
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700 mb-1">ประเภทการกระทำ</label>
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                {{ $step->action_type_text }}
                                                            </span>
                                                        </div>
                                                        
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700 mb-1">รายละเอียดการกระทำ</label>
                                                            <p class="text-sm text-gray-900">{{ $step->action_description }}</p>
                                                        </div>
                                                    </div>
                                                    
                                                    @if($step->conditions && count($step->conditions) > 0)
                                                        <div class="mt-3">
                                                            <label class="block text-sm font-medium text-gray-700 mb-1">เงื่อนไข</label>
                                                            <div class="text-sm text-gray-900">
                                                                @if(isset($step->conditions['time_delay']))
                                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mr-2">
                                                                        <i class="fas fa-clock mr-1"></i>รอ {{ $step->conditions['time_delay'] }} นาที
                                                                    </span>
                                                                @endif
                                                                @if(isset($step->conditions['user_response_required']))
                                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                        <i class="fas fa-user mr-1"></i>รอการตอบกลับ
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <i class="fas fa-list-ol text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-500">ยังไม่มีขั้นตอนการทำงาน</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="lg:col-span-1">
                    {{-- การดำเนินการ --}}
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-cogs mr-2"></i>
                                การดำเนินการ
                            </h3>
                            
                            <div class="space-y-3">
                                <a href="{{ route('workflows.edit', $workflow) }}" 
                                   class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-edit mr-2"></i>
                                    แก้ไข Workflow
                                </a>
                                
                                <form action="{{ route('workflows.toggleStatus', $workflow) }}" method="POST" class="w-full">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="w-full bg-{{ $workflow->is_active ? 'yellow' : 'green' }}-600 hover:bg-{{ $workflow->is_active ? 'yellow' : 'green' }}-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-{{ $workflow->is_active ? 'pause' : 'play' }} mr-2"></i>
                                        {{ $workflow->is_active ? 'ปิดใช้งาน' : 'เปิดใช้งาน' }}
                                    </button>
                                </form>
                                
                                <form action="{{ route('workflows.clone', $workflow) }}" method="POST" class="w-full">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-copy mr-2"></i>
                                        คัดลอก Workflow
                                    </button>
                                </form>
                                
                                <form action="{{ route('workflows.destroy', $workflow) }}" method="POST" 
                                      onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบ Workflow นี้?')" class="w-full">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-trash mr-2"></i>
                                        ลบ Workflow
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- สถิติการใช้งาน --}}
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-chart-bar mr-2"></i>
                                สถิติการใช้งาน
                            </h3>
                            
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">จำนวน Ticket ทั้งหมด</span>
                                    <span class="font-semibold text-gray-900">{{ $ticketWorkflows->total() }}</span>
                                </div>
                                
                                @php
                                    $runningCount = $ticketWorkflows->where('status', 'running')->count();
                                    $completedCount = $ticketWorkflows->where('status', 'completed')->count();
                                    $pausedCount = $ticketWorkflows->where('status', 'paused')->count();
                                    $cancelledCount = $ticketWorkflows->where('status', 'cancelled')->count();
                                @endphp
                                
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">กำลังดำเนินการ</span>
                                    <span class="font-semibold text-blue-600">{{ $runningCount }}</span>
                                </div>
                                
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">เสร็จสิ้น</span>
                                    <span class="font-semibold text-green-600">{{ $completedCount }}</span>
                                </div>
                                
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">หยุดชั่วคราว</span>
                                    <span class="font-semibold text-yellow-600">{{ $pausedCount }}</span>
                                </div>
                                
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">ยกเลิก</span>
                                    <span class="font-semibold text-red-600">{{ $cancelledCount }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- รายการ Ticket ที่ใช้ Workflow นี้ --}}
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-ticket-alt mr-2"></i>
                        Ticket ที่ใช้ Workflow นี้ ({{ $ticketWorkflows->total() }} รายการ)
                    </h3>
                    
                    @if($ticketWorkflows->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ผู้แจ้ง</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สถานะ</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ความคืบหน้า</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">วันที่เริ่ม</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">การจัดการ</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($ticketWorkflows as $ticketWorkflow)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <a href="{{ route('tickets.show', $ticketWorkflow->ticket) }}" class="text-indigo-600 hover:text-indigo-900">
                                                            #{{ $ticketWorkflow->ticket->id }} - {{ $ticketWorkflow->ticket->title }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $ticketWorkflow->ticket->user->name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($ticketWorkflow->ticket->status)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $ticketWorkflow->ticket->status->name }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-1">
                                                        <div class="bg-gray-200 rounded-full h-2 w-full">
                                                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $ticketWorkflow->getProgressPercentage() }}%"></div>
                                                        </div>
                                                        <div class="text-xs text-gray-500 mt-1">{{ $ticketWorkflow->getProgressPercentage() }}%</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $ticketWorkflow->started_at ? $ticketWorkflow->started_at->format('d/m/Y H:i') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex items-center space-x-2">
                                                    @if($ticketWorkflow->status === 'running')
                                                        <form action="{{ route('ticket-workflows.pause', $ticketWorkflow) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="text-yellow-600 hover:text-yellow-900" title="หยุดชั่วคราว">
                                                                <i class="fas fa-pause"></i>
                                                            </button>
                                                        </form>
                                                    @elseif($ticketWorkflow->status === 'paused')
                                                        <form action="{{ route('ticket-workflows.resume', $ticketWorkflow) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="text-green-600 hover:text-green-900" title="เริ่มต้นใหม่">
                                                                <i class="fas fa-play"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    @if($ticketWorkflow->status === 'running' || $ticketWorkflow->status === 'paused')
                                                        <form action="{{ route('ticket-workflows.cancel', $ticketWorkflow) }}" method="POST" 
                                                              onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะยกเลิก Workflow นี้?')" class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="text-red-600 hover:text-red-900" title="ยกเลิก">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-6">
                            {{ $ticketWorkflows->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-ticket-alt text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">ยังไม่มี Ticket ที่ใช้ Workflow นี้</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
