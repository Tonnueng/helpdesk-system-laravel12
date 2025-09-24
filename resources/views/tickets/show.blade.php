<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('รายละเอียดปัญหา: ') . $ticket->title }}
        </h2>
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

                    <h1 class="text-3xl font-bold mb-8 text-indigo-700">{{ __('รายละเอียดตั๋ว: ') . $ticket->title }}</h1>

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
                                            <dd class="mt-1 text-gray-900">{{ $ticket->created_at->format('d/m/Y H:i') }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">{{ __('วันที่พบปัญหา:') }}</dt>
                                            <dd class="mt-1 text-gray-900">{{ $ticket->reported_at ? $ticket->reported_at->format('d/m/Y H:i') : '-' }}</dd>
                                        </div>
                                    </dl>
                                    <hr class="my-6 border-gray-300">
                                    <h6 class="text-lg font-bold mb-4 text-gray-800">{{ __('รายละเอียด:') }}</h6>
                                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 text-gray-700 leading-relaxed break-words">
                                        {{ $ticket->description }}
                                    </div>
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

    <script>
        function handleFormSubmit(event) {
            // ฟังก์ชันนี้จะทำงานเมื่อฟอร์มอัพเดทถูกส่ง
            // หลังจากที่ฟอร์มถูกส่งสำเร็จ จะ trigger event เพื่อ refresh Kanban board
            setTimeout(() => {
                // Trigger custom event เพื่อให้ Kanban board refresh
                window.dispatchEvent(new CustomEvent('ticketUpdated'));
            }, 1000); // รอ 1 วินาทีเพื่อให้การอัพเดทเสร็จสิ้น
        }
    </script>
</x-app-layout>