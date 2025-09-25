<x-app-layout>
    <x-slot name="head">
        <style>
            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .line-clamp-3 {
                display: -webkit-box;
                -webkit-line-clamp: 3;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
        </style>
    </x-slot>
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
                        @if (Auth::user()->isManager() || Auth::user()->isCEO())
                        <div>
                            <select name="assigned_to" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-md focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">ผู้รับผิดชอบ</option>
                                <option value="me" {{ request('assigned_to') == 'me' ? 'selected' : '' }}>มอบหมายให้ฉัน</option>
                                <option value="unassigned" {{ request('assigned_to') == 'unassigned' ? 'selected' : '' }}>ยังไม่ได้มอบหมาย</option>
                                @foreach ($agents as $agent)
                                    <option value="{{ $agent->id }}" {{ request('assigned_to') == $agent->id ? 'selected' : '' }}>
                                        {{ $agent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
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

            {{-- Summary Cards - มินิมอล โทนพาสเทล --}}
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-6">
                {{-- ปัญหาทั้งหมด --}}
                <a href="{{ route('tickets.index') }}" class="group block">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-2xl border border-blue-200/50 hover:shadow-lg hover:scale-105 transition-all duration-300 ease-out">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-200/60 rounded-full flex items-center justify-center group-hover:bg-blue-300/80 transition-colors duration-300">
                                    <i class="fas fa-ticket-alt text-lg text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-blue-700 group-hover:text-blue-800 transition-colors">ปัญหาทั้งหมด</p>
                                    <p class="text-2xl font-bold text-blue-800 group-hover:text-blue-900 transition-colors">{{ $totalTickets }}</p>
                                </div>
                            </div>
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <i class="fas fa-arrow-right text-blue-500 text-sm"></i>
                            </div>
                        </div>
                    </div>
                </a>
                
                {{-- คิวรอ --}}
                <a href="{{ route('tickets.index', ['status' => \App\Models\Status::where('name', 'New')->first()->id]) }}" class="group block">
                    <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 p-4 rounded-2xl border border-indigo-200/50 hover:shadow-lg hover:scale-105 transition-all duration-300 ease-out">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-indigo-200/60 rounded-full flex items-center justify-center group-hover:bg-indigo-300/80 transition-colors duration-300">
                                    <i class="fas fa-inbox text-lg text-indigo-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-indigo-700 group-hover:text-indigo-800 transition-colors">คิวรอ</p>
                                    <p class="text-2xl font-bold text-indigo-800 group-hover:text-indigo-900 transition-colors">{{ \App\Models\Ticket::whereHas('status', function($q) { $q->where('name', 'New'); })->count() }}</p>
                                </div>
                            </div>
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <i class="fas fa-arrow-right text-indigo-500 text-sm"></i>
                            </div>
                        </div>
                    </div>
                </a>
                
                {{-- เสร็จสิ้น --}}
                <a href="{{ route('tickets.index', ['status' => \App\Models\Status::where('name', 'Resolved')->first()->id]) }}" class="group block">
                    <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-2xl border border-green-200/50 hover:shadow-lg hover:scale-105 transition-all duration-300 ease-out">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-green-200/60 rounded-full flex items-center justify-center group-hover:bg-green-300/80 transition-colors duration-300">
                                    <i class="fas fa-check-circle text-lg text-green-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-green-700 group-hover:text-green-800 transition-colors">เสร็จสิ้น</p>
                                    <p class="text-2xl font-bold text-green-800 group-hover:text-green-900 transition-colors">{{ $resolvedTickets }}</p>
                                </div>
                            </div>
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <i class="fas fa-arrow-right text-green-500 text-sm"></i>
                            </div>
                        </div>
                    </div>
                </a>
                
                {{-- กำลังดำเนินการ --}}
                <a href="{{ route('tickets.index', ['status' => \App\Models\Status::where('name', 'In Progress')->first()->id]) }}" class="group block">
                    <div class="bg-gradient-to-br from-amber-50 to-amber-100 p-4 rounded-2xl border border-amber-200/50 hover:shadow-lg hover:scale-105 transition-all duration-300 ease-out">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-amber-200/60 rounded-full flex items-center justify-center group-hover:bg-amber-300/80 transition-colors duration-300">
                                    <i class="fas fa-clock text-lg text-amber-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-amber-700 group-hover:text-amber-800 transition-colors">กำลังดำเนินการ</p>
                                    <p class="text-2xl font-bold text-amber-800 group-hover:text-amber-900 transition-colors">{{ $inProgressTickets }}</p>
                                </div>
                            </div>
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <i class="fas fa-arrow-right text-amber-500 text-sm"></i>
                            </div>
                        </div>
                    </div>
                </a>
                
                {{-- รอตรวจสอบ --}}
                <a href="{{ route('tickets.index', ['status' => \App\Models\Status::where('name', 'Pending')->first()->id]) }}" class="group block">
                    <div class="bg-gradient-to-br from-rose-50 to-rose-100 p-4 rounded-2xl border border-rose-200/50 hover:shadow-lg hover:scale-105 transition-all duration-300 ease-out">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-rose-200/60 rounded-full flex items-center justify-center group-hover:bg-rose-300/80 transition-colors duration-300">
                                    <i class="fas fa-exclamation-circle text-lg text-rose-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-rose-700 group-hover:text-rose-800 transition-colors">รอตรวจสอบ</p>
                                    <p class="text-2xl font-bold text-rose-800 group-hover:text-rose-900 transition-colors">{{ $newTickets }}</p>
                                </div>
                            </div>
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <i class="fas fa-arrow-right text-rose-500 text-sm"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Pagination Controls - มินิมอล โทนพาสเทล --}}
            <div class="bg-gradient-to-r from-slate-50 to-slate-100 p-4 rounded-2xl border border-slate-200/50 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-6">
                        <span class="text-sm text-slate-600 font-medium">
                            แสดง {{ $tickets->firstItem() ?? 0 }} ถึง {{ $tickets->lastItem() ?? 0 }} 
                            จาก {{ $tickets->total() }} ปัญหา
                        </span>
                        
                        <div class="flex items-center space-x-3">
                            <label for="per_page" class="text-sm text-slate-600 font-medium">แสดงต่อหน้า:</label>
                            <select name="per_page" id="per_page" class="text-sm bg-white border border-slate-300 rounded-lg px-3 py-2 text-slate-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" onchange="this.form.submit()">
                                <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                                <option value="20" {{ request('per_page') == '20' || !request('per_page') ? 'selected' : '' }}>20</option>
                                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-1">
                        @if ($tickets->hasPages())
                            {{-- ปุ่มก่อนหน้า --}}
                            @if ($tickets->onFirstPage())
                                <span class="px-4 py-2 text-sm text-slate-400 bg-slate-100 rounded-lg cursor-not-allowed">
                                    <i class="fas fa-chevron-left mr-1"></i>ก่อนหน้า
                                </span>
                            @else
                                <a href="{{ $tickets->previousPageUrl() }}" class="px-4 py-2 text-sm text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 hover:border-slate-400 transition-all duration-200">
                                    <i class="fas fa-chevron-left mr-1"></i>ก่อนหน้า
                                </a>
                            @endif
                            
                            {{-- หมายเลขหน้า --}}
                            @foreach ($tickets->getUrlRange(1, $tickets->lastPage()) as $page => $url)
                                @if ($page == $tickets->currentPage())
                                    <span class="px-4 py-2 text-sm text-white bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-sm">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="px-4 py-2 text-sm text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 hover:border-slate-400 transition-all duration-200">{{ $page }}</a>
                                @endif
                            @endforeach
                            
                            {{-- ปุ่มถัดไป --}}
                            @if ($tickets->hasMorePages())
                                <a href="{{ $tickets->nextPageUrl() }}" class="px-4 py-2 text-sm text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 hover:border-slate-400 transition-all duration-200">
                                    ถัดไป<i class="fas fa-chevron-right ml-1"></i>
                                </a>
                            @else
                                <span class="px-4 py-2 text-sm text-slate-400 bg-slate-100 rounded-lg cursor-not-allowed">
                                    ถัดไป<i class="fas fa-chevron-right ml-1"></i>
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            {{-- Filtered Status View - เมื่อคลิกการ์ดสถานะ --}}
            @if(request('status'))
                {{-- Header สำหรับสถานะที่เลือก - มินิมอล --}}
                @php
                    $selectedStatus = \App\Models\Status::find(request('status'));
                    $statusInfo = [
                        'New' => ['title' => 'คิวรอ', 'color' => 'indigo', 'icon' => 'fas fa-inbox'],
                        'In Progress' => ['title' => 'กำลังดำเนินการ', 'color' => 'amber', 'icon' => 'fas fa-clock'],
                        'Pending' => ['title' => 'รอตรวจสอบ', 'color' => 'rose', 'icon' => 'fas fa-exclamation-circle'],
                        'Resolved' => ['title' => 'เสร็จสิ้น', 'color' => 'green', 'icon' => 'fas fa-check-circle'],
                    ];
                    $currentStatus = $statusInfo[$selectedStatus->name] ?? ['title' => $selectedStatus->name, 'color' => 'gray', 'icon' => 'fas fa-circle'];
                @endphp
                
                <div class="bg-white border-b border-gray-200 py-4 mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-{{ $currentStatus['color'] }}-100 flex items-center justify-center">
                                <i class="{{ $currentStatus['icon'] }} text-sm text-{{ $currentStatus['color'] }}-600"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">{{ $currentStatus['title'] }}</h2>
                                <p class="text-sm text-gray-500">{{ $tickets->total() }} ปัญหา</p>
                            </div>
                        </div>
                        <a href="{{ route('tickets.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition-colors duration-200">
                            <i class="fas fa-arrow-left mr-1"></i>กลับไปดูทั้งหมด
                        </a>
                    </div>
                </div>

                {{-- Ticket List View - มินิมอล --}}
                <div class="space-y-3">
                    @forelse($tickets as $ticket)
                        <div class="bg-white rounded-lg border border-gray-200 hover:border-gray-300 transition-all duration-200 overflow-hidden">
                            <div class="p-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        {{-- Ticket Header --}}
                                        <div class="flex items-center space-x-2 mb-2">
                                            <span class="text-xs font-medium text-gray-400">#{{ $ticket->id }}</span>
                                            @php
                                                $priorityColors = [
                                                    'Low' => 'bg-gray-100 text-gray-600',
                                                    'Medium' => 'bg-blue-100 text-blue-600',
                                                    'High' => 'bg-orange-100 text-orange-600',
                                                    'Urgent' => 'bg-red-100 text-red-600',
                                                ];
                                                $priorityColor = $priorityColors[$ticket->priority->name] ?? 'bg-gray-100 text-gray-600';
                                            @endphp
                                            <span class="px-2 py-0.5 text-xs font-medium rounded {{ $priorityColor }}">
                                                {{ $ticket->priority->name }}
                                            </span>
                                        </div>
                                        
                                        {{-- Ticket Title --}}
                                        <h3 class="text-base font-medium text-gray-900 mb-1 line-clamp-2">
                                            {{ $ticket->title }}
                                        </h3>
                                        
                                        {{-- Ticket Meta --}}
                                        <div class="flex items-center justify-between text-sm text-gray-500">
                                            <div class="flex items-center space-x-3">
                                                <span>{{ $ticket->user->name }}</span>
                                                @if($ticket->assignedTo)
                                                    <span>→ {{ $ticket->assignedTo->name }}</span>
                                                @endif
                                                <span>{{ $ticket->created_at->format('d/m/Y') }}</span>
                                            </div>
                                            
                                            {{-- Action Button --}}
                                            <a href="{{ route('tickets.show', $ticket) }}" class="text-{{ $currentStatus['color'] }}-600 hover:text-{{ $currentStatus['color'] }}-700 transition-colors duration-200 text-sm font-medium">
                                                ดูรายละเอียด
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white p-8 rounded-lg border border-gray-200 text-center">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 mb-3">
                                <i class="{{ $currentStatus['icon'] }} text-lg text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-800 mb-1">ไม่มีปัญหาในสถานะนี้</h3>
                            <p class="text-gray-500 text-sm">ยังไม่มีการแจ้งปัญหาในสถานะ "{{ $currentStatus['title'] }}"</p>
                        </div>
                    @endforelse
                </div>
            @else
                {{-- Kanban Taskboard - เมื่อไม่มีการกรอง --}}
            @if ($tickets->isEmpty())
                <div class="bg-white p-12 rounded-xl shadow-lg text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 mb-4">
                        <i class="fas fa-ticket-alt text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ __('ไม่มีข้อมูล') }}</h3>
                    <p class="text-gray-600">{{ __('ยังไม่มีการแจ้งปัญหาเข้ามาในระบบ') }}</p>
                </div>
            @else
                <div class="grid gap-6 grid-cols-1 md:grid-cols-4" 
                     x-data="kanbanBoard()">
                    
                    @php
                        $statusColumns = [
                            'New' => ['title' => 'คิวรอ', 'color' => 'blue', 'icon' => 'fas fa-inbox'],
                            'In Progress' => ['title' => 'กำลังดำเนินการ', 'color' => 'orange', 'icon' => 'fas fa-play'],
                            'Pending' => ['title' => 'รอตรวจสอบ', 'color' => 'yellow', 'icon' => 'fas fa-eye'],
                            'Resolved' => ['title' => 'เสร็จสิ้น', 'color' => 'green', 'icon' => 'fas fa-check'],
                            'Closed' => ['title' => 'ปิดแล้ว', 'color' => 'gray', 'icon' => 'fas fa-lock'],
                            'Rejected' => ['title' => 'ปฏิเสธ', 'color' => 'red', 'icon' => 'fas fa-times']
                        ];
                        
                        // ใช้ $kanbanStatuses จาก Controller แทนการกำหนดใหม่
                        // $kanbanStatuses จะถูกส่งมาจาก TicketController
                    @endphp
                    
                    @foreach($kanbanStatuses as $statusName)
                        @php
                            $column = $statusColumns[$statusName] ?? ['title' => $statusName, 'color' => 'gray', 'icon' => 'fas fa-circle'];
                            $ticketsInStatus = $tickets->where('status.name', $statusName);
                        @endphp
                        
                        <div class="bg-gray-50 rounded-xl p-4 min-h-[600px] transition-all duration-200"
                             @dragover.prevent="dragOver('{{ $statusName }}')"
                             @dragleave="dragLeave()"
                             @drop.prevent="drop('{{ $statusName }}')"
                             :class="{ 
                                 'bg-{{ $column['color'] }}-50 border-2 border-{{ $column['color'] }}-300': dragOverColumn === '{{ $statusName }}',
                                 'opacity-50': isUpdating
                             }">
                            
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
                                             @dragend="draggedTicket = null"
                                             :class="{ 'opacity-50 scale-95': draggedTicket && draggedTicket.id === {{ $ticket->id }} }"
                                             onclick="if (!draggedTicket) window.location.href='{{ route('tickets.show', $ticket) }}'">
                                            
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
        
        function kanbanBoard() {
            return {
                tickets: @js($tickets->groupBy('status.name')),
                kanbanStatuses: @js($kanbanStatuses),
                dragOverColumn: null,
                draggedTicket: null,
                isUpdating: false,
                
                init() {
                    console.log('Kanban board initialized');
                },
                
                startDrag(ticket) {
                    this.draggedTicket = ticket;
                    console.log('Started dragging ticket:', ticket.id);
                },
                
                dragOver(column) {
                    this.dragOverColumn = column;
                },
                
                dragLeave() {
                    this.dragOverColumn = null;
                },
                
                async drop(column) {
                    if (this.draggedTicket && this.dragOverColumn) {
                        try {
                            this.isUpdating = true;
                            
                            const formData = new FormData();
                            formData.append('status_name', column);
                            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                            
                            const response = await fetch(`/tickets/${this.draggedTicket.id}/status`, {
                                method: 'PATCH',
                                body: formData
                            });

                            const data = await response.json();

                            if (response.ok && data.success) {
                                this.showNotification('สถานะอัปเดตเรียบร้อยแล้ว', 'success');
                                // Refresh the page to show updated data
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                throw new Error(data.error || 'เกิดข้อผิดพลาดในการอัปเดตสถานะ');
                            }
                        } catch (error) {
                            console.error('Error updating ticket status:', error);
                            this.showNotification('เกิดข้อผิดพลาด: ' + error.message, 'error');
                        } finally {
                            this.draggedTicket = null;
                            this.dragOverColumn = null;
                            this.isUpdating = false;
                        }
                    }
                },
                
                showNotification(message, type = 'info') {
                    const notification = document.createElement('div');
                    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
                        type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' :
                        type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' :
                        'bg-blue-100 text-blue-800 border border-blue-200'
                    }`;
                    
                    notification.innerHTML = `
                        <div class="flex items-center">
                            <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} mr-2"></i>
                            <span class="font-medium">${message}</span>
                            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-gray-500 hover:text-gray-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    
                    document.body.appendChild(notification);
                    
                    setTimeout(() => {
                        if (notification.parentElement) {
                            notification.remove();
                        }
                    }, 5000);
                }
            }
        }
    </script>
</x-app-layout>