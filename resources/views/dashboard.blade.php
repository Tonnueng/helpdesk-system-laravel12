<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-bold text-indigo-700 leading-tight">
            {{ __('แดชบอร์ด') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md sm:rounded-xl">
                <div class="p-8 text-gray-900">

                    {{-- แจ้งเตือนสำเร็จ --}}
                    @if (session('success'))
                        <div class="mb-6 flex items-start bg-green-50 border border-green-300 rounded-xl p-4"
                            role="alert">
                            <svg class="h-6 w-6 flex-shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11H9v4h2V7zm0 6H9v2h2v-2z" />
                            </svg>
                            <div class="ml-3 flex-1">
                                <p class="font-semibold text-green-700">{{ __('สำเร็จ!') }}</p>
                                <p class="text-green-600">{{ session('success') }}</p>
                            </div>
                            <button type="button" onclick="this.parentElement.remove()"
                                class="ml-4 text-green-500 hover:text-green-700">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M14.348 5.652a.5.5 0 10-.707-.707L10 8.586 6.36 4.945a.5.5 0 10-.707.707L9.293 10l-3.64 3.64a.5.5 0 10.707.707L10 11.414l3.64 3.64a.5.5 0 00.707-.707L10.707 10l3.64-3.64z" />
                                </svg>
                            </button>
                        </div>
                    @endif

                    {{-- แจ้งเตือนข้อผิดพลาด --}}
                    @if (session('error'))
                        <div class="mb-6 flex items-start bg-red-50 border border-red-300 rounded-xl p-4"
                            role="alert">
                            <svg class="h-6 w-6 flex-shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11H9v4h2V7zm0 6H9v2h2v-2z" />
                            </svg>
                            <div class="ml-3 flex-1">
                                <p class="font-semibold text-red-700">{{ __('ผิดพลาด!') }}</p>
                                <p class="text-red-600">{{ session('error') }}</p>
                            </div>
                            <button type="button" onclick="this.parentElement.remove()"
                                class="ml-4 text-red-500 hover:text-red-700">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M14.348 5.652a.5.5 0 10-.707-.707L10 8.586 6.36 4.945a.5.5 0 10-.707.707L9.293 10l-3.64 3.64a.5.5 0 10.707.707L10 11.414l3.64 3.64a.5.5 0 00.707-.707L10.707 10l3.64-3.64z" />
                                </svg>
                            </button>
                        </div>
                    @endif

                    {{-- กล่องสถิติ --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                        <div
                            class="bg-blue-600 text-white p-6 rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-1 text-center">
                            <h5 class="text-lg font-semibold">{{ __('ปัญหาทั้งหมด') }}</h5>
                            <p class="text-5xl font-bold mt-2">{{ $totalTickets }}</p>
                        </div>

                        <div
                            class="bg-yellow-500 text-white p-6 rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-1 text-center">
                            <h5 class="text-lg font-semibold">{{ __('ปัญหาที่ยังไม่แก้ไข') }}</h5>
                            <p class="text-5xl font-bold mt-2">{{ $openTickets }}</p>
                        </div>

                        <div
                            class="bg-green-600 text-white p-6 rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-1 text-center">
                            <h5 class="text-lg font-semibold">{{ __('ปัญหาที่แก้ไขแล้ว') }}</h5>
                            <p class="text-5xl font-bold mt-2">{{ $closedTickets }}</p>
                        </div>

                        @if (Auth::user()->canManageTickets())
                            <div
                                class="bg-indigo-600 text-white p-6 rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-1 text-center">
                                <h5 class="text-lg font-semibold">{{ __('ปัญหาที่มอบหมายให้ฉัน') }}</h5>
                                <p class="text-5xl font-bold mt-2">{{ $assignedTickets }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- แผนภูมิ --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <div class="bg-white p-6 rounded-xl shadow-md">
                            <h3 class="text-indigo-700 font-bold mb-4 text-gray-800">
                                {{ __('สถานะปัญหา (ยังไม่แก้ไช vs แก้ไขแล้ว)') }}
                            </h3>
                            <canvas id="ticketStatusChart"></canvas>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-md">
                            <h3 class="text-indigo-700 font-bold mb-4 text-gray-800">
                                {{ __('ภาพรวมจำนวนปัญหา') }}
                            </h3>
                            <canvas id="ticketOverviewChart"></canvas>
                        </div>
                    </div>

                    {{-- ข้อความต้อนรับ --}}
                    <div class="flex flex-col items-center text-center py-8">
                        <h4 class="text-2xl text-indigo-700 font-bold">
                            {{ __('ยินดีต้อนรับสู่ระบบแจ้งปัญหา!') }}
                        </h4>
                        <p class="text-gray-600 mt-2">
                            {{ __('นี่คือภาพรวมของปัญหาต่างๆ ในระบบของคุณ') }}
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Chart.js Script --}}
    <script type="module">
        document.addEventListener('DOMContentLoaded', () => {
            const openTickets = {{ $openTickets }};
            const closedTickets = {{ $closedTickets }};
            const totalTickets = {{ $totalTickets }};
            const assignedTickets = {{ $assignedTickets }};

            // Doughnut Chart
            new Chart(document.getElementById('ticketStatusChart'), {
                type: 'doughnut',
                data: {
                    labels: [
                        '{{ __('ปัญหาที่ยังไม่แก้ไข') }}',
                        '{{ __('ปัญหาที่แก้ไขแล้ว') }}'
                    ],
                    datasets: [{
                        data: [openTickets, closedTickets],
                        backgroundColor: ['rgb(255,205,86)', 'rgb(75,192,192)'],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    }
                }
            });

            // Bar Chart
            new Chart(document.getElementById('ticketOverviewChart'), {
                type: 'bar',
                data: {
                    labels: [
                        '{{ __('ทั้งหมด') }}',
                        '{{ __('ยังไม่แก้ไข') }}',
                        '{{ __('แก้ไขแล้ว') }}',
                        '{{ __('มอบหมายให้ฉัน') }}'
                    ],
                    datasets: [{
                        label: '{{ __('จำนวนปัญหา') }}',
                        data: [totalTickets, openTickets, closedTickets, assignedTickets],
                        backgroundColor: [
                            'rgba(54,162,235,0.6)',
                            'rgba(255,205,86,0.6)',
                            'rgba(75,192,192,0.6)',
                            'rgba(153,102,255,0.6)'
                        ],
                        borderColor: [
                            'rgba(54,162,235,1)',
                            'rgba(255,205,86,1)',
                            'rgba(75,192,192,1)',
                            'rgba(153,102,255,1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
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
```
