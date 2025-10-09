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
                {{ now('Asia/Bangkok')->format('d/m/Y H:i') }}
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header Section -->
            <div class="mb-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-1">แจ้งปัญหาใหม่</h3>
                <p class="text-sm text-gray-500">กรุณาเลือกหมวดหมู่ก่อน แล้วกรอกรายละเอียดปัญหา</p>
            </div>

            <!-- Main Form Card -->
            <div class="bg-white rounded-lg border border-gray-200">
                <form 
                    x-data="ticketFormData()"
                    @submit.prevent="submitForm"
                    method="POST" 
                    action="{{ route('tickets.store') }}" 
                    enctype="multipart/form-data"
                    novalidate
                    id="ticket-form">
                    @csrf

                    <div class="p-6">
                        <!-- Form Sections -->
                        <div class="space-y-6" x-show="!showPreview">
                            
                            <!-- Category Selection Section -->
                            <div class="border-l-4 border-blue-500 bg-blue-50/30 rounded-r-lg p-4 mb-6">
                                <div class="flex items-center mb-4">
                                    <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-tags text-white text-xs"></i>
                            </div>
                                    <h4 class="text-sm font-semibold text-blue-800">หมวดหมู่ปัญหา</h4>
                        </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Main Category -->
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">หมวดหมู่หลัก</label>
                                <select x-model="formData.main_category" 
                                        @change="loadSubCategories()"
                                        name="main_category"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:border-blue-400 hover:border-blue-300 transition-colors @error('main_category') border-red-500 @enderror">
                                            <option value="">-- เลือกหมวดหมู่หลัก --</option>
                                    @foreach ($mainCategories as $category)
                                        <option value="{{ $category->name }}" {{ old('main_category') == $category->name ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p x-show="errors.main_category" x-text="errors.main_category" class="text-sm text-red-600"></p>
                                        <x-input-error :messages="$errors->get('main_category')" class="mt-1" />
                            </div>

                            <!-- Sub Category -->
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">หมวดหมู่ย่อย</label>
                                <select x-model="formData.sub_category_id" 
                                        name="sub_category_id"
                                        :disabled="subCategories.length === 0"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:border-blue-400 hover:border-blue-300 transition-colors disabled:bg-gray-50 disabled:text-gray-500 @error('sub_category_id') border-red-500 @enderror">
                                            <option value="" x-text="subCategories.length === 0 ? '-- เลือกหมวดหมู่หลักก่อน --' : '-- เลือกหมวดหมู่ย่อย --'"></option>
                                    <template x-for="subCategory in subCategories" :key="subCategory.id">
                                        <option :value="subCategory.id" x-text="subCategory.name"></option>
                                    </template>
                                </select>
                                <p x-show="errors.sub_category_id" x-text="errors.sub_category_id" class="text-sm text-red-600"></p>
                                        <x-input-error :messages="$errors->get('sub_category_id')" class="mt-1" />
                        </div>
                    </div>
                            </div>

                            <!-- Problem Details Section -->
                            <div class="border-l-4 border-green-500 bg-green-50/30 rounded-r-lg p-4 mb-6"
                                 :class="{ 'opacity-50 pointer-events-none': !canFillOtherFields() }">
                                <div class="flex items-center mb-4">
                                    <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-edit text-white text-xs"></i>
                                    </div>
                                    <h4 class="text-sm font-semibold text-green-800">รายละเอียดปัญหา</h4>
                        </div>
                                <p class="text-xs text-orange-600 mb-4" x-show="!canFillOtherFields()">⚠️ กรุณาเลือกหมวดหมู่ก่อน</p>

                                <div class="space-y-4">
                            <!-- Title -->
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">หัวข้อปัญหา</label>
                                <input x-model.lazy="formData.title" 
                                       name="title" 
                                       id="title"
                                       type="text"
                                               :disabled="!canFillOtherFields()"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-400 focus:border-green-400 hover:border-green-300 transition-colors disabled:bg-gray-50 disabled:text-gray-500 @error('title') border-red-500 @enderror"
                                               placeholder="เช่น: ไม่สามารถเข้าสู่ระบบได้, หน้าจอแสดงผลผิดปกติ" />
                                <p x-show="errors.title" x-text="errors.title" class="text-sm text-red-600"></p>
                                        <x-input-error :messages="$errors->get('title')" class="mt-1" />
                            </div>

                            <!-- Description -->
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">รายละเอียดปัญหา</label>
                                <textarea x-model.lazy="formData.description" 
                                          name="description" 
                                          id="description"
                                                  rows="4"
                                                  :disabled="!canFillOtherFields()"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-400 focus:border-green-400 hover:border-green-300 transition-colors resize-none disabled:bg-gray-50 disabled:text-gray-500 @error('description') border-red-500 @enderror"
                                                  placeholder="อธิบายปัญหาที่พบ เช่น เวลาเกิดเหตุ สิ่งที่เกิดขึ้น และสิ่งที่ได้ลองทำ">{{ old('description') }}</textarea>
                                <p x-show="errors.description" x-text="errors.description" class="text-sm text-red-600"></p>
                                        <x-input-error :messages="$errors->get('description')" class="mt-1" />
                        </div>

                                    <!-- Links -->
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">ลิงค์ที่เกี่ยวข้อง (ถ้ามี)</label>
                                        <textarea x-model.lazy="formData.links" 
                                                  name="links" 
                                                  id="links"
                                                  rows="2"
                                                  :disabled="!canFillOtherFields()"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-400 focus:border-green-400 hover:border-green-300 transition-colors resize-none disabled:bg-gray-50 disabled:text-gray-500 @error('links') border-red-500 @enderror"
                                                  placeholder="ใส่ลิงค์ที่เกี่ยวข้อง เช่น หน้าจอที่มีปัญหา, เอกสารอ้างอิง, หรือลิงค์อื่นๆ (แยกแต่ละลิงค์ด้วยบรรทัดใหม่)">{{ old('links') }}</textarea>
                                        <p class="text-xs text-gray-500">💡 ใส่ลิงค์ที่เกี่ยวข้องกับปัญหา (ถ้ามี) เพื่อช่วยให้ทีมแก้ไขเข้าใจปัญหาได้ดีขึ้น</p>
                                        <x-input-error :messages="$errors->get('links')" class="mt-1" />
                        </div>
                    </div>
                            </div>

                            <!-- Priority and Time Section -->
                            <div class="border-l-4 border-orange-500 bg-orange-50/30 rounded-r-lg p-4 mb-6"
                                 :class="{ 'opacity-50 pointer-events-none': !canFillOtherFields() }">
                                <div class="flex items-center mb-4">
                                    <div class="w-6 h-6 bg-orange-500 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-clock text-white text-xs"></i>
                                    </div>
                                    <h4 class="text-sm font-semibold text-orange-800">ความสำคัญและเวลา</h4>
                        </div>
                                <p class="text-xs text-orange-600 mb-4" x-show="!canFillOtherFields()">⚠️ กรุณาเลือกหมวดหมู่ก่อน</p>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Priority -->
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">ระดับความสำคัญ</label>
                                <select x-model="formData.priority_id" 
                                        name="priority_id"
                                        id="priority_id"
                                                :disabled="!canFillOtherFields()"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-400 focus:border-orange-400 hover:border-orange-300 transition-colors disabled:bg-gray-50 disabled:text-gray-500 @error('priority_id') border-red-500 @enderror">
                                            <option value="">-- เลือกระดับความสำคัญ --</option>
                                    @foreach ($priorities as $priority)
                                        @php
                                            $thaiPriority = match ($priority->name) {
                                                'Low' => 'ต่ำ (สามารถใช้งานได้ปกติ)',
                                                'Medium' => 'ปานกลาง (มีผลต่อการทำงานบางส่วน)',
                                                'High' => 'สูง (ไม่สามารถทำงานต่อได้)',
                                                'Critical' => 'เร่งด่วน (ส่งผลต่อระบบทั้งหมด)',
                                                default => $priority->name,
                                            };
                                        @endphp
                                                <option value="{{ $priority->id }}" {{ old('priority_id') == $priority->id ? 'selected' : '' }}>
                                            {{ $thaiPriority }}
                                        </option>
                                    @endforeach
                                </select>
                                <p x-show="errors.priority_id" x-text="errors.priority_id" class="text-sm text-red-600"></p>
                                        <x-input-error :messages="$errors->get('priority_id')" class="mt-1" />
                            </div>

                            <!-- Reported Date/Time -->
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">วันที่และเวลาที่พบปัญหา</label>
                                <div class="relative">
                                    <input x-model="formData.reported_at" 
                                           name="reported_at" 
                                           id="reported_at"
                                           type="datetime-local"
                                                   :disabled="!canFillOtherFields()"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-400 focus:border-orange-400 hover:border-orange-300 transition-colors disabled:bg-gray-50 disabled:text-gray-500 @error('reported_at') border-red-500 @enderror" />
                                        </div>
                                        <x-input-error :messages="$errors->get('reported_at')" class="mt-1" />
                                    </div>
                                </div>
                            </div>

                            <!-- File Upload Section -->
                            <div class="border-l-4 border-indigo-500 bg-gradient-to-r from-indigo-50/50 to-blue-50/30 rounded-r-lg p-6 mb-6 shadow-lg hover:shadow-xl transition-all duration-300"
                                 :class="{ 'opacity-50 pointer-events-none': !canFillOtherFields() }" 
                                 id="file-upload-section">
                                <div class="flex items-center mb-6">
                                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-full flex items-center justify-center mr-4 shadow-lg transform hover:scale-110 transition-transform duration-200">
                                        <i class="fas fa-images text-white text-base"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-bold text-indigo-800 mb-1">รูปภาพประกอบ (ถ้ามี)</h4>
                                        <p class="text-sm text-indigo-600">เพิ่มรูปภาพที่เกี่ยวข้องกับปัญหา</p>
                                    </div>
                                </div>

                                <p class="text-xs text-indigo-600 mb-4" x-show="!canFillOtherFields()">⚠️ กรุณาเลือกหมวดหมู่ก่อน</p>
                                <p class="text-xs text-gray-600 mb-4" x-show="canFillOtherFields()">📷 อัพโหลดรูปภาพที่เกี่ยวข้องกับปัญหา เช่น หน้าจอที่มีปัญหา, ข้อความ error, หรือภาพประกอบอื่นๆ</p>
                                <p class="text-xs text-orange-600 mb-4" x-show="canFillOtherFields()">⚠️ ข้อจำกัด: สูงสุด 5 ไฟล์, ไฟล์ละไม่เกิน 2MB, รวมไม่เกิน 30MB</p>

                                <!-- Drag & Drop Area -->
                                <div class="mb-6">
                                    <div class="relative border-2 border-dashed border-indigo-200 rounded-2xl p-10 text-center transition-all duration-500 hover:border-indigo-400 hover:bg-gradient-to-br hover:from-indigo-50 hover:to-blue-50 cursor-pointer group" 
                                         id="drop-zone"
                                         onclick="document.getElementById('file-input').click()">
                                        
                                        <!-- Animated Background -->
                                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-50/0 to-blue-50/0 group-hover:from-indigo-50/50 group-hover:to-blue-50/30 rounded-2xl transition-all duration-500"></div>
                                        
                                        <!-- Upload Icon with Animation -->
                                        <div class="relative mb-6">
                                            <div class="w-20 h-20 bg-gradient-to-br from-indigo-100 to-blue-100 rounded-full flex items-center justify-center mx-auto shadow-xl group-hover:shadow-2xl group-hover:scale-110 transition-all duration-300">
                                                <i class="fas fa-cloud-upload-alt text-3xl text-indigo-600 group-hover:text-indigo-700 transition-colors duration-300"></i>
                                            </div>
                                            <!-- Floating particles effect -->
                                            <div class="absolute -top-2 -right-2 w-4 h-4 bg-indigo-400 rounded-full opacity-0 group-hover:opacity-100 group-hover:animate-pulse transition-opacity duration-500"></div>
                                            <div class="absolute -bottom-1 -left-1 w-3 h-3 bg-blue-400 rounded-full opacity-0 group-hover:opacity-100 group-hover:animate-pulse transition-opacity duration-700"></div>
                                        </div>
                                        
                                        <!-- Upload Text -->
                                        <div class="relative space-y-3">
                                            <h5 class="text-xl font-bold text-gray-800 group-hover:text-indigo-700 transition-colors duration-300">
                                                ลากไฟล์มาวางที่นี่
                                            </h5>
                                            <p class="text-sm text-gray-600 group-hover:text-indigo-600 transition-colors duration-300">
                                                หรือคลิกเพื่อเลือกไฟล์
                                            </p>
                                        </div>
                                        
                                        <!-- File Requirements with Icons -->
                                        <div class="relative mt-6 grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                                            <div class="flex items-center justify-center space-x-2 text-gray-600 group-hover:text-indigo-600 transition-colors duration-300">
                                                <i class="fas fa-file-image text-indigo-500"></i>
                                                <span>JPG, PNG, GIF</span>
                                            </div>
                                            <div class="flex items-center justify-center space-x-2 text-gray-600 group-hover:text-indigo-600 transition-colors duration-300">
                                                <i class="fas fa-weight-hanging text-indigo-500"></i>
                                                <span>สูงสุด 2MB</span>
                                            </div>
                                            <div class="flex items-center justify-center space-x-2 text-gray-600 group-hover:text-indigo-600 transition-colors duration-300">
                                                <i class="fas fa-layer-group text-indigo-500"></i>
                                                <span>หลายไฟล์พร้อมกัน</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Enhanced Warning -->
                                        <div class="relative mt-4 bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl p-3 group-hover:shadow-md transition-all duration-300">
                                            <div class="flex items-center justify-center space-x-2">
                                                <i class="fas fa-exclamation-triangle text-yellow-500 animate-pulse"></i>
                                                <p class="text-xs text-yellow-700 font-medium">
                                                    หากรูปภาพใหญ่กว่า 2MB อาจไม่สามารถอัพโหลดได้
                                                </p>
                                            </div>
                                    </div>
                                    </div>
                                    
                                    <!-- Hidden File Input -->
                                    <input type="file" 
                                           id="file-input"
                                           name="attachments[]" 
                                           multiple 
                                           accept="image/*"
                                           class="hidden">
                                </div>

                                <!-- Enhanced File Preview Grid -->
                                <div id="file-preview-grid" class="mb-4" style="display: none;">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-check text-green-600 text-sm"></i>
                                            </div>
                                            <h6 class="text-sm font-semibold text-gray-700">ไฟล์ที่เลือก (<span id="file-count" class="text-indigo-600">0</span> ไฟล์)</h6>
                                        </div>
                                        <button type="button" 
                                                onclick="clearAllFiles()"
                                                class="px-3 py-1.5 text-xs text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg flex items-center space-x-1 transition-all duration-200">
                                            <i class="fas fa-trash"></i>
                                            <span>ลบทั้งหมด</span>
                                        </button>
                                    </div>
                                    
                                    <div id="preview-container" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                                        <!-- File previews will be added here -->
                                    </div>
                                </div>

                                <!-- Error Messages -->
                                <x-input-error :messages="$errors->get('attachments')" class="mt-2" />
                                <x-input-error :messages="$errors->get('attachments.*')" class="mt-2" />
                            </div>

                            <!-- Submit Section -->
                            <div class="border-l-4 border-purple-500 bg-purple-50/30 rounded-r-lg p-4 mt-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 bg-purple-500 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-paper-plane text-white text-xs"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-purple-800">พร้อมส่งรายงาน</h4>
                                            <p class="text-xs text-gray-600">ตรวจสอบข้อมูลให้ครบถ้วน</p>
                                    </div>
                                    </div>
                                    <button type="submit" 
                                            :disabled="!canFillOtherFields()"
                                            class="px-6 py-3 bg-gradient-to-r from-purple-500 to-blue-600 text-white rounded-lg text-sm font-semibold hover:from-purple-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl transition-all duration-200">
                                        <i class="fas fa-paper-plane mr-2"></i>
                                        ส่งรายงานปัญหา
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Preview Section -->
                        <div x-show="showPreview" id="preview-section">
                            <div class="bg-gradient-to-r from-indigo-50 to-blue-50 rounded-lg p-6 mb-6">
                                <div class="flex items-center mb-6">
                                    <div class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center mr-4">
                                        <i class="fas fa-eye text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-indigo-800">ตรวจสอบข้อมูลก่อนส่ง</h3>
                                        <p class="text-sm text-indigo-600">กรุณาตรวจสอบข้อมูลให้ถูกต้องก่อนยืนยันการส่งรายงาน</p>
                                    </div>
                                </div>
                                
                                <!-- Preview Content -->
                                <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Left Column -->
                                        <div class="space-y-4">
                                            <div>
                                                <label class="text-sm font-medium text-gray-500">หมวดหมู่หลัก:</label>
                                                <p class="text-gray-900 font-semibold" x-text="formData.main_category"></p>
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-500">หมวดหมู่ย่อย:</label>
                                                <p class="text-gray-900 font-semibold" x-text="getSelectedSubCategoryName()"></p>
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-500">หัวข้อปัญหา:</label>
                                                <p class="text-gray-900 font-semibold" x-text="formData.title"></p>
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-500">ระดับความสำคัญ:</label>
                                                <p class="text-gray-900 font-semibold" x-text="getSelectedPriorityName()"></p>
                                            </div>
                                        </div>
                                        
                                        <!-- Right Column -->
                                        <div class="space-y-4">
                                            <div>
                                                <label class="text-sm font-medium text-gray-500">วันที่และเวลาที่พบปัญหา:</label>
                                                <p class="text-gray-900 font-semibold" x-text="formatDateTime(formData.reported_at)"></p>
                                            </div>
                                            <div x-show="formData.links">
                                                <label class="text-sm font-medium text-gray-500">ลิงค์ที่เกี่ยวข้อง:</label>
                                                <p class="text-gray-900 text-sm whitespace-pre-line" x-text="formData.links"></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Description -->
                                    <div class="mt-6">
                                        <label class="text-sm font-medium text-gray-500">รายละเอียดปัญหา:</label>
                                        <div class="mt-2 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                            <p class="text-gray-900 whitespace-pre-line" x-text="formData.description"></p>
                                        </div>
                                    </div>
                                    
                                    <!-- File Attachments Preview -->
                                    <div class="mt-6" x-show="selectedFiles.length > 0">
                                        <label class="text-sm font-medium text-gray-500">ไฟล์แนบ:</label>
                                        <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                                            <template x-for="(file, index) in selectedFiles" :key="index">
                                                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-file-image text-indigo-500"></i>
                                                        <span class="text-xs text-gray-700 truncate" x-text="file.name"></span>
                                                    </div>
                                                    <p class="text-xs text-gray-500 mt-1" x-text="formatFileSize(file.size)"></p>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="flex justify-between items-center mt-6">
                                    <button type="button" 
                                            @click="backToEdit()"
                                            class="px-6 py-3 bg-gray-500 text-white rounded-lg text-sm font-semibold hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                                        <i class="fas fa-edit mr-2"></i>
                                        แก้ไขข้อมูล
                                    </button>
                                    
                                    <button type="button" 
                                            @click="confirmSubmit()"
                                            class="px-8 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg text-sm font-semibold hover:from-green-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 shadow-lg hover:shadow-xl transition-all duration-200">
                                        <i class="fas fa-check mr-2"></i>
                                        ยืนยันการส่งรายงาน
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Pass PHP data to JavaScript
        window.priorities = {!! json_encode($priorities) !!};
        
        // Initialize form data with old values
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[x-data]');
            if (form && form._x_dataStack && form._x_dataStack[0]) {
                const data = form._x_dataStack[0];
                data.formData.title = `{{ old('title', '') }}`;
                data.formData.description = `{{ old('description', '') }}`;
                data.formData.main_category = `{{ old('main_category', '') }}`;
                data.formData.sub_category_id = `{{ old('sub_category_id', '') }}`;
                data.formData.priority_id = `{{ old('priority_id', '') }}`;
                data.formData.reported_at = `{{ old('reported_at', now('Asia/Bangkok')->format('Y-m-d\TH:i:s')) }}`;
                data.formData.links = `{{ old('links', '') }}`;
                
                // Load subcategories if main category is selected
                if (data.formData.main_category) {
                    data.loadSubCategories();
                }
            }
        });
    </script>

</x-app-layout>