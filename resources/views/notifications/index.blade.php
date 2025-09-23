<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-bell mr-2 text-blue-500"></i>
                การแจ้งเตือน
            </h2>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600">
                    <i class="fas fa-info-circle mr-1"></i>
                    {{ $notifications->total() }} รายการ
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-4">
                <div class="px-4 py-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-bell text-gray-600"></i>
                            <h3 class="text-gray-800 font-medium">การแจ้งเตือน</h3>
                            <span class="text-sm text-gray-500">{{ $notifications->total() }} รายการ</span>
                        </div>
                        <div class="flex space-x-2">
                            <button id="markAllRead" 
                                    class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                                <i class="fas fa-check-double mr-1"></i>
                                อ่านทั้งหมด
                            </button>
                            <button id="deleteAll" 
                                    class="px-3 py-1.5 text-sm text-red-600 border border-red-300 rounded-md hover:bg-red-50 transition-colors">
                                <i class="fas fa-trash mr-1"></i>
                                ลบทั้งหมด
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div id="notificationsList">
                    @forelse($notifications as $notification)
                        <div class="notification-item border-b border-gray-100 transition-colors {{ $notification->read_at ? 'opacity-75' : 'bg-blue-50 border-l-4 border-l-blue-500' }}" 
                             data-id="{{ $notification->id }}"
                             @if(isset($notification->data['ticket_id']))
                                 onclick="markAsReadAndRedirect({{ $notification->id }}, {{ $notification->data['ticket_id'] }})"
                                 class="cursor-pointer hover:bg-blue-50"
                             @else
                                 class="hover:bg-gray-50"
                             @endif>
                            <div class="p-4">
                                <div class="flex items-start space-x-3">
                                    <!-- Notification Icon -->
                                    <div class="flex-shrink-0 mt-1">
                                        @if(!$notification->read_at)
                                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                        @else
                                            <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                                        @endif
                                    </div>

                                    <!-- Notification Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h4 class="text-sm font-medium text-gray-900 mb-1">
                                                    {{ $notification->data['message'] ?? 'การแจ้งเตือน' }}
                                                </h4>
                                                
                                                @if(isset($notification->data['title']))
                                                    <p class="text-sm text-gray-600 mb-2">{{ $notification->data['title'] }}</p>
                                                @endif

                                                @if(isset($notification->data['comment']))
                                                    <p class="text-sm text-gray-500 mb-2">{{ Str::limit($notification->data['comment'], 100) }}</p>
                                                @endif

                                                <!-- Time and Actions -->
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center space-x-3">
                                                        <span class="text-xs text-gray-500">
                                                            {{ $notification->created_at->diffForHumans() }}
                                                        </span>
                                                        
                                                        @if(isset($notification->data['updated_by']))
                                                            <span class="text-xs text-gray-500">
                                                                โดย {{ $notification->data['updated_by'] }}
                                                            </span>
                                                        @endif
                                                        
                                                        @if(isset($notification->data['ticket_id']))
                                                            <span class="text-xs text-indigo-600 font-medium bg-indigo-100 px-2 py-1 rounded-full">
                                                                <i class="fas fa-ticket-alt mr-1"></i>คลิกเพื่อดูปัญหา (จะอ่านแล้วอัตโนมัติ)
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <div class="flex items-center space-x-2">
                                                        @if(isset($notification->data['ticket_id']))
                                                            <a href="/tickets/{{ $notification->data['ticket_id'] }}" 
                                                               class="text-xs text-blue-600 hover:text-blue-800">
                                                                ดูรายละเอียด
                                                            </a>
                                                        @endif
                                                        
                                                        @if(!$notification->read_at)
                                                            <button class="markAsRead text-xs text-green-600 hover:text-green-800" 
                                                                    data-id="{{ $notification->id }}">
                                                                อ่านแล้ว
                                                            </button>
                                                        @endif
                                                        
                                                        <button class="deleteNotification text-xs text-red-600 hover:text-red-800" 
                                                                data-id="{{ $notification->id }}">
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
                        <div class="text-center py-12">
                            <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                <i class="fas fa-bell text-gray-400 text-xl"></i>
                            </div>
                            <h3 class="text-base font-medium text-gray-900 mb-1">ไม่มีการแจ้งเตือน</h3>
                            <p class="text-sm text-gray-500">คุณจะเห็นการแจ้งเตือนใหม่ที่นี่เมื่อมีกิจกรรมเกิดขึ้น</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($notifications->hasPages())
                    <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
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

        function markAsReadAndRedirect(notificationId, ticketId) {
            // Mark as read ก่อน redirect
            fetch(`/notifications/${notificationId}/read`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // อัปเดต UI ให้แสดงว่าอ่านแล้ว
                    const notificationElement = document.querySelector(`[data-id="${notificationId}"]`);
                    if (notificationElement) {
                        notificationElement.classList.remove('bg-blue-50', 'border-l-blue-500');
                        notificationElement.classList.add('opacity-75');
                        
                        // อัปเดตจุดสี
                        const dot = notificationElement.querySelector('.w-2.h-2');
                        if (dot) {
                            dot.classList.remove('bg-blue-500');
                            dot.classList.add('bg-gray-300');
                        }
                    }
                    
                    // อัปเดตจำนวนการแจ้งเตือนที่ยังไม่อ่าน
                    updateNotificationCount(data.unread_count);
                }
                
                // Redirect ไปที่หน้ารายละเอียดปัญหา
                window.location.href = `/tickets/${ticketId}`;
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
                // Redirect แม้ว่าจะมีข้อผิดพลาด
                window.location.href = `/tickets/${ticketId}`;
            });
        }

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