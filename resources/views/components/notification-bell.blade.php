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
    <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none transition-colors duration-200">
        <i class="fas fa-bell text-xl"></i>
        
        <!-- Notification Badge -->
        <span x-show="unreadCount > 0" 
              x-text="unreadCount"
              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center animate-pulse">
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
         class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg overflow-hidden z-50">
        
        <div class="py-2">
            <div class="px-4 py-2 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-bell mr-2 text-blue-500"></i>
                    การแจ้งเตือน
                </h3>
            </div>
            
            <div class="max-h-64 overflow-y-auto">
                <template x-if="notifications.length === 0">
                    <div class="px-4 py-6 text-center">
                        <i class="fas fa-bell text-gray-300 text-2xl mb-2"></i>
                        <p class="text-sm text-gray-500">ไม่มีการแจ้งเตือนใหม่</p>
                    </div>
                </template>
                
                <template x-for="notification in notifications" :key="notification.id">
                    <div class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-start space-x-2">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 mb-1" x-text="notification.data.message"></p>
                                        <p class="text-xs text-gray-500" x-text="formatTime(notification.created_at)"></p>
                                    </div>
                                </div>
                            </div>
                            <button @click="markAsRead(notification.id)" 
                                    class="ml-2 text-xs text-blue-600 hover:text-blue-800 transition-colors duration-200">
                                <i class="fas fa-check mr-1"></i>อ่านแล้ว
                            </button>
                        </div>
                    </div>
                </template>
            </div>
            
            <div class="px-4 py-2 border-t border-gray-200">
                <a href="/notifications" class="text-sm text-blue-600 hover:text-blue-800 transition-colors duration-200 flex items-center justify-center">
                    <i class="fas fa-external-link-alt mr-1"></i>
                    ดูทั้งหมด
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
        }
    });
}
</script> 