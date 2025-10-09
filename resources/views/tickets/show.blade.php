<x-app-layout>
    <x-slot name="header">
       
    </x-slot>

    <div class="py-8 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl rounded-xl">
                <div class="p-6 text-gray-900">

                    {{-- ปุ่มกลับด้านบน --}}
                    <div class="mb-6">
                        <a href="{{ route('tickets.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-100 border border-indigo-200 rounded-lg font-semibold text-sm text-indigo-700 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-arrow-left mr-2"></i>
                            {{ __('กลับสู่หน้ารายการปัญหา') }}
                        </a>
                    </div>

                    <h1 class="text-3xl font-bold mb-8 text-indigo-700">{{ __('รายละเอียดปัญหา: ') . $ticket->title }}</h1>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="md:col-span-2">
                            {{-- Card ข้อมูลปัญหา --}}
                            <div class="bg-white rounded-xl shadow-lg border border-gray-200 h-full">
                                <div class="px-6 py-4 bg-gradient-to-r from-indigo-200 to-blue-300 text-indigo-800 font-bold text-lg rounded-t-xl flex justify-between items-center">
                                    <h5 class="mb-0">{{ __('ข้อมูลปัญหา') }} <span class="ml-2 px-3 py-1 bg-white text-indigo-600 rounded-full text-sm font-semibold">#{{ $ticket->id }}</span></h5>
                                    {{-- เพิ่มปุ่มแก้ไขตรงนี้ถ้ามี --}}
                                    {{-- @if (Auth::check() && (Auth::user()->can('update', $ticket) || Auth::user()->canManageTickets()))
                                        <a href="{{ route('tickets.edit', $ticket) }}" class="px-3 py-1 bg-white text-indigo-600 rounded-md text-sm hover:bg-gray-100 transition duration-150 ease-in-out">
                                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            แก้ไข
                                        </a>
                                    @endif --}}
                                </div>
                                <div class="p-6">
                                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-6">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">{{ __('หัวข้อปัญหา:') }}</dt>
                                            <dd class="mt-1 text-gray-900 font-semibold">{{ $ticket->title }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">{{ __('ผู้แจ้ง:') }}</dt>
                                            <dd class="mt-1 text-gray-900">{{ $ticket->user->name ?? 'N/A' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">{{ __('ประเภทปัญหา:') }}</dt>
                                            <dd class="mt-1 text-gray-900">{{ $ticket->category->name ?? 'N/A' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">{{ __('ระดับความสำคัญ:') }}</dt>
                                            <dd class="mt-1">
                                                @php
                                                    $priorityName = $ticket->priority->name;
                                                    $thaiPriority = '';
                                                    switch ($priorityName) {
                                                        case 'Low':
                                                            $thaiPriority = 'ต่ำ';
                                                            break;
                                                        case 'Medium':
                                                            $thaiPriority = 'ปานกลาง';
                                                            break;
                                                        case 'High':
                                                            $thaiPriority = 'สูง';
                                                            break;
                                                        case 'Critical': // เพิ่ม case สำหรับ Critical
                                                            $thaiPriority = 'วิกฤต';
                                                            break;
                                                        default:
                                                            $thaiPriority = $priorityName; // กรณีไม่ตรงกับเงื่อนไข ให้แสดงชื่อเดิม
                                                            break;
                                                    }
                                                @endphp
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                                                    @if($priorityName == 'Critical') bg-gradient-to-r from-red-200 to-red-300 text-red-800
                                                    @elseif($priorityName == 'High') bg-gradient-to-r from-orange-200 to-orange-300 text-orange-800
                                                    @elseif($priorityName == 'Medium') bg-gradient-to-r from-blue-200 to-blue-300 text-blue-800
                                                    @else bg-gradient-to-r from-emerald-200 to-green-300 text-emerald-800 @endif
                                                ">{{ $thaiPriority }}</span>
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">{{ __('สถานะ:') }}</dt>
                                            <dd class="mt-1">
                                                @php
                                                    $statusName = $ticket->status->name;
                                                    $thaiStatus = '';
                                                    switch ($statusName) {
                                                        case 'New':
                                                            $thaiStatus = 'ใหม่';
                                                            break;
                                                        case 'In Progress':
                                                            $thaiStatus = 'อยู่ระหว่างดำเนินการ';
                                                            break;
                                                        case 'Pending':
                                                            $thaiStatus = 'กำลังดำเนินการแก้ไข';
                                                            break;
                                                        case 'Resolved':
                                                            $thaiStatus = 'แก้ไขแล้ว';
                                                            break;
                                                        case 'Closed':
                                                            $thaiStatus = 'ปิดแล้ว';
                                                            break;
                                                        case 'Rejected':
                                                            $thaiStatus = 'ยกเลิก';
                                                            break;
                                                        default:
                                                            $thaiStatus = $statusName; // กรณีไม่ตรงกับเงื่อนไข ให้แสดงชื่อเดิม
                                                            break;
                                                    }
                                                @endphp
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                                                    @if($statusName == 'New') bg-gradient-to-r from-blue-200 to-blue-300 text-blue-800
                                                    @elseif($statusName == 'In Progress') bg-gradient-to-r from-indigo-200 to-indigo-300 text-indigo-800
                                                    @elseif($statusName == 'Resolved') bg-gradient-to-r from-emerald-200 to-green-300 text-emerald-800
                                                    @elseif($statusName == 'Closed') bg-gradient-to-r from-gray-200 to-gray-300 text-gray-800
                                                    @else bg-gradient-to-r from-red-200 to-red-300 text-red-800 @endif">
                                                    {{ $thaiStatus }}
                                                </span>
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">{{ __('ผู้รับผิดชอบ:') }}</dt>
                                            <dd class="mt-1 text-gray-900">{{ $ticket->assignedTo ? $ticket->assignedTo->name : '-' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">{{ __('วันที่แจ้ง:') }}</dt>
                                            <dd class="mt-1 text-gray-900">{{ $ticket->created_at->setTimezone('Asia/Bangkok')->format('d/m/Y H:i:s') }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">{{ __('วันที่พบปัญหา:') }}</dt>
                                            <dd class="mt-1 text-gray-900">{{ $ticket->reported_at ? $ticket->reported_at->setTimezone('Asia/Bangkok')->format('d/m/Y H:i:s') : '-' }}</dd>
                                        </div>
                                    </dl>
                                    <hr class="my-6 border-gray-300">
                                    <h6 class="text-lg font-bold mb-4 text-gray-800">{{ __('รายละเอียด:') }}</h6>
                                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 text-gray-700 leading-relaxed break-words">
                                        @php
                                            // แปลง URL ในข้อความให้เป็นลิงค์ที่คลิกได้
                                            $description = $ticket->description;
                                            
                                            // Pattern สำหรับหา URL
                                            $urlPattern = '/(https?:\/\/[^\s]+|www\.[^\s]+)/i';
                                            
                                            // แทนที่ URL ด้วยลิงค์ที่คลิกได้
                                            $description = preg_replace_callback($urlPattern, function($matches) {
                                                $url = $matches[1];
                                                
                                                // เพิ่ม https:// ถ้าไม่มี protocol
                                                if (!preg_match('/^https?:\/\//', $url)) {
                                                    $url = 'https://' . $url;
                                                }
                                                
                                                return '<a href="' . htmlspecialchars($url) . '" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-800 hover:underline">' . htmlspecialchars($matches[1]) . '</a>';
                                            }, $description);
                                            
                                            // แปลง newline เป็น <br>
                                            $description = nl2br($description);
                                        @endphp
                                        {!! $description !!}
                                    </div>

                                    {{-- ส่วนแสดงลิงค์ --}}
                                    @if($ticket->links)
                                    <hr class="my-6 border-gray-300">
                                    <h6 class="text-lg font-bold mb-4 text-gray-800 flex items-center">
                                        <i class="fas fa-link text-blue-500 mr-2"></i>
                                        ลิงค์ที่เกี่ยวข้อง
                                    </h6>
                                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                        @php
                                            // แยกลิงค์ด้วย newline หรือ comma
                                            $links = preg_split('/[\n\r,]+/', $ticket->links);
                                            $links = array_filter(array_map('trim', $links));
                                        @endphp
                                        
                                        @if(count($links) > 0)
                                            <ul class="space-y-2">
                                                @foreach($links as $link)
                                                    @if(!empty($link))
                                                        <li>
                                                            @php
                                                                // ตรวจสอบว่าเป็น URL หรือไม่
                                                                $isUrl = filter_var($link, FILTER_VALIDATE_URL) || 
                                                                         preg_match('/^https?:\/\//', $link) ||
                                                                         preg_match('/^www\./', $link);
                                                                
                                                                // เพิ่ม https:// ถ้าไม่มี protocol
                                                                if ($isUrl && !preg_match('/^https?:\/\//', $link)) {
                                                                    $link = 'https://' . $link;
                                                                }
                                                            @endphp
                                                            
                                                            @if($isUrl)
                                                                <a href="{{ $link }}" 
                                                                   target="_blank" 
                                                                   rel="noopener noreferrer"
                                                                   class="text-blue-600 hover:text-blue-800 hover:underline break-all flex items-center">
                                                                    <i class="fas fa-external-link-alt mr-2 text-xs"></i>
                                                                    {{ $link }}
                                                                </a>
                                                            @else
                                                                <span class="text-gray-600 break-all">{{ $link }}</span>
                                                            @endif
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-gray-500 italic">ไม่มีลิงค์ที่เกี่ยวข้อง</p>
                                        @endif
                                    </div>
                                    @endif

                                    {{-- ส่วนแสดงรูปภาพ --}}
                                    @php
                                        $validImages = $ticket->getImageUrls();
                                        $validImageCount = count($validImages);
                                    @endphp
                                    
                                    @if($validImageCount > 0)
                                    <hr class="my-6 border-gray-300">
                                    <h6 class="text-lg font-bold mb-4 text-gray-800 flex items-center">
                                        <i class="fas fa-images text-blue-500 mr-2"></i>
                                        รูปภาพประกอบ ({{ $validImageCount }} รูป)
                                    </h6>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($validImages as $index => $imageUrl)
                                            <div class="relative group">
                                                <div class="aspect-w-16 aspect-h-9 bg-gray-100 rounded-lg overflow-hidden border border-gray-200">
                                                    <img src="{{ $imageUrl }}" 
                                                         alt="รูปภาพประกอบ {{ $index + 1 }}"
                                                         class="w-full h-48 object-cover cursor-pointer hover:scale-105 transition-transform duration-200"
                                                         onclick="openImageModal('{{ $imageUrl }}', {{ $index + 1 }}, {{ $validImageCount }})"
                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    
                                                    {{-- Fallback when image fails to load --}}
                                                    <div class="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center hidden">
                                                        <div class="text-center text-gray-500">
                                                            <i class="fas fa-image text-3xl mb-2"></i>
                                                            <p class="text-sm">ไม่สามารถโหลดรูปภาพได้</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                {{-- Overlay with image number --}}
                                                <div class="absolute top-2 right-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded-full">
                                                    {{ $index + 1 }}/{{ $validImageCount }}
                                                </div>
                                                
                                                {{-- Primary image indicator --}}
                                                @if($index === 0)
                                                    <div class="absolute top-2 left-2 bg-blue-500 text-white text-xs px-2 py-1 rounded-full">
                                                        <i class="fas fa-star mr-1"></i>หลัก
                                                    </div>
                                                @endif
                                                
                                                {{-- Hover effect --}}
                                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100">
                                                    <i class="fas fa-search-plus text-white text-2xl"></i>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @elseif($ticket->image_count > 0)
                                    {{-- แสดงข้อความเมื่อมีข้อมูลรูปภาพแต่ไฟล์ไม่พบ --}}
                                    <hr class="my-6 border-gray-300">
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                        <div class="flex items-center">
                                            <i class="fas fa-exclamation-triangle text-yellow-500 mr-3"></i>
                                            <div>
                                                <h6 class="text-sm font-medium text-yellow-800">รูปภาพไม่พบ</h6>
                                                <p class="text-sm text-yellow-700 mt-1">
                                                    มีการแนบรูปภาพ {{ $ticket->image_count }} รูป แต่ไม่สามารถโหลดไฟล์ได้ 
                                                    อาจเป็นเพราะไฟล์ถูกลบหรือย้ายตำแหน่ง
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Card ความคิดเห็นและบันทึก --}}
                            <div class="bg-white rounded-xl shadow-lg border border-gray-200 mt-8">
                                <div class="px-6 py-4 bg-gradient-to-r from-green-200 to-emerald-300 text-green-800 font-bold text-lg rounded-t-xl">
                                    <h5 class="mb-0 flex items-center">
                                        <i class="fas fa-comments mr-2"></i>
                                        ความคิดเห็นและบันทึก ({{ $ticket->updates->count() }} รายการ)
                                    </h5>
                                </div>
                                <div class="p-6">
                                    @if ($ticket->updates->isEmpty())
                                        <div class="text-center py-8">
                                            <i class="fas fa-comment-slash text-4xl text-gray-300 mb-4"></i>
                                            <p class="text-gray-500 text-lg mb-2">ยังไม่มีความคิดเห็น</p>
                                            <p class="text-gray-400 text-sm">ความคิดเห็นและการอัปเดตจะแสดงที่นี่</p>
                                        </div>
                                    @else
                                        <div class="space-y-4">
                                            @foreach ($ticket->updates->sortByDesc('created_at') as $update)
                                                <div class="border border-gray-200 rounded-lg p-4 {{ $loop->iteration % 2 === 0 ? 'bg-gray-50' : 'bg-white' }}">
                                                    <div class="flex items-start justify-between mb-3">
                                                        <div class="flex items-center">
                                                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                                                                <i class="fas fa-user text-indigo-600 text-sm"></i>
                                                            </div>
                                                            <div>
                                                                <p class="font-semibold text-gray-900">{{ $update->user->name ?? 'ระบบ' }}</p>
                                                                <p class="text-sm text-gray-500">
                                                                    {{ $update->created_at->format('d/m/Y H:i') }}
                                                                    @if($update->type)
                                                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                            {{ ucfirst(str_replace('_', ' ', $update->type)) }}
                                                                        </span>
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        </div>
                                                        @if($update->status)
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                                สถานะ: {{ $update->status->name }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    
                                                    @if($update->comment)
                                                        <div class="bg-gray-50 rounded-lg p-3">
                                                            <p class="text-gray-800 whitespace-pre-wrap">{{ $update->comment }}</p>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($update->old_values || $update->new_values)
                                                        <div class="mt-3">
                                                            <p class="text-sm font-medium text-gray-700 mb-2">การเปลี่ยนแปลง:</p>
                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                                                @if($update->old_values)
                                                                    <div class="bg-red-50 border border-red-200 rounded p-2">
                                                                        <p class="font-medium text-red-800 mb-1">ก่อนหน้า:</p>
                                                                        <p class="text-red-700">{{ $update->old_values }}</p>
                                                                    </div>
                                                                @endif
                                                                @if($update->new_values)
                                                                    <div class="bg-green-50 border border-green-200 rounded p-2">
                                                                        <p class="font-medium text-green-800 mb-1">ใหม่:</p>
                                                                        <p class="text-green-700">{{ $update->new_values }}</p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Card ไฟล์แนบ --}}
                            <div class="bg-white rounded-xl shadow-lg border border-gray-200 mt-8">
                                <div class="px-6 py-4 bg-gradient-to-r from-gray-200 to-gray-300 text-gray-800 font-bold text-lg rounded-t-xl">
                                    <h5 class="mb-0">{{ __('ไฟล์แนบ') }}</h5>
                                </div>
                                <div class="p-6">
                                    @if ($ticket->attachments->isEmpty())
                                        <p class="text-gray-500 italic">{{ __('ไม่มีไฟล์แนบสำหรับปัญหานี้') }}</p>
                                    @else
                                        <ul class="divide-y divide-gray-200 border border-gray-200 rounded-lg">
                                            @foreach ($ticket->attachments as $attachment)
                                                <li class="p-4 flex justify-between items-center">
                                                    <div class="flex items-center">
                                                        @php
                                                            $extension = pathinfo($attachment->filename, PATHINFO_EXTENSION);
                                                            $iconSvg = '';
                                                            $iconColor = 'text-gray-500'; // Default color

                                                            switch (strtolower($extension)) {
                                                                case 'jpg':
                                                                case 'jpeg':
                                                                case 'png':
                                                                case 'gif':
                                                                    $iconSvg = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>';
                                                                    $iconColor = 'text-blue-500';
                                                                    break;
                                                                case 'pdf':
                                                                    $iconSvg = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>';
                                                                    $iconColor = 'text-red-500';
                                                                    break;
                                                                case 'doc':
                                                                case 'docx':
                                                                    $iconSvg = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>';
                                                                    $iconColor = 'text-blue-600';
                                                                    break;
                                                                case 'xls':
                                                                case 'xlsx':
                                                                    $iconSvg = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>';
                                                                    $iconColor = 'text-green-600';
                                                                    break;
                                                                case 'zip':
                                                                case 'rar':
                                                                    $iconSvg = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>';
                                                                    $iconColor = 'text-yellow-600';
                                                                    break;
                                                                default:
                                                                    $iconSvg = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0014.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>';
                                                                    $iconColor = 'text-gray-500';
                                                            }
                                                        @endphp
                                                        <div class="{{ $iconColor }} mr-3">{!! $iconSvg !!}</div>
                                                        <div>
                                                            <a href="{{ Storage::url(str_replace('public/', '', $attachment->filepath)) }}"
                                                                target="_blank" download class="text-indigo-600 hover:text-indigo-800 font-semibold break-all">
                                                                {{ $attachment->filename }}
                                                            </a>
                                                            @php
                                                                $filePathForSize = str_replace('public/', '', $attachment->filepath);
                                                            @endphp
                                                            @if (Storage::exists($filePathForSize))
                                                                <small class="block mt-1 text-gray-500 text-sm">({{ round(Storage::size($filePathForSize) / 1024, 2) }} KB)</small>
                                                            @else
                                                                <small class="block mt-1 text-red-500 text-sm">{{ __('(ไฟล์ไม่พบ)') }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @if (Auth::check() && Auth::user()->canManageTickets())
                                                        <form action="{{ route('attachments.destroy', $attachment) }}"
                                                            method="POST" class="ml-4"
                                                            onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบไฟล์แนบนี้?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="p-2 text-red-600 hover:text-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 rounded-full"
                                                                title="{{ __('ลบไฟล์แนบ') }}">
                                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-1">
                            @if (Auth::check() && Auth::user()->canManageTickets())
                                {{-- Card การจัดการสำหรับผู้ดูแล --}}
                                <div class="bg-white rounded-xl shadow-lg border border-gray-200 mb-8">
                                    <div class="px-6 py-4 bg-gradient-to-r from-violet-200 to-purple-300 text-violet-800 font-bold text-lg rounded-t-xl">
                                        <h5 class="mb-0">{{ __('การจัดการสำหรับผู้ดูแล') }}</h5>
                                    </div>
                                    <div class="p-6">
                                        <form action="{{ route('tickets.update', $ticket) }}" method="POST" onsubmit="handleFormSubmit(event)">
                                            @csrf
                                            @method('PUT')

                                            <div class="mb-4">
                                                <label for="status_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('เปลี่ยนสถานะ') }}</label>
                                                <select class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('status_id') border-red-500 @enderror"
                                                    id="status_id" name="status_id" required>
                                                    @foreach ($statuses as $status)
                                                        <option value="{{ $status->id }}"
                                                            {{ $ticket->status_id == $status->id ? 'selected' : '' }}>
                                                            @php
                                                                $statusName = $status->name;
                                                                $thaiStatus = '';
                                                                switch ($statusName) {
                                                                    case 'New':
                                                                        $thaiStatus = 'คิวรอ';
                                                                        break;
                                                                    case 'In Progress':
                                                                        $thaiStatus = 'กำลังดำเนินการ';
                                                                        break;
                                                                    case 'Pending':
                                                                        $thaiStatus = 'รอตรวจสอบ';
                                                                        break;    
                                                                    case 'Resolved':
                                                                        $thaiStatus = 'เสร็จสิ้น';
                                                                        break;
                                                                    case 'Closed':
                                                                        $thaiStatus = 'ปิดแล้ว';
                                                                        break;
                                                                    case 'Rejected':
                                                                        $thaiStatus = 'ปฏิเสธ';
                                                                        break;
                                                                    default:
                                                                        $thaiStatus = $statusName; // กรณีไม่ตรงกับเงื่อนไข ให้แสดงชื่อเดิม
                                                                        break;
                                                                }
                                                            @endphp
                                                            {{ $thaiStatus }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('status_id')
                                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-4">
                                                <label for="assigned_to_user_id"
                                                    class="block text-sm font-medium text-gray-700 mb-1">{{ __('มอบหมายให้') }}</label>
                                                <select
                                                    class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('assigned_to_user_id') border-red-500 @enderror"
                                                    id="assigned_to_user_id" name="assigned_to_user_id">
                                                    <option value="">{{ __('ไม่ได้มอบหมาย') }}</option>
                                                    @foreach ($agents as $agent)
                                                        <option value="{{ $agent->id }}"
                                                            {{ $ticket->assigned_to_user_id == $agent->id ? 'selected' : '' }}>
                                                            {{ $agent->name }} 
                                                            @if($agent->role === 'leader') (หัวหน้าทีม)
                                                            @elseif($agent->role === 'manager') (ผู้จัดการ)
                                                            @elseif($agent->role === 'ceo') (CEO)
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('assigned_to_user_id')
                                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            @if (Auth::user()->isLeader() || Auth::user()->isManager() || Auth::user()->isCEO())
                                            <div class="mb-4">
                                                <label for="priority_id"
                                                    class="block text-sm font-medium text-gray-700 mb-1">{{ __('ระดับความสำคัญ') }}</label>
                                                <select
                                                    class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('priority_id') border-red-500 @enderror"
                                                    id="priority_id" name="priority_id" required>
                                                    @foreach ($priorities as $priority)
                                                        <option value="{{ $priority->id }}"
                                                            {{ $ticket->priority_id == $priority->id ? 'selected' : '' }}>
                                                            @php
                                                                $priorityName = $priority->name;
                                                                $thaiPriority = '';
                                                                switch ($priorityName) {
                                                                    case 'Critical':
                                                                        $thaiPriority = 'เร่งด่วน';
                                                                        break;
                                                                    case 'High':
                                                                        $thaiPriority = 'สูง';
                                                                        break;
                                                                    case 'Medium':
                                                                        $thaiPriority = 'ปานกลาง';
                                                                        break;
                                                                    case 'Low':
                                                                        $thaiPriority = 'ต่ำ';
                                                                        break;
                                                                    default:
                                                                        $thaiPriority = $priorityName;
                                                                        break;
                                                                }
                                                            @endphp
                                                            {{ $thaiPriority }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('priority_id')
                                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            @endif
                                            <div class="mb-6">
                                                <label for="comment"
                                                    class="block text-sm font-medium text-gray-700 mb-1">{{ __('เพิ่มบันทึก/ความคิดเห็น') }}</label>
                                                <textarea class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('comment') border-red-500 @enderror" id="comment" name="comment" rows="4" placeholder="{{ __('เพิ่มบันทึกการทำงาน หรือความคิดเห็นเกี่ยวกับปัญหา...') }}">{{ old('comment') }}</textarea>
                                                @error('comment')
                                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="flex justify-end">
                                                <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-200 to-green-300 text-emerald-800 border border-emerald-300 rounded-lg font-semibold hover:from-emerald-300 hover:to-green-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    <i class="fas fa-save mr-2"></i>
                                                    {{ __('อัปเดตปัญหา') }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                {{-- Card การดำเนินการสำคัญ (ลบ) --}}
                                <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                                    <div class="px-6 py-4 bg-gradient-to-r from-red-200 to-red-300 text-red-800 font-bold text-lg rounded-t-xl">
                                        <h5 class="mb-0">{{ __('การดำเนินการสำคัญ') }}</h5>
                                    </div>
                                    <div class="p-6">
                                        <form action="{{ route('tickets.destroy', $ticket) }}" method="POST"
                                            onsubmit="return confirm('{{ __('คุณแน่ใจหรือไม่ที่จะลบปัญหานี้? การดำเนินการนี้ไม่สามารถย้อนกลับได้') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <div class="flex justify-center">
                                                <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-200 to-red-300 text-red-800 border border-red-300 rounded-lg font-semibold hover:from-red-300 hover:to-red-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    <i class="fas fa-trash mr-2"></i>
                                                    {{ __('ลบปัญหา') }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div> {{-- End grid --}}


                </div>
            </div>
        </div>
    </div>

    {{-- Comments and Updates Section --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-comments text-blue-500 mr-2"></i>
                    ความคิดเห็นและบันทึกการทำงาน
                </h2>
                
                @if($ticket->updates->count() > 0)
                    <div class="space-y-4">
                        @foreach($ticket->updates->sortBy('created_at') as $update)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start space-x-3">
                                    {{-- Avatar --}}
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                            <span class="text-white font-semibold text-sm">
                                                {{ substr($update->user->name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    {{-- Content --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center space-x-2">
                                                <h4 class="text-sm font-semibold text-gray-900">
                                                    {{ $update->user->name }}
                                                </h4>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($update->type === 'comment') bg-blue-100 text-blue-800
                                                    @elseif($update->type === 'status_change') bg-green-100 text-green-800
                                                    @elseif($update->type === 'assignment') bg-purple-100 text-purple-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    @if($update->type === 'comment')
                                                        <i class="fas fa-comment mr-1"></i>ความคิดเห็น
                                                    @elseif($update->type === 'status_change')
                                                        <i class="fas fa-exchange-alt mr-1"></i>เปลี่ยนสถานะ
                                                    @elseif($update->type === 'assignment')
                                                        <i class="fas fa-user-plus mr-1"></i>มอบหมายงาน
                                                    @else
                                                        <i class="fas fa-edit mr-1"></i>{{ ucfirst(str_replace('_', ' ', $update->type)) }}
                                                    @endif
                                                </span>
                                            </div>
                                            <time class="text-sm text-gray-500">
                                                {{ $update->created_at->format('d/m/Y H:i') }}
                                            </time>
                                        </div>
                                        
                                        {{-- Comment Content --}}
                                        @if($update->comment)
                                            <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ $update->comment }}</div>
                                        @endif
                                        
                                        {{-- Status Change Details --}}
                                        @if($update->type === 'status_change' && $update->old_values && $update->new_values)
                                            <div class="mt-2 p-2 bg-gray-50 rounded border-l-4 border-green-400">
                                                <div class="text-xs text-gray-600 mb-1">การเปลี่ยนแปลงสถานะ:</div>
                                                <div class="flex items-center space-x-2 text-sm">
                                                    <span class="px-2 py-1 bg-gray-200 rounded">{{ $update->old_values }}</span>
                                                    <i class="fas fa-arrow-right text-gray-400"></i>
                                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded">{{ $update->new_values }}</span>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        {{-- Assignment Details --}}
                                        @if($update->type === 'assignment' && $update->old_values && $update->new_values)
                                            <div class="mt-2 p-2 bg-gray-50 rounded border-l-4 border-purple-400">
                                                <div class="text-xs text-gray-600 mb-1">การมอบหมายงาน:</div>
                                                <div class="flex items-center space-x-2 text-sm">
                                                    <span class="px-2 py-1 bg-gray-200 rounded">{{ $update->old_values ?: 'ยังไม่ได้มอบหมาย' }}</span>
                                                    <i class="fas fa-arrow-right text-gray-400"></i>
                                                    <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded">{{ $update->new_values }}</span>
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
                        <div class="text-gray-400 mb-4">
                            <i class="fas fa-comments text-4xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">ยังไม่มีความคิดเห็น</h3>
                        <p class="text-gray-500">เริ่มต้นการสนทนาโดยเพิ่มความคิดเห็นแรกของคุณ</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Image Modal --}}
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4">
        <div class="relative max-w-4xl max-h-full bg-white rounded-lg overflow-hidden">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between p-4 bg-gray-100 border-b">
                <h3 class="text-lg font-semibold text-gray-800">
                    รูปภาพประกอบ - <span id="modalImageNumber">1</span>/<span id="modalTotalImages">1</span>
                </h3>
                <button onclick="closeImageModal()" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            {{-- Modal Body --}}
            <div class="relative">
                <img id="modalImage" src="" alt="รูปภาพประกอบ" class="max-w-full max-h-96 mx-auto object-contain">
                
                {{-- Navigation Arrows --}}
                <button id="prevBtn" onclick="navigateImage(-1)" class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 focus:outline-none">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button id="nextBtn" onclick="navigateImage(1)" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 focus:outline-none">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            
            {{-- Modal Footer --}}
            <div class="flex items-center justify-between p-4 bg-gray-100 border-t">
                <button onclick="downloadImage()" class="flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none">
                    <i class="fas fa-download mr-2"></i>
                    ดาวน์โหลด
                </button>
                <button onclick="closeImageModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:outline-none">
                    ปิด
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentImageIndex = 0;
        let totalImages = 0;
        let imageUrls = [];

        function handleFormSubmit(event) {
            // ฟังก์ชันนี้จะทำงานเมื่อฟอร์มอัพเดทถูกส่ง
            // หลังจากที่ฟอร์มถูกส่งสำเร็จ จะ trigger event เพื่อ refresh Kanban board
            setTimeout(() => {
                // Trigger custom event เพื่อให้ Kanban board refresh
                window.dispatchEvent(new CustomEvent('ticketUpdated'));
            }, 1000); // รอ 1 วินาทีเพื่อให้การอัพเดทเสร็จสิ้น
        }

        function openImageModal(imageUrl, imageNumber, total) {
            currentImageIndex = imageNumber - 1;
            totalImages = total;
            
            // Get all image URLs from the page
            imageUrls = [];
            document.querySelectorAll('img[alt*="รูปภาพประกอบ"]').forEach(img => {
                imageUrls.push(img.src);
            });
            
            // Update modal
            document.getElementById('modalImage').src = imageUrl;
            document.getElementById('modalImageNumber').textContent = imageNumber;
            document.getElementById('modalTotalImages').textContent = total;
            
            // Show/hide navigation buttons
            document.getElementById('prevBtn').style.display = total > 1 ? 'block' : 'none';
            document.getElementById('nextBtn').style.display = total > 1 ? 'block' : 'none';
            
            // Show modal
            document.getElementById('imageModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
            document.body.style.overflow = 'auto'; // Restore scrolling
        }

        function navigateImage(direction) {
            currentImageIndex += direction;
            
            // Handle wraparound
            if (currentImageIndex < 0) {
                currentImageIndex = totalImages - 1;
            } else if (currentImageIndex >= totalImages) {
                currentImageIndex = 0;
            }
            
            // Update modal
            document.getElementById('modalImage').src = imageUrls[currentImageIndex];
            document.getElementById('modalImageNumber').textContent = currentImageIndex + 1;
        }

        function downloadImage() {
            const imageUrl = document.getElementById('modalImage').src;
            const link = document.createElement('a');
            link.href = imageUrl;
            link.download = `ticket-image-${currentImageIndex + 1}.jpg`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Close modal when clicking outside
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (!document.getElementById('imageModal').classList.contains('hidden')) {
                switch(e.key) {
                    case 'Escape':
                        closeImageModal();
                        break;
                    case 'ArrowLeft':
                        navigateImage(-1);
                        break;
                    case 'ArrowRight':
                        navigateImage(1);
                        break;
                }
            }
        });
    </script>
</x-app-layout>