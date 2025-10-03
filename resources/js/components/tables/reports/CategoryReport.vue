<template>
    <BaseReportTable :report-title="'รายงานหมวดหมู่อุปกรณ์'"
        :report-description="'จัดการและติดตามหมวดหมู่อุปกรณ์ทั้งหมดในระบบ'" :data="categories" :columns="columns"
        :available-filters="availableFilters" :search-placeholder="'ค้นหาด้วยชื่อหมวดหมู่หรือรหัสหมวดหมู่...'"
        :page-size="20" @export="exportCategories">
        <!-- Custom cell templates -->
        <template #cell-equipments_count="{ item }">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                {{ item.equipments_count || 0 }} รายการ
            </span>
        </template>

        <template #cell-created_at="{ item }">
            {{ formatDate(item.created_at) }}
        </template>

        <template #cell-updated_at="{ item }">
            {{ formatDate(item.updated_at) }}
        </template>
    </BaseReportTable>
</template>

<script>
import api from '../../../api';
import BaseReportTable from './BaseReportTable.vue';

export default {
    name: 'CategoryReport',
    components: {
        BaseReportTable
    },
    data() {
        return {
            categories: [],
            columns: [
                { key: 'id', label: 'ไอดี', type: 'number' },
                { key: 'cate_id', label: 'รหัสหมวดหมู่' },
                { key: 'name', label: 'ชื่อหมวดหมู่' },
                { key: 'equipments_count', label: 'จำนวนอุปกรณ์', type: 'number' },
                { key: 'created_at', label: 'วันที่เพิ่ม', type: 'date' },
            ],
            availableFilters: [
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
        async fetchCategories() {
            try {
                const response = await api.get('/api/categories');
                this.categories = response.data;
            } catch (error) {
                console.error('Failed to fetch categories:', error);
                this.categories = [];
            }
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
        exportCategories(exportParams) {
            const sortMap = {
                cate_id: "code",
                name: "name"
            };

            const params = new URLSearchParams({
                ...exportParams.filters,
                search: exportParams.search,
                sort: sortMap[exportParams.sort] || "name",
                direction: exportParams.direction
            });

            window.location.href = `/admin/report/export/categories?${params.toString()}`;
        }
    },
    mounted() {
        this.fetchCategories();
    }
}
</script>
