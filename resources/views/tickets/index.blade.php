<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-indigo-700 text-gray-800 leading-tight">
            {{ __('รายการปัญหา') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Session Messages (Success/Error) --}}
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
                            <strong class="font-bold">{{ __('สำเร็จ!') }}</strong>
                            <span class="block sm:inline">{{ session('success') }}</span>
                            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                                <button type="button" class="text-green-700 hover:text-green-900" onclick="this.parentElement.parentElement.style.display='none';" aria-label="Close">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </span>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
                            <strong class="font-bold">{{ __('ผิดพลาด!') }}</strong>
                            <span class="block sm:inline">{{ session('error') }}</span>
                            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                                <button type="button" class="text-red-700 hover:text-red-900" onclick="this.parentElement.parentElement.style.display='none';" aria-label="Close">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </span>
                        </div>
                    @endif

                    <div class="flex justify-end mb-6">
                        <a href="{{ route('tickets.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v6m-3-3h6m-9 0a9 9 0 1118 0 9 9 0 01-18 0z"></path></svg>
                            {{ __('แจ้งปัญหาใหม่') }}
                        </a>
                    </div>

                    @if ($tickets->isEmpty())
                        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg" role="alert">
                            <p class="font-bold">{{ __('ไม่มีข้อมูล') }}</p>
                            <p class="text-sm">{{ __('ยังไม่มีการแจ้งปัญหาเข้ามาในระบบ') }}</p>
                        </div>
                    @else
                        <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th scope="col" class="py-3 px-6">{{ __('ID') }}</th>
                                        <th scope="col" class="py-3 px-6">{{ __('หัวข้อปัญหา') }}</th>
                                        <th scope="col" class="py-3 px-6">{{ __('ประเภท') }}</th>
                                        <th scope="col" class="py-3 px-6">{{ __('ระดับความสำคัญ') }}</th>
                                        <th scope="col" class="py-3 px-6">{{ __('สถานะ') }}</th>
                                        <th scope="col" class="py-3 px-6">{{ __('ผู้แจ้ง') }}</th>
                                        <th scope="col" class="py-3 px-6">{{ __('วันที่แจ้ง') }}</th>
                                        <th scope="col" class="py-3 px-6 text-center">{{ __('ดำเนินการ') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tickets as $ticket)
                                        <tr class="bg-white border-b hover:bg-gray-50">
                                            <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap">{{ $ticket->id }}</td>
                                            <td class="py-4 px-6">
                                                <a href="{{ route('tickets.show', $ticket) }}" class="text-indigo-600 hover:underline font-semibold">
                                                    {{ $ticket->title }}
                                                </a>
                                            </td>
                                            <td class="py-4 px-6">{{ $ticket->category->name }}</td>
                                            <td class="py-4 px-6">
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
                                                        case 'Critical': 
                                                            $thaiPriority = 'เร่งด่วน';
                                                            break;
                                                        default:
                                                            $thaiPriority = $priorityName;
                                                            break;
                                                    }
                                                @endphp
                                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                                    @if($priorityName == 'Critical') bg-red-600 text-white {{-- สีแดงเข้มสำหรับวิกฤต --}}
                                                    @elseif($priorityName == 'High') bg-red-100 text-red-800
                                                    @elseif($priorityName == 'Medium') bg-yellow-100 text-yellow-800
                                                    @else bg-green-100 text-green-800 @endif
                                                ">{{ $thaiPriority }}</span>
                                            </td>
                                            <td class="py-4 px-6">
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
                                                            $thaiStatus = 'กำลังดำเนินการ';
                                                            break;
                                                        case 'Resolved':
                                                            $thaiStatus = 'แก้ไขแล้ว';
                                                            break;
                                                        case 'Closed':
                                                            $thaiStatus = 'ปิดแล้ว';
                                                            break;
                                                        case 'Rejected':
                                                            $thaiStatus = 'ปฎิเสธ';
                                                            break;
                                                        default:
                                                            $thaiStatus = $statusName;
                                                            break;
                                                    }
                                                @endphp
                                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                                    @if($statusName == 'New') bg-blue-100 text-blue-800
                                                    @elseif($statusName == 'In Progress') bg-indigo-100 text-indigo-800
                                                    @elseif($statusName == 'Resolved') bg-green-100 text-green-800
                                                    @elseif($statusName == 'Closed') bg-gray-100 text-gray-800
                                                    @else bg-red-100 text-red-800 @endif">
                                                    {{ $thaiStatus }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-6">{{ $ticket->user->name }}</td>
                                            <td class="py-4 px-6">{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="py-4 px-6 flex items-center justify-center space-x-2">
                                                <a href="{{ route('tickets.show', $ticket) }}"
                                                    class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                    {{ __('ดู') }}
                                                </a>
                                                @if (Auth::check() && Auth::user()->canManageTickets())
                                                    <form action="{{ route('tickets.destroy', $ticket) }}"
                                                        method="POST" class="inline-block"
                                                        onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบปัญหานี้? การดำเนินการนี้ไม่สามารถย้อนกลับได้');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                            {{ __('ลบ') }}
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-6 flex justify-center">
                            {{ $tickets->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>