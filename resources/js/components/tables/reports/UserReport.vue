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

        <template #cell-actions="{ item }">
            <div class="flex space-x-2">
                <button @click="showUserModal(item)"
                    class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-lg hover:bg-blue-200 transition-colors">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                        </path>
                    </svg>
                    รายละเอียด
                </button>
                <button @click="viewUserLogs(item)"
                    class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-lg hover:bg-green-200 transition-colors">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    ประวัติ
                </button>
            </div>
        </template>

        <!-- Let BaseReportTable handle date formatting -->
    </BaseReportTable>

    <!-- User Details Modal -->
    <div v-if="selectedUser" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">รายละเอียดผู้ใช้</h3>
                <button @click="closeUserModal" 
                    class="text-gray-400 hover:text-gray-600 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">รหัสผู้ใช้</label>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedUser.uid || '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">ชื่อ-นามสกุน</label>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedUser.name || '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">อีเมล</label>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedUser.email || '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">เบอร์โทรศัพท์</label>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedUser.phonenumber || '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">ตำแหน่ง</label>
                    <span :class="getRoleBadgeClass(selectedUser.role)"
                        class="inline-flex px-2 py-1 rounded-full text-xs font-semibold">
                        {{ getRoleLabel(selectedUser.role) }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">วันที่สมัคร</label>
                    <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedUser.created_at) }}</p>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button @click="closeUserModal" 
                    class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    ปิด
                </button>
            </div>
        </div>
    </div>
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
            selectedUser: null,
            columns: [
                { key: 'id', label: 'ไอดี', type: 'number' },
                { key: 'uid', label: 'รหัสผู้ใช้' },
                { key: 'name', label: 'ชื่อ-นามสกุน' },
                { key: 'email', label: 'อีเมล' },
                { key: 'phonenumber', label: 'เบอร์โทรศัพท์' },
                { key: 'role', label: 'ตำแหน่ง', type: 'badge' },
                { key: 'created_at', label: 'วันที่เพิ่ม', type: 'date' },
                { key: 'actions', label: 'การดำเนินการ' }
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
        },
        closeUserModal() {
            this.selectedUser = null;
            // Remove search parameter from URL
            const url = new URL(window.location);
            url.searchParams.delete('search');
            window.history.replaceState({}, '', url);
        },
        viewUserLogs(user = null) {
            const targetUser = user || this.selectedUser;
            if (targetUser) {
                console.log('viewUserLogs - selectedUser:', targetUser.name);
                // Redirect to log report filtered by this user
                const encodedUser = encodeURIComponent(targetUser.name);
                console.log('viewUserLogs - redirecting to:', `/admin/report/log?admin=${encodedUser}`);
                window.location.href = `/admin/report/log?admin=${encodedUser}`;
            }
        },
        showUserModal(user) {
            this.selectedUser = user;
        },
        checkForSearchParameter() {
            const urlParams = new URLSearchParams(window.location.search);
            const searchTerm = urlParams.get('search');
            
            if (searchTerm) {
                console.log('checkForSearchParameter - searching for:', searchTerm);
                console.log('checkForSearchParameter - available users:', this.users.map(u => u.name));
                
                // Find user by exact name match first, then partial matches
                let user = this.users.find(u => u.name.toLowerCase() === searchTerm.toLowerCase());
                
                // If no exact match, try partial matches
                if (!user) {
                    user = this.users.find(u => 
                        u.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                        u.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
                        u.uid.toLowerCase().includes(searchTerm.toLowerCase())
                    );
                }
                
                console.log('checkForSearchParameter - found user:', user ? user.name : 'none');
                
                if (user) {
                    this.selectedUser = user;
                    // Clear the URL parameter to prevent BaseReportTable from filtering
                    const url = new URL(window.location);
                    url.searchParams.delete('search');
                    window.history.replaceState({}, '', url);
                }
            }
        }
    },
    async mounted() {
        await this.fetchUsers();
        this.checkForSearchParameter();
    }
};
</script>
