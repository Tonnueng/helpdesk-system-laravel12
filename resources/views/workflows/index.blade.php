<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-3xl font-bold text-indigo-700 leading-tight">
                <i class="fas fa-project-diagram mr-3"></i>
                {{ __('จัดการ Workflow') }}
            </h2>
            <a href="{{ route('workflows.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-plus mr-2"></i>
                สร้าง Workflow ใหม่
            </a>
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

            {{-- ฟอร์มค้นหาและกรอง --}}
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 mb-6">
                <div class="p-6">
                    <form method="GET" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- ค้นหา --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">ค้นหา</label>
                                <input type="text" name="search" value="{{ request('search') }}" 
                                       placeholder="ค้นหาตามชื่อหรือคำอธิบาย..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            {{-- สถานะ --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">สถานะ</label>
                                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">ทั้งหมด</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>เปิดใช้งาน</option>
                                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>ปิดใช้งาน</option>
                                </select>
                            </div>

                            {{-- ปุ่มค้นหา --}}
                            <div class="flex items-end">
                                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-search mr-2"></i>
                                    ค้นหา
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- รายการ Workflows --}}
            <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-list mr-2"></i>
                        รายการ Workflows ({{ $workflows->total() }} รายการ)
                    </h3>

                    @if($workflows->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชื่อ Workflow</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ประเภทการเรียกใช้</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">จำนวนขั้นตอน</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สถานะ</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">วันที่สร้าง</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">การจัดการ</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($workflows as $workflow)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $workflow->name }}</div>
                                                    @if($workflow->description)
                                                        <div class="text-sm text-gray-500">{{ Str::limit($workflow->description, 50) }}</div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
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
                                                @if($workflow->trigger_conditions_text)
                                                    <div class="text-xs text-gray-500 mt-1">{{ $workflow->trigger_conditions_text }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                    <i class="fas fa-list-ol mr-1"></i>
                                                    {{ $workflow->steps->count() }} ขั้นตอน
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($workflow->is_active)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i class="fas fa-check-circle mr-1"></i>เปิดใช้งาน
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <i class="fas fa-times-circle mr-1"></i>ปิดใช้งาน
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $workflow->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex items-center space-x-2">
                                                    <a href="{{ route('workflows.show', $workflow) }}" 
                                                       class="text-indigo-600 hover:text-indigo-900" title="ดูรายละเอียด">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('workflows.edit', $workflow) }}" 
                                                       class="text-blue-600 hover:text-blue-900" title="แก้ไข">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('workflows.toggleStatus', $workflow) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" 
                                                                class="text-{{ $workflow->is_active ? 'yellow' : 'green' }}-600 hover:text-{{ $workflow->is_active ? 'yellow' : 'green' }}-900"
                                                                title="{{ $workflow->is_active ? 'ปิดใช้งาน' : 'เปิดใช้งาน' }}">
                                                            <i class="fas fa-{{ $workflow->is_active ? 'pause' : 'play' }}"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('workflows.clone', $workflow) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-purple-600 hover:text-purple-900" title="คัดลอก">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('workflows.destroy', $workflow) }}" method="POST" class="inline" 
                                                          onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบ Workflow นี้?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900" title="ลบ">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-6">
                            {{ $workflows->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-project-diagram text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">ยังไม่มี Workflow</h3>
                            <p class="text-gray-500 mb-6">เริ่มต้นสร้าง Workflow แรกของคุณเพื่อจัดการกระบวนการทำงานอัตโนมัติ</p>
                            <a href="{{ route('workflows.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg inline-flex items-center">
                                <i class="fas fa-plus mr-2"></i>
                                สร้าง Workflow แรก
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
