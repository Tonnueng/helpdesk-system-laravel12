    <!-- Warning Notice -->
    <div class="p-4 bg-rose-50 border border-rose-200 rounded-lg">
        <div class="flex items-start">
            <i class="fas fa-exclamation-triangle text-rose-500 mt-1 mr-3"></i>
            <div>
                <p class="text-sm text-rose-800 font-medium mb-1">คำเตือน: การดำเนินการนี้ไม่สามารถย้อนกลับได้</p>
                <p class="text-sm text-rose-700">
                    เมื่อคุณลบบัญชี ข้อมูลและทรัพยากรทั้งหมดจะถูกลบอย่างถาวร กรุณาดาวน์โหลดข้อมูลที่ต้องการเก็บไว้ก่อนดำเนินการ
                </p>
            </div>
        </div>
    </div>

    <!-- 🔴 ปุ่มเปิด Modal -->
    <div class="pt-4">
        <button type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-rose-500 to-red-600 text-white font-medium rounded-lg hover:from-rose-600 hover:to-red-700 focus:ring-4 focus:ring-rose-200 transition duration-200 shadow-lg hover:shadow-xl">
            <i class="fas fa-trash-alt mr-2"></i>
            ลบบัญชีผู้ใช้
        </button>
    </div>

    <!-- 🧾 Modal ยืนยันการลบ -->
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 space-y-6">
            @csrf
            @method('delete')

            <div class="text-center">
                <div class="w-16 h-16 bg-rose-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-rose-600 text-2xl"></i>
                </div>
                <h2 class="text-xl font-semibold text-slate-800 mb-2">
                    คุณแน่ใจหรือไม่ว่าต้องการลบบัญชีนี้?
                </h2>
                <p class="text-sm text-slate-600">
                    เมื่อบัญชีถูกลบ ข้อมูลทั้งหมดจะหายไปอย่างถาวร กรุณากรอกรหัสผ่านเพื่อยืนยันการลบ
                </p>
            </div>

            <div class="space-y-2">
                <label for="password" class="block text-sm font-medium text-slate-700">
                    <i class="fas fa-lock mr-2 text-rose-500"></i>รหัสผ่านเพื่อยืนยัน
                </label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="กรอกรหัสผ่านของคุณ"
                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition duration-200"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="text-red-500 text-sm" />
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button"
                    x-on:click="$dispatch('close')"
                    class="px-6 py-3 bg-slate-200 hover:bg-slate-300 text-slate-800 font-medium rounded-lg transition duration-200">
                    ยกเลิก
                </button>

                <button type="submit"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-rose-500 to-red-600 text-white font-medium rounded-lg hover:from-rose-600 hover:to-red-700 focus:ring-4 focus:ring-rose-200 transition duration-200 shadow-lg hover:shadow-xl">
                    <i class="fas fa-trash-alt mr-2"></i>
                    ลบบัญชี
                </button>
            </div>
        </form>
    </x-modal>
