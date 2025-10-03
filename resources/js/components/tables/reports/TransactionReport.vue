<template>
    <BaseReportTable :report-title="'รายงานธุรกรรม'" :report-description="'ติดตามและวิเคราะห์ธุรกรรมทั้งหมดในระบบ'"
        :data="transactions" :columns="columns" :available-filters="availableFilters"
        :search-placeholder="'ค้นหาด้วยรหัสธุรกรรม, ชื่อผู้ใช้, หรือชื่ออุปกรณ์...'" :page-size="20"
        @export="exportTransactions">
        <!-- Custom cell templates -->
        <template #cell-transaction_type="{ item }">
            <span :class="getTransactionTypeBadgeClass(item.transaction_type)"
                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                {{ getTransactionTypeLabel(item.transaction_type) }}
            </span>
        </template>

        <template #cell-status="{ item }">
            <span :class="getStatusBadgeClass(item.status)"
                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                {{ getStatusLabel(item.status) }}
            </span>
        </template>

        <template #cell-amount="{ item }">
            <span class="font-medium" :class="getAmountClass(item.transaction_type)">
                {{ formatAmount(item.amount) }}
            </span>
        </template>

        <template #cell-created_at="{ item }">
            {{ formatDate(item.created_at) }}
        </template>

        <template #cell-actions="{ item }">
            <button @click="showTransactionDetails(item)"
                class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-lg hover:bg-blue-200 transition-colors">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                    </path>
                </svg>
                รายละเอียด
            </button>
        </template>
    </BaseReportTable>

    <!-- Transaction Details Modal -->
    <div v-if="selectedTransaction" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">รายละเอียดธุรกรรม</h3>
                <button @click="selectedTransaction = null" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">รหัสธุรกรรม</label>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedTransaction.transaction_id }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">ประเภทธุรกรรม</label>
                    <span :class="getTransactionTypeBadgeClass(selectedTransaction.transaction_type)"
                        class="inline-block px-2 py-1 rounded-full text-xs">
                        {{ getTransactionTypeLabel(selectedTransaction.transaction_type) }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">ผู้ใช้</label>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedTransaction.user?.name || 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">อุปกรณ์</label>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedTransaction.equipment?.name || 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">สถานะ</label>
                    <span :class="getStatusBadgeClass(selectedTransaction.status)"
                        class="inline-block px-2 py-1 rounded-full text-xs">
                        {{ getStatusLabel(selectedTransaction.status) }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">จำนวนเงิน</label>
                    <p class="mt-1 text-sm font-medium" :class="getAmountClass(selectedTransaction.transaction_type)">
                        {{ formatAmount(selectedTransaction.amount) }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">วันที่เริ่มต้น</label>
                    <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedTransaction.start_date) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">วันที่สิ้นสุด</label>
                    <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedTransaction.end_date) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">วันที่สร้าง</label>
                    <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedTransaction.created_at) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">วันที่อัปเดต</label>
                    <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedTransaction.updated_at) }}</p>
                </div>
            </div>

            <div v-if="selectedTransaction.description" class="mt-4">
                <label class="block text-sm font-medium text-gray-700">รายละเอียด</label>
                <p class="mt-1 text-sm text-gray-900">{{ selectedTransaction.description }}</p>
            </div>

            <div v-if="selectedTransaction.notes" class="mt-4">
                <label class="block text-sm font-medium text-gray-700">หมายเหตุ</label>
                <p class="mt-1 text-sm text-gray-900">{{ selectedTransaction.notes }}</p>
            </div>
        </div>
    </div>
</template>

<script>
import api from '../../../api';
import BaseReportTable from './BaseReportTable.vue';

export default {
    name: 'TransactionReport',
    components: {
        BaseReportTable
    },
    data() {
        return {
            transactions: [],
            selectedTransaction: null,
            columns: [
                { key: 'id', label: 'ไอดี', type: 'number' },
                { key: 'transaction_id', label: 'รหัสธุรกรรม' },
                { key: 'transaction_type', label: 'ประเภท', type: 'badge' },
                { key: 'user.name', label: 'ผู้ใช้' },
                { key: 'equipment.name', label: 'อุปกรณ์' },
                { key: 'status', label: 'สถานะ', type: 'badge' },
                { key: 'amount', label: 'จำนวนเงิน' },
                { key: 'start_date', label: 'เริ่มวันที่', type: 'date' },
                { key: 'end_date', label: 'ถึงวันที่', type: 'date' },
                { key: 'created_at', label: 'วันที่สร้าง', type: 'date' },
                { key: 'actions', label: 'การดำเนินการ' }
            ],
            availableFilters: [
                {
                    key: 'transaction_type',
                    label: 'ประเภทธุรกรรม',
                    type: 'select',
                    placeholder: 'เลือกประเภทธุรกรรม',
                    options: [
                        { value: 'borrow', label: 'การยืม' },
                        { value: 'return', label: 'การคืน' },
                        { value: 'penalty', label: 'ค่าปรับ' },
                        { value: 'maintenance', label: 'ค่าซ่อมบำรุง' },
                        { value: 'deposit', label: 'เงินมัดจำ' },
                        { value: 'refund', label: 'เงินคืน' }
                    ]
                },
                {
                    key: 'status',
                    label: 'สถานะ',
                    type: 'select',
                    placeholder: 'เลือกสถานะ',
                    options: [
                        { value: 'pending', label: 'รอดำเนินการ' },
                        { value: 'completed', label: 'เสร็จสิ้น' },
                        { value: 'cancelled', label: 'ยกเลิก' },
                        { value: 'failed', label: 'ล้มเหลว' },
                        { value: 'refunded', label: 'คืนเงินแล้ว' }
                    ]
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
        async fetchTransactions() {
            try {
                const response = await api.get('/api/transactions');
                this.transactions = response.data.data || response.data || [];
            } catch (error) {
                console.error('Failed to fetch transactions:', error);
                this.transactions = [];
            }
        },
        getTransactionTypeBadgeClass(type) {
            const classes = {
                'borrow': 'bg-blue-100 text-blue-800',
                'return': 'bg-green-100 text-green-800',
                'penalty': 'bg-red-100 text-red-800',
                'maintenance': 'bg-yellow-100 text-yellow-800',
                'deposit': 'bg-purple-100 text-purple-800',
                'refund': 'bg-orange-100 text-orange-800'
            };
            return classes[type] || 'bg-gray-100 text-gray-800';
        },
        getTransactionTypeLabel(type) {
            const labels = {
                'borrow': 'การยืม',
                'return': 'การคืน',
                'penalty': 'ค่าปรับ',
                'maintenance': 'ค่าซ่อมบำรุง',
                'deposit': 'เงินมัดจำ',
                'refund': 'เงินคืน'
            };
            return labels[type] || type;
        },
        getStatusBadgeClass(status) {
            const classes = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'completed': 'bg-green-100 text-green-800',
                'cancelled': 'bg-red-100 text-red-800',
                'failed': 'bg-red-100 text-red-800',
                'refunded': 'bg-orange-100 text-orange-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },
        getStatusLabel(status) {
            const labels = {
                'pending': 'รอดำเนินการ',
                'completed': 'เสร็จสิ้น',
                'cancelled': 'ยกเลิก',
                'failed': 'ล้มเหลว',
                'refunded': 'คืนเงินแล้ว'
            };
            return labels[status] || status;
        },
        getAmountClass(type) {
            const classes = {
                'borrow': 'text-blue-600',
                'return': 'text-green-600',
                'penalty': 'text-red-600',
                'maintenance': 'text-yellow-600',
                'deposit': 'text-purple-600',
                'refund': 'text-orange-600'
            };
            return classes[type] || 'text-gray-600';
        },
        formatAmount(amount) {
            if (amount === null || amount === undefined) return '-';
            return new Intl.NumberFormat('th-TH', {
                style: 'currency',
                currency: 'THB'
            }).format(amount);
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
        showTransactionDetails(transaction) {
            this.selectedTransaction = transaction;
        },
        exportTransactions(exportParams) {
            const params = new URLSearchParams({
                ...exportParams.filters,
                search: exportParams.search,
                sort: exportParams.sort,
                direction: exportParams.direction
            });

            window.location.href = `/admin/report/export/transactions?${params.toString()}`;
        }
    },
    mounted() {
        this.fetchTransactions();
    }
};
</script>
