<template>
  <BaseTable :data="requests || []" :columns="columns" :title="'หน้าจัดการคำขอ'"
    :search-placeholder="'ค้นหา รหัสคำขอ, ผู้ขอ, อุปกรณ์, รหัสอุปกรณ์, สถานะ...'" :user-role="'admin'"
    :available-filters="availableFilters" :search-fields="['req_id', 'user_name', 'equipment_name', 'equipment_code', 'status']"
    :page-size="pageSize" :default-sort="'created_at'" :default-sort-direction="'desc'" :show-add-button="false">
    <template #rows="{ item: request, actions }">
      <td class="px-4 py-2">
        <span class="text-blue-600 hover:text-blue-800 cursor-pointer hover:underline"
          @click="viewRequestDetails(request)">
          {{ request.req_id }}
        </span>
      </td>

      <td class="px-4 py-2">
        <span class="text-blue-600 hover:text-blue-800 cursor-pointer hover:underline"
          @click="viewUserProfile(request)">
          {{ request.user_name }}
        </span>
      </td>
      <td class="px-4 py-2">{{ request.equipment_code || '-' }}</td>
      <td class="px-4 py-2">
        <span class="text-blue-600 hover:text-blue-800 cursor-pointer hover:underline"
          @click="viewEquipmentDetails(request)">
          {{ request.equipment_name }}
        </span>
      </td>


      <td class="px-4 py-2">{{ request.start_at || '-' }}</td>
      <td class="px-4 py-2">{{ request.end_at || '-' }}</td>

      <td class="px-4 py-2">
        <span :class="getStatusClass(request.status)"
          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
          {{ getStatusText(request.status) }}
        </span>
      </td>

      <td class="px-4 py-2">
        <button @click="openDetails(request)" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
          ดูรายละเอียด
        </button>
      </td>
    </template>
  </BaseTable>
</template>

<script>
import BaseTable from "./BaseTable.vue";

export default {
  name: "AdminApproveTable",
  components: { BaseTable },
  props: {
    // Expects an array of request objects
    requests: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      pageSize: 10
    };
  },
  computed: {
    columns() {
      const cols = [
        { key: "req_id", label: "รหัสคำขอ" },
        { key: "user_name", label: "ผู้ขอ" },
        { key: "equipment_name", label: "อุปกรณ์" },
        { key: "equipment_code", label: "รหัสอุปกรณ์" },
        { key: "start_at", label: "วันที่ขอ" },
        { key: "end_at", label: "วันที่ส่ง" },
        { key: "status", label: "สถานะ" },
        { key: "actions", label: "การดำเนินการ" }
      ];
      return cols;
    },
    availableFilters() {
      return [
        {
          key: "status",
          label: "สถานะ",
          type: "select",
          placeholder: "เลือกสถานะ",
          options: [
            { value: "all", label: "ทั้งหมด" },
            { value: "pending", label: "รอดำเนินการ" },
            { value: "approved", label: "อนุมัติแล้ว" },
            { value: "check_out", label: "รับแล้ว" },
            { value: "check_in", label: "คืนแล้ว" },
            { value: "rejected", label: "ปฏิเสธ" },
            { value: "cancelled", label: "ยกเลิก" }
          ]
        }
      ];
    }
  },
  methods: {
    openDetails(request) {
      window.location.href = `/admin/requests/${request.req_id}`;
    },
    viewRequestDetails(request) {
      window.location.href = `/admin/requests/${request.req_id}`;
    },
    viewUserProfile(request) {
      window.location.href = `/admin/report/user?search=${encodeURIComponent(request.user_name || "")}`;
    },
    viewEquipmentDetails(request) {
      window.location.href = `/admin/equipment?search=${encodeURIComponent(request.equipment_name || "")}`;
    },
    getStatusClass(status) {
      if (!status) return "bg-gray-100 text-gray-800";
      switch (status.toLowerCase()) {
        case "pending": return "bg-yellow-100 text-yellow-800";
        case "approved": return "bg-green-100 text-green-800";
        case "check_out": return "bg-blue-100 text-blue-800";
        case "check_in": return "bg-purple-100 text-purple-800";
        case "rejected": return "bg-red-100 text-red-800";
        case "cancelled": return "bg-gray-100 text-gray-800";
        default: return "bg-gray-100 text-gray-800";
      }
    },
    getStatusText(status) {
      if (!status) return "-";
      switch (status.toLowerCase()) {
        case "pending": return "รออนุมัติ";
        case "approved": return "อนุมัติแล้ว";
        case "check_out": return "รับแล้ว";
        case "check_in": return "คืนแล้ว";
        case "rejected": return "ปฏิเสธ";
        case "cancelled": return "ยกเลิก";
        default: return this.capitalize(status);
      }
    },
    capitalize(str) {
      if (!str) return "";
      return str.charAt(0).toUpperCase() + str.slice(1).replace(/_/g, " ");
    }
  }
};
</script>
