<template>
    <BaseReportTable
        :breadcrumbs="breadcrumbs"
        :report-title="'รายงานอุปกรณ์'"
        :report-description="'จัดการและติดตามข้อมูลอุปกรณ์ทั้งหมดในระบบ'"
        :data="equipments"
        :columns="columns"
        :available-filters="availableFilters"
        :search-placeholder="'ค้นหาด้วยชื่ออุปกรณ์, หมายเลขครุภัณฑ์, หรือหมวดหมู่...'"
        :page-size="20"
        @export="exportEquipments"
    >
        <!-- Custom cell templates -->
        <template #cell-status="{ item }">
            <span :class="getStatusBadgeClass(item.status)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                {{ getStatusLabel(item.status) }}
                </span>
        </template>

        <template #cell-category="{ item }">
            <span v-if="item.category?.name" 
                  class="text-blue-600 hover:text-blue-800 cursor-pointer hover:underline" 
                  @click="viewEquipmentByCategory(item.category)">
                {{ item.category.name }}
            </span>
            <span v-else class="text-gray-400">ไม่มีหมวดหมู่</span>
        </template>

        <template #cell-name="{ item }">
            <span class="text-blue-600 hover:text-blue-800 cursor-pointer hover:underline" 
                  @click="viewEquipmentDetails(item)">
                {{ item.name }}
            </span>
        </template>

        <template #cell-code="{ item }">
            <span class="text-blue-600 hover:text-blue-800 cursor-pointer hover:underline" 
                  @click="viewEquipmentDetails(item)">
                {{ item.code }}
            </span>
        </template>

        <template #cell-created_at="{ item }">
            {{ formatDate(item.created_at) }}
        </template>
    </BaseReportTable>
</template>

<script>
import api from '../../../api';
import BaseReportTable from './BaseReportTable.vue';

export default {
    name: 'EquipmentReport',
    components: {
        BaseReportTable
    },
    data() {
        return {
            breadcrumbs: [
                { label: 'แดชบอร์ด', url: '/admin' },
                { label: 'รายงาน', url: '/admin/report' },
                { label: 'รายงานอุปกรณ์' }
            ],
            equipments: [],
            categories: [],
            columns: [
                { key: 'id', label: 'ไอดี', type: 'number' },
                { key: 'code', label: 'หมายเลขครุภัณฑ์' },
                { key: 'name', label: 'ชื่ออุปกรณ์' },
                { key: 'category.name', label: 'หมวดหมู่' },
                { key: 'status', label: 'สถานะ', type: 'badge' },
                { key: 'created_at', label: 'วันที่เพิ่ม', type: 'date' }
            ],
            availableFilters: [
                {
                    key: 'status',
                    label: 'สถานะ',
                    type: 'select',
                    placeholder: 'เลือกสถานะ',
                    options: [
                        { value: 'available', label: 'พร้อมใช้งาน' },
                        { value: 'unavailable', label: 'ถูกยืม' },
                        { value: 'retired', label: 'รอคืน' },
                        { value: 'maintenance', label: 'ซ่อมบำรุง' },
                    ]
                },
                {
                    key: 'category.name',
                    label: 'หมวดหมู่',
                    type: 'select',
                    placeholder: 'เลือกหมวดหมู่',
                    options: []
                },
                {
                    key: 'created_at',
                    label: 'ช่วงวันที่เพิ่ม',
                    type: 'daterange',
                    fromPlaceholder: 'วันที่เริ่มต้น',
                    toPlaceholder: 'วันที่สิ้นสุด'
                }
            ]
        };
    },
    methods: {
        async fetchEquipments() {
            try {
                 const response = await api.get('/api/equipments');
                 // Equipments API returns a simple array
                this.equipments = response.data;
            } catch (error) {
                console.error('Failed to fetch equipments:', error);
                 this.equipments = [];
            }
        },
        async fetchCategories() {
            try {
                const response = await api.get('/api/categories');
                this.categories = response.data;
                // Update category filter options
                this.availableFilters.find(f => f.key === 'category.name').options = 
                    this.categories.map(cat => ({
                        value: cat.name,
                        label: cat.name
                    }));
            } catch (error) {
                console.error('Failed to fetch categories:', error);
                this.categories = [];
            }
        },
        getStatusBadgeClass(status) {
            const classes = {
                'available': 'bg-green-100 text-green-800',
                'unavailable': 'bg-red-100 text-red-800',
                'maintenance': 'bg-yellow-100 text-yellow-800',
                'retired': 'bg-red-100 text-red-800',
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },
        getStatusLabel(status) {
            const labels = {
                'available': 'พร้อมใช้งาน',
                'unavailable': 'ถูกยืม',
                'maintenance': 'ซ่อมบำรุง',
                'retired': 'รอคืน',
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
        exportEquipments(exportParams) {
            const params = new URLSearchParams({
                ...exportParams.filters,
                search: exportParams.search,
                sort: exportParams.sort,
                direction: exportParams.direction
            });

            window.location.href = `/admin/report/export/equipments?${params.toString()}`;
        },
        viewEquipmentDetails(equipment) {
            // Redirect to equipment management page with search for this equipment
            window.location.href = `/admin/equipment?search=${encodeURIComponent(equipment.name)}`;
        },
        viewEquipmentByCategory(category) {
            // Redirect to equipment management page filtered by this category
            window.location.href = `/admin/equipment?category=${encodeURIComponent(category.name)}&category_id=${category.id}`;
        }
    },
    mounted() {
        this.fetchEquipments();
        this.fetchCategories();
    }
}
</script>
