@props(['count' => 0])

<div class="relative" x-data="{ open: false, notifications: [], unreadCount: {{ $count }} }" x-init="
    // โหลด notifications เมื่อ component โหลด
    loadNotifications();
    
    // ตั้งค่า polling ทุก 30 วินาที
    setInterval(() => {
        loadNotifications();
    }, 30000);
    
    function loadNotifications() {
        fetch('/notifications/unread')
            .then(response => response.json())
            .then(data => {
                notifications = data.notifications.data || [];
                unreadCount = data.unread_count;
            });
    }
">
    <!-- Notification Bell -->
    <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 rounded-lg transition-all duration-200 group">
        <i class="fas fa-bell text-xl group-hover:text-blue-600 transition-colors duration-200"></i>
        
        <!-- Notification Badge -->
        <span x-show="unreadCount > 0" 
              x-text="unreadCount"
              class="absolute -top-1 -right-1 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center shadow-lg animate-bounce">
        </span>
    </button>

    <!-- Notification Dropdown -->
    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden z-50">
        
        <div class="py-2">
            <div class="px-4 py-3 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-bell text-white text-sm"></i>
                    </div>
                    การแจ้งเตือน
                    <span x-show="unreadCount > 0" 
                          x-text="unreadCount"
                          class="ml-auto bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                    </span>
                </h3>
            </div>
            
            <div class="max-h-64 overflow-y-auto">
                <template x-if="notifications.length === 0">
                    <div class="px-4 py-8 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-bell text-gray-300 text-xl"></i>
                        </div>
                        <p class="text-sm text-gray-500 font-medium">ไม่มีการแจ้งเตือนใหม่</p>
                        <p class="text-xs text-gray-400 mt-1">คุณจะได้รับการแจ้งเตือนเมื่อมีกิจกรรมใหม่</p>
                    </div>
                </template>
                
                <template x-for="notification in notifications" :key="notification.id">
                    <div class="px-4 py-3 border-b border-gray-100 transition-all duration-200 group"
                         :class="{
                             'hover:bg-blue-50 cursor-pointer': notification.data.ticket_id,
                             'hover:bg-gray-50': !notification.data.ticket_id
                         }"
                         @click="handleNotificationClick(notification)">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-start space-x-3">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full mt-2 flex-shrink-0 animate-pulse"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 mb-1 leading-relaxed" x-text="notification.data.message"></p>
                                        <div class="flex items-center space-x-2 flex-wrap">
                                            <p class="text-xs text-gray-500" x-text="formatTime(notification.created_at)"></p>
                                            <span class="text-xs text-blue-500 font-medium">ใหม่</span>
                                            <span x-show="notification.data.ticket_id" 
                                                  class="text-xs text-indigo-600 font-medium bg-indigo-100 px-2 py-1 rounded-full">
                                                <i class="fas fa-ticket-alt mr-1"></i>คลิกเพื่อดูปัญหา (จะอ่านแล้วอัตโนมัติ)
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button @click.stop="markAsRead(notification.id)" 
                                    class="ml-2 opacity-0 group-hover:opacity-100 text-xs text-blue-600 hover:text-blue-800 transition-all duration-200 bg-blue-100 hover:bg-blue-200 px-2 py-1 rounded-md">
                                <i class="fas fa-check mr-1"></i>อ่านแล้ว
                            </button>
                        </div>
                    </div>
                </template>
            </div>
            
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                <a href="/notifications" class="text-sm text-blue-600 hover:text-blue-800 transition-all duration-200 flex items-center justify-center bg-white hover:bg-blue-50 px-4 py-2 rounded-lg border border-gray-200 hover:border-blue-300">
                    <i class="fas fa-external-link-alt mr-2"></i>
                    ดูการแจ้งเตือนทั้งหมด
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function formatTime(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diff = now - date;
    
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);
    
    if (minutes < 1) return 'เมื่อสักครู่';
    if (minutes < 60) return `${minutes} นาทีที่แล้ว`;
    if (hours < 24) return `${hours} ชั่วโมงที่แล้ว`;
    return `${days} วันที่แล้ว`;
}

function markAsRead(id) {
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
            // อัปเดตจำนวน notifications
            this.unreadCount = data.unread_count;
            // ลบ notification ออกจากรายการ
            this.notifications = this.notifications.filter(n => n.id !== id);
            
            // อัปเดต notification badge ใน navigation ถ้ามี
            const notificationBadge = document.querySelector('.notification-badge');
            if (notificationBadge) {
                if (data.unread_count > 0) {
                    notificationBadge.textContent = data.unread_count;
                    notificationBadge.classList.remove('hidden');
                } else {
                    notificationBadge.classList.add('hidden');
                }
            }
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

function handleNotificationClick(notification) {
    // ถ้ามี ticket_id ให้ redirect ไปที่หน้ารายละเอียดปัญหา
    if (notification.data.ticket_id) {
        // ปิด dropdown ก่อน
        this.open = false;
        
        // Mark as read ทันที
        markAsRead(notification.id);
        
        // Redirect ไปที่หน้ารายละเอียดปัญหา
        window.location.href = `/tickets/${notification.data.ticket_id}`;
    }
}
</script> 