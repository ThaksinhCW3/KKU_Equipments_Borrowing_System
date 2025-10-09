<template>
  <div class="relative inline-block">
    <!-- Notification Button -->
    <button 
      @click="toggleDropdown" 
      class="p-2 rounded-full hover:bg-gray-200 relative"
    >
      <!-- Bell Icon -->
      <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 64 64">
        <path
          d="M 32 10 C 29.662 10 28.306672 11.604938 27.638672 13.085938 C 24.030672 13.809937 17.737984 16.956187 16.958984 24.742188 C 16.665984 29.334188 16.1185 37.883781 13.0625 39.300781 C 12.8505 39.398781 12.655234 39.533219 12.490234 39.699219 C 12.235234 39.954219 10 42.294 10 46 C 10 47.104 10.896 48 12 48 L 25.257812 48 C 25.652433 51.372928 28.522752 54 32 54 C 35.477248 54 38.347567 51.372928 38.742188 48 L 52 48 C 53.104 48 54 47.104 54 46 C 54 42.294 51.764766 39.954219 51.509766 39.699219 C 51.344766 39.534219 51.1495 39.397828 50.9375 39.298828 C 47.8825 37.881828 47.333203 29.333922 47.033203 24.669922 C 46.258203 16.945922 39.966375 13.806984 36.359375 13.083984 C 35.692375 11.603984 34.338 10 32 10 z M 32 14 C 32.603 14 32.766719 14.619859 32.886719 15.255859 C 33.063719 16.190859 33.884422 16.914062 34.857422 16.914062 C 34.931422 16.914063 42.311828 17.650047 43.048828 24.998047 C 43.557828 32.932047 44.389891 40.250797 48.837891 42.716797 C 49.024891 42.956797 49.333937 43.401 49.585938 44 L 14.414062 44 C 14.667063 43.397 14.976203 42.95375 15.158203 42.71875 C 19.609203 40.25475 20.442312 32.935313 20.945312 25.070312 C 21.688313 17.650312 29.068578 16.914062 29.142578 16.914062 C 30.099578 16.914062 30.934375 16.156391 31.109375 15.275391 C 31.232375 14.660391 31.396 14 32 14 z M 29.335938 48 L 34.664062 48 C 34.319789 49.152328 33.262739 50 32 50 C 30.737261 50 29.680211 49.152328 29.335938 48 z">
        </path>
      </svg>
      
      <!-- Notification Count Badge -->
      <span 
        v-if="unreadCount > 0"
        class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full"
      >
        {{ unreadCount }}
      </span>
    </button>

    <!-- Notification Dropdown -->
    <div 
      v-show="isOpen" 
      class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
    >
      <!-- Header -->
      <div class="p-4 border-b flex justify-between items-center">
        <span class="font-semibold text-gray-700">Notifications</span>
        <button 
          v-if="unreadCount > 0"
          @click="markAllAsRead" 
          class="text-sm text-blue-600 hover:underline"
          :disabled="isMarkingAll"
        >
          {{ isMarkingAll ? 'กำลังดำเนินการ...' : 'Mark all read' }}
        </button>
      </div>

      <!-- Notification List -->
      <div class="max-h-72 overflow-y-auto">
        <div v-if="loading" class="p-4 text-center text-gray-500">
          กำลังโหลด...
        </div>
        
        <div v-else-if="notifications.length === 0" class="p-4 text-gray-500">
          ยังไม่มีการแจ้งเตือน
        </div>
        
        <div v-else>
          <div 
            v-for="notification in notifications" 
            :key="notification.id"
            class="px-4 py-3 hover:bg-gray-100 border-b flex justify-between items-start"
            :class="{ 'read-notification': notification.read_at }"
          >
            <div 
              class="cursor-pointer flex-1"
              @click="handleNotificationClick(notification)"
            >
              <div class="font-semibold text-gray-800">
                {{ getNotificationSender(notification) }}
              </div>
              
              <div 
                class="text-sm"
                :class="getNotificationColor(notification)"
              >
                {{ notification.data.message }}
              </div>
              
              <div class="text-xs text-gray-400 mt-1">
                {{ getNotificationDetails(notification) }} |
                {{ notification.created_at_human }}
              </div>
            </div>
            
            <button 
              v-if="!notification.read_at"
              @click="markAsRead(notification.id)"
              class="text-xs text-blue-600 hover:underline ml-2"
              :disabled="isMarking"
            >
              Mark read
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'NotificationBell',
  data() {
    return {
      isOpen: false,
      notifications: [],
      unreadCount: 0,
      loading: false,
      isMarking: false,
      isMarkingAll: false,
      pollInterval: null
    }
  },
  mounted() {
    this.fetchNotifications()
    this.startPolling()
    
    // Close dropdown when clicking outside
    document.addEventListener('click', this.handleClickOutside)
  },
  beforeUnmount() {
    this.stopPolling()
    document.removeEventListener('click', this.handleClickOutside)
  },
  methods: {
    async fetchNotifications() {
      try {
        this.loading = true
        const response = await axios.get('/api/notifications')
        this.notifications = response.data.notifications
        this.unreadCount = response.data.unread_count
      } catch (error) {
        console.error('Error fetching notifications:', error)
      } finally {
        this.loading = false
      }
    },
    
    async markAsRead(notificationId) {
      try {
        this.isMarking = true
        await axios.patch(`/api/notifications/${notificationId}/read`)
        
        // Update local state
        const notification = this.notifications.find(n => n.id === notificationId)
        if (notification) {
          notification.read_at = new Date().toISOString()
          this.unreadCount = Math.max(0, this.unreadCount - 1)
        }
      } catch (error) {
        console.error('Error marking notification as read:', error)
      } finally {
        this.isMarking = false
      }
    },
    
    async markAllAsRead() {
      try {
        this.isMarkingAll = true
        await axios.patch('/api/notifications/mark-all-read')
        
        // Update local state
        this.notifications.forEach(notification => {
          if (!notification.read_at) {
            notification.read_at = new Date().toISOString()
          }
        })
        this.unreadCount = 0
      } catch (error) {
        console.error('Error marking all notifications as read:', error)
      } finally {
        this.isMarkingAll = false
      }
    },
    
    handleNotificationClick(notification) {
      // Mark as read if not already read
      if (!notification.read_at) {
        this.markAsRead(notification.id)
      }
      
      // Handle ban/unban notifications
      if (notification.data.type === 'ban') {
        // Redirect to banned page
        window.location.href = notification.data.action_url || '/banned'
        return
      }
      if (notification.data.type === 'unban') {
        // Redirect to login page
        window.location.href = notification.data.action_url || '/login'
        return
      }
      
      // Navigate to URL if available
      if (notification.data.url && notification.data.url !== '#') {
        window.location.href = notification.data.url
      }
      
      this.isOpen = false
    },
    
    toggleDropdown() {
      this.isOpen = !this.isOpen
      if (this.isOpen) {
        this.fetchNotifications()
      }
    },
    
    handleClickOutside(event) {
      if (!this.$el.contains(event.target)) {
        this.isOpen = false
      }
    },
    
    getNotificationSender(notification) {
      // For ban/unban notifications
      if (notification.data.type === 'ban' || notification.data.type === 'unban') {
        return notification.data.title || 'ระบบแจ้งเตือน'
      }
      // For verification notifications
      if (notification.data.type === 'verification_submitted' || notification.data.type === 'verification_processed') {
        return notification.data.user_name || notification.data.user_email || 'admin'
      }
      // For borrow request notifications
      return notification.data.user || 'admin'
    },
    
    getNotificationColor(notification) {
      // For ban/unban notifications
      if (notification.data.type === 'ban') {
        return 'text-red-600'
      }
      if (notification.data.type === 'unban') {
        return 'text-green-600'
      }
      // For verification notifications
      if (notification.data.type === 'verification_submitted') {
        return 'text-yellow-600'
      }
      if (notification.data.type === 'verification_processed') {
        return notification.data.status === 'approved' ? 'text-green-600' : 'text-red-600'
      }
      // For borrow request notifications
      if (notification.data.status) {
        switch (notification.data.status) {
          case 'rejected':
            return 'text-red-600'
          case 'approved':
            return 'text-green-600'
          case 'cancelled':
            return 'text-gray-600'
          default:
            return 'text-yellow-600'
        }
      }
      return 'text-gray-600'
    },
    
    getNotificationDetails(notification) {
      // For ban/unban notifications
      if (notification.data.type === 'ban') {
        let details = `เหตุผล: ${notification.data.ban_reason}`
        if (notification.data.banned_by_email || notification.data.banned_by_phone) {
          details += ' | ติดต่อ: '
          if (notification.data.banned_by_email) details += notification.data.banned_by_email
          if (notification.data.banned_by_email && notification.data.banned_by_phone) details += ', '
          if (notification.data.banned_by_phone) details += notification.data.banned_by_phone
        }
        return details
      }
      if (notification.data.type === 'unban') {
        let details = 'บัญชีได้รับการปลดแบน'
        if (notification.data.unbanned_by_email || notification.data.unbanned_by_phone) {
          details += ' | ติดต่อ: '
          if (notification.data.unbanned_by_email) details += notification.data.unbanned_by_email
          if (notification.data.unbanned_by_email && notification.data.unbanned_by_phone) details += ', '
          if (notification.data.unbanned_by_phone) details += notification.data.unbanned_by_phone
        }
        return details
      }
      // For verification notifications
      if (notification.data.type === 'verification_submitted') {
        return 'การยืนยันตัวตน'
      }
      if (notification.data.type === 'verification_processed') {
        return 'การยืนยันตัวตน'
      }
      // For borrow request notifications
      return `อุปกรณ์: ${notification.data.equipment}`
    },
    
    getStatusColor(status) {
      switch (status) {
        case 'rejected':
          return 'text-red-600'
        case 'approved':
          return 'text-green-600'
        case 'cancelled':
          return 'text-gray-600'
        default:
          return 'text-yellow-600'
      }
    },
    
    startPolling() {
      // Poll for new notifications every 30 seconds
      this.pollInterval = setInterval(() => {
        this.fetchNotifications()
      }, 30000)
    },
    
    stopPolling() {
      if (this.pollInterval) {
        clearInterval(this.pollInterval)
        this.pollInterval = null
      }
    }
  }
}
</script>

<style scoped>
.read-notification {
  opacity: 0.6 !important;
  background-color: #f9fafb;
}
.read-notification .font-semibold {
  color: #6b7280 !important;
}
.read-notification .text-sm {
  color: #9ca3af !important;
}
</style>
