<template>
  <div class="bg-white rounded-lg shadow">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900">การยืนยันตัวตน</h3>
        <div class="flex space-x-2">
          <select v-model="statusFilter" @change="fetchVerifications" class="text-sm border-gray-300 rounded-md">
            <option value="">ทุกสถานะ</option>
            <option value="pending">รอการอนุมัติ</option>
            <option value="approved">อนุมัติแล้ว</option>
            <option value="rejected">ปฏิเสธแล้ว</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ผู้ใช้</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สถานะ</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">รูปบัตร</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">วันที่ส่ง</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ผู้ประมวลผล</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">การดำเนินการ</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="verification in verifications.data" :key="verification.id" class="hover:bg-gray-50">
            <!-- User Info -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div>
                <div class="text-sm font-medium text-gray-900">{{ verification.user.name }}</div>
                <div class="text-sm text-gray-500">{{ verification.user.email }}</div>
              </div>
            </td>

            <!-- Status -->
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="getStatusClass(verification.status)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                {{ getStatusText(verification.status) }}
              </span>
            </td>

            <!-- Student ID Image -->
            <td class="px-6 py-4 whitespace-nowrap">
              <button @click="viewImage(verification)" class="text-blue-600 hover:text-blue-900 text-sm">
                ดูรูปภาพ
              </button>
            </td>

            <!-- Created Date -->
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ formatDate(verification.created_at) }}
            </td>

            <!-- Processed By -->
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              <div v-if="verification.processed_by">
                <div>{{ verification.processed_by_user?.name || 'ผู้ดูแลระบบ' }}</div>
                <div class="text-xs text-gray-500">{{ formatDate(verification.process_at) }}</div>
              </div>
              <span v-else class="text-gray-400">-</span>
            </td>

            <!-- Actions -->
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <div class="flex space-x-2">
                <button @click="viewDetails(verification)" class="text-blue-600 hover:text-blue-900">
                  ดูรายละเอียด
                </button>
                <template v-if="verification.status === 'pending'">
                  <button @click="approveVerification(verification)" class="text-green-600 hover:text-green-900">
                    อนุมัติ
                  </button>
                  <button @click="rejectVerification(verification)" class="text-red-600 hover:text-red-900">
                    ปฏิเสธ
                  </button>
                </template>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
      <div class="flex-1 flex justify-between sm:hidden">
        <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1" 
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50">
          ก่อนหน้า
        </button>
        <button @click="goToPage(currentPage + 1)" :disabled="currentPage === lastPage" 
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50">
          ถัดไป
        </button>
      </div>
      <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
        <div>
          <p class="text-sm text-gray-700">
            แสดง <span class="font-medium">{{ verifications.from || 0 }}</span> ถึง 
            <span class="font-medium">{{ verifications.to || 0 }}</span> จาก 
            <span class="font-medium">{{ verifications.total || 0 }}</span> รายการ
          </p>
        </div>
        <div>
          <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
            <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50">
              ก่อนหน้า
            </button>
            <button v-for="page in visiblePages" :key="page" @click="goToPage(page)"
                    :class="page === currentPage ? 'bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'"
                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium">
              {{ page }}
            </button>
            <button @click="goToPage(currentPage + 1)" :disabled="currentPage === lastPage"
                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50">
              ถัดไป
            </button>
          </nav>
        </div>
      </div>
    </div>
  </div>

  <!-- Image Modal -->
  <div v-if="showImageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="closeImageModal">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white" @click.stop>
      <div class="mt-3">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">รูปบัตรนักศึกษา</h3>
          <button @click="closeImageModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <div class="flex justify-center">
          <img :src="selectedImage" alt="Student ID" class="max-w-full h-auto max-h-96 rounded-lg shadow-lg">
        </div>
      </div>
    </div>
  </div>

  <!-- Action Modal -->
  <div v-if="showActionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="closeActionModal">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white" @click.stop>
      <div class="mt-3">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">
            {{ actionType === 'approve' ? 'อนุมัติการยืนยันตัวตน' : 'ปฏิเสธการยืนยันตัวตน' }}
          </h3>
          <button @click="closeActionModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            {{ actionType === 'approve' ? 'หมายเหตุ (ไม่บังคับ)' : 'เหตุผลในการปฏิเสธ *' }}
          </label>
          <textarea v-model="actionNote" rows="3" 
                    :class="actionType === 'reject' && !actionNote ? 'border-red-300' : 'border-gray-300'"
                    class="w-full text-sm border rounded-md focus:ring-blue-500 focus:border-blue-500"
                    :placeholder="actionType === 'approve' ? 'หมายเหตุการอนุมัติ...' : 'กรุณาระบุเหตุผลในการปฏิเสธ...'">
          </textarea>
        </div>

        <div class="flex justify-end space-x-3">
          <button @click="closeActionModal" 
                  class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
            ยกเลิก
          </button>
          <button @click="confirmAction" 
                  :disabled="actionType === 'reject' && !actionNote"
                  :class="actionType === 'approve' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'"
                  class="px-4 py-2 text-sm font-medium text-white rounded-md disabled:opacity-50">
            {{ actionType === 'approve' ? 'อนุมัติ' : 'ปฏิเสธ' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'VerificationTable',
  data() {
    return {
      verifications: {
        data: [],
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
        from: 0,
        to: 0
      },
      currentPage: 1,
      lastPage: 1,
      statusFilter: '',
      showImageModal: false,
      selectedImage: '',
      showActionModal: false,
      actionType: '',
      actionNote: '',
      selectedVerification: null,
      loading: false
    }
  },
  computed: {
    visiblePages() {
      const pages = [];
      const start = Math.max(1, this.currentPage - 2);
      const end = Math.min(this.lastPage, this.currentPage + 2);
      
      for (let i = start; i <= end; i++) {
        pages.push(i);
      }
      return pages;
    }
  },
  mounted() {
    this.fetchVerifications();
  },
  methods: {
    async fetchVerifications() {
      this.loading = true;
      try {
        const params = new URLSearchParams({
          page: this.currentPage,
          status: this.statusFilter
        });
        
        const response = await fetch(`/admin/verification/api?${params}`);
        const data = await response.json();
        
        this.verifications = data;
        this.currentPage = data.current_page;
        this.lastPage = data.last_page;
      } catch (error) {
        console.error('Error fetching verifications:', error);
        this.showNotification('เกิดข้อผิดพลาดในการโหลดข้อมูล', 'error');
      } finally {
        this.loading = false;
      }
    },
    
    goToPage(page) {
      if (page >= 1 && page <= this.lastPage && page !== this.currentPage) {
        this.currentPage = page;
        this.fetchVerifications();
      }
    },
    
    viewImage(verification) {
      this.selectedImage = verification.student_id_image_path;
      this.showImageModal = true;
    },
    
    closeImageModal() {
      this.showImageModal = false;
      this.selectedImage = '';
    },
    
    viewDetails(verification) {
      // Navigate to detail page
      window.location.href = `/admin/verification/${verification.id}`;
    },
    
    approveVerification(verification) {
      this.actionType = 'approve';
      this.selectedVerification = verification;
      this.actionNote = '';
      this.showActionModal = true;
    },
    
    rejectVerification(verification) {
      this.actionType = 'reject';
      this.selectedVerification = verification;
      this.actionNote = '';
      this.showActionModal = true;
    },
    
    closeActionModal() {
      this.showActionModal = false;
      this.actionType = '';
      this.actionNote = '';
      this.selectedVerification = null;
    },
    
    async confirmAction() {
      if (this.actionType === 'reject' && !this.actionNote.trim()) {
        this.showNotification('กรุณาระบุเหตุผลในการปฏิเสธ', 'error');
        return;
      }
      
      try {
        const response = await fetch(`/admin/verification/${this.selectedVerification.id}/${this.actionType}`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
            admin_note: this.actionNote
          })
        });
        
        if (response.ok) {
          this.showNotification(
            this.actionType === 'approve' ? 'อนุมัติการยืนยันตัวตนเรียบร้อยแล้ว' : 'ปฏิเสธการยืนยันตัวตนเรียบร้อยแล้ว',
            'success'
          );
          this.closeActionModal();
          this.fetchVerifications();
        } else {
          throw new Error('Action failed');
        }
      } catch (error) {
        console.error('Error processing action:', error);
        this.showNotification('เกิดข้อผิดพลาดในการดำเนินการ', 'error');
      }
    },
    
    getStatusClass(status) {
      switch (status) {
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'approved': return 'bg-green-100 text-green-800';
        case 'rejected': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
      }
    },
    
    getStatusText(status) {
      switch (status) {
        case 'pending': return 'รอการอนุมัติ';
        case 'approved': return 'อนุมัติแล้ว';
        case 'rejected': return 'ปฏิเสธแล้ว';
        default: return status;
      }
    },
    
    formatDate(dateString) {
      if (!dateString) return '-';
      const date = new Date(dateString);
      return date.toLocaleDateString('th-TH', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
      });
    },
    
    showNotification(message, type = 'info') {
      // Simple notification - you can replace with a proper notification system
      const notification = document.createElement('div');
      notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
      }`;
      notification.textContent = message;
      
      document.body.appendChild(notification);
      
      setTimeout(() => {
        notification.remove();
      }, 3000);
    }
  }
}
</script>
