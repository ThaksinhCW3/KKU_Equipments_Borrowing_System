<template>
    <BaseReportTable :report-title="'รายงานผู้ใช้'" :report-description="'จัดการและติดตามข้อมูลผู้ใช้ในระบบ'"
        :data="users" :columns="columns" :available-filters="availableFilters"
        :search-placeholder="'ค้นหาด้วยชื่อ, อีเมล, หรือรหัสผู้ใช้...'" :page-size="20" @export="exportUsers">
        <!-- Custom cell templates -->
        <template #cell-role="{ item }">
            <span :class="getRoleBadgeClass(item.role)"
                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                {{ getRoleLabel(item.role) }}
            </span>
        </template>

        <!-- Let BaseReportTable handle date formatting -->
    </BaseReportTable>
</template>

<script>
import api from '../../../api';
import BaseReportTable from './BaseReportTable.vue';

export default {
    name: 'UserReport',
    components: {
        BaseReportTable
    },
    data() {
        return {
            users: [],
            columns: [
                { key: 'id', label: 'ไอดี', type: 'number' },
                { key: 'uid', label: 'รหัสผู้ใช้' },
                { key: 'name', label: 'ชื่อ' },
                { key: 'email', label: 'อีเมล' },
                { key: 'phonenumber', label: 'เบอร์โทรศัพท์' },
                { key: 'role', label: 'ตำแหน่ง', type: 'badge' },
                { key: 'created_at', label: 'วันที่เพิ่ม', type: 'date' }
            ],
            availableFilters: [
                {
                    key: 'role',
                    label: 'ตำแหน่ง',
                    type: 'select',
                    placeholder: 'เลือกตำแหน่ง',
                    options: [
                        { value: 'admin', label: 'ผู้ดูแลระบบ' },
                        { value: 'staff', label: 'เจ้าหน้าที่' },
                        { value: 'borrower', label: 'ผู้ยืม' }
                    ]
                },
                {
                    key: 'created_at',
                    label: 'ช่วงวันที่สมัคร',
                    type: 'daterange',
                    fromPlaceholder: 'วันที่เริ่มต้น',
                    toPlaceholder: 'วันที่สิ้นสุด'
                }
            ]
        };
    },
    methods: {

        async fetchUsers() {
            try {
                console.log('Fetching users from API...');
                const response = await api.get('/api/users');
                console.log('API Response:', response);
                console.log('Users data:', response.data);
                
                // Debug: Log the first user's created_at value
                if (response.data && response.data.length > 0) {
                    console.log('First user created_at value:', response.data[0].created_at);
                    console.log('Type of created_at:', typeof response.data[0].created_at);
                }
                
                this.users = response.data;
            } catch (error) {
                console.error('Failed to fetch users:', error);
                console.error('Error details:', error.response?.data || error.message);
                this.users = [];
            }
        },
        getRoleBadgeClass(role) {
            const classes = {
                'admin': 'bg-purple-100 text-purple-800',
                'staff': 'bg-blue-100 text-blue-800',
                'borrower': 'bg-green-100 text-green-800'
            };
            return classes[role] || 'bg-gray-100 text-gray-800';
        },
        getRoleLabel(role) {
            const labels = {
                'admin': 'ผู้ดูแลระบบ',
                'staff': 'เจ้าหน้าที่',
                'borrower': 'ผู้ยืม'
            };
            return labels[role] || role;
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
        exportUsers(exportParams) {
            const params = new URLSearchParams({
                ...exportParams.filters,
                search: exportParams.search,
                sort: exportParams.sort,
                direction: exportParams.direction
            });

            window.location.href = `/admin/report/export/users?${params.toString()}`;
        }
    },
    mounted() {
        this.fetchUsers();
    }
};
</script>
