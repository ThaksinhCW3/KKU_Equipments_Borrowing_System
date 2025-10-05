<template>
  <div class="bg-white p-6 rounded-lg shadow">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">{{ title }}</h2>
        <p class="text-sm text-gray-600 mt-1">แสดง {{ filteredData.length }} รายการ</p>
      </div>
      <div class="flex gap-2">
        <!-- Add Button -->
        <button 
          v-if="showAddButton && userRole === 'admin'" 
          @click="$emit('create')"
          class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          {{ addButtonText }}
        </button>
      </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="mb-6">
      <!-- Quick Filters Slot -->
      <slot name="quick-filters"></slot>
      
      <!-- Search Bar -->
      <div class="relative mb-4">
        <input 
          type="text" 
          v-model="searchQuery" 
          :placeholder="searchPlaceholder"
          class="pl-10 pr-4 py-3 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full" 
        />
        <svg class="w-5 h-5 absolute left-3 top-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
      </div>

      <!-- Advanced Filters -->
      <div class="flex flex-wrap gap-2 items-center relative" ref="filtersWrap">
        <button 
          v-if="showAdvancedFilters" 
          @click="filtersOpen = !filtersOpen" 
          class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
            </path>
          </svg>
          ตัวกรองขั้นสูง
          <span v-if="activeFiltersCount > 0"
            class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-blue-600 rounded-full">
            {{ activeFiltersCount }}
          </span>
        </button>

        <button v-if="activeFiltersCount > 0 || searchQuery" @click="clearAllFilters"
          class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 text-sm font-medium rounded-lg hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
            </path>
          </svg>
          ล้างตัวกรอง
        </button>


        <!-- Advanced Filters Dropdown -->
        <div 
          v-if="filtersOpen && showAdvancedFilters" 
          class="absolute left-0 top-12 z-20 bg-white border border-gray-200 rounded-lg shadow-lg p-6"
          :class="{
            'w-80': availableFilters.length <= 2,
            'w-96': availableFilters.length > 2 && availableFilters.length <= 6,
            'w-[28rem]': availableFilters.length > 6 && availableFilters.length <= 9,
            'w-[32rem]': availableFilters.length > 9
          }"
        >
          <div class="gap-4" :class="{
            'grid grid-cols-1': availableFilters.length <= 2,
            'grid grid-cols-1 md:grid-cols-2': availableFilters.length > 2 && availableFilters.length <= 6,
            'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3': availableFilters.length > 6
          }">
            <!-- Dynamic Filter Fields -->
            <div v-for="filter in availableFilters" :key="filter.key" class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">{{ filter.label }}</label>
              
              <!-- Text Input -->
              <input v-if="filter.type === 'text'" v-model="filters[filter.key]"
                :placeholder="filter.placeholder"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
              
              <!-- Select Dropdown -->
              <select v-else-if="filter.type === 'select'" v-model="filters[filter.key]"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">{{ filter.placeholder }}</option>
                <option v-for="option in getFilterOptions(filter)" :key="option.value" :value="option.value">
                  {{ option.label }}
                </option>
              </select>
              
              <!-- Date Input -->
              <input v-else-if="filter.type === 'date'" v-model="filters[filter.key]" type="date"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
              
              <!-- Date Range -->
              <div v-else-if="filter.type === 'daterange'" class="space-y-2">
                <div>
                  <label class="block text-xs font-medium text-gray-600 mb-1">จากวันที่</label>
                  <input v-model="filters[filter.key + '_from']" type="date"
                    :placeholder="filter.fromPlaceholder"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-600 mb-1">ถึงวันที่</label>
                  <input v-model="filters[filter.key + '_to']" type="date"
                    :placeholder="filter.toPlaceholder"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
              </div>
            </div>
          </div>

          <!-- Filter Actions -->
          <div class="flex justify-end gap-2 mt-6 pt-4 border-t border-gray-200">
            <button @click="clearAllFilters" 
              class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
              ล้างทั้งหมด
            </button>
            <button @click="applyFilters" 
              class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
              ใช้ตัวกรอง
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th v-for="column in columns" :key="column.key" 
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              <div class="flex items-center justify-between">
                <span>{{ column.label }}</span>
                <div class="flex flex-col ml-2">
                  <button @click="sortData(column.key, 'asc')" :class="[
                      'p-1 rounded hover:bg-gray-200 transition-colors',
                      sortKey === column.key && sortDirection === 'asc' 
                        ? 'text-blue-600 bg-blue-100' 
                        : 'text-gray-400 hover:text-gray-600'
                  ]" :title="`Sort ${column.label} ascending`">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 15l7-7 7 7"></path>
                    </svg>
                  </button>
                  <button @click="sortData(column.key, 'desc')" :class="[
                      'p-1 rounded hover:bg-gray-200 transition-colors',
                      sortKey === column.key && sortDirection === 'desc' 
                        ? 'text-blue-600 bg-blue-100' 
                        : 'text-gray-400 hover:text-gray-600'
                  ]" :title="`Sort ${column.label} descending`">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 9l-7 7-7-7"></path>
                    </svg>
                  </button>
                </div>
              </div>
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="(item, index) in paginatedData" :key="getItemKey(item)" 
            class="hover:bg-gray-50 transition-colors duration-150">
            <slot name="rows" :item="item" :index="index" :actions="getActions(item)"></slot>
          </tr>
          <tr v-if="(filteredData || []).length === 0">
            <td :colspan="columns.length" class="px-6 py-12 text-center text-gray-500">
              <div class="flex flex-col items-center">
                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                  </path>
                </svg>
                <p class="text-lg font-medium text-gray-900 mb-1">ไม่พบข้อมูล</p>
                <p class="text-sm text-gray-500">ลองปรับเปลี่ยนตัวกรองหรือคำค้นหา</p>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="showPagination" class="mt-6 flex items-center justify-between">
      <div class="flex-1 flex justify-between sm:hidden">
        <button @click="prevPage" :disabled="currentPage === 1"
          class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
          ก่อนหน้า
        </button>
        <span class="flex items-center px-4 py-2 text-sm text-gray-500">
          หน้า {{ currentPage }} จาก {{ totalPages }}
        </span>
        <button @click="nextPage" :disabled="currentPage === totalPages"
          class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
          ถัดไป
        </button>
      </div>
      <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
        <div>
          <p class="text-sm text-gray-700">
            แสดง <span class="font-medium">{{ (currentPage - 1) * pageSize + 1 }}</span>
            ถึง <span class="font-medium">{{ Math.min(currentPage * pageSize, filteredData.length) }}</span>
            จาก <span class="font-medium">{{ filteredData.length }}</span> รายการ
          </p>
        </div>
        <div>
          <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
            <button @click="prevPage" :disabled="currentPage === 1"
              class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
              <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                  d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                  clip-rule="evenodd" />
              </svg>
            </button>
            <button v-for="page in visiblePages" :key="page" @click="goToPage(page)" :class="[
                'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                page === currentPage
                  ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                  : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
            ]">
              {{ page }}
            </button>
            <button @click="nextPage" :disabled="currentPage === totalPages"
              class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
              <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                  d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                  clip-rule="evenodd" />
              </svg>
            </button>
          </nav>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
    </div>
  </div>
</template>

<script>
export default {
  name: "BaseTable",
  props: {
    // Data
    data: {
      type: Array,
      default: () => []
    },
    columns: {
      type: Array,
      default: () => []
    },
    
    // Configuration
    title: {
      type: String,
      default: "รายการ"
    },
    searchPlaceholder: {
      type: String,
      default: "ค้นหา..."
    },
    addButtonText: {
      type: String,
      default: "เพิ่มรายการใหม่"
    },
    
    // Features
    showPagination: {
      type: Boolean,
      default: true
    },
    showAdvancedFilters: {
      type: Boolean,
      default: true
    },
    showAddButton: {
      type: Boolean,
      default: true
    },
    
    // Pagination
    pageSize: {
      type: Number,
      default: 15
    },
    
    // User role
    userRole: {
      type: String,
      default: ""
    },
    
    // Loading state
    loading: {
      type: Boolean,
      default: false
    },
    
    // Search configuration
    searchFields: {
      type: Array,
      default: () => ['name', 'code', 'description']
    },
    
    // Sort configuration
    sortField: {
      type: String,
      default: 'created_at'
    },
    
    // Filter configuration
    availableStatuses: {
      type: Array,
      default: () => ['available', 'retired', 'maintenance']
    },
    
    // Advanced filters configuration
    availableFilters: {
      type: Array,
      default: () => []
    }
  },
  
  data() {
    return {
      searchQuery: "",
      sortKey: "",
      sortDirection: "asc",
      currentPage: 1,
      filtersOpen: false,
      filters: {},
      appliedFilters: {} // Separate object for actually applied filters
    };
  },
  
  computed: {
    filteredData() {
      let list = [...this.data];
      
      // Apply search
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        list = list.filter(item => {
          return this.searchFields.some(field => {
            const value = this.getNestedValue(item, field);
            return value && value.toString().toLowerCase().includes(query);
          });
        });
      }
      
      // Apply advanced filters (only use appliedFilters, not the temporary filters)
      this.availableFilters.forEach(filter => {
        if (filter.type === 'daterange') {
          const fromValue = this.appliedFilters[filter.key + '_from'];
          const toValue = this.appliedFilters[filter.key + '_to'];

          if (fromValue || toValue) {
            list = list.filter(item => {
              const itemValue = this.getNestedValue(item, filter.key);
              const itemDate = this.parseDate(itemValue);
              
              if (!itemDate || isNaN(itemDate.getTime())) {
                return false;
              }

              if (fromValue) {
                const fromDate = new Date(fromValue);
                fromDate.setHours(0, 0, 0, 0);
                if (itemDate < fromDate) {
                  return false;
                }
              }

              if (toValue) {
                const toDate = new Date(toValue);
                toDate.setHours(23, 59, 59, 999);
                if (itemDate > toDate) {
                  return false;
                }
              }

              return true;
            });
          }
        } else {
          const filterValue = this.appliedFilters[filter.key];
          if (filterValue) {
            if (filter.type === 'date') {
              list = list.filter(item => {
                const itemValue = this.getNestedValue(item, filter.key);
                const itemDate = this.parseDate(itemValue);
                const filterDate = new Date(filterValue);

                if (!itemDate || isNaN(itemDate.getTime()) || isNaN(filterDate.getTime())) {
                  return false;
                }

                const itemDateOnly = itemDate.toISOString().slice(0, 10);
                const filterDateOnly = filterDate.toISOString().slice(0, 10);
                return itemDateOnly === filterDateOnly;
              });
            } else if (filter.type === 'select') {
              list = list.filter(item => {
                const itemValue = this.getNestedValue(item, filter.key);
                return itemValue && itemValue.toString() === filterValue.toString();
              });
            } else {
              // Text filter
              list = list.filter(item => {
                const itemValue = this.getNestedValue(item, filter.key);
                return itemValue && itemValue.toString().toLowerCase().includes(filterValue.toLowerCase());
              });
            }
          }
        }
      });
      
      // Apply sorting
      if (this.sortKey) {
        list.sort((a, b) => {
          const aValue = this.getNestedValue(a, this.sortKey);
          const bValue = this.getNestedValue(b, this.sortKey);
          
          // Handle null/undefined - push to end
          if (aValue === null || aValue === undefined) {
            return this.sortDirection === 'asc' ? 1 : -1;
          }
          if (bValue === null || bValue === undefined) {
            return this.sortDirection === 'asc' ? -1 : 1;
          }

          // If it's a date-like value, parse both
          if (this.sortKey.includes('date') || this.sortKey.includes('created_at') || this.sortKey.includes('updated_at')) {
            const aDate = this.parseDate(aValue);
            const bDate = this.parseDate(bValue);

            // Check if both dates are valid
            const aValid = aDate instanceof Date && !isNaN(aDate.getTime());
            const bValid = bDate instanceof Date && !isNaN(bDate.getTime());

            if (aValid && bValid) {
              const result = aDate.getTime() - bDate.getTime();
              return this.sortDirection === 'asc' ? result : -result;
            }

            // If one date is invalid, push it to the end
            if (!aValid && bValid) return this.sortDirection === 'asc' ? 1 : -1;
            if (aValid && !bValid) return this.sortDirection === 'asc' ? -1 : 1;

            // Both invalid, keep original order
            return 0;
          }

          // Fallback: numbers first
          let aCompare, bCompare;
          if (!isNaN(aValue) && !isNaN(bValue)) {
            aCompare = Number(aValue);
            bCompare = Number(bValue);
          } else {
            // Strings fallback
            aCompare = aValue.toString().toLowerCase();
            bCompare = bValue.toString().toLowerCase();
          }
          
          let result = 0;
          if (aCompare < bCompare) result = -1;
          else if (aCompare > bCompare) result = 1;
          
          return this.sortDirection === 'asc' ? result : -result;
        });
      }
      
      return list;
    },
    
    totalPages() {
      return Math.ceil(this.filteredData.length / this.pageSize) || 0;
    },
    
    pageCount() {
      return this.totalPages;
    },
    
    pageStart() {
      return (this.currentPage - 1) * this.pageSize;
    },
    
    pageEnd() {
      const end = this.pageStart + this.pageSize;
      return Math.min(end, this.filteredData.length);
    },
    
    visiblePages() {
      const pages = [];
      const start = Math.max(1, this.currentPage - 2);
      const end = Math.min(this.totalPages, this.currentPage + 2);
      
      for (let i = start; i <= end; i++) {
        pages.push(i);
      }
      return pages;
    },
    
    paginatedData() {
      if (!this.showPagination) {
        return this.filteredData;
      }
      return this.filteredData.slice(this.pageStart, this.pageEnd);
    },
    
    computedCounts() {
      const counts = { all: this.data.length };
      
      // Status counts
      this.availableStatuses.forEach(status => {
        counts[status] = this.data.filter(item => item.status === status).length;
      });
      
      return counts;
    },
    
    activeFiltersText() {
      const activeFilters = [];
      this.availableFilters.forEach(filter => {
        if (filter.type === 'daterange') {
          if (this.filters[filter.key + '_from'] || this.filters[filter.key + '_to']) {
            activeFilters.push(filter.label);
          }
        } else if (this.filters[filter.key]) {
          activeFilters.push(filter.label);
        }
      });
      return activeFilters.length > 0 ? activeFilters.join(', ') : 'all';
    },
    
    activeFiltersCount() {
      let count = 0;
      this.availableFilters.forEach(filter => {
        if (filter.type === 'daterange') {
          if (this.appliedFilters[filter.key + '_from'] || this.appliedFilters[filter.key + '_to']) {
            count++;
          }
        } else if (this.appliedFilters[filter.key]) {
          count++;
        }
      });
      return count;
    }
  },
  
  methods: {
    getNestedValue(obj, path) {
      return path.split('.').reduce((current, key) => current?.[key], obj);
    },
    
    getItemKey(item) {
      return item.id || item.cate_id || Math.random();
    },
    
    getActions(item) {
      return {
        edit: () => this.$emit('edit', item),
        delete: () => this.$emit('delete', item),
        view: () => this.$emit('view', item)
      };
    },
    
    toggleSort() {
      this.sortDirection = this.sortDirection === "asc" ? "desc" : "asc";
    },
    
    sortData(key, direction = null) {
      if (direction) {
        // If direction is specified, use it directly
        this.sortKey = key;
        this.sortDirection = direction;
      } else {
        // If no direction specified, toggle current direction
        if (this.sortKey === key) {
          this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
          this.sortKey = key;
          this.sortDirection = 'asc';
        }
      }
      this.currentPage = 1;
    },
    
    goToPage(page) {
      this.currentPage = page;
    },
    
    nextPage() {
      if (this.currentPage < this.pageCount) {
        this.currentPage += 1;
      }
    },
    
    prevPage() {
      if (this.currentPage > 1) {
        this.currentPage -= 1;
      }
    },
    
    clearFilters() {
      this.clearAllFilters();
    },
    
    clearAllFilters() {
      // Reset filters to empty state (this will show placeholders in select fields)
      this.filters = {};
      this.appliedFilters = {};
      this.searchQuery = "";
      this.currentPage = 1;
      this.applyFilters(); // Apply the cleared filters immediately
    },
    
    applyFilters() {
      // Copy current filter values to applied filters
      this.appliedFilters = { ...this.filters };
      this.filtersOpen = false;
      this.currentPage = 1;
    },
    
    getFilterOptions(filter) {
      // Support dynamic options
      if (filter.dynamicOptions && filter.getOptions) {
        return filter.getOptions();
      }
      // Fallback to static options
      return filter.options || [];
    },
    
    onFilterChange(filterKey, filterValue) {
      // Emit event to parent component for handling dependent filters
      this.$emit('filter-change', filterKey, filterValue);
      
      // Don't apply filters immediately - wait for "ใช้ตัวกรอง" button
    },
    
    parseDate(dateString) {
      if (!dateString) return null;
      
      // If it's already a Date object
      if (dateString instanceof Date) {
        return dateString;
      }
      
      // Handle ISO format (from API): "2025-08-22T03:19:26.000000Z"
      if (/^\d{4}-\d{2}-\d{2}T/.test(dateString)) {
        return new Date(dateString);
      }
      
      // Handle MySQL timestamp format: "YYYY-MM-DD HH:mm:ss"
      if (/^\d{4}-\d{2}-\d{2}\s/.test(dateString)) {
        return new Date(dateString);
      }
      
      // Handle MySQL date format: "YYYY-MM-DD"
      if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
        return new Date(dateString + 'T00:00:00');
      }
      
      // Handle Thai Buddhist year format "DD/MM/YYYY HH:mm" or "DD/MM/YYYY"
      if (typeof dateString === 'string' && dateString.includes('/')) {
        const [datePart, timePart] = dateString.split(' ');
        const [day, month, year] = datePart.split('/');
        
        if (day && month && year) {
          let gregorianYear = parseInt(year);
          // If year is > 2500, it's Thai Buddhist year, convert to Gregorian
          if (gregorianYear > 2500) {
            gregorianYear -= 543;
          }
          
          let hours = 0, minutes = 0, seconds = 0;
          if (timePart) {
            const [h, m, s] = timePart.split(':');
            hours = parseInt(h) || 0;
            minutes = parseInt(m) || 0;
            seconds = parseInt(s) || 0;
          }
          
          return new Date(gregorianYear, parseInt(month) - 1, parseInt(day), hours, minutes, seconds);
        }
      }
      
      // Fallback - try regular date parsing
      return new Date(dateString);
    },
    
    capitalize(str) {
      if (!str) return "";
      return str.charAt(0).toUpperCase() + str.slice(1);
    },
    
    // Public methods for external access
    refresh() {
      this.$emit('refresh');
    },
    
    setFilter(key, value) {
      this.filters[key] = value;
    },
    
    getFilter(key) {
      return this.filters[key];
    }
  },
  
  watch: {
    filteredData() {
      // Reset to first page when data changes
      this.currentPage = 1;
    }
  },
  
  mounted() {
    // Initialize filters
    this.availableFilters.forEach(filter => {
      this.filters[filter.key] = '';
      if (filter.type === 'daterange') {
        this.filters[filter.key + '_from'] = '';
        this.filters[filter.key + '_to'] = '';
      }
    });

    this._onClickOutside = (e) => {
      const wrap = this.$refs.filtersWrap;
      if (!wrap) return;
      if (this.filtersOpen && !wrap.contains(e.target)) {
        this.filtersOpen = false;
      }
    };
    document.addEventListener("click", this._onClickOutside);
  },
  
  beforeUnmount() {
    document.removeEventListener("click", this._onClickOutside);
  }
};
</script>
