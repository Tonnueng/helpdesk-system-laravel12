<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <i class="fas fa-exclamation-triangle text-2xl text-indigo-600"></i>
                <h2 class="text-3xl font-bold text-indigo-700 leading-tight">
                    แจ้งปัญหาใหม่
                </h2>
            </div>
            <div class="text-sm text-gray-600">
                <i class="fas fa-calendar mr-1"></i>
                {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Progress Steps -->
            <div class="mb-12">
                <div class="flex items-center justify-center space-x-8">
                    <!-- Step 1 -->
                    <div data-step="1" class="flex items-center">
                        <div class="step-circle w-10 h-10 rounded-full bg-indigo-600 text-white text-sm font-semibold flex items-center justify-center">
                            1
                        </div>
                        <span class="step-label ml-2 text-sm font-medium text-indigo-600">หมวดหมู่</span>
                    </div>
                    
                    <!-- Connector -->
                    <div class="w-16 h-0.5 bg-gray-200"></div>
                    
                    <!-- Step 2 -->
                    <div data-step="2" class="flex items-center">
                        <div class="step-circle w-10 h-10 rounded-full bg-gray-200 text-gray-500 text-sm font-semibold flex items-center justify-center">
                            2
                        </div>
                        <span class="step-label ml-2 text-sm font-medium text-gray-500">รายละเอียด</span>
                    </div>
                    
                    <!-- Connector -->
                    <div class="w-16 h-0.5 bg-gray-200"></div>
                    
                    <!-- Step 3 -->
                    <div data-step="3" class="flex items-center">
                        <div class="step-circle w-10 h-10 rounded-full bg-gray-200 text-gray-500 text-sm font-semibold flex items-center justify-center">
                            3
                        </div>
                        <span class="step-label ml-2 text-sm font-medium text-gray-500">ความสำคัญ</span>
                    </div>
                    
                    <!-- Connector -->
                    <div class="w-16 h-0.5 bg-gray-200"></div>
                    
                    <!-- Step 4 -->
                    <div data-step="4" class="flex items-center">
                        <div class="step-circle w-10 h-10 rounded-full bg-gray-200 text-gray-500 text-sm font-semibold flex items-center justify-center">
                            4
                        </div>
                        <span class="step-label ml-2 text-sm font-medium text-gray-500">ส่งรายงาน</span>
                    </div>
                </div>
            </div>

            <!-- Main Form Card -->
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
                <form 
                    x-data="{
                        currentStep: 1,
                        formData: {
                            title: `{{ old('title', '') }}`,
                            description: `{{ old('description', '') }}`,
                            main_category: `{{ old('main_category', '') }}`,
                            sub_category_id: `{{ old('sub_category_id', '') }}`,
                            priority_id: `{{ old('priority_id', '') }}`,
                            reported_at: `{{ old('reported_at', now()->format('Y-m-d\TH:i')) }}`
                        },
                        subCategories: [],
                        errors: {},
                        
                        async loadSubCategories() {
                            if (this.formData.main_category) {
                                try {
                                    const response = await fetch(`/api/categories/subcategories/${this.formData.main_category}`);
                                    this.subCategories = await response.json();
                                    this.formData.sub_category_id = '';
                                } catch (error) {
                                    console.error('Error loading subcategories:', error);
                                    this.subCategories = [];
                                }
                            } else {
                                this.subCategories = [];
                                this.formData.sub_category_id = '';
                            }
                        },
                        
                        nextStep() {
                            if (this.validateCurrentStep()) {
                                this.currentStep++;
                                this.updateProgress();
                            }
                        },
                        
                        prevStep() {
                            this.currentStep--;
                            this.updateProgress();
                        },
                        
                        updateProgress() {
                            const steps = document.querySelectorAll('[data-step]');
                            steps.forEach((step, index) => {
                                const stepNumber = index + 1;
                                const circle = step.querySelector('.step-circle');
                                const label = step.querySelector('.step-label');
                                
                                if (stepNumber < this.currentStep) {
                                    circle.className = 'step-circle w-10 h-10 rounded-full bg-emerald-600 text-white text-sm font-semibold flex items-center justify-center';
                                    label.className = 'step-label ml-2 text-sm font-medium text-emerald-600';
                                } else if (stepNumber === this.currentStep) {
                                    circle.className = 'step-circle w-10 h-10 rounded-full bg-indigo-600 text-white text-sm font-semibold flex items-center justify-center';
                                    label.className = 'step-label ml-2 text-sm font-medium text-indigo-600';
                                } else {
                                    circle.className = 'step-circle w-10 h-10 rounded-full bg-gray-200 text-gray-500 text-sm font-semibold flex items-center justify-center';
                                    label.className = 'step-label ml-2 text-sm font-medium text-gray-500';
                                }
                            });
                        },
                        
                        validateCurrentStep() {
                            this.errors = {};
                            let isValid = true;
                            
                            if (this.currentStep === 1) {
                                if (!this.formData.main_category) {
                                    this.errors.main_category = 'กรุณาเลือกหมวดหมู่หลัก';
                                    isValid = false;
                                }
                                if (!this.formData.sub_category_id) {
                                    this.errors.sub_category_id = 'กรุณาเลือกหมวดหมู่ย่อย';
                                    isValid = false;
                                }
                            } else if (this.currentStep === 2) {
                                if (!this.formData.title.trim()) {
                                    this.errors.title = 'กรุณากรอกหัวข้อปัญหา';
                                    isValid = false;
                                }
                                if (!this.formData.description.trim()) {
                                    this.errors.description = 'กรุณากรอกรายละเอียดของปัญหา';
                                    isValid = false;
                                }
                            } else if (this.currentStep === 3) {
                                if (!this.formData.priority_id) {
                                    this.errors.priority_id = 'กรุณาเลือกระดับความสำคัญ';
                                    isValid = false;
                                }
                            }
                            
                            return isValid;
                        },
                        
                        submitForm() {
                            if (this.validateCurrentStep()) {
                                this.$el.submit();
                            }
                        }
                    }"
                    @submit.prevent="submitForm"
                    method="POST" 
                    action="{{ route('tickets.store') }}" 
                    enctype="multipart/form-data"
                    novalidate>
                    @csrf

                    <!-- Step 1: Category Selection -->
                    <div x-show="currentStep === 1" 
                         x-transition:enter="transition ease-out duration-500" 
                         x-transition:enter-start="opacity-0 transform translate-y-8" 
                         x-transition:enter-end="opacity-100 transform translate-y-0" 
                         class="p-8 md:p-12">
                        
                        <!-- Header -->
                        <div class="text-center mb-8">
                            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-indigo-100 mb-4">
                                <i class="fas fa-tags text-2xl text-indigo-600"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 mb-2">เลือกหมวดหมู่ปัญหา</h3>
                            <p class="text-gray-600">กรุณาเลือกหมวดหมู่ที่เกี่ยวข้องกับปัญหาของคุณ</p>
                        </div>

                        <div class="space-y-6">
                            <!-- Main Category -->
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">หมวดหมู่หลัก</label>
                                <select x-model="formData.main_category" 
                                        @change="loadSubCategories()"
                                        name="main_category"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('main_category') border-red-500 @enderror">
                                    <option value="" disabled>-- เลือกหมวดหมู่หลัก --</option>
                                    @foreach ($mainCategories as $category)
                                        <option value="{{ $category->name }}" {{ old('main_category') == $category->name ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p x-show="errors.main_category" x-text="errors.main_category" class="text-sm text-red-600"></p>
                                <x-input-error :messages="$errors->get('main_category')" class="mt-2" />
                            </div>

                            <!-- Sub Category -->
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">หมวดหมู่ย่อย</label>
                                <select x-model="formData.sub_category_id" 
                                        name="sub_category_id"
                                        :disabled="subCategories.length === 0"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors disabled:bg-gray-50 disabled:text-gray-500 @error('sub_category_id') border-red-500 @enderror">
                                    <option value="" disabled x-text="subCategories.length === 0 ? '-- เลือกหมวดหมู่หลักก่อน --' : '-- เลือกหมวดหมู่ย่อย --'"></option>
                                    <template x-for="subCategory in subCategories" :key="subCategory.id">
                                        <option :value="subCategory.id" x-text="subCategory.name"></option>
                                    </template>
                                </select>
                                <p x-show="errors.sub_category_id" x-text="errors.sub_category_id" class="text-sm text-red-600"></p>
                                <x-input-error :messages="$errors->get('sub_category_id')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="flex justify-end mt-8">
                            <button type="button" @click="nextStep()" 
                                    class="px-8 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                                ถัดไป
                                <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Problem Details -->
                    <div x-show="currentStep === 2" 
                         x-transition:enter="transition ease-out duration-500" 
                         x-transition:enter-start="opacity-0 transform translate-y-8" 
                         x-transition:enter-end="opacity-100 transform translate-y-0" 
                         class="p-8 md:p-12">
                        
                        <!-- Header -->
                        <div class="text-center mb-8">
                            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mb-4">
                                <i class="fas fa-edit text-2xl text-blue-600"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 mb-2">รายละเอียดปัญหา</h3>
                            <p class="text-gray-600">กรุณาให้รายละเอียดที่ชัดเจนเกี่ยวกับปัญหาที่พบ</p>
                        </div>

                        <div class="space-y-6">
                            <!-- Title -->
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">หัวข้อปัญหา</label>
                                <input x-model.lazy="formData.title" 
                                       name="title" 
                                       type="text"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('title') border-red-500 @enderror"
                                       placeholder="เช่น: ไม่สามารถเข้าสู่ระบบได้, หน้าจอแสดงผลผิดปกติ, อุปกรณ์ไม่ทำงาน" />
                                <p x-show="errors.title" x-text="errors.title" class="text-sm text-red-600"></p>
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>

                            <!-- Description -->
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">รายละเอียดปัญหา</label>
                                <textarea x-model.lazy="formData.description" 
                                          name="description" 
                                          rows="6"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none @error('description') border-red-500 @enderror"
                                          placeholder="ตัวอย่าง:
- ปัญหาเกิดเมื่อเวลา 14:30 น.
- ข้อความที่แสดง: \"Error 404: Page not found\"
- ได้ลองรีเฟรชหน้าเว็บแล้วแต่ยังไม่แก้ไข
- เกิดขณะกำลังทำรายการชำระเงิน">{{ old('description') }}</textarea>
                                <p x-show="errors.description" x-text="errors.description" class="text-sm text-red-600"></p>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="flex justify-between mt-8">
                            <button type="button" @click="prevStep()" 
                                    class="px-8 py-3 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i>
                                ย้อนกลับ
                            </button>
                            <button type="button" @click="nextStep()" 
                                    class="px-8 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                                ถัดไป
                                <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Priority Selection -->
                    <div x-show="currentStep === 3" 
                         x-transition:enter="transition ease-out duration-500" 
                         x-transition:enter-start="opacity-0 transform translate-y-8" 
                         x-transition:enter-end="opacity-100 transform translate-y-0" 
                         class="p-8 md:p-12">
                        
                        <!-- Header -->
                        <div class="text-center mb-8">
                            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-emerald-100 mb-4">
                                <i class="fas fa-clock text-2xl text-emerald-600"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 mb-2">ความสำคัญและเวลา</h3>
                            <p class="text-gray-600">กำหนดระดับความสำคัญและเวลาที่พบปัญหา</p>
                        </div>

                        <div class="space-y-6">
                            <!-- Priority -->
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">ระดับความสำคัญ</label>
                                <select x-model="formData.priority_id" 
                                        name="priority_id"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('priority_id') border-red-500 @enderror">
                                    <option value="" disabled>-- เลือกระดับความสำคัญ --</option>
                                    @foreach ($priorities as $priority)
                                        @php
                                            $thaiPriority = match ($priority->name) {
                                                'Low' => 'ต่ำ (สามารถใช้งานได้ปกติ)',
                                                'Medium' => 'ปานกลาง (มีผลต่อการทำงานบางส่วน)',
                                                'High' => 'สูง (ไม่สามารถทำงานต่อได้)',
                                                'Critical' => 'เร่งด่วน (ส่งผลต่อระบบทั้งหมด)',
                                                default => $priority->name,
                                            };
                                            $priorityColor = match ($priority->name) {
                                                'Low' => 'text-emerald-600',
                                                'Medium' => 'text-blue-600',
                                                'High' => 'text-orange-600',
                                                'Critical' => 'text-red-600',
                                                default => '',
                                            };
                                        @endphp
                                        <option value="{{ $priority->id }}" {{ old('priority_id') == $priority->id ? 'selected' : '' }} class="{{ $priorityColor }}">
                                            {{ $thaiPriority }}
                                        </option>
                                    @endforeach
                                </select>
                                <p x-show="errors.priority_id" x-text="errors.priority_id" class="text-sm text-red-600"></p>
                                <x-input-error :messages="$errors->get('priority_id')" class="mt-2" />
                            </div>

                            <!-- Reported Date/Time -->
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">วันที่และเวลาที่พบปัญหา</label>
                                <div class="relative">
                                    <input x-model="formData.reported_at" 
                                           name="reported_at" 
                                           type="datetime-local"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors @error('reported_at') border-red-500 @enderror" />
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <i class="fas fa-calendar text-gray-400"></i>
                                    </div>
                                </div>
                                <x-input-error :messages="$errors->get('reported_at')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="flex justify-between mt-8">
                            <button type="button" @click="prevStep()" 
                                    class="px-8 py-3 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i>
                                ย้อนกลับ
                            </button>
                            <button type="button" @click="nextStep()" 
                                    class="px-8 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                                ถัดไป
                                <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 4: Review and Submit -->
                    <div x-show="currentStep === 4" 
                         x-transition:enter="transition ease-out duration-500" 
                         x-transition:enter-start="opacity-0 transform translate-y-8" 
                         x-transition:enter-end="opacity-100 transform translate-y-0" 
                         class="p-8 md:p-12">
                        
                        <!-- Header -->
                        <div class="text-center mb-8">
                            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-emerald-100 mb-4">
                                <i class="fas fa-check-circle text-2xl text-emerald-600"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 mb-2">ตรวจสอบข้อมูล</h3>
                            <p class="text-gray-600">กรุณาตรวจสอบข้อมูลก่อนส่งรายงานปัญหา</p>
                        </div>

                        <!-- Review Card -->
                        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                            <div class="space-y-4">
                                <!-- Category -->
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-tags text-indigo-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-700 mb-1">หมวดหมู่</h4>
                                        <p class="text-gray-600" x-text="formData.main_category"></p>
                                        <p class="text-sm text-gray-500" x-text="subCategories.find(cat => cat.id == formData.sub_category_id)?.name || 'กำลังโหลด...'"></p>
                                    </div>
                                </div>

                                <!-- Title -->
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-heading text-blue-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-700 mb-1">หัวข้อปัญหา</h4>
                                        <p class="text-gray-600" x-text="formData.title || 'ยังไม่ได้กรอก'"></p>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-align-left text-emerald-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-700 mb-1">รายละเอียด</h4>
                                        <p class="text-gray-600 text-sm leading-relaxed" x-text="formData.description || 'ยังไม่ได้กรอก'"></p>
                                    </div>
                                </div>

                                <!-- Priority -->
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-exclamation-triangle text-orange-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-700 mb-1">ความสำคัญ</h4>
                                        <p class="text-gray-600" x-text="getPriorityText(formData.priority_id)"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="flex justify-between mt-8">
                            <button type="button" @click="prevStep()" 
                                    class="px-8 py-3 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i>
                                ย้อนกลับ
                            </button>
                            <button type="submit" 
                                    class="px-8 py-3 bg-gradient-to-r from-emerald-200 to-green-300 text-emerald-800 border border-emerald-300 rounded-xl font-semibold hover:from-emerald-300 hover:to-green-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors">
                                <i class="fas fa-paper-plane mr-2"></i>
                                ส่งรายงานปัญหา
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Helper function for priority text
        function getPriorityText(priorityId) {
            const priorities = @json($priorities);
            const priority = priorities.find(p => p.id == priorityId);
            if (!priority) return 'ยังไม่ได้เลือก';
            
            const thaiPriority = {
                'Low': 'ต่ำ (สามารถใช้งานได้ปกติ)',
                'Medium': 'ปานกลาง (มีผลต่อการทำงานบางส่วน)',
                'High': 'สูง (ไม่สามารถทำงานต่อได้)',
                'Critical': 'เร่งด่วน (ส่งผลต่อระบบทั้งหมด)'
            };
            
            return thaiPriority[priority.name] || priority.name;
        }
    </script>
</x-app-layout>