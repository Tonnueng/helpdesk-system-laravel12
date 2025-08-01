<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-bold text-indigo-700 leading-tight">
            {{ __('แจ้งปัญหาใหม่') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12 bg-gray-100">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 md:p-10 rounded-xl shadow-md">
                <h1 class="text-3xl font-bold mb-8 text-center text-indigo-700">{{ __('แบบฟอร์มแจ้งปัญหาใหม่') }}</h1>

                <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- หัวข้อปัญหา -->
                    <div>
                        <x-input-label for="title" :value="__('หัวข้อปัญหา')" />
                        <x-text-input id="title" name="title" type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-300 focus:ring-opacity-50 @error('title') border-red-500 @enderror"
                            :value="old('title')" required autofocus
                            placeholder="{{ __('ระบุหัวข้อปัญหาที่ชัดเจนและกระชับ') }}" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2 text-red-500" />
                    </div>

                    <!-- รายละเอียดปัญหา -->
                    <div>
                        <x-input-label for="description" :value="__('รายละเอียดปัญหา')" />
                        <textarea id="description" name="description" rows="7"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-300 focus:ring-opacity-50 @error('description') border-red-500 @enderror"
                            required placeholder="{{ __('อธิบายปัญหาให้ละเอียดที่สุด เช่น เกิดเมื่อไหร่ อย่างไร มีข้อความผิดพลาดอะไรบ้าง และได้ลองแก้ไขอะไรไปแล้วบ้าง') }}">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2 text-red-500" />
                    </div>

                    <!-- ประเภทและความสำคัญ -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="category_id" :value="__('ประเภทปัญหา')" />
                            <select id="category_id" name="category_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-300 focus:ring-opacity-50 @error('category_id') border-red-500 @enderror"
                                required>
                                <option value="">{{ __('เลือกประเภท') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2 text-red-500" />
                        </div>

                        <div>
                            <x-input-label for="priority_id" :value="__('ระดับความสำคัญ')" />
                            <select id="priority_id" name="priority_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-300 focus:ring-opacity-50 @error('priority_id') border-red-500 @enderror"
                                required>
                                <option value="">{{ __('เลือกระดับความสำคัญ') }}</option>
                                @foreach ($priorities as $priority)
                                    @php
                                        $thaiPriority = match ($priority->name) {
                                            'Low' => 'ต่ำ',
                                            'Medium' => 'ปานกลาง',
                                            'High' => 'สูง',
                                            'Critical' => 'เร่งด่วน',
                                            default => $priority->name,
                                        };
                                    @endphp
                                    <option value="{{ $priority->id }}"
                                        {{ old('priority_id') == $priority->id ? 'selected' : '' }}>
                                        {{ $thaiPriority }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('priority_id')" class="mt-2 text-red-500" />
                        </div>
                    </div>

                    <!-- วันที่ที่พบปัญหา -->
                    <div>
                        <x-input-label for="reported_at" :value="__('วันที่และเวลาที่พบปัญหา')" />
                        <x-text-input id="reported_at" name="reported_at" type="datetime-local"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-300 focus:ring-opacity-50 @error('reported_at') border-red-500 @enderror"
                            :value="old('reported_at', now()->format('Y-m-d\TH:i'))" />
                        <x-input-error :messages="$errors->get('reported_at')" class="mt-2 text-red-500" />
                    </div>

                    <!-- ไฟล์แนบ -->
                    <div>
                        <x-input-label for="attachments" :value="__('ไฟล์แนบ (รูปภาพ/เอกสาร)')" />
                        <input type="file" id="attachments" name="attachments[]" multiple
                            class="block w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-indigo-50 file:text-indigo-700
                            hover:file:bg-indigo-100" />
                        <x-input-error :messages="$errors->get('attachments.*')" class="mt-2 text-red-500" />
                        <p class="mt-2 text-sm text-gray-500">
                            {{ __('ไฟล์ที่รองรับ: jpg, png, pdf, doc, docx. ขนาดสูงสุด 2MB ต่อไฟล์') }}
                        </p>
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
