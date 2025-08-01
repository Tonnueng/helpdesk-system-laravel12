<section class="p-8 bg-white rounded-xl shadow-md space-y-6">
    <header class="border-b pb-4">
        <h2 class="text-3xl font-bold text-indigo-700">
            {{ __('ลบบัญชีผู้ใช้') }}
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            {{ __('เมื่อคุณลบบัญชี ข้อมูลและทรัพยากรทั้งหมดจะถูกลบอย่างถาวร กรุณาดาวน์โหลดข้อมูลที่ต้องการเก็บไว้ก่อนดำเนินการ') }}
        </p>
    </header>

    <!-- 🔴 ปุ่มเปิด Modal -->
    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-md transition ease-in-out"
    >{{ __('ลบบัญชี') }}</x-danger-button>

    <!-- 🧾 Modal ยืนยันการลบ -->
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 space-y-6">
            @csrf
            @method('delete')

            <h2 class="text-xl font-semibold text-gray-800">
                {{ __('คุณแน่ใจหรือไม่ว่าต้องการลบบัญชีนี้?') }}
            </h2>

            <p class="text-sm text-gray-500">
                {{ __('เมื่อบัญชีถูกลบ ข้อมูลทั้งหมดจะหายไปอย่างถาวร กรุณากรอกรหัสผ่านเพื่อยืนยันการลบ') }}
            </p>

            <div>
                <x-input-label for="password" value="{{ __('รหัสผ่าน') }}" class="sr-only" />
                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="{{ __('รหัสผ่าน') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-300 focus:ring-opacity-50"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-red-500" />
            </div>

            <div class="flex justify-end gap-3">
                <x-secondary-button class="px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300 text-gray-800 transition ease-in-out"
                    x-on:click="$dispatch('close')">
                    {{ __('ยกเลิก') }}
                </x-secondary-button>

                <x-danger-button
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-md transition ease-in-out"
                >
                    {{ __('ลบบัญชี') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
