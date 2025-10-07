<template>
  <!-- Breadcrumb -->
  <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
    <a href="/admin" class="hover:text-gray-700 hover:underline">แดชบอร์ด</a>
    <span>/</span>
    <span class="font-semibold text-gray-900">หน้าจัดการคำขอ</span>
  </nav>

  <div class="bg-white p-6 rounded-lg shadow">
    <div class="flex justify-between items-center flex-wrap gap-y-4 mb-6">
      <div class="flex items-center flex-wrap gap-2">
        <button v-for="status in statusSummary" :key="status.key" @click="setStatusFilter(status.key)"
          :class="[status.class, activeStatusFilter === status.key ? 'ring-2 ring-offset-1 ring-blue-500' : 'hover:opacity-80']"
          class="px-4 py-1.5 rounded text-sm font-medium transition-all duration-150">
          <span>{{ status.label }}: {{ status.count }}</span>
        </button>
      </div>

      <div class="flex items-center space-x-2">
        <button @click="toggleSort"
          class="border border-gray-300 rounded-md px-5 py-2 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 w-35">
          Date: {{ sortDirection.toUpperCase() }}
        </button>
      </div>
    </div>
    
     <div class="flex items-center space-x-4 mb-4">
       <div class="relative flex-1 max-w-md">
         <input type="text" v-model="searchQuery" placeholder="ค้นหา รหัสคำขอ, ผู้ขอ, อุปกรณ์, สถานะ"
           class="pl-10 pr-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full" />
         <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
             d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
         </svg>
       </div>
       <div class="flex items-center space-x-2">
         <button @click="clearSearch" v-if="searchQuery"
           class="px-3 py-2 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-md hover:bg-gray-50">
           ล้าง
         </button>
       </div>
     </div>

    <div class="flex justify-between items-center mb-4">
      <h2 class="text-lg font-semibold">
        คำขอทั้งหมด: {{ filteredRequests.length }}
      </h2>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm border">
        <thead class="bg-gray-50 border-b">
          <tr>
            <th class="text-left px-4 py-2">รหัสคำขอ</th>
            <th class="text-left px-4 py-2">ผู้ขอ</th>
            <th class="text-left px-4 py-2">อุปกรณ์</th>
            <th class="text-left px-4 py-2">วันที่ขอ</th>
            <th class="text-left px-4 py-2">วันที่ส่ง</th>
            <th class="text-left px-4 py-2">สถานะ</th>
            <th class="text-left px-4 py-2">การดำเนินการ</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="request in paginatedRequests" :key="request.id" class="border-b">
            <td class="px-4 py-2">
              <span class="text-blue-600 hover:text-blue-800 cursor-pointer hover:underline" @click="viewRequestDetails(request)">
                {{ request.req_id }}
              </span>
            </td>
            <td class="px-4 py-2">
              <span class="text-blue-600 hover:text-blue-800 cursor-pointer hover:underline" @click="viewUserProfile(request)">
                {{ request.user_name }}
              </span>
            </td>
            <td class="px-4 py-2">
              <span class="text-blue-600 hover:text-blue-800 cursor-pointer hover:underline" @click="viewEquipmentDetails(request)">
                {{ request.equipment_name }}
              </span>
            </td>
            <td class="px-4 py-2">{{ request.start_at }}</td>
            <td class="px-4 py-2">{{ request.end_at }}</td>
            <td class="px-4 py-2">
              <span :class="getStatusClass(request.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                {{ getStatusText(request.status) }}
              </span>
            </td>
            <td class="px-4 py-2">
              <button @click="openDetails(request)" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                ดูรายละเอียด
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="mt-4 flex flex-wrap justify-between items-center gap-4">
      <div class="text-sm text-gray-600">
        Showing {{ pageStart + 1 }} - {{ pageEnd }} of
        {{ filteredRequests.length }} items
      </div>
      <div class="flex items-center space-x-1">
        <button class="px-3 py-1 border rounded disabled:opacity-50" :disabled="currentPage === 1" @click="prevPage">
          Prev
        </button>
        <button v-for="p in visiblePageNumbers" :key="p" class="px-3 py-1 border rounded"
          :class="{ 'bg-blue-600 text-white': currentPage === p }" @click="goToPage(p)">
          {{ p }}
        </button>
        <button class="px-3 py-1 border rounded disabled:opacity-50"
          :disabled="currentPage === pageCount || pageCount === 0" @click="nextPage">
          Next
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "AdminApproveTable",
  props: {
    // Expects an array of request objects
    // Example: [{ id: 1, req_id: 'R001', user_name: 'John', ..., status: 'pending', date: '2025-09-17' }]
    requests: Array
  },
  data() {
    return {
      searchQuery: "",
      currentPage: 1,
      pageSize: 10,
      activeStatusFilter: 'all', // NEW: To track which status filter is active
      sortDirection: 'desc',     // NEW: To track sort order (asc/desc)
    };
  },
  computed: {
    // MODIFIED: Show all requests including rejected and check_in
    displayableRequests() {
      if (!this.requests) return [];
      return this.requests; // Show all requests
    },
    // NEW: Generates the summary for the status badges
    statusSummary() {
      const counts = {
        all: this.displayableRequests.length
      };
      this.displayableRequests.forEach(req => {
        const status = req.status.toLowerCase();
        counts[status] = (counts[status] || 0) + 1;
      });

      const summary = [{
        key: 'all',
        label: 'All',
        count: counts.all,
        class: 'bg-gray-200 text-gray-800'
      }];

      // Create badges for each status found in the data
      Object.keys(counts).filter(key => key !== 'all').forEach(status => {
        summary.push({
          key: status,
          label: this.getStatusText(status),
          count: counts[status],
          class: this.getStatusClass(status)
        });
      });

      return summary;
    },
    // MODIFIED: This computed property now handles filtering AND sorting
    filteredRequests() {
      let filtered = [...this.displayableRequests];

      // 1. Apply status filter
      if (this.activeStatusFilter !== 'all') {
        filtered = filtered.filter(r => r.status.toLowerCase() === this.activeStatusFilter);
      }
       // 2. Apply search query
       if (this.searchQuery) {
         const q = this.searchQuery.toLowerCase();
         filtered = filtered.filter(
           (r) =>
             String(r.req_id).toLowerCase().includes(q) ||           // รหัสคำขอ
             (r.user_name && r.user_name.toLowerCase().includes(q)) || // ผู้ขอ
             (r.equipment_name && r.equipment_name.toLowerCase().includes(q)) || // อุปกรณ์
             this.getStatusText(r.status).toLowerCase().includes(q) || // สถานะ (Thai text)
             r.status.toLowerCase().includes(q) // สถานะ (English)
         );
       }

      // 3. Apply sorting
      filtered.sort((a, b) => {
        const dateA = new Date(a.start_at);
        const dateB = new Date(b.start_at);
        return this.sortDirection === 'asc' ? dateA - dateB : dateB - dateA;
      });

      return filtered;
    },
    pageCount() {
      return Math.ceil(this.filteredRequests.length / this.pageSize) || 0;
    },
    pageStart() {
      return (this.currentPage - 1) * this.pageSize;
    },
    pageEnd() {
      return Math.min(this.pageStart + this.pageSize, this.filteredRequests.length);
    },
    paginatedRequests() {
      return this.filteredRequests.slice(this.pageStart, this.pageEnd);
    },
    visiblePageNumbers() {
      const pages = [];
      const total = this.pageCount;
      const maxVisible = 5;
      if (total <= maxVisible) {
        for (let i = 1; i <= total; i++) pages.push(i);
        return pages;
      }
      let start = Math.max(1, this.currentPage - 2);
      let end = Math.min(total, start + maxVisible - 1);
      start = Math.max(1, end - maxVisible + 1);
      for (let i = start; i <= end; i++) pages.push(i);
      return pages;
    },
  },
  methods: {
    // NEW: Sets the active status for filtering
    setStatusFilter(statusKey) {
      this.activeStatusFilter = statusKey;
    },
    // NEW: Toggles the sort direction
    toggleSort() {
      this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
    },
    // MODIFIED: Assigns colors to status badges for all statuses
    getStatusClass(status) {
      switch (status.toLowerCase()) {
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'approved': return 'bg-green-100 text-green-800';
        case 'check_out': return 'bg-blue-100 text-blue-800';
        case 'check_in': return 'bg-purple-100 text-purple-800';
        case 'rejected': return 'bg-red-100 text-red-800';
        case 'cancelled': return 'bg-gray-100 text-gray-800';
        case 'in use': return 'bg-blue-100 text-blue-800';
        default: return 'bg-gray-100 text-gray-800';
      }
    },
    capitalize(str) {
      if (!str) return '';
      return str.charAt(0).toUpperCase() + str.slice(1).replace(/_/g, ' ');
    },
    // NEW: Get Thai text for status
    getStatusText(status) {
      switch (status.toLowerCase()) {
        case 'pending': return 'รออนุมัติ';
        case 'approved': return 'อนุมัติแล้ว';
        case 'check_out': return 'รับแล้ว';
        case 'check_in': return 'คืนแล้ว';
        case 'rejected': return 'ปฏิเสธ';
        case 'cancelled': return 'ยกเลิก';
        case 'in use': return 'กำลังใช้งาน';
        default: return this.capitalize(status);
      }
    },
    goToPage(p) {
      this.currentPage = p;
    },
    nextPage() {
      if (this.currentPage < this.pageCount) this.currentPage++;
    },
    prevPage() {
      if (this.currentPage > 1) this.currentPage--;
    },
    openDetails(request) {
      window.location.href = `/admin/requests/${request.req_id}`;
    },
    viewRequestDetails(request) {
      // Same as openDetails - redirect to request details page
      window.location.href = `/admin/requests/${request.req_id}`;
    },
    viewUserProfile(request) {
      // Redirect to user report page filtered by this user
      window.location.href = `/admin/report/user?search=${encodeURIComponent(request.user_name)}`;
    },
     viewEquipmentDetails(request) {
       // Search for the equipment in the equipment page
       window.location.href = `/admin/equipment?search=${encodeURIComponent(request.equipment_name)}`;
     },
     clearSearch() {
       this.searchQuery = '';
     },
  },
  watch: {
    searchQuery() {
      this.currentPage = 1;
    },
    activeStatusFilter() {
      this.currentPage = 1;
    },
    filteredRequests() {
      if (this.currentPage > this.pageCount && this.pageCount > 0) {
        this.currentPage = this.pageCount;
      } else if (this.pageCount === 0) {
        this.currentPage = 1;
      }
    },
  },
};
</script>
