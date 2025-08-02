<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <h2 class="text-3xl font-bold text-indigo-700 leading-tight">
                {{ __('แจ้งปัญหาใหม่') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12 bg-gradient-to-br from-indigo-50 to-gray-50">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white p-8 md:p-10 rounded-2xl shadow-lg border border-indigo-100 transform transition-all hover:shadow-xl">
                <div class="text-center mb-10">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-indigo-100 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-indigo-700">{{ __('แบบฟอร์มแจ้งปัญหาใหม่') }}</h1>
                    <p class="mt-2 text-gray-600">
                        {{ __('กรุณากรอกข้อมูลให้ครบถ้วนเพื่อให้เราช่วยแก้ปัญหาได้อย่างรวดเร็ว') }}</p>
                </div>

                <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data"
                    class="space-y-8">
                    @csrf

                    <!-- หัวข้อปัญหา -->
                    <div class="space-y-2">
                        <x-input-label for="title" :value="__('หัวข้อปัญหา')" class="text-gray-700 font-medium" />
                        <x-text-input id="title" name="title" type="text"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 py-3 px-4 @error('title') border-red-500 @enderror"
                            :value="old('title')" required autofocus
                            placeholder="{{ __('เช่น: ไม่สามารถเข้าสู่ระบบได้, หน้าจอแสดงผลผิดปกติ, อุปกรณ์ไม่ทำงาน') }}" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2 text-red-600 text-sm" />
                    </div>

                    <!-- รายละเอียดปัญหา -->
                    <div class="space-y-2">
                        <x-input-label for="description" :value="__('รายละเอียดปัญหา')" class="text-gray-700 font-medium" />
                        <textarea id="description" name="description" rows="7"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 py-3 px-4 @error('description') border-red-500 @enderror"
                            required
                            placeholder="{{ __('ตัวอย่าง:
                            - ปัญหาเกิดเมื่อเวลา 14:30 น.
                            - ข้อความที่แสดง: "Error 404: Page not found"
                            - ได้ลองรีเฟรชหน้าเว็บแล้วแต่ยังไม่แก้ไข
                            - เกิดขณะกำลังทำรายการชำระเงิน') }}">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2 text-red-600 text-sm" />
                    </div>

                    <!-- ประเภทและความสำคัญ -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <x-input-label for="category_id" :value="__('ประเภทปัญหา')" class="text-gray-700 font-medium" />
                            <select id="category_id" name="category_id"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 py-3 px-4 @error('category_id') border-red-500 @enderror"
                                required>
                                <option value="" class="text-gray-400">{{ __('-- เลือกประเภท --') }}</option>
                                @foreach ($categories as $category)
                                    @php
                                        $thaiCategory = match ($category->name) {
                                            'Hardware' => 'เกี่ยวกับอุปกรณ์',
                                            'Software' => 'เกี่ยวกับระบบ',
                                            'Network' => 'เกี่ยวกับอินเทอร์เน็ต',
                                            'Account Access' => 'เกี่ยวกับบัญชีผู้ใช้',
                                            'Other' => 'อื่นๆ',
                                            default => $category->name,
                                        };
                                    @endphp
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}
                                        class="hover:bg-indigo-50">
                                        {{ $thaiCategory }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2 text-red-600 text-sm" />
                        </div>

                        <div class="space-y-2">
                            <x-input-label for="priority_id" :value="__('ระดับความสำคัญ')" class="text-gray-700 font-medium" />
                            <select id="priority_id" name="priority_id"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 py-3 px-4 @error('priority_id') border-red-500 @enderror"
                                required>
                                <option value="" class="text-gray-400">{{ __('-- เลือกระดับ --') }}</option>
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
                                            'Low' => 'text-green-600',
                                            'Medium' => 'text-blue-600',
                                            'High' => 'text-orange-600',
                                            'Critical' => 'text-red-600',
                                            default => '',
                                        };
                                    @endphp
                                    <option value="{{ $priority->id }}"
                                        {{ old('priority_id') == $priority->id ? 'selected' : '' }}
                                        class="{{ $priorityColor }} hover:bg-indigo-50">
                                        {{ $thaiPriority }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('priority_id')" class="mt-2 text-red-600 text-sm" />
                        </div>
                    </div>

                    <!-- วันที่ที่พบปัญหา -->
                    <div class="space-y-2">
                        <x-input-label for="reported_at" :value="__('วันที่และเวลาที่พบปัญหา')" class="text-gray-700 font-medium" />
                        <div class="relative">
                            <x-text-input id="reported_at" name="reported_at" type="datetime-local"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:ring-opacity-50 py-3 px-4 @error('reported_at') border-red-500 @enderror"
                                :value="old('reported_at', now()->format('Y-m-d\TH:i'))" />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('reported_at')" class="mt-2 text-red-600 text-sm" />
                    </div>

                    <!-- ไฟล์แนบ -->
                    <div class="space-y-2">
                        <x-input-label for="attachments" :value="__('ไฟล์แนบ (รูปภาพ/เอกสาร)')" class="text-gray-700 font-medium" />
                        <div
                            class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                    viewBox="0 0 48 48" aria-hidden="true">
                                    <path
                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="attachments"
                                        class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <span>{{ __('อัพโหลดไฟล์') }}</span>
                                        <input id="attachments" name="attachments[]" type="file" multiple
                                            class="sr-only">
                                    </label>
                                    <p class="pl-1">{{ __('หรือลากและวางไฟล์ที่นี่') }}</p>
                                </div>
                                <p class="text-xs text-gray-500">
                                    {{ __('รองรับไฟล์: JPG, PNG, PDF, DOC, DOCX ขนาดสูงสุด 2MB ต่อไฟล์') }}
                                </p>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('attachments.*')" class="mt-2 text-red-600 text-sm" />
                    </div>

                    <!-- ปุ่มส่งฟอร์ม -->
                    <div class="flex justify-end pt-4">
                        <x-primary-button
                            class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-full font-semibold text-white uppercase tracking-widest shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.169.409l5 1.429A1 1 0 0019 16.553L10.894 2.553z" />
                            </svg>
                            {{ __('แจ้งปัญหา') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
