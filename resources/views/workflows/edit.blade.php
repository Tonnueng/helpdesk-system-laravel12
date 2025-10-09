<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-3xl font-bold text-indigo-700 leading-tight">
                <i class="fas fa-edit mr-3"></i>
                {{ __('แก้ไข Workflow') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('workflows.show', $workflow) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <i class="fas fa-eye mr-2"></i>
                    ดูรายละเอียด
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
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            {{-- คำแนะนำการแก้ไข Workflow --}}
            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-6">
                <div class="flex">
                    <i class="fas fa-edit text-blue-500 text-xl mr-3 mt-1"></i>
                    <div class="flex-1">
                        <h3 class="font-semibold text-blue-800 mb-2">🔧 การแก้ไข Workflow</h3>
                        <p class="text-blue-700 text-sm mb-3">
                            หน้านี้ใช้สำหรับแก้ไขข้อมูลพื้นฐานของ Workflow และจัดการ Actions (ขั้นตอนการทำงาน)
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div class="bg-white rounded-lg p-3">
                                <strong class="text-blue-800">📝 ข้อมูลพื้นฐาน:</strong>
                                <ul class="mt-1 text-blue-600 space-y-1">
                                    <li>• ชื่อและคำอธิบาย Workflow</li>
                                    <li>• ประเภทการเรียกใช้และเงื่อนไข</li>
                                    <li>• สถานะการเปิดใช้งาน</li>
                                </ul>
                            </div>
                            <div class="bg-white rounded-lg p-3">
                                <strong class="text-blue-800">⚙️ Actions (ขั้นตอนการทำงาน):</strong>
                                <ul class="mt-1 text-blue-600 space-y-1">
                                    <li>• ➕ เพิ่มขั้นตอนใหม่ (มอบหมาย, แจ้งเตือน, ฯลฯ)</li>
                                    <li>• ✏️ แก้ไขขั้นตอนที่มีอยู่</li>
                                    <li>• 🗑️ ลบขั้นตอนที่ไม่ต้องการ</li>
                                    <li>• 📊 จัดลำดับการทำงาน</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- แจ้งเตือนข้อผิดพลาด --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-300 rounded-xl p-4" role="alert">
                    <div class="flex">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3 mt-0.5"></i>
                        <div class="flex-1">
                            <p class="font-semibold text-red-700">{{ __('เกิดข้อผิดพลาด!') }}</p>
                            <ul class="mt-2 text-sm text-red-600 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ฟอร์มแก้ไข Workflow --}}
            <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                <div class="p-6">
                    <form action="{{ route('workflows.update', $workflow) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        {{-- ส่วนข้อมูลพื้นฐาน --}}
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-info-circle text-indigo-600 mr-2"></i>
                                ข้อมูลพื้นฐานของ Workflow
                            </h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- ชื่อ Workflow --}}
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    ชื่อ Workflow <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="name" name="name" value="{{ old('name', $workflow->name) }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="เช่น Critical Priority Auto-Assignment"
                                       required>
                                <p class="mt-1 text-xs text-gray-500">
                                    💡 ชื่อที่สื่อความหมายชัดเจน ช่วยให้จำได้ง่าย
                                </p>
                            </div>

                            {{-- คำอธิบาย --}}
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                    คำอธิบาย
                                </label>
                                <textarea id="description" name="description" rows="3"
                                         class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                         placeholder="อธิบายวัตถุประสงค์และการใช้งานของ Workflow นี้">{{ old('description', $workflow->description) }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">
                                    📝 อธิบายว่า Workflow นี้ทำอะไร เพื่อให้ผู้อื่นเข้าใจง่าย
                                </p>
                            </div>

                            {{-- ประเภทการเรียกใช้ --}}
                            <div>
                                <label for="trigger_type" class="block text-sm font-medium text-gray-700 mb-2">
                                    ประเภทการเรียกใช้ <span class="text-red-500">*</span>
                                </label>
                                <select id="trigger_type" name="trigger_type" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        required onchange="toggleTriggerConditions()">
                                    <option value="">เลือกประเภทการเรียกใช้</option>
                                    <option value="auto" {{ old('trigger_type', $workflow->trigger_type) === 'auto' ? 'selected' : '' }}>🔄 อัตโนมัติ (ทุกครั้ง)</option>
                                    <option value="manual" {{ old('trigger_type', $workflow->trigger_type) === 'manual' ? 'selected' : '' }}>👆 ด้วยตนเอง</option>
                                    <option value="category_based" {{ old('trigger_type', $workflow->trigger_type) === 'category_based' ? 'selected' : '' }}>📂 ตามประเภทปัญหา</option>
                                    <option value="priority_based" {{ old('trigger_type', $workflow->trigger_type) === 'priority_based' ? 'selected' : '' }}>⚡ ตามระดับความสำคัญ</option>
                                    <option value="status_based" {{ old('trigger_type', $workflow->trigger_type) === 'status_based' ? 'selected' : '' }}>📊 ตามสถานะ</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">
                                    🎯 เลือกว่าจะเรียกใช้ Workflow เมื่อไหร่
                                </p>
                            </div>

                            {{-- ลำดับการเรียง --}}
                            <div>
                                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                                    ลำดับการเรียง
                                </label>
                                <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $workflow->sort_order) }}" min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="0">
                                <p class="mt-1 text-xs text-gray-500">
                                    📋 กำหนดลำดับการทำงาน (0 = ทำงานก่อน)
                                </p>
                            </div>

                            {{-- เงื่อนไขการเรียกใช้ --}}
                            <div id="trigger_conditions" class="md:col-span-2" style="display: none;">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    เงื่อนไขการเรียกใช้
                                </label>
                                
                                {{-- Category-based conditions --}}
                                <div id="category_condition" class="mb-4" style="display: none;">
                                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">ประเภทปัญหา</label>
                                    <select id="category_id" name="trigger_conditions[category_id]" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">เลือกประเภทปัญหา</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                    {{ old('trigger_conditions.category_id', $workflow->trigger_conditions['category_id'] ?? '') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Priority-based conditions --}}
                                <div id="priority_condition" class="mb-4" style="display: none;">
                                    <label for="priority_id" class="block text-sm font-medium text-gray-700 mb-2">ระดับความสำคัญ</label>
                                    <select id="priority_id" name="trigger_conditions[priority_id]" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">เลือกระดับความสำคัญ</option>
                                        @foreach($priorities as $priority)
                                            <option value="{{ $priority->id }}" 
                                                    {{ old('trigger_conditions.priority_id', $workflow->trigger_conditions['priority_id'] ?? '') == $priority->id ? 'selected' : '' }}>
                                                {{ $priority->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Status-based conditions --}}
                                <div id="status_condition" class="mb-4" style="display: none;">
                                    <label for="status_id" class="block text-sm font-medium text-gray-700 mb-2">สถานะ</label>
                                    <select id="status_id" name="trigger_conditions[status_id]" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">เลือกสถานะ</option>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status->id }}" 
                                                    {{ old('trigger_conditions.status_id', $workflow->trigger_conditions['status_id'] ?? '') == $status->id ? 'selected' : '' }}>
                                                {{ $status->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- สถานะ --}}
                            <div class="md:col-span-2">
                                <div class="flex items-center">
                                    <input type="checkbox" id="is_active" name="is_active" value="1" 
                                           {{ old('is_active', $workflow->is_active) ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                        เปิดใช้งาน Workflow นี้
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- ขั้นตอนการทำงาน --}}
                        <div class="mt-8 border-t pt-6">
                            <div class="mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2 flex items-center">
                                    <i class="fas fa-list-ol text-indigo-600 mr-2"></i>
                                    ขั้นตอนการทำงาน (Actions) - {{ $workflow->steps->count() }} ขั้นตอน
                                </h3>
                                <p class="text-sm text-gray-600 mb-4">
                                    ⚙️ Actions คือขั้นตอนที่ Workflow จะทำเมื่อถูกเรียกใช้ เช่น มอบหมายปัญหา, ส่งการแจ้งเตือน, เปลี่ยนสถานะ
                                </p>
                            </div>
                            
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
                                                </div>
                                                
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('workflow-steps.edit', [$workflow, $step]) }}" 
                                                       class="text-blue-600 hover:text-blue-900" title="แก้ไขขั้นตอน">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('workflow-steps.destroy', [$workflow, $step]) }}" 
                                                          method="POST" class="inline" 
                                                          onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบขั้นตอนนี้?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900" title="ลบขั้นตอน">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                                    <div class="flex items-center justify-center">
                                        <div class="text-center">
                                            <h4 class="font-medium text-green-800 mb-2">➕ เพิ่มขั้นตอนใหม่</h4>
                                            <p class="text-sm text-green-600 mb-3">
                                                เพิ่ม Actions ใหม่ เช่น มอบหมาย, แจ้งเตือน, เปลี่ยนสถานะ
                                            </p>
                                            <a href="{{ route('workflow-steps.create', $workflow) }}" 
                                               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center mx-auto">
                                                <i class="fas fa-plus mr-2"></i>
                                                เพิ่มขั้นตอนใหม่
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-8 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <i class="fas fa-list-ol text-4xl text-yellow-400 mb-4"></i>
                                    <h4 class="text-lg font-medium text-yellow-800 mb-2">⚠️ ยังไม่มี Actions</h4>
                                    <p class="text-yellow-600 mb-4">
                                        Workflow นี้ยังไม่มีขั้นตอนการทำงาน (Actions) <br>
                                        ต้องเพิ่ม Actions เพื่อให้ Workflow ทำงานได้
                                    </p>
                                    <a href="{{ route('workflow-steps.create', $workflow) }}" 
                                       class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg flex items-center mx-auto">
                                        <i class="fas fa-plus mr-2"></i>
                                        เพิ่มขั้นตอนแรก
                                    </a>
                                </div>
                            @endif
                        </div>

                        {{-- คำแนะนำเพิ่มเติม --}}
                        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="font-medium text-blue-800 mb-2">💡 คำแนะนำการแก้ไข</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <strong class="text-blue-800">📝 ข้อมูลพื้นฐาน:</strong>
                                    <ul class="mt-1 text-blue-600 space-y-1">
                                        <li>• แก้ไขชื่อและคำอธิบายได้ตามต้องการ</li>
                                        <li>• เปลี่ยนประเภทการเรียกใช้ได้</li>
                                        <li>• ปิด/เปิดใช้งานได้</li>
                                    </ul>
                                </div>
                                <div>
                                    <strong class="text-blue-800">⚙️ Actions:</strong>
                                    <ul class="mt-1 text-blue-600 space-y-1">
                                        <li>• แก้ไข Actions ที่มีอยู่</li>
                                        <li>• เพิ่ม Actions ใหม่</li>
                                        <li>• ลบ Actions ที่ไม่ต้องการ</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- ปุ่มบันทึก --}}
                        <div class="mt-6 flex justify-end space-x-4">
                            <a href="{{ route('workflows.show', $workflow) }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg flex items-center">
                                <i class="fas fa-times mr-2"></i>
                                ยกเลิก
                            </a>
                            <button type="submit" 
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg flex items-center">
                                <i class="fas fa-save mr-2"></i>
                                บันทึกการเปลี่ยนแปลง
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleTriggerConditions() {
            const triggerType = document.getElementById('trigger_type').value;
            const conditionsDiv = document.getElementById('trigger_conditions');
            const categoryDiv = document.getElementById('category_condition');
            const priorityDiv = document.getElementById('priority_condition');
            const statusDiv = document.getElementById('status_condition');

            // ซ่อนทุกเงื่อนไขก่อน
            categoryDiv.style.display = 'none';
            priorityDiv.style.display = 'none';
            statusDiv.style.display = 'none';

            // แสดงเงื่อนไขตามประเภทที่เลือก
            if (triggerType === 'category_based') {
                conditionsDiv.style.display = 'block';
                categoryDiv.style.display = 'block';
            } else if (triggerType === 'priority_based') {
                conditionsDiv.style.display = 'block';
                priorityDiv.style.display = 'block';
            } else if (triggerType === 'status_based') {
                conditionsDiv.style.display = 'block';
                statusDiv.style.display = 'block';
            } else {
                conditionsDiv.style.display = 'none';
            }
        }

        // เรียกใช้ฟังก์ชันเมื่อโหลดหน้า
        document.addEventListener('DOMContentLoaded', function() {
            toggleTriggerConditions();
        });
    </script>
</x-app-layout>
