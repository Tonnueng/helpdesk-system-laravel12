    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <!-- Grid Layout for Form Fields -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- 👤 ฟิลด์ชื่อ -->
            <div class="space-y-2">
                <label for="name" class="block text-sm font-medium text-slate-700">
                    <i class="fas fa-user mr-2 text-sky-500"></i>ชื่อ
                </label>
                <input id="name" name="name" type="text"
                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition duration-200"
                    value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
                <x-input-error class="text-red-500 text-sm" :messages="$errors->get('name')" />
            </div>

            <!-- 📞 ฟิลด์เบอร์โทรศัพท์ -->
            <div class="space-y-2">
                <label for="phone" class="block text-sm font-medium text-slate-700">
                    <i class="fas fa-phone mr-2 text-sky-500"></i>เบอร์โทรศัพท์
                </label>
                <input id="phone" name="phone" type="text"
                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition duration-200"
                    value="{{ old('phone', $user->phone ?? '') }}" autocomplete="tel" />
                <x-input-error class="text-red-500 text-sm" :messages="$errors->get('phone')" />
            </div>

            <!-- 🏢 ฟิลด์แผนก -->
            <div class="space-y-2">
                <label for="department" class="block text-sm font-medium text-slate-700">
                    <i class="fas fa-building mr-2 text-sky-500"></i>แผนก
                </label>
                <select id="department" name="department"
                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition duration-200">
                    <option value="">-- เลือกแผนก --</option>
                    <option value="programer" {{ old('department', $user->department ?? '') == 'programer' ? 'selected' : '' }}>Programer</option>
                    <option value="product" {{ old('department', $user->department ?? '') == 'product' ? 'selected' : '' }}>Product</option>
                    <option value="marketing" {{ old('department', $user->department ?? '') == 'marketing' ? 'selected' : '' }}>Marketing</option>
                    <option value="admin" {{ old('department', $user->department ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="hr" {{ old('department', $user->department ?? '') == 'hr' ? 'selected' : '' }}>HR</option>
                    <option value="manager" {{ old('department', $user->department ?? '') == 'manager' ? 'selected' : '' }}>Manager</option>
                    <option value="editor" {{ old('department', $user->department ?? '') == 'editor' ? 'selected' : '' }}>Editor</option>
                    <option value="finance" {{ old('department', $user->department ?? '') == 'finance' ? 'selected' : '' }}>Finance</option>
                </select>
                <x-input-error class="text-red-500 text-sm" :messages="$errors->get('department')" />
            </div>

            <!-- 🧑‍💼 ฟิลด์ตำแหน่ง -->
            <div class="space-y-2">
                <label for="position" class="block text-sm font-medium text-slate-700">
                    <i class="fas fa-briefcase mr-2 text-sky-500"></i>ตำแหน่ง
                </label>
                <select id="position" name="position"
                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition duration-200">
                    <option value="">-- เลือกตำแหน่ง --</option>
                    <option value="หัวหน้า" {{ old('position', $user->position ?? '') == 'หัวหน้า' ? 'selected' : '' }}>หัวหน้า</option>
                    <option value="พนักงานปกติ" {{ old('position', $user->position ?? '') == 'พนักงานปกติ' ? 'selected' : '' }}>พนักงานปกติ</option>
                </select>
                <x-input-error class="text-red-500 text-sm" :messages="$errors->get('position')" />
            </div>

        </div>

        <!-- ✉️ ฟิลด์อีเมล (Full Width) -->
        <div class="space-y-2">
            <label for="email" class="block text-sm font-medium text-slate-700">
                <i class="fas fa-envelope mr-2 text-sky-500"></i>อีเมล
            </label>
            <input id="email" name="email" type="email"
                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition duration-200"
                value="{{ old('email', $user->email) }}" required autocomplete="username" />
            <x-input-error class="text-red-500 text-sm" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div class="mt-3 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-amber-500 mt-1 mr-3"></i>
                        <div>
                            <p class="text-sm text-amber-800 font-medium">ที่อยู่อีเมลของคุณยังไม่ได้รับการยืนยัน</p>
                            <button form="send-verification"
                                class="mt-2 text-sm text-amber-600 hover:text-amber-800 font-medium underline transition duration-200">
                                คลิกที่นี่เพื่อส่งอีเมลยืนยันใหม่
                            </button>
                        </div>
                    </div>

                    @if (session('status') === 'verification-link-sent')
                        <div class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                <p class="text-sm text-green-800 font-medium">
                                    ลิงก์ยืนยันใหม่ถูกส่งไปยังที่อยู่อีเมลของคุณแล้ว
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        @if(auth()->user()->role === 'head')
        <!-- 🛡️ ฟิลด์ Role (สำหรับ Head เท่านั้น) -->
        <div class="space-y-2">
            <label for="role" class="block text-sm font-medium text-slate-700">
                <i class="fas fa-shield-alt mr-2 text-sky-500"></i>สิทธิ์ผู้ใช้ (Role)
            </label>
            <select id="role" name="role"
                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition duration-200">
                <option value="">-- เลือกสิทธิ์ --</option>
                <option value="owner" {{ old('role', $user->role ?? '') == 'owner' ? 'selected' : '' }}>Owner</option>
                <option value="head" {{ old('role', $user->role ?? '') == 'head' ? 'selected' : '' }}>Head</option>
                <option value="agent" {{ old('role', $user->role ?? '') == 'agent' ? 'selected' : '' }}>Agent</option>
                <option value="user" {{ old('role', $user->role ?? '') == 'user' ? 'selected' : '' }}>User</option>
            </select>
            <x-input-error class="text-red-500 text-sm" :messages="$errors->get('role')" />
        </div>
        @endif

        <!-- 💾 ปุ่มบันทึก -->
        <div class="flex items-center justify-between pt-6 border-t border-slate-200">
            <div class="flex items-center space-x-4">
                <button type="submit"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-sky-500 to-blue-600 text-white font-medium rounded-lg hover:from-sky-600 hover:to-blue-700 focus:ring-4 focus:ring-sky-200 transition duration-200 shadow-lg hover:shadow-xl">
                    <i class="fas fa-save mr-2"></i>
                    บันทึกการเปลี่ยนแปลง
                </button>

                @if (session('status') === 'profile-updated')
                    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                        class="flex items-center px-4 py-2 bg-green-50 border border-green-200 rounded-lg">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        <span class="text-sm font-medium text-green-800">บันทึกแล้ว</span>
                    </div>
                @endif
            </div>
        </div>
    </form>
