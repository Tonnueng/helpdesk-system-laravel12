<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-3xl font-bold text-indigo-700 leading-tight">
                <i class="fas fa-chart-line mr-3"></i>
                {{ __('แดชบอร์ด') }}
            </h2>
            <div class="text-sm text-gray-600">
                <i class="fas fa-calendar mr-1"></i>
                {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- แจ้งเตือนสำเร็จ --}}
            @if (session('success'))
                <div class="mb-6 flex items-start bg-green-50 border border-green-300 rounded-xl p-4" role="alert">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="font-semibold text-green-700">{{ __('สำเร็จ!') }}</p>
                        <p class="text-green-600">{{ session('success') }}</p>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            {{-- แจ้งเตือนข้อผิดพลาด --}}
            @if (session('error'))
                <div class="mb-6 flex items-start bg-red-50 border border-red-300 rounded-xl p-4" role="alert">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="font-semibold text-red-700">{{ __('ผิดพลาด!') }}</p>
                        <p class="text-red-600">{{ session('error') }}</p>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            {{-- สถิติหลัก --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                {{-- ปัญหาทั้งหมด --}}
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">{{ __('ปัญหาทั้งหมด') }}</p>
                            <p class="text-3xl font-bold mt-1 text-white">{{ number_format($totalTickets) }}</p>
                        </div>
                        <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                            <i class="fas fa-ticket-alt text-2xl text-white"></i>
                        </div>
                    </div>
                </div>

                {{-- ปัญหาที่ยังไม่แก้ไข --}}
                @php
                    $openTickets = 0;
                    if (isset($ticketsByStatus['New'])) $openTickets += $ticketsByStatus['New'];
                    if (isset($ticketsByStatus['In Progress'])) $openTickets += $ticketsByStatus['In Progress'];
                @endphp
                <div class="bg-gradient-to-r from-yellow-500 to-orange-500 text-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-yellow-100 text-sm font-medium">{{ __('ปัญหาที่ยังไม่แก้ไข') }}</p>
                            <p class="text-3xl font-bold mt-1 text-white">{{ number_format($openTickets) }}</p>
                        </div>
                        <div class="bg-yellow-400 bg-opacity-30 rounded-full p-3">
                            <i class="fas fa-clock text-2xl text-white"></i>
                        </div>
                    </div>
                </div>

                {{-- ปัญหาที่แก้ไขแล้ว --}}
                @php
                    $closedTickets = 0;
                    if (isset($ticketsByStatus['Resolved'])) $closedTickets += $ticketsByStatus['Resolved'];
                    if (isset($ticketsByStatus['Closed'])) $closedTickets += $ticketsByStatus['Closed'];
                @endphp
                <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">{{ __('ปัญหาที่แก้ไขแล้ว') }}</p>
                            <p class="text-3xl font-bold mt-1 text-white">{{ number_format($closedTickets) }}</p>
                        </div>
                        <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                            <i class="fas fa-check-circle text-2xl text-white"></i>
                        </div>
                    </div>
                </div>

                {{-- ปัญหาที่มอบหมายให้ฉัน --}}
                @if (Auth::user()->canManageTickets())
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium">{{ __('มอบหมายให้ฉัน') }}</p>
                                <p class="text-3xl font-bold mt-1 text-white">{{ number_format($assignedTickets) }}</p>
                            </div>
                            <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                                <i class="fas fa-user-tie text-2xl text-white"></i>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- สำหรับผู้ใช้ทั่วไป --}}
                    <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 text-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-indigo-100 text-sm font-medium">{{ __('ปัญหาของฉัน') }}</p>
                                <p class="text-3xl font-bold mt-1 text-white">{{ number_format($totalTickets) }}</p>
                            </div>
                            <div class="bg-indigo-400 bg-opacity-30 rounded-full p-3">
                                <i class="fas fa-user text-2xl text-white"></i>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- กราฟและแผนภูมิ --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                {{-- กราฟสถิติรายเดือน --}}
                <div class="bg-white p-6 rounded-xl shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-chart-bar mr-2 text-blue-500"></i>
                            {{ __('สถิติรายเดือน') }}
                        </h3>
                    </div>
                    <div style="height: 300px;">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>

                {{-- กราฟสถิติรายวัน (7 วันล่าสุด) --}}
                <div class="bg-white p-6 rounded-xl shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-chart-line mr-2 text-green-500"></i>
                            {{ __('สถิติรายวัน (7 วันล่าสุด)') }}
                        </h3>
                    </div>
                    <div style="height: 300px;">
                        <canvas id="recentChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- สถิติรายละเอียด --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                {{-- สถิติตามประเภท --}}
                <div class="bg-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-tags mr-2 text-blue-500"></i>
                        {{ __('ตามประเภท') }}
                    </h3>
                    <div class="space-y-3">
                        @forelse($ticketsByCategory as $category => $count)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">{{ $category }}</span>
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                    {{ $count }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-4 text-gray-500">
                                <i class="fas fa-info-circle text-lg mb-2"></i>
                                <p>ไม่มีข้อมูลประเภทปัญหา</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- สถิติตามระดับความสำคัญ --}}
                <div class="bg-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-exclamation-triangle mr-2 text-orange-500"></i>
                        {{ __('ตามระดับความสำคัญ') }}
                    </h3>
                    <div class="space-y-3">
                        @forelse($ticketsByPriority as $priority => $count)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">{{ $priority }}</span>
                                <span class="text-xs font-medium px-2.5 py-0.5 rounded-full
                                    @if($priority === 'Critical') bg-red-100 text-red-800
                                    @elseif($priority === 'High') bg-orange-100 text-orange-800
                                    @elseif($priority === 'Medium') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    {{ $count }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-4 text-gray-500">
                                <i class="fas fa-info-circle text-lg mb-2"></i>
                                <p>ไม่มีข้อมูลระดับความสำคัญ</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- สถิติประสิทธิภาพ --}}
                <div class="bg-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-tachometer-alt mr-2 text-purple-500"></i>
                        {{ __('ประสิทธิภาพ') }}
                    </h3>
                    <div class="space-y-4">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <p class="text-sm text-gray-600">{{ __('เวลาการแก้ไขเฉลี่ย') }}</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $avgResolutionTime }} ชม.</p>
                        </div>
                        
                        @if (Auth::user()->canManageTickets())
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <p class="text-sm text-gray-600">{{ __('ปัญหาที่แก้ไขแล้ว') }}</p>
                                <p class="text-2xl font-bold text-green-600">{{ $myResolvedTickets }}</p>
                            </div>
                            
                            <div class="text-center p-4 bg-purple-50 rounded-lg">
                                <p class="text-sm text-gray-600">{{ __('เวลาการแก้ไขเฉลี่ยของฉัน') }}</p>
                                <p class="text-2xl font-bold text-purple-600">{{ $myAvgResolutionTime }} ชม.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Top Performers --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                {{-- Top Categories --}}
                <div class="bg-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-trophy mr-2 text-yellow-500"></i>
                        {{ __('ประเภทที่แจ้งบ่อยที่สุด') }}
                    </h3>
                    <div class="space-y-3">
                        @forelse($topCategories as $category)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold mr-3">
                                        {{ $loop->iteration }}
                                    </span>
                                    <span class="text-sm font-medium text-gray-700">{{ $category->name }}</span>
                                </div>
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                    {{ $category->tickets_count }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-4 text-gray-500">
                                <i class="fas fa-info-circle text-lg mb-2"></i>
                                <p>ไม่มีข้อมูลประเภทปัญหา</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Top Agents --}}
                <div class="bg-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-medal mr-2 text-green-500"></i>
                        {{ __('เจ้าหน้าที่ที่แก้ไขปัญหาได้มากที่สุด') }}
                    </h3>
                    <div class="space-y-3">
                        @forelse($topAgents as $agent)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <span class="w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-sm font-bold mr-3">
                                        {{ $loop->iteration }}
                                    </span>
                                    <div>
                                        <span class="text-sm font-medium text-gray-700">{{ $agent->name }}</span>
                                        <p class="text-xs text-gray-500">{{ ucfirst($agent->role) }}</p>
                                    </div>
                                </div>
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                    {{ $agent->resolved_count }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-4 text-gray-500">
                                <i class="fas fa-info-circle text-lg mb-2"></i>
                                <p>ไม่มีข้อมูลเจ้าหน้าที่</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-bolt mr-2 text-indigo-500"></i>
                    {{ __('การดำเนินการด่วน') }}
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('tickets.create') }}" class="flex items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors duration-200">
                        <i class="fas fa-plus-circle text-blue-500 text-xl mr-3"></i>
                        <div>
                            <p class="font-medium text-blue-700">{{ __('แจ้งปัญหาใหม่') }}</p>
                            <p class="text-sm text-blue-600">{{ __('สร้างปัญหาใหม่') }}</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('tickets.index') }}" class="flex items-center p-4 bg-green-50 hover:bg-green-100 rounded-lg transition-colors duration-200">
                        <i class="fas fa-list text-green-500 text-xl mr-3"></i>
                        <div>
                            <p class="font-medium text-green-700">{{ __('ดูรายการปัญหา') }}</p>
                            <p class="text-sm text-green-600">{{ __('จัดการปัญหาทั้งหมด') }}</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('notifications.index') }}" class="flex items-center p-4 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition-colors duration-200">
                        <i class="fas fa-bell text-yellow-500 text-xl mr-3"></i>
                        <div>
                            <p class="font-medium text-yellow-700">{{ __('การแจ้งเตือน') }}</p>
                            <p class="text-sm text-yellow-600">{{ __('ดูการแจ้งเตือน') }}</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('profile.edit') }}" class="flex items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors duration-200">
                        <i class="fas fa-user-cog text-purple-500 text-xl mr-3"></i>
                        <div>
                            <p class="font-medium text-purple-700">{{ __('ตั้งค่าโปรไฟล์') }}</p>
                            <p class="text-sm text-purple-600">{{ __('แก้ไขข้อมูลส่วนตัว') }}</p>
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </div>

    {{-- Chart.js Script --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Monthly Chart
            const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
            new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: @json(array_column($monthlyStats, 'month')),
                    datasets: [{
                        label: 'จำนวนปัญหา',
                        data: @json(array_column($monthlyStats, 'count')),
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // Recent Chart
            const recentCtx = document.getElementById('recentChart').getContext('2d');
            new Chart(recentCtx, {
                type: 'line',
                data: {
                    labels: @json(array_column($recentStats, 'date')),
                    datasets: [{
                        label: 'จำนวนปัญหา',
                        data: @json(array_column($recentStats, 'count')),
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
