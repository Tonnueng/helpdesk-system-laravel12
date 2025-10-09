<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-3xl font-bold text-indigo-700 leading-tight">
                <i class="fas fa-plus mr-3"></i>
                {{ __('สร้าง Workflow ใหม่') }}
            </h2>
            <a href="{{ route('workflows.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                กลับ
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            {{-- คำแนะนำการใช้งาน --}}
            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-6">
                <div class="flex">
                    <i class="fas fa-info-circle text-blue-500 text-xl mr-3 mt-1"></i>
                    <div class="flex-1">
                        <h3 class="font-semibold text-blue-800 mb-2">💡 คำแนะนำการสร้าง Workflow</h3>
                        <p class="text-blue-700 text-sm mb-3">
                            Workflow คือระบบอัตโนมัติที่ช่วยจัดการปัญหาตามเงื่อนไขที่กำหนด 
                            เช่น การมอบหมายอัตโนมัติ การส่งต่อปัญหา หรือการแจ้งเตือน
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div class="bg-white rounded-lg p-3">
                                <strong class="text-blue-800">🎯 ตัวอย่างการใช้งาน:</strong>
                                <ul class="mt-1 text-blue-600 space-y-1">
                                    <li>• มอบหมายปัญหาความสำคัญสูงให้ผู้จัดการ</li>
                                    <li>• ตอบกลับอัตโนมัติเมื่อมีปัญหาใหม่</li>
                                    <li>• ส่งต่อปัญหาให้ฝ่ายที่เกี่ยวข้อง</li>
                                </ul>
                            </div>
                            <div class="bg-white rounded-lg p-3">
                                <strong class="text-blue-800">⚡ ประเภทการเรียกใช้:</strong>
                                <ul class="mt-1 text-blue-600 space-y-1">
                                    <li>• <strong>อัตโนมัติ:</strong> ทำงานทุกครั้งที่สร้างปัญหา</li>
                                    <li>• <strong>ตามความสำคัญ:</strong> ทำงานเมื่อระดับความสำคัญตรงตามที่กำหนด</li>
                                    <li>• <strong>ด้วยตนเอง:</strong> เรียกใช้เมื่อต้องการ</li>
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

            {{-- ฟอร์มสร้าง Workflow --}}
            <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                <div class="p-6">
                    <form action="{{ route('workflows.store') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- ชื่อ Workflow --}}
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    ชื่อ Workflow <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="เช่น Critical Priority Auto-Assignment"
                                       required>
                                <p class="mt-1 text-xs text-gray-500">
                                    💡 ใช้ชื่อที่สื่อความหมายชัดเจน เช่น "มอบหมายปัญหาความสำคัญสูง" หรือ "แจ้งเตือนปัญหาอุปกรณ์"
                                </p>
                            </div>

                            {{-- คำอธิบาย --}}
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                    คำอธิบาย
                                </label>
                                <textarea id="description" name="description" rows="3"
                                         class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                         placeholder="อธิบายวัตถุประสงค์และการใช้งานของ Workflow นี้">{{ old('description') }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">
                                    📝 อธิบายว่า Workflow นี้จะทำอะไร เมื่อไหร่ และเพื่ออะไร เช่น "มอบหมายปัญหาความสำคัญสูงให้ผู้จัดการฝ่าย IT ทันที"
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
                                    <option value="auto" {{ old('trigger_type') === 'auto' ? 'selected' : '' }}>🔄 อัตโนมัติ (ทุกครั้ง)</option>
                                    <option value="manual" {{ old('trigger_type') === 'manual' ? 'selected' : '' }}>👆 ด้วยตนเอง</option>
                                    <option value="category_based" {{ old('trigger_type') === 'category_based' ? 'selected' : '' }}>📂 ตามประเภทปัญหา</option>
                                    <option value="priority_based" {{ old('trigger_type') === 'priority_based' ? 'selected' : '' }}>⚡ ตามระดับความสำคัญ</option>
                                    <option value="status_based" {{ old('trigger_type') === 'status_based' ? 'selected' : '' }}>📊 ตามสถานะ</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">
                                    🎯 เลือกว่าจะเรียกใช้ Workflow เมื่อไหร่ เช่น "ตามความสำคัญ" = ทำงานเมื่อปัญหาเป็นระดับ Critical
                                </p>
                            </div>

                            {{-- ลำดับการเรียง --}}
                            <div>
                                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                                    ลำดับการเรียง
                                </label>
                                <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="0">
                                <p class="mt-1 text-xs text-gray-500">
                                    📋 กำหนดลำดับการทำงาน (0 = ทำงานก่อน, 10 = ทำงานหลัง) สำหรับ Workflow ที่มีเงื่อนไขเดียวกัน
                                </p>
                            </div>

                            {{-- เงื่อนไขการเรียกใช้ --}}
                            <div id="trigger_conditions" class="md:col-span-2" style="display: none;">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    เงื่อนไขการเรียกใช้
                                </label>
                                <p class="mb-3 text-xs text-gray-500 bg-yellow-50 p-2 rounded-lg">
                                    ⚙️ เลือกเงื่อนไขที่เฉพาะเจาะจงเพื่อให้ Workflow ทำงานตามที่ต้องการ
                                </p>
                                
                                {{-- Category-based conditions --}}
                                <div id="category_condition" class="mb-4" style="display: none;">
                                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">ประเภทปัญหา</label>
                                    <select id="category_id" name="trigger_conditions[category_id]" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">เลือกประเภทปัญหา</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('trigger_conditions.category_id') == $category->id ? 'selected' : '' }}>
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
                                            <option value="{{ $priority->id }}" {{ old('trigger_conditions.priority_id') == $priority->id ? 'selected' : '' }}>
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
                                            <option value="{{ $status->id }}" {{ old('trigger_conditions.status_id') == $status->id ? 'selected' : '' }}>
                                                {{ $status->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- สถานะ --}}
                            <div class="md:col-span-2">
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                            ✅ เปิดใช้งาน Workflow นี้
                                        </label>
                                    </div>
                                    <p class="mt-2 text-xs text-green-600">
                                        💡 เปิดใช้งานเพื่อให้ Workflow เริ่มทำงานทันที หรือปิดไว้เพื่อทดสอบก่อน
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- คำแนะนำเพิ่มเติม --}}
                        <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-800 mb-2">📌 ขั้นตอนถัดไป</h4>
                            <p class="text-sm text-gray-600 mb-2">
                                หลังจากสร้าง Workflow แล้ว คุณจะสามารถ:
                            </p>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• ➕ เพิ่มขั้นตอนการทำงาน (Actions) เช่น มอบหมาย, เปลี่ยนสถานะ, ส่งการแจ้งเตือน</li>
                                <li>• 🧪 ทดสอบ Workflow ด้วยการสร้างปัญหาใหม่</li>
                                <li>• 📊 ดูประวัติการทำงานในหน้า Workflow Details</li>
                                <li>• ⚙️ แก้ไขหรือปิดใช้งานเมื่อไม่ต้องการ</li>
                            </ul>
                        </div>

                        {{-- ปุ่มบันทึก --}}
                        <div class="mt-6 flex justify-end space-x-4">
                            <a href="{{ route('workflows.index') }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg flex items-center">
                                <i class="fas fa-times mr-2"></i>
                                ยกเลิก
                            </a>
                            <button type="submit" 
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg flex items-center">
                                <i class="fas fa-save mr-2"></i>
                                สร้าง Workflow
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
