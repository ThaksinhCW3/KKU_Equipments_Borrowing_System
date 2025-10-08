<template>
  <BaseTable
    :data="verifications.data"
    :columns="columns"
    :title="'การยืนยันตัวตน'"
    :search-placeholder="'ค้นหาชื่อ, อีเมล, หรือ UID...'"
    :user-role="userRole"
    :available-filters="availableFilters"
    :search-fields="['user.name', 'user.email', 'user.uid', 'status']"
    :loading="loading"
    :show-add-button="false"
    @edit="viewDetails"
    @delete="deleteVerification"
  >
    <!-- Custom row template -->
    <template #rows="{ item: verification, actions }">
      <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center">
          <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
          </div>
        </div>
      </td>
      <td class="px-6 py-4 whitespace-nowrap">
        <div class="text-sm font-medium text-gray-900">{{ verification.user.name }}</div>
        <div class="text-sm text-gray-500">{{ verification.user.email }}</div>
      </td>
      <td class="px-6 py-4 whitespace-nowrap">
        <div class="space-y-1">
          <span 
            v-if="!verification.user.is_banned"
            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
            :class="getStatusClass(verification.status)"
          >
            {{ getStatusText(verification.status) }}
          </span>
          <div v-if="verification.user.is_banned" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"></path>
            </svg>
            ถูกแบน
          </div>
        </div>
      </td>
      <td class="px-6 py-4 whitespace-nowrap">
        <div v-if="verification.student_id_image_path" class="w-16 h-16 cursor-pointer" @click="viewImage(verification)">
          <img 
            :src="verification.student_id_image_path" 
            alt="Student ID" 
            class="w-full h-full object-cover rounded-lg border border-gray-200 hover:border-blue-300 transition-colors"
          />
        </div>
        <div v-else class="w-16 h-16 bg-gray-100 rounded-lg border border-gray-200 flex items-center justify-center">
          <span class="text-gray-400 text-xs">ไม่มีรูป</span>
        </div>
      </td>
      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
        {{ formatDate(verification.created_at) }}
      </td>
      <td v-if="userRole === 'admin'" class="px-6 py-4 whitespace-nowrap text-sm font-medium">
        <div class="flex space-x-2">
          <button 
            @click="viewDetails(verification)"
            class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-3 py-1 rounded-md text-sm font-medium transition-colors"
          >
            ดูรายละเอียด
          </button>
          <template v-if="verification.status === 'pending'">
            <button 
              @click="approveVerification(verification)"
              class="text-green-600 hover:text-green-900 bg-green-100 hover:bg-green-200 px-3 py-1 rounded-md text-sm font-medium transition-colors"
            >
              อนุมัติ
            </button>
            <button 
              @click="rejectVerification(verification)"
              class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-3 py-1 rounded-md text-sm font-medium transition-colors"
            >
              ปฏิเสธ
            </button>
          </template>
          <template v-else-if="verification.status === 'approved'">
            <button 
              v-if="!verification.user.is_banned"
              @click="banUser(verification)"
              class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-3 py-1 rounded-md text-sm font-medium transition-colors"
            >
              แบนผู้ใช้
            </button>
            <button 
              v-else
              @click="unbanUser(verification)"
              class="text-green-600 hover:text-green-900 bg-green-100 hover:bg-green-200 px-3 py-1 rounded-md text-sm font-medium transition-colors"
            >
              ยกเลิกการแบน
            </button>
          </template>
          <template v-else-if="verification.status === 'rejected' && verification.user.is_banned">
            <button 
              @click="unbanUser(verification)"
              class="text-green-600 hover:text-green-900 bg-green-100 hover:bg-green-200 px-3 py-1 rounded-md text-sm font-medium transition-colors"
            >
              ยกเลิกการแบน
            </button>
          </template>
        </div>
      </td>
    </template>
  </BaseTable>

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
</template>

<script>
import BaseTable from "./BaseTable.vue";
import api from "../../api";

export default {
  name: 'VerificationTable',
  components: {
    BaseTable,
  },
  data() {
    const el = document.getElementById("verification-table");
    return {
      userRole: el?.dataset?.role || "admin",
      verifications: {
        data: [],
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
        from: 0,
        to: 0
      },
      loading: false,
      
      // Image modal
      showImageModal: false,
      selectedImage: '',
      selectedVerification: null,
    }
  },
  computed: {
    columns() {
      const baseColumns = [
        { key: 'avatar', label: 'รูป' },
        { key: 'user', label: 'ผู้ใช้' },
        { key: 'status', label: 'สถานะ' },
        { key: 'image', label: 'รูปบัตร' },
        { key: 'created_at', label: 'วันที่ส่ง' },
      ];
      
      if (this.userRole === 'admin') {
        baseColumns.push({ key: 'actions', label: 'การดำเนินการ' });
      }
      
      return baseColumns;
    },
    
    availableFilters() {
      return [
        {
          key: 'status',
          label: 'สถานะ',
          type: 'select',
          placeholder: 'เลือกสถานะ',
          options: [
            { value: 'pending', label: 'รอการอนุมัติ' },
            { value: 'approved', label: 'อนุมัติแล้ว' },
            { value: 'rejected', label: 'ปฏิเสธแล้ว' }
          ]
        }
      ];
    }
  },
  methods: {
    // Data fetching
    async fetchVerifications() {
      this.loading = true;
      try {
        const response = await api.get('/admin/verification/api');
        this.verifications = response.data;
      } catch (error) {
        this.showError('ไม่สามารถโหลดข้อมูลการยืนยันตัวตนได้');
      } finally {
        this.loading = false;
      }
    },
    
    // Status helpers
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
    
    // Date formatting
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
    
    // Image modal
    viewImage(verification) {
      this.selectedImage = verification.student_id_image_path;
      this.showImageModal = true;
    },
    
    closeImageModal() {
      this.showImageModal = false;
      this.selectedImage = '';
    },
    
    // Actions
    viewDetails(verification) {
      window.location.href = `/admin/verification/${verification.id}`;
    },
    
    deleteVerification(verification) {
      // This could be implemented if needed
      // Removed debug/log statement
    },
    
    // Verification actions
    approveVerification(verification) {
      this.selectedVerification = verification;
      this.confirmApprove();
    },
    
    rejectVerification(verification) {
      this.selectedVerification = verification;
      this.showRejectModal();
    },
    
    banUser(verification) {
      this.selectedVerification = verification;
      this.showBanModal();
    },
    
    unbanUser(verification) {
      this.selectedVerification = verification;
      this.showUnbanModal();
    },
    
    confirmApprove() {
      this.ensureSwal().then(() => {
        window.Swal.fire({
          title: 'อนุมัติการยืนยันตัวตน',
          text: 'คุณต้องการอนุมัติการยืนยันตัวตนนี้หรือไม่?',
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'อนุมัติ',
          cancelButtonText: 'ยกเลิก'
        }).then((result) => {
          if (result.isConfirmed) {
            this.processApprove();
          }
        });
      });
    },
    
    showRejectModal() {
      this.ensureSwal().then(() => {
        window.Swal.fire({
          title: 'ปฏิเสธการยืนยันตัวตน',
          html: `
            <div class="text-left space-y-2">
              <label class="flex items-center gap-2"><input type="radio" name="reason" value="รูปไม่ชัด"> รูปไม่ชัด</label>
              <label class="flex items-center gap-2"><input type="radio" name="reason" value="รูปไม่ใช่บัตรนักศึกษา"> รูปไม่ใช่บัตรนักศึกษา</label>
              <label class="flex items-center gap-2"><input type="radio" name="reason" value="ข้อมูลไม่ตรงกับบัตร"> ข้อมูลไม่ตรงกับบัตร</label>
              <label class="flex items-center gap-2"><input type="radio" name="reason" value="อื่นๆ"> อื่นๆ</label>
              <input id="reason-text" type="text" placeholder="ระบุเหตุผลเพิ่มเติม (ถ้าเลือก อื่นๆ)" maxlength="50" class="w-full border rounded px-2 py-1" />
            </div>
          `,
          showCancelButton: true,
          confirmButtonText: 'ปฏิเสธ',
          cancelButtonText: 'ยกเลิก',
          focusConfirm: false,
          preConfirm: () => {
            const selected = document.querySelector('input[name="reason"]:checked');
            const text = (document.getElementById('reason-text') || {}).value || '';
            let reason = selected ? selected.value : '';
            if (!reason) {
              window.Swal.showValidationMessage('กรุณาเลือกเหตุผล');
              return false;
            }
            if (reason === 'อื่นๆ') {
              if (!text.trim()) {
                window.Swal.showValidationMessage('กรุณาระบุเหตุผลเพิ่มเติม');
                return false;
              }
              reason = text.trim();
            }
            return reason;
          }
        }).then((result) => {
          if (result.isConfirmed) {
            this.processReject(result.value);
          }
        });
      });
    },
    
    showBanModal() {
      this.ensureSwal().then(() => {
        window.Swal.fire({
          title: 'แบนผู้ใช้',
          html: `
            <div class="text-left space-y-2">
              <p class="text-red-600 font-medium">คุณกำลังจะแบนผู้ใช้: <strong>${this.selectedVerification.user.name}</strong></p>
              <p class="text-sm text-gray-600">การแบนจะทำให้ผู้ใช้ไม่สามารถเข้าถึงระบบได้</p>
              <div class="space-y-2">
                <label class="flex items-center gap-2"><input type="radio" name="ban-reason" value="ใช้ข้อมูลปลอมในการยืนยันตัวตน"> ใช้ข้อมูลปลอมในการยืนยันตัวตน</label>
                <label class="flex items-center gap-2"><input type="radio" name="ban-reason" value="ละเมิดกฎระเบียบของระบบ"> ละเมิดกฎระเบียบของระบบ</label>
                <label class="flex items-center gap-2"><input type="radio" name="ban-reason" value="พฤติกรรมไม่เหมาะสม"> พฤติกรรมไม่เหมาะสม</label>
                <label class="flex items-center gap-2"><input type="radio" name="ban-reason" value="อื่นๆ"> อื่นๆ</label>
                <input id="ban-reason-text" type="text" placeholder="ระบุเหตุผลเพิ่มเติม (ถ้าเลือก อื่นๆ)" maxlength="200" class="w-full border rounded px-2 py-1" />
              </div>
            </div>
          `,
          showCancelButton: true,
          confirmButtonText: 'แบนผู้ใช้',
          cancelButtonText: 'ยกเลิก',
          confirmButtonColor: '#dc2626',
          focusConfirm: false,
          preConfirm: () => {
            const selected = document.querySelector('input[name="ban-reason"]:checked');
            const text = (document.getElementById('ban-reason-text') || {}).value || '';
            let reason = selected ? selected.value : '';
            if (!reason) {
              window.Swal.showValidationMessage('กรุณาเลือกเหตุผล');
              return false;
            }
            if (reason === 'อื่นๆ') {
              if (!text.trim()) {
                window.Swal.showValidationMessage('กรุณาระบุเหตุผลเพิ่มเติม');
                return false;
              }
              reason = text.trim();
            }
            return reason;
          }
        }).then((result) => {
          if (result.isConfirmed) {
            this.processBan(result.value);
          }
        });
      });
    },
    
    showUnbanModal() {
      this.ensureSwal().then(() => {
        window.Swal.fire({
          title: 'ยกเลิกการแบนผู้ใช้',
          html: `
            <div class="text-left space-y-2">
              <p class="text-green-600 font-medium">คุณกำลังจะยกเลิกการแบนผู้ใช้: <strong>${this.selectedVerification.user.name}</strong></p>
              <p class="text-sm text-gray-600">การยกเลิกการแบนจะทำให้ผู้ใช้สามารถเข้าถึงระบบได้อีกครั้ง</p>
              <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                <p class="text-sm text-yellow-800">
                  <strong>เหตุผลที่ถูกแบน:</strong> ${this.selectedVerification.user.ban_reason || 'ไม่ระบุ'}
                </p>
              </div>
            </div>
          `,
          showCancelButton: true,
          confirmButtonText: 'ยกเลิกการแบน',
          cancelButtonText: 'ยกเลิก',
          confirmButtonColor: '#10b981',
          focusConfirm: false
        }).then((result) => {
          if (result.isConfirmed) {
            this.processUnban();
          }
        });
      });
    },
    
    async processApprove() {
      try {
        const response = await api.post(`/admin/verification/${this.selectedVerification.id}/approve`);
        
        // Check if response is successful (status 200-299)
        if (response.status >= 200 && response.status < 300) {
          this.showSuccess('อนุมัติการยืนยันตัวตนเรียบร้อยแล้ว');
          this.fetchVerifications();
        } else {
          throw new Error('Approve failed');
        }
      } catch (error) {
        this.showError('เกิดข้อผิดพลาดในการอนุมัติ');
      }
    },
    
    async processReject(reason) {
      try {
        const response = await api.post(`/admin/verification/${this.selectedVerification.id}/reject`, {
          reject_note: reason
        });
        
        // Check if response is successful (status 200-299)
        if (response.status >= 200 && response.status < 300) {
          this.showSuccess('ปฏิเสธการยืนยันตัวตนเรียบร้อยแล้ว');
          this.fetchVerifications();
        } else {
          throw new Error('Reject failed');
        }
      } catch (error) {
        this.showError('เกิดข้อผิดพลาดในการปฏิเสธ');
      }
    },
    
    async processBan(reason) {
      try {
        const response = await api.post(`/admin/verification/${this.selectedVerification.id}/ban`, {
          ban_reason: reason
        });
        
        // Check if response is successful (status 200-299)
        if (response.status >= 200 && response.status < 300) {
          this.showSuccess('แบนผู้ใช้เรียบร้อยแล้ว');
          this.fetchVerifications();
        } else {
          throw new Error('Ban failed');
        }
      } catch (error) {
        this.showError('เกิดข้อผิดพลาดในการแบนผู้ใช้');
      }
    },
    
    async processUnban() {
      try {
        const response = await api.post(`/admin/verification/${this.selectedVerification.id}/unban`);
        
        // Check if response is successful (status 200-299)
        if (response.status >= 200 && response.status < 300) {
          this.showSuccess('ยกเลิกการแบนผู้ใช้เรียบร้อยแล้ว');
          this.fetchVerifications();
        } else {
          throw new Error('Unban failed');
        }
      } catch (error) {
        this.showError('เกิดข้อผิดพลาดในการยกเลิกการแบน');
      }
    },
    
    // Utility methods
    ensureSwal() {
      return new Promise((resolve) => {
        if (window.Swal) return resolve();
        const script = document.createElement("script");
        script.src = "https://cdn.jsdelivr.net/npm/sweetalert2@11";
        script.onload = () => resolve();
        document.head.appendChild(script);
      });
    },
    
    showSuccess(message) {
      this.ensureSwal().then(() => {
        window.Swal.fire({
          title: "สำเร็จ",
          text: message,
          icon: "success",
          timer: 1200,
          showConfirmButton: false,
        });
      });
    },
    
    showError(message) {
      this.ensureSwal().then(() => {
        window.Swal.fire({
          title: "เกิดข้อผิดพลาด",
          text: message,
          icon: "error",
        });
      });
    }
  },
  
  mounted() {
    this.fetchVerifications();
  }
}
</script>