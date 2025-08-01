<section class="p-8 bg-white rounded-xl shadow-md">
    <header class="mb-6 border-b pb-4">
        <h2 class="text-3xl font-bold text-indigo-700">
            {{ __('ข้อมูลโปรไฟล์') }}
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            {{ __('อัปเดตข้อมูลบัญชีและที่อยู่อีเมลของโปรไฟล์ของคุณ') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')
        


        <!-- 👤 ฟิลด์ชื่อ -->
        <div>
            <x-input-label for="name" :value="__('ชื่อ')" />
            <x-text-input id="name" name="name" type="text"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-300 focus:ring-opacity-50"
                :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2 text-red-500" :messages="$errors->get('name')" />
        </div>

        <!-- ✉️ ฟิลด์อีเมล -->
        <div>
            <x-input-label for="email" :value="__('อีเมล')" />
            <x-text-input id="email" name="email" type="email"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-300 focus:ring-opacity-50"
                :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2 text-red-500" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div class="mt-3 text-sm text-gray-600">
                    {{ __('ที่อยู่อีเมลของคุณยังไม่ได้รับการยืนยัน') }}
                    <button form="send-verification"
                        class="ml-2 underline text-indigo-600 hover:text-indigo-800 font-medium transition ease-in-out">
                        {{ __('คลิกที่นี่เพื่อส่งอีเมลยืนยันใหม่') }}
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-green-600 font-semibold">
                            {{ __('ลิงก์ยืนยันใหม่ถูกส่งไปยังที่อยู่อีเมลของคุณแล้ว') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- 💾 ปุ่มบันทึก -->
        <div class="flex items-center gap-4 pt-4">
            <x-primary-button
                class="rounded-lg px-6 py-2 text-white bg-indigo-600 hover:bg-indigo-700 shadow transition ease-in-out">
                {{ __('บันทึก') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-semibold text-green-600">
                    {{ __('บันทึกแล้ว') }}
                </p>
            @endif
        </div>
    </form>
</section>
