<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <i class="fas fa-ticket-alt text-2xl text-indigo-600"></i>
                <h2 class="text-3xl font-bold text-indigo-700 leading-tight">
            {{ __('รายการปัญหา') }}
        </h2>
            </div>
            <a href="{{ route('tickets.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-200 to-blue-300 text-indigo-800 border border-indigo-300 rounded-xl font-semibold hover:from-indigo-300 hover:to-blue-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                {{ __('แจ้งปัญหาใหม่') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Session Messages --}}
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

            {{-- Search and Filter Bar --}}
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 mb-6">
                <form method="GET" action="{{ route('tickets.index') }}" 
                      x-data="filterData()"
                      class="space-y-3">
                    {{-- Search Bar --}}
                    <div class="flex gap-3">
                        <div class="flex-1">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" 
                                       name="search" 
                                       value="{{ request('search') }}"
                                       placeholder="ค้นหาปัญหา..."
                                       class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm">
                            </div>
                        </div>
                        <button type="button" 
                                @click="showFilters = !showFilters"
                                class="px-4 py-2.5 text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-colors text-sm">
                            <i class="fas fa-filter mr-1.5"></i>
                            กรอง
                        </button>
                        <button type="submit" 
                                class="px-4 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-colors text-sm">
                            <i class="fas fa-search mr-1.5"></i>
                            ค้นหา
                        </button>
                    </div>

                    {{-- Filter Options --}}
                    <div x-show="showFilters" 
                         x-transition:enter="transition ease-out duration-150" 
                         x-transition:enter-start="opacity-0 transform -translate-y-1" 
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 pt-3 border-t border-gray-100">
                        <div>
                            <select name="status" 
                                    @change="$el.form.submit()"
                                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-md focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">สถานะทั้งหมด</option>
                                @foreach ($statuses as $status)
                                    @php
                                        $statusName = $status->name;
                                        $thaiStatus = '';
                                        switch ($statusName) {
                                            case 'New': $thaiStatus = 'คิวรอ'; break;
                                            case 'In Progress': $thaiStatus = 'กำลังดำเนินการ'; break;
                                            case 'Pending': $thaiStatus = 'รอตรวจสอบ'; break;
                                            case 'Resolved': $thaiStatus = 'เสร็จสิ้น'; break;
                                            case 'Closed': $thaiStatus = 'ปิดแล้ว'; break;
                                            case 'Rejected': $thaiStatus = 'ปฏิเสธ'; break;
                                            default: $thaiStatus = $statusName; break;
                                        }
                                    @endphp
                                    <option value="{{ $status->id }}" {{ request('status') == $status->id ? 'selected' : '' }}>
                                        {{ $thaiStatus }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <select name="main_category" 
                                    x-model="mainCategory"
                                    @change="loadSubCategories()"
                                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-md focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">หมวดหมู่หลัก</option>
                                @foreach ($categories->whereNull('parent_category') as $mainCategory)
                                    <option value="{{ $mainCategory->name }}" {{ request('main_category') == $mainCategory->name ? 'selected' : '' }}>
                                        {{ $mainCategory->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <select name="sub_category" 
                                    x-model="subCategory"
                                    x-html="subCategoryOptions"
                                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-md focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                            </select>
                        </div>
                        <div>
                            <select name="priority" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-md focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">ความสำคัญ</option>
                                @foreach ($priorities as $priority)
                                    @php
                                        $priorityName = $priority->name;
                                                    $thaiPriority = '';
                                                    switch ($priorityName) {
                                            case 'High': $thaiPriority = 'เร่งด่วน'; break;
                                            case 'Medium': $thaiPriority = 'สูง'; break;
                                            case 'Low': $thaiPriority = 'ปานกลาง'; break;
                                            case 'Critical': $thaiPriority = 'ต่ำ'; break;
                                            default: $thaiPriority = $priorityName; break;
                                                    }
                                                @endphp
                                    <option value="{{ $priority->id }}" {{ request('priority') == $priority->id ? 'selected' : '' }}>
                                        {{ $thaiPriority }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <select name="sort_by" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-md focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>วันที่แจ้ง</option>
                                <option value="title" {{ request('sort_by') == 'title' ? 'selected' : '' }}>หัวข้อ</option>
                                <option value="priority_id" {{ request('sort_by') == 'priority_id' ? 'selected' : '' }}>ความสำคัญ</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Kanban Taskboard --}}
            @if ($tickets->isEmpty())
                <div class="bg-white p-12 rounded-xl shadow-lg text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 mb-4">
                        <i class="fas fa-ticket-alt text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ __('ไม่มีข้อมูล') }}</h3>
                    <p class="text-gray-600">{{ __('ยังไม่มีการแจ้งปัญหาเข้ามาในระบบ') }}</p>
                </div>
            @else
                <div class="grid gap-6" 
                     :class="{
                        'grid-cols-1': true,
                        'md:grid-cols-2': kanbanStatuses.length <= 2,
                        'md:grid-cols-3': kanbanStatuses.length === 3,
                        'md:grid-cols-4': kanbanStatuses.length === 4,
                        'md:grid-cols-5': kanbanStatuses.length === 5,
                        'md:grid-cols-6': kanbanStatuses.length === 6
                     }"
                     x-data="{
                        tickets: @js($tickets->groupBy('status.name')),
                        kanbanStatuses: @js($kanbanStatuses),
                        dragOverColumn: null,
                        draggedTicket: null,
                        
                        init() {
                            // Listen for page refresh events
                            window.addEventListener('ticketUpdated', () => {
                                this.refreshTickets();
                            });
                            
                            // Refresh tickets every 30 seconds
                            setInterval(() => {
                                this.refreshTickets();
                            }, 30000);
                        },
                        
                        refreshTickets() {
                            // สร้าง URL พร้อม query parameters
                            const url = new URL('{{ route("tickets.ajax") }}', window.location.origin);
                            const form = this.$el;
                            const formData = new FormData(form);
                            
                            // เพิ่ม query parameters
                            for (let [key, value] of formData.entries()) {
                                if (value) {
                                    url.searchParams.append(key, value);
                                }
                            }
                            
                            fetch(url)
                                .then(response => response.json())
                                .then(data => {
                                    if (data.tickets) {
                                        this.tickets = data.tickets;
                                    }
                                    if (data.kanbanStatuses) {
                                        this.kanbanStatuses = data.kanbanStatuses;
                                    }
                                })
                                .catch(error => console.error('Error refreshing tickets:', error));
                        },
                        
                        startDrag(ticket) {
                            this.draggedTicket = ticket;
                        },
                        
                        dragOver(column) {
                            this.dragOverColumn = column;
                        },
                        
                        dragLeave() {
                            this.dragOverColumn = null;
                        },
                        
                        drop(column) {
                            if (this.draggedTicket && this.dragOverColumn) {
                                // Here you would typically make an AJAX call to update the ticket status
                                console.log('Moving ticket', this.draggedTicket.id, 'to column', column);
                                // For now, we'll just reset the drag state
                                this.draggedTicket = null;
                                this.dragOverColumn = null;
                            }
                        }
                     }">
                    
                    @php
                        $statusColumns = [
                            'New' => ['title' => 'คิวรอ', 'color' => 'blue', 'icon' => 'fas fa-inbox'],
                            'In Progress' => ['title' => 'กำลังดำเนินการ', 'color' => 'orange', 'icon' => 'fas fa-play'],
                            'Pending' => ['title' => 'รอตรวจสอบ', 'color' => 'yellow', 'icon' => 'fas fa-eye'],
                            'Resolved' => ['title' => 'เสร็จสิ้น', 'color' => 'green', 'icon' => 'fas fa-check'],
                            'Closed' => ['title' => 'ปิดแล้ว', 'color' => 'gray', 'icon' => 'fas fa-lock'],
                            'Rejected' => ['title' => 'ปฏิเสธ', 'color' => 'red', 'icon' => 'fas fa-times']
                        ];
                        
                        // กำหนดสถานะที่จะแสดงใน Kanban Board
                        $kanbanStatuses = ['New', 'In Progress', 'Pending', 'Resolved'];
                        
                        // ถ้ามีการเลือกสถานะในตัวกรอง ให้เพิ่มสถานะนั้นเข้าไป
                        if (request('status')) {
                            $selectedStatus = \App\Models\Status::find(request('status'));
                            if ($selectedStatus && in_array($selectedStatus->name, ['Closed', 'Rejected'])) {
                                $kanbanStatuses[] = $selectedStatus->name;
                            }
                        }
                    @endphp
                    
                    @foreach($kanbanStatuses as $statusName)
                        @php
                            $column = $statusColumns[$statusName] ?? ['title' => $statusName, 'color' => 'gray', 'icon' => 'fas fa-circle'];
                            $ticketsInStatus = $tickets->where('status.name', $statusName);
                        @endphp
                        
                        <div class="bg-gray-50 rounded-xl p-4 min-h-[600px]"
                             @dragover.prevent="dragOver('{{ $statusName }}')"
                             @dragleave="dragLeave()"
                             @drop.prevent="drop('{{ $statusName }}')"
                             :class="{ 'bg-{{ $column['color'] }}-50': dragOverColumn === '{{ $statusName }}' }">
                            
                            {{-- Column Header --}}
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-2">
                                    <i class="{{ $column['icon'] }} text-{{ $column['color'] }}-600"></i>
                                    <h6 class="font-semibold text-gray-800">{{ $column['title'] }}</h6>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $column['color'] }}-100 text-{{ $column['color'] }}-800">
                                    {{ $ticketsInStatus->count() }}
                                </span>
                            </div>
                            
                            {{-- Tickets in this column --}}
                            <div class="space-y-3">
                                @if($ticketsInStatus->count() > 0)
                                    @foreach($ticketsInStatus as $ticket)
                                        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200 cursor-pointer group"
                                             draggable="true"
                                             @dragstart="startDrag(@js($ticket))"
                                             onclick="window.location.href='{{ route('tickets.show', $ticket) }}'">
                                            
                                            {{-- Ticket Header --}}
                                            <div class="flex items-start justify-between mb-3">
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-sm font-medium text-gray-500">#{{ $ticket->id }}</span>
                                                    @php
                                                        $priorityName = $ticket->priority->name;
                                                        $priorityColor = match ($priorityName) {
                                                            'Critical' => 'bg-red-100 text-red-800 border-red-200',
                                                            'High' => 'bg-orange-100 text-orange-800 border-orange-200',
                                                            'Medium' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                            'Low' => 'bg-green-100 text-green-800 border-green-200',
                                                            default => 'bg-gray-100 text-gray-800 border-gray-200',
                                                        };
                                                @endphp
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full border {{ $priorityColor }}">
                                                        @switch($priorityName)
                                                            @case('Critical') เร่งด่วน @break
                                                            @case('High') สูง @break
                                                            @case('Medium') ปานกลาง @break
                                                            @case('Low') ต่ำ @break
                                                            @default {{ $priorityName }} @break
                                                        @endswitch
                                                    </span>
                                                </div>
                                                <div class="flex items-center space-x-1">
                                                    @if ($ticket->attachments->count() > 0)
                                                        <i class="fas fa-paperclip text-gray-400 text-xs"></i>
                                                    @endif
                                                    @if ($ticket->updates->count() > 0)
                                                        <i class="fas fa-comments text-gray-400 text-xs"></i>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            {{-- Ticket Title --}}
                                            <h6 class="font-semibold text-gray-800 text-sm leading-tight mb-2 group-hover:text-indigo-600 transition-colors">
                                                {{ Str::limit($ticket->title, 50) }}
                                            </h6>
                                            
                                            {{-- Ticket Description --}}
                                            <p class="text-sm text-gray-600 mb-3 leading-relaxed">
                                                {{ Str::limit($ticket->description, 80) }}
                                            </p>
                                            
                                            {{-- Ticket Footer --}}
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-2">
                                                    @if ($ticket->assignedTo)
                                                        <div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center">
                                                            <span class="text-xs font-semibold text-indigo-600">
                                                                {{ substr($ticket->assignedTo->name, 0, 1) }}
                                                            </span>
                                                        </div>
                                                        <span class="text-xs text-gray-600">{{ $ticket->assignedTo->name }}</span>
                                                    @else
                                                        <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center">
                                                            <i class="fas fa-user text-gray-400 text-xs"></i>
                                                        </div>
                                                        <span class="text-xs text-gray-500">ยังไม่ได้มอบหมาย</span>
                                                    @endif
                                                </div>
                                                <span class="text-xs text-gray-500">
                                                    {{ $ticket->created_at->format('M d') }}
                                                </span>
                                            </div>
                                            
                                            {{-- Category Tag --}}
                                            <div class="mt-3">
                                                <span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-full">
                                                    <i class="fas fa-tag mr-1"></i>
                                                    {{ $ticket->category->name }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-8">
                                        <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-gray-100 flex items-center justify-center">
                                            <i class="fas fa-plus text-gray-400"></i>
                        </div>
                                        <p class="text-sm text-gray-500">ไม่มีปัญหาในคอลัมน์นี้</p>
                        </div>
                    @endif
                </div>
            </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script>
        function filterData() {
            return {
                showFilters: false,
                mainCategory: '{{ request('main_category') }}',
                subCategory: '{{ request('sub_category') }}',
                subCategories: [],
                subCategoryOptions: '<option value="">หมวดหมู่ย่อย</option>',
                
                async loadSubCategories() {
                    if (!this.mainCategory) {
                        this.subCategories = [];
                        this.subCategory = '';
                        this.subCategoryOptions = '<option value="">หมวดหมู่ย่อย</option>';
                        return;
                    }
                    
                    try {
                        console.log('Loading subcategories for:', this.mainCategory);
                        const response = await fetch(`/api/categories/${this.mainCategory}/subcategories`);
                        const data = await response.json();
                        console.log('API Response:', data);
                        
                        this.subCategories = data.subcategories || [];
                        
                        // สร้าง HTML options
                        let options = '<option value="">หมวดหมู่ย่อย</option>';
                        this.subCategories.forEach(category => {
                            const selected = this.subCategory == category.id ? 'selected' : '';
                            options += `<option value="${category.id}" ${selected}>${category.name}</option>`;
                        });
                        this.subCategoryOptions = options;
                        console.log('Generated options:', this.subCategoryOptions);
                        this.subCategory = '';
                    } catch (error) {
                        console.error('Error loading subcategories:', error);
                        this.subCategories = [];
                        this.subCategoryOptions = '<option value="">หมวดหมู่ย่อย</option>';
                    }
                },
                
                init() {
                    if (this.mainCategory) {
                        this.loadSubCategories();
                    }
                }
            }
        }
    </script>
</x-app-layout>