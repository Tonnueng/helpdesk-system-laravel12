<section class="p-8 bg-white rounded-xl shadow-md">
    <header class="mb-6 border-b pb-4">
        <h2 class="text-3xl font-bold text-indigo-700">
            {{ __('เปลี่ยนรหัสผ่าน') }}
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            {{ __('เพื่อความปลอดภัย กรุณาใช้รหัสผ่านที่สุ่มและคาดเดายาก') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <!-- 🔐 รหัสผ่านปัจจุบัน -->
        <div>
            <x-input-label for="update_password_current_password" :value="__('รหัสผ่านปัจจุบัน')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-300 focus:ring-opacity-50"
                autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')"
                class="mt-2 text-red-500" />
        </div>

        <!-- 🔒 รหัสผ่านใหม่ -->
        <div>
            <x-input-label for="update_password_password" :value="__('รหัสผ่านใหม่')" />
            <x-text-input id="update_password_password" name="password" type="password"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-300 focus:ring-opacity-50"
                autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')"
                class="mt-2 text-red-500" />
        </div>

        <!-- ✅ ยืนยันรหัสผ่านใหม่ -->
        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('ยืนยันรหัสผ่าน')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-300 focus:ring-opacity-50"
                autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')"
                class="mt-2 text-red-500" />
        </div>

        <!-- 💾 ปุ่มบันทึก -->
        <div class="flex items-center gap-4 pt-4">
            <x-primary-button
                class="rounded-lg px-6 py-2 text-white bg-indigo-600 hover:bg-indigo-700 shadow transition ease-in-out">
                {{ __('บันทึก') }}
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-semibold text-green-600">
                    {{ __('บันทึกแล้ว') }}
                </p>
            @endif
        </div>
    </form>
</section>
