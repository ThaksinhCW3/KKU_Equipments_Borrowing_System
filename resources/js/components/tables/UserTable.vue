<template>
    <BaseTable :data="users" :columns="columns" :title="'รายการผู้ใช้'"
        :search-placeholder="'ค้นหาด้วยชื่อ, อีเมล, หรือหมายเลขโทรศัพท์...'" :add-button-text="'เพิ่มผู้ใช้ใหม่'"
        :user-role="userRole" :available-filters="availableFilters"
        :search-fields="['name', 'email', 'phonenumber', 'uid', 'role']" :loading="loading" @create="openCreateModal"
        @edit="openModal" @delete="deleteUser">
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
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(user.created_at) }}
            </td>
            <td v-if="userRole === 'admin'" class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex space-x-2">
                    <button @click="actions.edit"
                        class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-3 py-1 rounded-md text-sm font-medium transition-colors">
                        แก้ไข
                    </button>
                    <button @click="actions.delete"
                        class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-3 py-1 rounded-md text-sm font-medium transition-colors">
                        ลบ
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
            userRole: el?.dataset?.role || "admin",
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