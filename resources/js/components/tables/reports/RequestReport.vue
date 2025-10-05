<template>
    <BaseReportTable
        :report-title="'รายงานคำร้องขอยืม'"
        :report-description="'ติดตามและวิเคราะห์คำร้องขอยืมอุปกรณ์ทั้งหมด'"
        :data="requests"
        :columns="columns"
        :available-filters="availableFilters"
        :search-placeholder="'ค้นหาด้วยรหัสคำร้อง, ชื่อผู้ใช้, หรือชื่ออุปกรณ์...'"
        :page-size="20"
        @export="exportRequests"
    >
        <!-- Custom cell templates -->
        <template #cell-status="{ item }">
            <span :class="getStatusBadgeClass(item.status)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                {{ getStatusLabel(item.status) }}
            </span>
        </template>

        <template #cell-start_at="{ item }">
            {{ formatDate(item.start_at) }}
        </template>

        <template #cell-end_at="{ item }">
            {{ formatDate(item.end_at) }}
        </template>

        <template #cell-created_at="{ item }">
            {{ formatDate(item.created_at) }}
        </template>

        <template #cell-reject_reason="{ item }">
            <span v-if="item.reject_reason" class="text-red-600">{{ item.reject_reason }}</span>
            <span v-else class="text-gray-400">ไม่มี</span>
        </template>

        <template #cell-cancel_reason="{ item }">
            <span v-if="item.cancel_reason" class="text-orange-600">{{ item.cancel_reason }}</span>
            <span v-else class="text-gray-400">ไม่มี</span>
        </template>

        <template #cell-req_id="{ item }">
            <span class="text-blue-600 hover:text-blue-800 cursor-pointer hover:underline" 
                  @click="viewRequestDetails(item)">
                {{ item.req_id }}
            </span>
        </template>

        <template #cell-user_name="{ item }">
            <span class="text-blue-600 hover:text-blue-800 cursor-pointer hover:underline" 
                  @click="viewUserRequests(item.user_name)">
                {{ item.user_name }}
            </span>
        </template>

        <template #cell-equipment_name="{ item }">
            <span class="text-blue-600 hover:text-blue-800 cursor-pointer hover:underline" 
                  @click="viewEquipmentDetails(item.equipment_name)">
                {{ item.equipment_name }}
            </span>
        </template>
    </BaseReportTable>
</template>

<script>
import api from '../../../api';
import BaseReportTable from './BaseReportTable.vue';

export default {
    name: 'RequestReport',
    components: {
        BaseReportTable
    },
    data() {
        return {
            requests: [],
            columns: [
                { key: 'id', label: 'ไอดี', type: 'number' },
                { key: 'req_id', label: 'รหัสคำร้อง' },
                { key: 'user_name', label: 'ชื่อผู้ใช้' },
                { key: 'equipment_name', label: 'ชื่ออุปกรณ์' },
                { key: 'start_at', label: 'เริ่มวันที่', type: 'date' },
                { key: 'end_at', label: 'ถึงวันที่', type: 'date' },
                { key: 'status', label: 'สถานะ', type: 'badge' },
                { key: 'reject_reason', label: 'สาเหตุการปฏิเสธ' },
                { key: 'cancel_reason', label: 'สาเหตุการยกเลิก' },
                { key: 'created_at', label: 'วันที่เพิ่ม', type: 'date' }
            ],
            availableFilters: [
                {
                    key: 'status',
                    label: 'สถานะ',
                    type: 'select',
                    placeholder: 'เลือกสถานะ',
                    options: [
                        { value: 'pending', label: 'รอดำเนินการ' },
                        { value: 'approved', label: 'อนุมัติแล้ว' },
                        { value: 'rejected', label: 'ปฏิเสธ' },
                        { value: 'check_out', label: 'ยืมออกแล้ว' },
                        { value: 'check_in', label: 'คืนแล้ว' },
                        { value: 'cancelled', label: 'ยกเลิก' }
                    ]
                },
                {
                    key: 'user_name',
                    label: 'ชื่อผู้ใช้',
                    type: 'text',
                    placeholder: 'ค้นหาด้วยชื่อผู้ใช้'
                },
                {
                    key: 'equipment_name',
                    label: 'ชื่ออุปกรณ์',
                    type: 'text',
                    placeholder: 'ค้นหาด้วยชื่ออุปกรณ์'
                },
                {
                    key: 'created_at',
                    label: 'ช่วงวันที่สร้าง',
                    type: 'daterange',
                    fromPlaceholder: 'วันที่เริ่มต้น',
                    toPlaceholder: 'วันที่สิ้นสุด'
                }
            ]
        };
    },
    methods: {
        async fetchRequests() {
            try {
                const response = await api.get('/api/requests');
                this.requests = response.data;
            } catch (error) {
                console.error('Failed to fetch Requests:', error);
                this.requests = [];
            }
        },
        getStatusBadgeClass(status) {
            const classes = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'approved': 'bg-green-100 text-green-800',
                'rejected': 'bg-red-100 text-red-800',
                'check_out': 'bg-blue-100 text-blue-800',
                'check_in': 'bg-gray-100 text-gray-800',
                'cancelled': 'bg-orange-100 text-orange-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },
        getStatusLabel(status) {
            const labels = {
                'pending': 'รอดำเนินการ',
                'approved': 'อนุมัติแล้ว',
                'rejected': 'ปฏิเสธ',
                'check_out': 'ยืมออกแล้ว',
                'check_in': 'คืนแล้ว',
                'cancelled': 'ยกเลิก'
            };
            return labels[status] || status;
        },
        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('th-TH', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        exportRequests(exportParams) {
            const params = new URLSearchParams({
                ...exportParams.filters,
                search: exportParams.search,
                sort: exportParams.sort,
                direction: exportParams.direction
            });

            window.location.href = `/admin/report/export/requests?${params.toString()}`;
        },
        viewRequestDetails(request) {
            // Redirect to request details page
            window.location.href = `/admin/requests/${request.req_id}`;
        },
        viewUserRequests(userName) {
            // Redirect to user report page filtered by this user
            window.location.href = `/admin/report/user?search=${encodeURIComponent(userName)}`;
        },
        viewEquipmentDetails(equipmentName) {
            // Redirect to equipment page filtered by this equipment
            window.location.href = `/admin/equipment?search=${encodeURIComponent(equipmentName)}`;
        }
    },
    mounted() {
        this.fetchRequests();
    }
};
</script>
