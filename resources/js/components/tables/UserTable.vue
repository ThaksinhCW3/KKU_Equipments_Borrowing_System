<template>
    <BaseTable :data="users" :columns="columns" :breadcrumbs="breadcrumbs" :title="'รายการผู้ใช้'"
        :search-placeholder="'ค้นหาด้วยชื่อ, อีเมล, หรือหมายเลขโทรศัพท์...'"
        :user-role="userRole" :available-filters="availableFilters"
        :search-fields="['name', 'email', 'phonenumber', 'uid', 'role', 'status']" :loading="loading" @create="openCreateModal"
        @edit="openModal" @delete="deleteUser" :show-add-button="false">
        <!-- Custom row template -->
        <template #rows="{ item: user, actions }">
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                <div class="text-sm text-gray-500">{{ user.uid }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ user.email }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ user.phonenumber || 'ไม่ระบุ' }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getRoleClass(user.role)">
                    {{ getRoleLabel(user.role) }}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="space-y-1">
                    <span v-if="!user.is_banned" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                        ใช้งานได้
                    </span>
                    <div v-if="user.is_banned" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"></path>
                        </svg>
                        ถูกแบน
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(user.created_at) }}
            </td>
            <td v-if="userRole === 'admin'" class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex space-x-2 flex-wrap">
                    <button @click="actions.edit"
                        class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-3 py-1 rounded-md text-sm font-medium transition-colors">
                        แก้ไข
                    </button>
                    <button @click="actions.delete"
                        class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-3 py-1 rounded-md text-sm font-medium transition-colors">
                        ลบ
                    </button>
                    <!-- Ban/Unban actions (only for non-admin users) -->
                    <button v-if="!user.is_banned && user.role !== 'admin'" @click="banUser(user)"
                        class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-3 py-1 rounded-md text-sm font-medium transition-colors">
                        แบนผู้ใช้
                    </button>
                    <button v-else-if="user.is_banned && user.role !== 'admin'" @click="unbanUser(user)"
                        class="text-green-600 hover:text-green-900 bg-green-100 hover:bg-green-200 px-3 py-1 rounded-md text-sm font-medium transition-colors">
                        ยกเลิกการแบน
                    </button>
                </div>
            </td>
        </template>
    </BaseTable>

    <!-- User Modal -->
    <UserModal :isOpen="isOpen" :mode="modalMode" :user="selectedUser" :user-role="userRole" @close="closeModal"
        @save="updateUser" @create="createUser" />
</template>

<script>
import BaseTable from "./BaseTable.vue";
import UserModal from "../modals/UserModal.vue";
import api from "../../api";

export default {
    name: "UserTable",
    components: {
        BaseTable,
        UserModal,
    },
    data() {
        const el = document.getElementById("user-table");
        return {
            breadcrumbs: [
                { label: 'แดชบอร์ด', url: '/admin' },
                { label: 'จัดการผู้ใช้' }
            ],
            userRole: el?.dataset?.role || "",
            users: [],
            loading: false,

            // Modal state
            isOpen: false,
            modalMode: 'edit',
            selectedUser: null,
        };
    },
    computed: {
        columns() {
            const baseColumns = [
                { key: 'avatar', label: 'รูป' },
                { key: 'name', label: 'ชื่อผู้ใช้' },
                { key: 'email', label: 'อีเมล' },
                { key: 'phonenumber', label: 'เบอร์โทร' },
                { key: 'role', label: 'บทบาท' },
                { key: 'status', label: 'สถานะ' },
                { key: 'created_at', label: 'วันที่สมัคร' },
            ];

            if (this.userRole === 'admin') {
                baseColumns.push({ key: 'actions', label: 'การดำเนินการ' });
            }

            return baseColumns;
        },

        availableFilters() {
            return [
                {
                    key: 'role',
                    label: 'บทบาท',
                    type: 'select',
                    placeholder: 'เลือกบทบาท',
                    options: [
                        { value: 'admin', label: 'ผู้ดูแลระบบ' },
                        { value: 'borrower', label: 'ผู้ยืม' },
                        { value: 'staff', label: 'เจ้าหน้าที่' }
                    ]
                },
                {
                    key: 'is_banned',
                    label: 'สถานะการแบน',
                    type: 'select',
                    placeholder: 'เลือกสถานะการแบน',
                    options: [
                        { value: 'true', label: 'ถูกแบน' },
                        { value: 'false', label: 'ไม่ถูกแบน' }
                    ]
                }
            ];
        }
    },
    methods: {
        // Data fetching
        async fetchUsers() {
            this.loading = true;
            try {
                const response = await api.get('/api/users');
                this.users = response.data || [];
            } catch (error) {
                console.error('Failed to fetch users:', error);
                this.users = [];
                this.showError('ไม่สามารถโหลดข้อมูลผู้ใช้ได้');
            } finally {
                this.loading = false;
            }
        },

        // Role helpers
        getRoleClass(role) {
            switch (role) {
                case "admin":
                    return "bg-red-100 text-red-800";
                case "borrower":
                    return "bg-green-100 text-green-800";
                case "staff":
                    return "bg-yellow-100 text-yellow-800";
                default:
                    return "bg-gray-100 text-gray-800";
            }
        },

        getRoleLabel(role) {
            switch (role) {
                case "admin":
                    return "ผู้ดูแลระบบ";
                case "staff":
                    return "เจ้าหน้าที่";
                case "borrower":
                    return "ผู้ยืม";
                default:
                    return "ไม่ระบุ";
            }
        },

        // Date formatting
        formatDate(dateString) {
            if (!dateString) return 'ไม่ระบุ';
            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('th-TH', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch (e) {
                return 'ไม่ระบุ';
            }
        },

        // Modal methods
        openCreateModal() {
            this.modalMode = 'create';
            this.selectedUser = null;
            this.isOpen = true;
        },

        openModal(user) {
            this.modalMode = 'edit';
            this.selectedUser = { ...user };
            this.isOpen = true;
        },

        closeModal() {
            this.isOpen = false;
            this.selectedUser = null;
        },

        // API methods
        async updateUser(payload) {
            try {
                const response = await api.put(`/api/users/${payload.id}`, payload);
                const updated = response.data;

                const idx = this.users.findIndex((u) => u.id === updated.id);
                if (idx !== -1) {
                    this.users.splice(idx, 1, updated);
                }

                this.closeModal();
                this.showSuccess(`อัปเดตสำเร็จ: ${payload.name}`);
            } catch (error) {
                console.error('Update user failed:', error);
                this.showError(error.response?.data?.message || 'ไม่สามารถอัปเดตผู้ใช้ได้');
            }
        },

        async createUser(payload) {
            try {
                const response = await api.post('/api/users', payload);
                const created = response.data;

                this.users.unshift(created);
                this.closeModal();
                this.showSuccess(`เพิ่มผู้ใช้สำเร็จ: ${payload.name}`);
            } catch (error) {
                console.error('Create user failed:', error);
                this.showError(error.response?.data?.message || 'ไม่สามารถเพิ่มผู้ใช้ได้');
            }
        },

        deleteUser(user) {
            this.ensureSwal().then(() => {
                window.Swal.fire({
                    title: "ลบผู้ใช้?",
                    text: `คุณกำลังจะลบ: ${user.name} (${user.email})`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "ลบ",
                    cancelButtonText: "ยกเลิก",
                    confirmButtonColor: "#ef4444",
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            await api.delete(`/api/users/${user.id}`);
                            this.users = this.users.filter((u) => u.id !== user.id);
                            this.showSuccess(`${user.name} ถูกลบเรียบร้อย`);
                        } catch (error) {
                            console.error('Delete user failed:', error);
                            this.showError(error.response?.data?.message || 'ลบผู้ใช้ไม่สำเร็จ');
                        }
                    }
                });
            });
        },

        // Ban/Unban methods
        banUser(user) {
            this.ensureSwal().then(() => {
                window.Swal.fire({
                    title: 'แบนผู้ใช้',
                    html: `
                        <div class="text-left space-y-2">
                            <p class="text-red-600 font-medium">คุณกำลังจะแบนผู้ใช้: <strong>${user.name}</strong></p>
                            <p class="text-sm text-gray-600">การแบนจะทำให้ผู้ใช้ไม่สามารถเข้าถึงระบบได้</p>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2"><input type="radio" name="ban-reason" value="ละเมิดกฎระเบียบของระบบ"> ละเมิดกฎระเบียบของระบบ</label>
                                <label class="flex items-center gap-2"><input type="radio" name="ban-reason" value="พฤติกรรมไม่เหมาะสม"> พฤติกรรมไม่เหมาะสม</label>
                                <label class="flex items-center gap-2"><input type="radio" name="ban-reason" value="ส่งสแปมหรือเนื้อหาที่ไม่เหมาะสม"> ส่งสแปมหรือเนื้อหาที่ไม่เหมาะสม</label>
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
                        this.processBan(user, result.value);
                    }
                });
            });
        },

        unbanUser(user) {
            this.ensureSwal().then(() => {
                window.Swal.fire({
                    title: 'ยกเลิกการแบนผู้ใช้',
                    html: `
                        <div class="text-left space-y-2">
                            <p class="text-green-600 font-medium">คุณกำลังจะยกเลิกการแบนผู้ใช้: <strong>${user.name}</strong></p>
                            <p class="text-sm text-gray-600">การยกเลิกการแบนจะทำให้ผู้ใช้สามารถเข้าถึงระบบได้อีกครั้ง</p>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                <p class="text-sm text-yellow-800">
                                    <strong>เหตุผลที่ถูกแบน:</strong> ${user.ban_reason || 'ไม่ระบุ'}
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
                        this.processUnban(user);
                    }
                });
            });
        },

        // API processing methods for ban/unban
        async processBan(user, reason) {
            try {
                const response = await api.post(`/api/users/${user.id}/ban`, {
                    ban_reason: reason
                });
                
                if (response.status >= 200 && response.status < 300) {
                    this.showSuccess('แบนผู้ใช้เรียบร้อยแล้ว');
                    this.fetchUsers();
                } else {
                    throw new Error('Ban failed');
                }
            } catch (error) {
                console.error('Ban user failed:', error);
                this.showError('เกิดข้อผิดพลาดในการแบนผู้ใช้');
            }
        },

        async processUnban(user) {
            try {
                const response = await api.post(`/api/users/${user.id}/unban`);
                
                if (response.status >= 200 && response.status < 300) {
                    this.showSuccess('ยกเลิกการแบนผู้ใช้เรียบร้อยแล้ว');
                    this.fetchUsers();
                } else {
                    throw new Error('Unban failed');
                }
            } catch (error) {
                console.error('Unban user failed:', error);
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
        this.fetchUsers();
    }
};
</script>