<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-circle text-indigo-600"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">โปรไฟล์</h2>
                    <p class="text-sm text-gray-600">จัดการข้อมูลส่วนตัวของคุณ</p>
                </div>
            </div>
            <div class="text-sm text-gray-500">
                <i class="fas fa-clock mr-1"></i>
                {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Profile Header Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-sky-200 to-blue-300 px-6 py-6">
                    <div class="flex items-center space-x-4">
                        <!-- Avatar -->
                        <div class="relative">
                            <div class="w-16 h-16 bg-white bg-opacity-30 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-circle text-slate-700 text-3xl"></i>
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-400 rounded-full border-2 border-white flex items-center justify-center">
                                <i class="fas fa-check text-white text-xs"></i>
                            </div>
                        </div>
                        
                        <!-- User Info -->
                        <div class="flex-1">
                            <h1 class="text-2xl font-bold text-slate-800 mb-1">{{ Auth::user()->name }}</h1>
                            <p class="text-slate-600 mb-2">{{ Auth::user()->email }}</p>
                            
                            <!-- Role Badge -->
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-3 py-1 bg-white bg-opacity-50 rounded-full text-sm font-medium text-slate-700">
                                    <i class="fas fa-shield-alt mr-1"></i>
                                    {{ ucfirst(Auth::user()->role ?? 'User') }}
                                </span>
                                
                                @if(Auth::user()->department)
                                <span class="inline-flex items-center px-3 py-1 bg-white bg-opacity-50 rounded-full text-sm font-medium text-slate-700">
                                    <i class="fas fa-building mr-1"></i>
                                    {{ ucfirst(Auth::user()->department) }}
                                </span>
                                @endif
                                
                                @if(Auth::user()->position)
                                <span class="inline-flex items-center px-3 py-1 bg-white bg-opacity-50 rounded-full text-sm font-medium text-slate-700">
                                    <i class="fas fa-briefcase mr-1"></i>
                                    {{ Auth::user()->position }}
                                </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Stats -->
                        <div class="text-right">
                            <div class="text-slate-600 text-sm mb-1">สมาชิกตั้งแต่</div>
                            <div class="text-slate-800 text-lg font-semibold">{{ Auth::user()->created_at->format('M Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Information Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-sky-50 to-blue-50 px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-sky-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-id-card text-sky-600"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-slate-800">ข้อมูลโปรไฟล์</h2>
                            <p class="text-sm text-slate-600">จัดการข้อมูลส่วนตัวของคุณ</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Password Change Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-50 to-green-50 px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-key text-emerald-600"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-slate-800">เปลี่ยนรหัสผ่าน</h2>
                            <p class="text-sm text-slate-600">อัปเดตรหัสผ่านเพื่อความปลอดภัย</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Delete Account Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-rose-50 to-red-50 px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-rose-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-rose-600"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-slate-800">ลบบัญชีผู้ใช้</h2>
                            <p class="text-sm text-slate-600">การดำเนินการนี้ไม่สามารถย้อนกลับได้</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
