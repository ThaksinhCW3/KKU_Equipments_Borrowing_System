<template>
    <BaseReportTable ref="baseTable" :report-title="'รายงานกิจกรรมระบบ'" :report-description="'ติดตามและวิเคราะห์กิจกรรมต่างๆ ในระบบ'"
        :data="logs" :columns="columns" :available-filters="availableFilters"
        :search-placeholder="'ค้นหาด้วยชื่อผู้ใช้, การดำเนินการ, เป้าหมาย, หรือรายละเอียด...'" :page-size="20"
        @export="exportLogs" @filter-change="onFilterChange">
        
        <!-- Quick Filter Buttons -->
        <template #quick-filters>
            <div class="flex flex-wrap gap-2 mb-4">
                <button @click="applyQuickFilter('action', 'create')" 
                    class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full hover:bg-green-200">
                    สร้าง
                </button>
                <button @click="applyQuickFilter('action', 'update')" 
                    class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full hover:bg-blue-200">
                    แก้ไข
                </button>
                <button @click="applyQuickFilter('action', 'delete')" 
                    class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 text-sm rounded-full hover:bg-red-200">
                    ลบ
                </button>
                <button @click="applyQuickFilter('target_type', 'equipment')" 
                    class="inline-flex items-center px-3 py-1 bg-indigo-100 text-indigo-800 text-sm rounded-full hover:bg-indigo-200">
                    อุปกรณ์
                </button>
                <button @click="clearAllFilters" 
                    class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full hover:bg-gray-200">
                    ล้างทั้งหมด
                </button>
            </div>
        </template>
        <!-- Custom cell templates -->
        <template #cell-action="{ item }">
            <span :class="getActionBadgeClass(item.action)"
                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full cursor-pointer hover:opacity-80 transition-opacity"
                @click="filterByAction(item.action)"
                :title="`คลิกเพื่อกรองตามการดำเนินการ: ${item.action}`">
                {{ item.action || '-' }}
            </span>
        </template>

        <template #cell-severity="{ item }">
            <span :class="getSeverityBadgeClass(item.severity)"
                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                {{ item.severity || 'info' }}
            </span>
        </template>

        <template #cell-target_type="{ item }">
            <span :class="getTargetTypeBadgeClass(item.target_type)"
                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full cursor-pointer hover:opacity-80 transition-opacity"
                @click="filterByTargetType(item.target_type)"
                :title="`คลิกเพื่อกรองตามประเภท: ${getTargetTypeLabel(item.target_type)}`">
                {{ getTargetTypeLabel(item.target_type) }}
            </span>
        </template>

        <template #cell-target_name="{ item }">
            <span v-if="item.target_name"
                  class="text-blue-600 hover:text-blue-800 cursor-pointer hover:underline font-medium"
                  @click="viewTargetDetails(item)"
                  :title="`คลิกเพื่อดูรายละเอียด: ${item.target_name}`">
                {{ item.target_name }}
            </span>
            <span v-else class="text-gray-400">-</span>
        </template>

        <!-- User Name Column -->
        <template #cell-admin.name="{ item }">
            <span v-if="item.admin && item.admin.name"
                  class="text-blue-600 hover:text-blue-800 cursor-pointer hover:underline font-medium"
                  @click="viewAdminDetails(item.admin_id)"
                  :title="`คลิกเพื่อดูรายละเอียดผู้ดูแล: ${item.admin.name}`">
                {{ item.admin.name }}
            </span>
            <span v-else class="text-gray-400">-</span>
        </template>

        <template #cell-admin_id="{ item }">
            <span v-if="item.admin_id"
                  class="text-blue-600 hover:text-blue-800 cursor-pointer hover:underline font-medium"
                  @click="viewAdminDetails(item.admin_id)"
                  :title="`คลิกเพื่อดูรายละเอียดผู้ดูแล: ${item.admin_id}`">
                {{ item.admin_id }}
            </span>
            <span v-else class="text-gray-400">-</span>
        </template>

        <template #cell-description="{ item }">
            <div class="max-w-xs truncate" :title="item.description || item.formatted_description">
                <div v-html="formatDescriptionWithLinks(item.description || item.formatted_description)"></div>
            </div>
        </template>

        <template #cell-actions="{ item }">
            <button @click="showLogDetails(item)"
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

    <!-- Log Details Modal -->
    <div v-if="selectedLog" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">รายละเอียดบันทึก</h3>
                <button @click="selectedLog = null" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">รหัส</label>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedLog.id }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">ผู้ใช้</label>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedLog.admin?.name || 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">การดำเนินการ</label>
                    <span :class="getActionBadgeClass(selectedLog.action)"
                        class="inline-block px-2 py-1 rounded-full text-xs">
                        {{ selectedLog.action || '-' }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">โมดูล</label>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedLog.module || '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">ประเภทเป้าหมาย</label>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedLog.target_type || '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">ชื่อเป้าหมาย</label>
                    <span v-if="selectedLog.target_name"
                          class="text-blue-600 hover:text-blue-800 cursor-pointer hover:underline font-medium"
                          @click="viewTargetDetails(selectedLog)"
                          :title="`คลิกเพื่อดูรายละเอียด: ${selectedLog.target_name}`">
                        {{ selectedLog.target_name }}
                    </span>
                    <span v-else class="text-gray-400">-</span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">ระดับความสำคัญ</label>
                    <span :class="getSeverityBadgeClass(selectedLog.severity)"
                        class="inline-block px-2 py-1 rounded-full text-xs">
                        {{ selectedLog.severity || 'info' }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">ที่อยู่ IP</label>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedLog.ip_address || '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">User Agent</label>
                    <p class="mt-1 text-sm text-gray-900 break-all">{{ selectedLog.user_agent || '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">วันที่สร้าง</label>
                    <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedLog.created_at) }}</p>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">รายละเอียด</label>
                <div class="mt-1 text-sm text-gray-900"
                    v-html="formatDescriptionWithLinks(selectedLog.description || selectedLog.formatted_description)">
                </div>
            </div>

            <div v-if="selectedLog.old_values && Object.keys(selectedLog.old_values).length > 0" class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">ค่าก่อนหน้า</label>
                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                    <div v-for="(value, key) in selectedLog.old_values" :key="key" class="mb-2 last:mb-0">
                        <span class="font-medium text-red-700">{{ formatFieldName(key) }}:</span>
                        <span class="ml-2 text-red-900" v-html="formatFieldValueWithLinks(value)"></span>
                    </div>
                </div>
            </div>

            <div v-if="selectedLog.new_values && Object.keys(selectedLog.new_values).length > 0" class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">ค่าใหม่</label>
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <div v-for="(value, key) in selectedLog.new_values" :key="key" class="mb-2 last:mb-0">
                        <span class="font-medium text-green-700">{{ formatFieldName(key) }}:</span>
                        <span class="ml-2 text-green-900" v-html="formatFieldValueWithLinks(value)"></span>
                    </div>
                </div>
            </div>

            <div v-if="selectedLog.request_data && Object.keys(selectedLog.request_data).length > 0" class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">ข้อมูลคำขอ</label>
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <div v-for="(value, key) in selectedLog.request_data" :key="key" class="mb-2 last:mb-0">
                        <span class="font-medium text-blue-700">{{ formatFieldName(key) }}:</span>
                        <span class="ml-2 text-blue-900" v-html="formatFieldValueWithLinks(value)"></span>
                    </div>
                </div>
            </div>

            <div v-if="selectedLog.notes" class="mt-4">
                <label class="block text-sm font-medium text-gray-700">หมายเหตุ</label>
                <p class="mt-1 text-sm text-gray-900">{{ selectedLog.notes }}</p>
            </div>
        </div>
    </div>
</template>

<script>
import api from '../../../api';
import BaseReportTable from './BaseReportTable.vue'

export default {
    name: "LogReport",
    components: {
        BaseReportTable
    },
    data() {
        return {
            logs: [],
            selectedLog: null,
            // Define relationships between actions and target types (based on actual data)
            actionTargetTypeMap: {
                // Equipment actions
                'create': ['equipment', 'borrow_request'], // Create can be equipment OR borrow_request
                'update': ['equipment'],
                'delete': ['equipment'],
                
                // Borrow request actions
                'approve': ['borrow_request'],
                'reject': ['borrow_request'],
                'cancel': ['borrow_request'],
                'check_out': ['borrow_request'],
                'check_in': ['borrow_request'],
                
                // User actions (login/logout are always user, not system)
                'login': ['user'],
                'logout': ['user'],
                
                // System actions (if they exist)
                'export_initiated': ['system'],
                'export_completed': ['system'],
                'export_failed': ['system']
            },
            // Define relationships between target types and actions (based on actual data)
            targetTypeActionMap: {
                'equipment': ['create', 'update', 'delete'],
                'category': ['create', 'update', 'delete'], // Category actions may exist
                'user': ['login', 'logout'],
                'borrow_request': ['create', 'approve', 'reject', 'cancel', 'check_out', 'check_in'],
                'system': ['export_initiated', 'export_completed', 'export_failed']
            },
            columns: [
                { key: 'id', label: 'รหัส', type: 'number' },
                { key: 'admin.name', label: 'ผู้ใช้' },
                { key: 'action', label: 'การดำเนินการ', type: 'badge' },
                { key: 'target_type', label: 'ประเภทเป้าหมาย' },
                { key: 'target_name', label: 'เป้าหมาย' },
                { key: 'module', label: 'โมดูล' },
                { key: 'severity', label: 'ระดับความสำคัญ', type: 'badge' },
                { key: 'description', label: 'รายละเอียด' },
                { key: 'created_at', label: 'วันที่', type: 'date' },
                { key: 'actions', label: 'การดำเนินการ' }
            ],
            availableFilters: [
                {
                    key: 'action',
                    label: 'การดำเนินการ',
                    type: 'select',
                    placeholder: 'เลือกการดำเนินการ',
                    dynamicOptions: true,
                    getOptions: () => this.filteredActionOptions
                },
                {
                    key: 'target_type',
                    label: 'ประเภทเป้าหมาย',
                    type: 'select',
                    placeholder: 'เลือกประเภทเป้าหมาย',
                    dynamicOptions: true,
                    getOptions: () => this.filteredTargetTypeOptions
                },
                {
                    key: 'created_at',
                    label: 'ช่วงวันที่',
                    type: 'daterange',
                    fromPlaceholder: 'วันที่เริ่มต้น',
                    toPlaceholder: 'วันที่สิ้นสุด'
                }
            ]
        };
    },
    computed: {
        // Get filtered target type options based on selected action
        filteredTargetTypeOptions() {
            const baseTable = this.$refs.baseTable;
            if (!baseTable || !baseTable.filters) return this.getTargetTypeOptions();
            
            const selectedAction = baseTable.filters.action;
            if (!selectedAction) return this.getTargetTypeOptions();
            
            const allowedTargetTypes = this.actionTargetTypeMap[selectedAction] || [];
            return this.getTargetTypeOptions().filter(option => 
                allowedTargetTypes.includes(option.value)
            );
        },
        
        // Get filtered action options based on selected target type
        filteredActionOptions() {
            const baseTable = this.$refs.baseTable;
            if (!baseTable || !baseTable.filters) return this.getActionOptions();
            
            const selectedTargetType = baseTable.filters.target_type;
            if (!selectedTargetType) return this.getActionOptions();
            
            const allowedActions = this.targetTypeActionMap[selectedTargetType] || [];
            return this.getActionOptions().filter(option => 
                allowedActions.includes(option.value)
            );
        }
    },
    methods: {
        async fetchLogs(filters = {}) {
            try {
                // Build query parameters
                const params = new URLSearchParams();
                
                // Add filter parameters
                Object.keys(filters).forEach(key => {
                    if (filters[key]) {
                        if (key === 'created_at_from') {
                            params.append('date_from', filters[key]);
                        } else if (key === 'created_at_to') {
                            params.append('date_to', filters[key]);
                        } else {
                            params.append(key, filters[key]);
                        }
                    }
                });
                
                const url = `/api/logs${params.toString() ? '?' + params.toString() : ''}`;
                console.log('Fetching logs with URL:', url);
                
                const response = await api.get(url);
                this.logs = response.data.data || response.data || [];
            } catch (error) {
                console.error('Failed to fetch logs:', error);
                this.logs = [];
            }
        },
        getActionBadgeClass(action) {
            const classes = {
                'create': 'bg-green-100 text-green-800',
                'update': 'bg-blue-100 text-blue-800',
                'delete': 'bg-red-100 text-red-800',
                'login': 'bg-green-100 text-green-800',
                'logout': 'bg-gray-100 text-gray-800',
                'approve': 'bg-green-100 text-green-800',
                'reject': 'bg-red-100 text-red-800',
                'cancel': 'bg-yellow-100 text-yellow-800',
                'check_out': 'bg-blue-100 text-blue-800',
                'check_in': 'bg-green-100 text-green-800',
                'export_initiated': 'bg-indigo-100 text-indigo-800',
                'export_completed': 'bg-green-100 text-green-800',
                'export_failed': 'bg-red-100 text-red-800',
            };
            return classes[action] || 'bg-gray-100 text-gray-800';
        },
        getSeverityBadgeClass(severity) {
            const classes = {
                'critical': 'bg-red-100 text-red-800',
                'error': 'bg-red-100 text-red-800',
                'warning': 'bg-yellow-100 text-yellow-800',
                'info': 'bg-blue-100 text-blue-800',
                'success': 'bg-green-100 text-green-800',
            };
            return classes[severity] || 'bg-gray-100 text-gray-800';
        },
        getTargetTypeBadgeClass(targetType) {
            const classes = {
                'equipment': 'bg-blue-100 text-blue-800',
                'category': 'bg-green-100 text-green-800',
                'user': 'bg-purple-100 text-purple-800',
                'borrow_request': 'bg-orange-100 text-orange-800',
                'verification_request': 'bg-indigo-100 text-indigo-800',
                'system': 'bg-gray-100 text-gray-800',
            };
            return classes[targetType] || 'bg-gray-100 text-gray-800';
        },
        getTargetTypeLabel(targetType) {
            const labels = {
                'equipment': 'อุปกรณ์',
                'category': 'หมวดหมู่',
                'user': 'ผู้ใช้',
                'borrow_request': 'คำขอยืม',
                'verification_request': 'คำขอการยืนยัน',
                'system': 'ระบบ',
            };
            return labels[targetType] || targetType || '-';
        },
        showLogDetails(log) {
            this.selectedLog = log;
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
        exportLogs(exportParams) {
            const query = new URLSearchParams({
                ...exportParams.filters,
                search: exportParams.search,
                sort: exportParams.sort,
                direction: exportParams.direction
            }).toString();

            window.location.href = `/admin/report/export/log?${query}`;
        },
        formatFieldName(key) {
            const fieldNames = {
                'id': 'รหัส',
                'name': 'ชื่อ',
                'code': 'รหัสอุปกรณ์',
                'brand': 'ยี่ห้อ',
                'model': 'รุ่น',
                'description': 'คำอธิบาย',
                'category_id': 'หมวดหมู่',
                'status': 'สถานะ',
                'condition': 'สภาพ',
                'serial_number': 'หมายเลขซีเรียล',
                'equipment_item_id': 'รหัสรายการอุปกรณ์',
                'items': 'รายการ',
                'accessories': 'อุปกรณ์ที่ติดมากับเครื่อง',
                'specifications': 'ข้อมูลจำเพาะ',
                'deleted_items': 'รายการที่ลบ',
                'start_at': 'วันที่เริ่มต้น',
                'end_at': 'วันที่สิ้นสุด',
                'quantity': 'จำนวน',
                'equipments_id': 'รหัสอุปกรณ์',
                'request_reason': 'เหตุผลการขอ',
                'request_reason_other': 'เหตุผลอื่นๆ',
                'extra_accessories': 'อุปกรณ์ที่ติดมากับเครื่องเพิ่มเติม',
                '_method': 'วิธีการ',
                'phonenumber': 'เบอร์โทรศัพท์',
                'email': 'อีเมล',
                'role': 'บทบาท',
                'verified': 'สถานะการยืนยัน',
                'uid': 'รหัสผู้ใช้',
                'created_at': 'วันที่สร้าง',
                'updated_at': 'วันที่แก้ไข',
                'ip_address': 'ที่อยู่ IP',
                'user_agent': 'เบราว์เซอร์',
                'session_id': 'รหัสเซสชัน',
                'request_method': 'วิธีการคำขอ',
                'request_url': 'URL คำขอ',
                'severity': 'ระดับความสำคัญ',
                'module': 'โมดูล',
                'target_type': 'ประเภทเป้าหมาย',
                'target_id': 'รหัสเป้าหมาย',
                'target_name': 'ชื่อเป้าหมาย',
                'notes': 'หมายเหตุ'
            };
            return fieldNames[key] || key;
        },
        formatDescriptionWithLinks(description) {
            if (!description) return '-';

            // Regular expression to match request IDs like REQVAEL52MSWI
            const reqIdPattern = /\b(REQ[A-Z0-9]{10,})\b/g;

            return description.replace(reqIdPattern, (match) => {
                return `<a href="/admin/requests/${match}" 
                    class="text-blue-600 hover:text-blue-800 underline font-medium cursor-pointer" 
                    title="ดูรายละเอียดคำขอ ${match}">${match}</a>`;
            });
        },
        formatFieldValueWithLinks(value) {
            if (value === null || value === undefined) {
                return '-';
            }

            if (typeof value === 'boolean') {
                return value ? 'ใช่' : 'ไม่';
            }

            if (typeof value === 'object') {
                if (Array.isArray(value)) {
                    return value.length > 0 ? value.join(', ') : '-';
                }
                return JSON.stringify(value, null, 2);
            }

            if (typeof value === 'string' && value.trim() === '') {
                return '-';
            }

            // Format specific values for better readability
            const statusTranslations = {
                'available': 'พร้อมใช้งาน',
                'retired': 'รอคืน',
                'unavailable': 'ไม่พร้อมใช้งาน',
                'maintenance': 'ซ่อมบำรุง',
                'pending': 'รอดำเนินการ',
                'approved': 'อนุมัติแล้ว',
                'rejected': 'ปฏิเสธ',
                'cancelled': 'ยกเลิก',
                'checked_out': 'ยืมออกแล้ว',
                'checked_in': 'คืนแล้ว'
            };

            const conditionTranslations = {
                'good': 'สภาพดี',
                'repairable': 'สามารถซ่อมได้',
                'unrepairable': 'ไม่สามารถซ่อมได้',
                'broken': 'พัง',
                'lost': 'หายไป',
            };

            let stringValue = value.toString();

            // Apply translations
            if (statusTranslations[stringValue]) {
                stringValue = statusTranslations[stringValue];
            } else if (conditionTranslations[stringValue]) {
                stringValue = conditionTranslations[stringValue];
            }

            // Add clickable links for request IDs
            return this.formatDescriptionWithLinks(stringValue);
        },
        applyQuickFilter(filterKey, filterValue) {
            // Access the BaseReportTable component through ref
            const baseTable = this.$refs.baseTable;
            if (baseTable) {
                // Clear other filters first
                baseTable.filters = {};
                // Set the specific filter
                baseTable.filters[filterKey] = filterValue;
                // Apply the filters
                baseTable.applyFilters();
            }
        },
        clearAllFilters() {
            // Access the BaseReportTable component through ref
            const baseTable = this.$refs.baseTable;
            if (baseTable && baseTable.clearAllFilters) {
                baseTable.clearAllFilters();
            }
        },
        filterByAction(action) {
            // Access the BaseReportTable component through ref
            const baseTable = this.$refs.baseTable;
            if (baseTable && baseTable.setFilter) {
                baseTable.setFilter('action', action);
            }
        },
        filterByTargetType(targetType) {
            // Access the BaseReportTable component through ref
            const baseTable = this.$refs.baseTable;
            if (baseTable && baseTable.setFilter) {
                baseTable.setFilter('target_type', targetType);
            }
        },
        viewTargetDetails(logItem) {
            // Navigate to relevant pages based on target type
            switch (logItem.target_type) {
                case 'equipment':
                    // Navigate to equipment page with search filter
                    window.location.href = `/admin/equipment?search=${encodeURIComponent(logItem.target_name)}`;
                    break;
                case 'category':
                    // Navigate to equipment page filtered by category
                    window.location.href = `/admin/equipment?category=${encodeURIComponent(logItem.target_name)}`;
                    break;
                case 'borrow_request':
                    // Navigate to request details
                    window.location.href = `/admin/requests/${logItem.target_name}`;
                    break;
                case 'user':
                    // Navigate to user report with search filter
                    window.location.href = `/admin/report/user?search=${encodeURIComponent(logItem.target_name)}`;
                    break;
                default:
                    // For other types, show details in modal or do nothing
                    this.showLogDetails(logItem);
                    break;
            }
        },
        viewAdminDetails(adminId) {
            // Navigate to user report with search filter for the admin
            window.location.href = `/admin/report/user?search=${encodeURIComponent(adminId)}`;
        },
        getActionOptions() {
            return [
                { value: 'create', label: 'สร้าง' },
                { value: 'update', label: 'แก้ไข' },
                { value: 'delete', label: 'ลบ' },
                { value: 'login', label: 'เข้าสู่ระบบ' },
                { value: 'logout', label: 'ออกจากระบบ' },
                { value: 'approve', label: 'อนุมัติ' },
                { value: 'reject', label: 'ปฏิเสธ' },
                { value: 'cancel', label: 'ยกเลิก' },
                { value: 'check_out', label: 'เช็คเอาท์' },
                { value: 'check_in', label: 'เช็คอิน' },
                { value: 'export_initiated', label: 'เริ่มส่งออก' },
                { value: 'export_completed', label: 'ส่งออกเสร็จ' },
                { value: 'export_failed', label: 'ส่งออกล้มเหลว' }
            ];
        },
        getTargetTypeOptions() {
            return [
                { value: 'equipment', label: 'อุปกรณ์' },
                { value: 'category', label: 'หมวดหมู่' },
                { value: 'user', label: 'ผู้ใช้' },
                { value: 'borrow_request', label: 'คำขอยืม' },
                { value: 'system', label: 'ระบบ' }
            ];
        },
        // Handle filter changes to update dependent filters
        onFilterChange(filterKey, filterValue) {
            const baseTable = this.$refs.baseTable;
            if (!baseTable) return;
            
            // If action changed, reset target_type if it's not compatible
            if (filterKey === 'action' && filterValue) {
                const allowedTargetTypes = this.actionTargetTypeMap[filterValue] || [];
                const currentTargetType = baseTable.filters.target_type;
                
                if (currentTargetType && !allowedTargetTypes.includes(currentTargetType)) {
                    // Clear incompatible target_type
                    baseTable.filters.target_type = '';
                    baseTable.applyFilters();
                }
            }
            
            // If target_type changed, reset action if it's not compatible
            if (filterKey === 'target_type' && filterValue) {
                const allowedActions = this.targetTypeActionMap[filterValue] || [];
                const currentAction = baseTable.filters.action;
                
                if (currentAction && !allowedActions.includes(currentAction)) {
                    // Clear incompatible action
                    baseTable.filters.action = '';
                    baseTable.applyFilters();
                }
            }
            
            // For date filters, refetch data from API
            if (filterKey === 'created_at_from' || filterKey === 'created_at_to') {
                this.fetchLogsFromAPI();
            }
        },
        
        // Fetch logs from API with current filters
        async fetchLogsFromAPI() {
            const baseTable = this.$refs.baseTable;
            if (!baseTable) return;
            
            await this.fetchLogs(baseTable.filters);
        }
    },
    mounted() {
        this.fetchLogs();
    },
};
</script>
