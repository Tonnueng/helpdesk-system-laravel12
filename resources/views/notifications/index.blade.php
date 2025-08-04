<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-bell mr-2 text-blue-500"></i>
                {{ __('การแจ้งเตือน') }}
            </h2>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600">
                    <i class="fas fa-info-circle mr-1"></i>
                    {{ $notifications->total() }} รายการ
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow-lg mb-6">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="bg-white bg-opacity-20 rounded-full p-3">
                                <i class="fas fa-bell text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-white text-lg font-semibold">ศูนย์การแจ้งเตือน</h3>
                                <p class="text-blue-100 text-sm">จัดการการแจ้งเตือนทั้งหมดของคุณ</p>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <button id="markAllRead" 
                                    class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white font-medium py-2 px-4 rounded-lg transition-all duration-200 flex items-center space-x-2">
                                <i class="fas fa-check-double"></i>
                                <span>อ่านทั้งหมด</span>
                            </button>
                            <button id="deleteAll" 
                                    class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg transition-all duration-200 flex items-center space-x-2">
                                <i class="fas fa-trash"></i>
                                <span>ลบทั้งหมด</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div id="notificationsList">
                    @forelse($notifications as $notification)
                        <div class="notification-item border-b border-gray-100 hover:bg-gray-50 transition-all duration-200 {{ $notification->read_at ? 'opacity-75' : 'bg-blue-50 border-l-4 border-l-blue-500' }}" 
                             data-id="{{ $notification->id }}">
                            <div class="p-6">
                                <div class="flex items-start space-x-4">
                                    <!-- Notification Icon -->
                                    <div class="flex-shrink-0">
                                        @if(!$notification->read_at)
                                            <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>
                                        @else
                                            <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                                        @endif
                                    </div>

                                    <!-- Notification Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h4 class="text-sm font-semibold text-gray-900 mb-2">
                                                    {{ $notification->data['message'] ?? 'การแจ้งเตือน' }}
                                                </h4>
                                                
                                                <!-- Ticket Details -->
                                                @if(isset($notification->data['title']))
                                                    <div class="bg-gray-50 rounded-lg p-3 mb-3">
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                            <div>
                                                                <p class="text-xs text-gray-500 mb-1">หัวข้อปัญหา</p>
                                                                <p class="text-sm font-medium text-gray-900">{{ $notification->data['title'] }}</p>
                                                            </div>
                                                            
                                                            @if(isset($notification->data['category']))
                                                                <div>
                                                                    <p class="text-xs text-gray-500 mb-1">ประเภท</p>
                                                                    <p class="text-sm text-gray-700">{{ $notification->data['category'] }}</p>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        @if(isset($notification->data['priority']))
                                                            <div class="mt-3">
                                                                <p class="text-xs text-gray-500 mb-1">ระดับความสำคัญ</p>
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                                    @if($notification->data['priority'] === 'Critical') bg-red-100 text-red-800
                                                                    @elseif($notification->data['priority'] === 'High') bg-orange-100 text-orange-800
                                                                    @elseif($notification->data['priority'] === 'Medium') bg-yellow-100 text-yellow-800
                                                                    @else bg-green-100 text-green-800
                                                                    @endif">
                                                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                                                    {{ $notification->data['priority'] }}
                                                                </span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif

                                                @if(isset($notification->data['comment']))
                                                    <div class="bg-blue-50 border-l-4 border-blue-400 p-3 mb-3">
                                                        <p class="text-xs text-gray-500 mb-1">ความคิดเห็น</p>
                                                        <p class="text-sm text-gray-700">{{ $notification->data['comment'] }}</p>
                                                    </div>
                                                @endif

                                                <!-- Time and Actions -->
                                                <div class="flex items-center justify-between mt-4">
                                                    <div class="flex items-center space-x-4">
                                                        <span class="text-xs text-gray-500 flex items-center">
                                                            <i class="fas fa-clock mr-1"></i>
                                                            {{ $notification->created_at->diffForHumans() }}
                                                        </span>
                                                        
                                                        @if(isset($notification->data['updated_by']))
                                                            <span class="text-xs text-gray-500 flex items-center">
                                                                <i class="fas fa-user mr-1"></i>
                                                                โดย {{ $notification->data['updated_by'] }}
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <div class="flex items-center space-x-2">
                                                        @if(isset($notification->data['ticket_id']))
                                                            <a href="{{ route('tickets.show', $notification->data['ticket_id']) }}" 
                                                               class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 transition-colors duration-200">
                                                                <i class="fas fa-eye mr-1"></i>
                                                                ดูรายละเอียด
                                                            </a>
                                                        @endif
                                                        
                                                        @if(!$notification->read_at)
                                                            <button class="markAsRead inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 transition-colors duration-200" 
                                                                    data-id="{{ $notification->id }}">
                                                                <i class="fas fa-check mr-1"></i>
                                                                อ่านแล้ว
                                                            </button>
                                                        @endif
                                                        
                                                        <button class="deleteNotification inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 transition-colors duration-200" 
                                                                data-id="{{ $notification->id }}">
                                                            <i class="fas fa-trash mr-1"></i>
                                                            ลบ
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-16">
                            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-bell text-gray-400 text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">ไม่มีการแจ้งเตือน</h3>
                            <p class="text-gray-500">คุณจะเห็นการแจ้งเตือนใหม่ที่นี่เมื่อมีกิจกรรมเกิดขึ้น</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($notifications->hasPages())
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Mark as read
        document.querySelectorAll('.markAsRead').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const button = this;
                
                // Add loading state
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>กำลังดำเนินการ...';
                button.disabled = true;
                
                fetch(`/notifications/${id}/read`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const notificationItem = this.closest('.notification-item');
                        notificationItem.classList.add('opacity-75');
                        notificationItem.classList.remove('bg-blue-50', 'border-l-blue-500');
                        notificationItem.classList.add('border-l-gray-300');
                        this.remove();
                        updateNotificationCount(data.unread_count);
                        
                        // Show success message
                        showToast('ทำเครื่องหมายเป็นอ่านแล้ว', 'success');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    button.innerHTML = '<i class="fas fa-check mr-1"></i>อ่านแล้ว';
                    button.disabled = false;
                    showToast('เกิดข้อผิดพลาด', 'error');
                });
            });
        });

        // Delete notification
        document.querySelectorAll('.deleteNotification').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('คุณแน่ใจหรือไม่ที่จะลบการแจ้งเตือนนี้?')) {
                    const id = this.dataset.id;
                    const button = this;
                    
                    // Add loading state
                    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>กำลังลบ...';
                    button.disabled = true;
                    
                    fetch(`/notifications/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.closest('.notification-item').remove();
                            updateNotificationCount(data.unread_count);
                            showToast('ลบการแจ้งเตือนแล้ว', 'success');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        button.innerHTML = '<i class="fas fa-trash mr-1"></i>ลบ';
                        button.disabled = false;
                        showToast('เกิดข้อผิดพลาด', 'error');
                    });
                }
            });
        });

        // Mark all as read
        document.getElementById('markAllRead').addEventListener('click', function() {
            const button = this;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>กำลังดำเนินการ...';
            button.disabled = true;
            
            fetch('/notifications/mark-all-read', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelectorAll('.notification-item').forEach(item => {
                        item.classList.add('opacity-75');
                        item.classList.remove('bg-blue-50', 'border-l-blue-500');
                        item.classList.add('border-l-gray-300');
                    });
                    document.querySelectorAll('.markAsRead').forEach(button => button.remove());
                    updateNotificationCount(0);
                    
                    button.innerHTML = '<i class="fas fa-check-double mr-1"></i>อ่านทั้งหมด';
                    button.disabled = false;
                    showToast('ทำเครื่องหมายทั้งหมดเป็นอ่านแล้ว', 'success');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                button.innerHTML = '<i class="fas fa-check-double mr-1"></i>อ่านทั้งหมด';
                button.disabled = false;
                showToast('เกิดข้อผิดพลาด', 'error');
            });
        });

        // Delete all notifications
        document.getElementById('deleteAll').addEventListener('click', function() {
            if (confirm('คุณแน่ใจหรือไม่ที่จะลบการแจ้งเตือนทั้งหมด?')) {
                const button = this;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>กำลังลบ...';
                button.disabled = true;
                
                fetch('/notifications', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('notificationsList').innerHTML = `
                            <div class="text-center py-16">
                                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-bell text-gray-400 text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">ไม่มีการแจ้งเตือน</h3>
                                <p class="text-gray-500">คุณจะเห็นการแจ้งเตือนใหม่ที่นี่เมื่อมีกิจกรรมเกิดขึ้น</p>
                            </div>
                        `;
                        updateNotificationCount(0);
                        
                        button.innerHTML = '<i class="fas fa-trash mr-1"></i>ลบทั้งหมด';
                        button.disabled = false;
                        showToast('ลบการแจ้งเตือนทั้งหมดแล้ว', 'success');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    button.innerHTML = '<i class="fas fa-trash mr-1"></i>ลบทั้งหมด';
                    button.disabled = false;
                    showToast('เกิดข้อผิดพลาด', 'error');
                });
            }
        });

        function updateNotificationCount(count) {
            const notificationBadge = document.querySelector('.notification-badge');
            if (notificationBadge) {
                if (count > 0) {
                    notificationBadge.textContent = count;
                    notificationBadge.classList.remove('hidden');
                } else {
                    notificationBadge.classList.add('hidden');
                }
            }
        }

        function showToast(message, type = 'info') {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
                type === 'success' ? 'bg-green-500 text-white' :
                type === 'error' ? 'bg-red-500 text-white' :
                'bg-blue-500 text-white'
            }`;
            toast.innerHTML = `
                <div class="flex items-center space-x-2">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);
            
            // Remove after 3 seconds
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }
    </script>
    @endpush
</x-app-layout> 