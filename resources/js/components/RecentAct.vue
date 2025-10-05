<template>
  <div class="min-w-full">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 border-t border-b">
        <tr>
          <th class="text-left px-4 py-2">รหัสคำขอ</th>
          <th class="text-left px-4 py-2">ผู้ใช้</th>
          <th class="text-left px-4 py-2">อุปกรณ์</th>
          <th class="text-left px-4 py-2">วันที่เริ่ม</th>
          <th class="text-left px-4 py-2">วันที่สิ้นสุด</th>
          <th class="text-left px-4 py-2">สถานะ</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="request in requests" :key="request.id" 
            class="hover:bg-gray-50 cursor-pointer transition-colors"
            @click="viewRequestDetail(request.id)">
          <td class="px-4 py-2 font-mono text-xs">{{ request.id }}</td>
          <td class="px-4 py-2">{{ request.user_name }}</td>
          <td class="px-4 py-2">{{ request.equipment_name }}</td>
          <td class="px-4 py-2">{{ formatDate(request.start) }}</td>
          <td class="px-4 py-2">{{ formatDate(request.end) }}</td>
          <td class="px-4 py-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm" :class="getStatusClass(request.status)">
              {{ getStatusText(request.status) }}
            </span>
          </td>
        </tr>
        <tr v-if="requests.length === 0">
          <td colspan="6" class="px-4 py-8 text-center text-gray-500">
            <div class="text-lg mb-2">📋</div>
            <div>ไม่มีคำขอล่าสุด</div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name: 'RecentAct',
  props: {
    requests: {
      type: Array,
      required: true
    }
  },
  methods: {
    getStatusClass(status) {
      const statusLower = status.toLowerCase();
      switch (statusLower) {
        case 'approved':
          return 'bg-green-100 text-green-700';
        case 'rejected':
          return 'bg-red-100 text-red-700';
        case 'check_out':
          return 'bg-blue-100 text-blue-700';
        case 'check_in':
          return 'bg-purple-100 text-purple-700';
        case 'cancelled':
          return 'bg-gray-100 text-gray-700';
        default:
          return 'bg-yellow-100 text-yellow-700';
      }
    },
    getStatusText(status) {
      const statusLower = status.toLowerCase();
      switch (statusLower) {
        case 'pending':
          return 'รอดำเนินการ';
        case 'approved':
          return 'อนุมัติแล้ว';
        case 'rejected':
          return 'ปฏิเสธ';
        case 'check_out':
          return 'มารับของแล้ว';
        case 'check_in':
          return 'มาคืนของแล้ว';
        case 'cancelled':
          return 'ยกเลิก';
        default:
          return status;
      }
    },
    viewRequestDetail(requestId) {
      // Navigate to the request detail page
      window.location.href = `/admin/requests/${requestId}`;
    },
    formatDate(dateString) {
      if (!dateString) return '-';
      
      // Convert Y-m-d format to d/m/Y
      const date = new Date(dateString);
      if (isNaN(date.getTime())) return dateString;
      
      const day = date.getDate().toString().padStart(2, '0');
      const month = (date.getMonth() + 1).toString().padStart(2, '0');
      const year = date.getFullYear();
      
      return `${day}/${month}/${year}`;
    }
  }
}
</script>
