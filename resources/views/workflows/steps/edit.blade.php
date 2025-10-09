<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-3xl font-bold text-indigo-700 leading-tight">
                <i class="fas fa-edit mr-3"></i>
                แก้ไขขั้นตอนการทำงาน
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('workflows.edit', $workflow) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    กลับไปแก้ไข Workflow
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            {{-- คำแนะนำการแก้ไข Step --}}
            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-6">
                <div class="flex">
                    <i class="fas fa-edit text-blue-500 text-xl mr-3 mt-1"></i>
                    <div class="flex-1">
                        <h3 class="font-semibold text-blue-800 mb-2">⚙️ แก้ไขขั้นตอนการทำงาน</h3>
                        <p class="text-blue-700 text-sm mb-3">
                            ขั้นตอนที่ {{ $step->step_order }} ของ Workflow: <strong>{{ $workflow->name }}</strong>
                        </p>
                        <div class="bg-white rounded-lg p-3">
                            <strong class="text-blue-800">📝 การแก้ไข:</strong>
                            <ul class="mt-1 text-blue-600 space-y-1 text-sm">
                                <li>• เปลี่ยนประเภทการกระทำ (Action Type)</li>
                                <li>• ปรับแต่งการตั้งค่า (Configuration)</li>
                                <li>• เปลี่ยนลำดับการทำงาน</li>
                                <li>• กำหนดเวลาหน่วง (ถ้าจำเป็น)</li>
                            </ul>
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

            {{-- ฟอร์มแก้ไข Step --}}
            <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                <div class="p-6">
                    <form action="{{ route('workflow-steps.update', [$workflow, $step]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- ลำดับการทำงาน --}}
                            <div>
                                <label for="step_order" class="block text-sm font-medium text-gray-700 mb-2">
                                    ลำดับการทำงาน <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="step_order" name="step_order" 
                                       value="{{ old('step_order', $step->step_order) }}" min="1"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                       required>
                                <p class="mt-1 text-xs text-gray-500">
                                    📋 กำหนดลำดับการทำงาน (1 = ทำงานก่อน, 2 = ทำงานหลัง)
                                </p>
                            </div>

                            {{-- ประเภทการกระทำ --}}
                            <div>
                                <label for="action_type" class="block text-sm font-medium text-gray-700 mb-2">
                                    ประเภทการกระทำ <span class="text-red-500">*</span>
                                </label>
                                <select id="action_type" name="action_type" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        required onchange="toggleActionConfig()">
                                    <option value="">เลือกประเภทการกระทำ</option>
                                    <option value="assign" {{ old('action_type', $step->action_type) === 'assign' ? 'selected' : '' }}>👤 มอบหมายงาน</option>
                                    <option value="status_change" {{ old('action_type', $step->action_type) === 'status_change' ? 'selected' : '' }}>📊 เปลี่ยนสถานะ</option>
                                    <option value="notification" {{ old('action_type', $step->action_type) === 'notification' ? 'selected' : '' }}>🔔 ส่งการแจ้งเตือน</option>
                                    <option value="auto_reply" {{ old('action_type', $step->action_type) === 'auto_reply' ? 'selected' : '' }}>💬 ตอบกลับอัตโนมัติ</option>
                                    <option value="escalation" {{ old('action_type', $step->action_type) === 'escalation' ? 'selected' : '' }}>⬆️ ส่งต่อปัญหา</option>
                                    <option value="email_notification" {{ old('action_type', $step->action_type) === 'email_notification' ? 'selected' : '' }}>📧 ส่งอีเมล</option>
                                    <option value="wait_for_response" {{ old('action_type', $step->action_type) === 'wait_for_response' ? 'selected' : '' }}>⏳ รอการตอบกลับ</option>
                                    <option value="set_due_date" {{ old('action_type', $step->action_type) === 'set_due_date' ? 'selected' : '' }}>📅 กำหนดวันครบกำหนด</option>
                                    <option value="add_comment" {{ old('action_type', $step->action_type) === 'add_comment' ? 'selected' : '' }}>💭 เพิ่มความคิดเห็น</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">
                                    🎯 เลือกประเภทการกระทำที่ต้องการ
                                </p>
                            </div>

                            {{-- การตั้งค่าการกระทำ --}}
                            <div id="action_config_section" class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    การตั้งค่าการกระทำ
                                </label>
                                <p class="mb-3 text-xs text-gray-500 bg-yellow-50 p-2 rounded-lg">
                                    ⚙️ ตั้งค่าการกระทำตามประเภทที่เลือก
                                </p>
                                
                                {{-- Assign Action Config --}}
                                <div id="assign_config" class="mb-4 p-4 border border-gray-200 rounded-lg" style="display: none;">
                                    <label for="assign_user_id" class="block text-sm font-medium text-gray-700 mb-2">มอบหมายให้</label>
                                    <select id="assign_user_id" name="action_config[user_id]" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">เลือกผู้รับผิดชอบ</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" 
                                                    {{ old('action_config.user_id', $step->action_config['user_id'] ?? '') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->role }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Status Change Config --}}
                                <div id="status_change_config" class="mb-4 p-4 border border-gray-200 rounded-lg" style="display: none;">
                                    <label for="status_id" class="block text-sm font-medium text-gray-700 mb-2">เปลี่ยนสถานะเป็น</label>
                                    <select id="status_id" name="action_config[status_id]" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">เลือกสถานะ</option>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status->id }}" 
                                                    {{ old('action_config.status_id', $step->action_config['status_id'] ?? '') == $status->id ? 'selected' : '' }}>
                                                {{ $status->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Notification Config --}}
                                <div id="notification_config" class="mb-4 p-4 border border-gray-200 rounded-lg" style="display: none;">
                                    <label for="notification_message" class="block text-sm font-medium text-gray-700 mb-2">ข้อความแจ้งเตือน</label>
                                    <textarea id="notification_message" name="action_config[message]" rows="3"
                                             class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                             placeholder="กรอกข้อความแจ้งเตือน">{{ old('action_config.message', $step->action_config['message'] ?? '') }}</textarea>
                                </div>

                                {{-- Auto Reply Config --}}
                                <div id="auto_reply_config" class="mb-4 p-4 border border-gray-200 rounded-lg" style="display: none;">
                                    <label for="auto_reply_message" class="block text-sm font-medium text-gray-700 mb-2">ข้อความตอบกลับ</label>
                                    <textarea id="auto_reply_message" name="action_config[message]" rows="3"
                                             class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                             placeholder="กรอกข้อความตอบกลับอัตโนมัติ">{{ old('action_config.message', $step->action_config['message'] ?? '') }}</textarea>
                                </div>

                                {{-- Add Comment Config --}}
                                <div id="add_comment_config" class="mb-4 p-4 border border-gray-200 rounded-lg" style="display: none;">
                                    <label for="comment_text" class="block text-sm font-medium text-gray-700 mb-2">ข้อความความคิดเห็น</label>
                                    <textarea id="comment_text" name="action_config[comment]" rows="3"
                                             class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                             placeholder="กรอกข้อความความคิดเห็น">{{ old('action_config.comment', $step->action_config['comment'] ?? '') }}</textarea>
                                </div>

                                {{-- Set Due Date Config --}}
                                <div id="set_due_date_config" class="mb-4 p-4 border border-gray-200 rounded-lg" style="display: none;">
                                    <label for="due_date_hours" class="block text-sm font-medium text-gray-700 mb-2">ครบกำหนดใน (ชั่วโมง)</label>
                                    <input type="number" id="due_date_hours" name="action_config[hours]" min="1" max="168"
                                           value="{{ old('action_config.hours', $step->action_config['hours'] ?? '') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                           placeholder="24">
                                    <p class="mt-1 text-xs text-gray-500">
                                        📅 กำหนดวันครบกำหนดเป็นชั่วโมงนับจากตอนนี้
                                    </p>
                                </div>

                                {{-- Wait for Response Config --}}
                                <div id="wait_for_response_config" class="mb-4 p-4 border border-gray-200 rounded-lg" style="display: none;">
                                    <label for="wait_hours" class="block text-sm font-medium text-gray-700 mb-2">รอ (ชั่วโมง)</label>
                                    <input type="number" id="wait_hours" name="action_config[hours]" min="1" max="168"
                                           value="{{ old('action_config.hours', $step->action_config['hours'] ?? '') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                           placeholder="24">
                                    <p class="mt-1 text-xs text-gray-500">
                                        ⏳ รอการตอบกลับเป็นชั่วโมง
                                    </p>
                                </div>
                            </div>

                            {{-- เวลาหน่วง (Delay) --}}
                            <div class="md:col-span-2">
                                <label for="delay_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                                    เวลาหน่วง (นาที)
                                </label>
                                <input type="number" id="delay_minutes" name="delay_minutes" 
                                       value="{{ old('delay_minutes', $step->delay_minutes) }}" min="0" max="10080"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="0">
                                <p class="mt-1 text-xs text-gray-500">
                                    ⏰ หน่วงเวลาการทำงานขั้นตอนนี้ (0 = ทำงานทันที)
                                </p>
                            </div>
                        </div>

                        {{-- ปุ่มบันทึก --}}
                        <div class="mt-8 flex justify-end space-x-4">
                            <a href="{{ route('workflows.edit', $workflow) }}" 
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
        function toggleActionConfig() {
            const actionType = document.getElementById('action_type').value;
            
            // ซ่อนทุก config
            const configs = [
                'assign_config', 'status_change_config', 'notification_config',
                'auto_reply_config', 'add_comment_config', 'set_due_date_config',
                'wait_for_response_config'
            ];
            
            configs.forEach(config => {
                document.getElementById(config).style.display = 'none';
            });
            
            // แสดง config ตาม action type
            if (actionType) {
                document.getElementById(actionType + '_config').style.display = 'block';
            }
        }

        // เรียกใช้ฟังก์ชันเมื่อโหลดหน้า
        document.addEventListener('DOMContentLoaded', function() {
            toggleActionConfig();
        });
    </script>
</x-app-layout>
