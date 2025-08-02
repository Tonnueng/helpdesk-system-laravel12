<section class="p-8 bg-white rounded-2xl shadow-xl transition-all duration-300">
    <header class="mb-6 pb-4 border-b border-gray-200">
        <h2 class="text-3xl font-extrabold text-indigo-700 leading-tight">
            {{ __('เปลี่ยนรหัสผ่าน') }}
        </h2>
        <p class="mt-2 text-md text-gray-500">
            {{ __('เพื่อความปลอดภัย กรุณาใช้รหัสผ่านที่สุ่มและคาดเดายาก') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <!-- รหัสผ่านปัจจุบัน -->
        <div>
            <x-input-label for="update_password_current_password" :value="__('รหัสผ่านปัจจุบัน')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password"
                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm transition duration-300 ease-in-out focus:border-indigo-500 focus:ring-indigo-500"
                autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-red-500" />
        </div>

        <!-- รหัสผ่านใหม่ -->
        <div>
            <x-input-label for="update_password_password" :value="__('รหัสผ่านใหม่')" />
            <x-text-input id="update_password_password" name="password" type="password"
                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm transition duration-300 ease-in-out focus:border-indigo-500 focus:ring-indigo-500"
                autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-red-500" />
        </div>

        <!-- ยืนยันรหัสผ่านใหม่ -->
        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('ยืนยันรหัสผ่าน')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm transition duration-300 ease-in-out focus:border-indigo-500 focus:ring-indigo-500"
                autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-red-500" />
        </div>

        <!-- ปุ่มบันทึกและสถานะ -->
        <div class="flex items-center gap-4 pt-4">
            <x-primary-button
                class="rounded-full px-6 py-2 font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md hover:shadow-lg transition duration-300 ease-in-out">
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
