    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <!-- Security Notice -->
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-shield-alt text-emerald-500 mr-3"></i>
                <p class="text-sm text-emerald-800">
                    เพื่อความปลอดภัย กรุณาใช้รหัสผ่านที่แข็งแกร่งและไม่เคยใช้ที่อื่น
                </p>
            </div>
        </div>

        <!-- Grid Layout for Password Fields -->
        <div class="grid grid-cols-1 gap-6">
            
            <!-- รหัสผ่านปัจจุบัน -->
            <div class="space-y-2">
                <label for="update_password_current_password" class="block text-sm font-medium text-slate-700">
                    <i class="fas fa-lock mr-2 text-emerald-500"></i>รหัสผ่านปัจจุบัน
                </label>
                <input id="update_password_current_password" name="current_password" type="password"
                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-200"
                    autocomplete="current-password" />
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="text-red-500 text-sm" />
            </div>

            <!-- รหัสผ่านใหม่ -->
            <div class="space-y-2">
                <label for="update_password_password" class="block text-sm font-medium text-slate-700">
                    <i class="fas fa-key mr-2 text-emerald-500"></i>รหัสผ่านใหม่
                </label>
                <input id="update_password_password" name="password" type="password"
                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-200"
                    autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="text-red-500 text-sm" />
            </div>

            <!-- ยืนยันรหัสผ่านใหม่ -->
            <div class="space-y-2">
                <label for="update_password_password_confirmation" class="block text-sm font-medium text-slate-700">
                    <i class="fas fa-check-double mr-2 text-emerald-500"></i>ยืนยันรหัสผ่านใหม่
                </label>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-200"
                    autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="text-red-500 text-sm" />
            </div>

        </div>

        <!-- ปุ่มบันทึกและสถานะ -->
        <div class="flex items-center justify-between pt-6 border-t border-slate-200">
            <div class="flex items-center space-x-4">
                <button type="submit"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-green-600 text-white font-medium rounded-lg hover:from-emerald-600 hover:to-green-700 focus:ring-4 focus:ring-emerald-200 transition duration-200 shadow-lg hover:shadow-xl">
                    <i class="fas fa-save mr-2"></i>
                    อัปเดตรหัสผ่าน
                </button>

                @if (session('status') === 'password-updated')
                    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                        class="flex items-center px-4 py-2 bg-green-50 border border-green-200 rounded-lg">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        <span class="text-sm font-medium text-green-800">รหัสผ่านอัปเดตแล้ว</span>
                    </div>
                @endif
            </div>
        </div>
    </form>
