<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>
    
    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
      <div class="relative bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
          <div class="flex items-center space-x-3">
            <div class="p-2 bg-blue-100 rounded-lg">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="mode === 'view'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path v-if="mode === 'view'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                <path v-if="mode === 'edit'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                <path v-if="mode === 'create'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-gray-900">
                {{ mode === 'view' ? 'รายละเอียดหมวดหมู่' : mode === 'edit' ? 'แก้ไขหมวดหมู่' : 'เพิ่มหมวดหมู่ใหม่' }}
              </h3>
              <p class="text-sm text-gray-500">
                {{ mode === 'view' ? category?.name : mode === 'edit' ? form?.name : 'หมวดหมู่ใหม่' }} 
                {{ mode === 'view' ? `(${category?.cate_id})` : mode === 'edit' ? `(${form?.cate_id})` : '' }}
              </p>
            </div>
          </div>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Content -->
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
          <!-- View Mode -->
          <div v-if="mode === 'view' && category" class="space-y-6">
            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Left Column -->
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">รหัสหมวดหมู่</label>
                  <div class="px-3 py-2 bg-gray-50 rounded-md border">
                    <span class="text-sm font-mono text-gray-900">{{ category.cate_id }}</span>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อหมวดหมู่</label>
                  <div class="px-3 py-2 bg-gray-50 rounded-md border">
                    <span class="text-sm text-gray-900">{{ category.name }}</span>
                  </div>
                </div>
              </div>

              <!-- Right Column -->
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">จำนวนอุปกรณ์</label>
                  <div class="px-3 py-2 bg-gray-50 rounded-md border">
                    <span class="text-sm text-gray-900">
                      {{ categoryEquipments.length }} รายการ
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Equipment List -->
            <div v-if="categoryEquipments.length > 0" class="pt-4 border-t border-gray-200">
              <h4 class="text-lg font-medium text-gray-900 mb-4">อุปกรณ์ในหมวดหมู่นี้</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div 
                  v-for="equipment in categoryEquipments" 
                  :key="equipment.id"
                  class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer"
                  @click="viewEquipment(equipment)"
                >
                  <div class="flex items-start space-x-3">
                    <img 
                      v-if="getFirstPhoto(equipment)" 
                      :src="getFirstPhoto(equipment)" 
                      alt="Equipment photo"
                      class="w-12 h-12 object-cover rounded-lg"
                    />
                    <div class="flex-1 min-w-0">
                      <h5 class="text-sm font-medium text-gray-900 truncate">{{ equipment.name }}</h5>
                      <p class="text-xs text-gray-500">{{ equipment.code }}</p>
                      <span 
                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full mt-1"
                        :class="getStatusClass(equipment.status)"
                      >
                        {{ getStatusLabel(equipment.status) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div v-else class="pt-4 border-t border-gray-200">
              <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">ไม่มีอุปกรณ์ในหมวดหมู่นี้</h3>
                <p class="mt-1 text-sm text-gray-500">ยังไม่มีอุปกรณ์ที่ถูกจัดอยู่ในหมวดหมู่นี้</p>
              </div>
            </div>

            <!-- Additional Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-200">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">วันที่สร้าง</label>
                <div class="px-3 py-2 bg-gray-50 rounded-md border">
                  <span class="text-sm text-gray-900">
                    {{ formatDate(category.created_at) }}
                  </span>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">วันที่อัปเดตล่าสุด</label>
                <div class="px-3 py-2 bg-gray-50 rounded-md border">
                  <span class="text-sm text-gray-900">
                    {{ formatDate(category.updated_at) }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Edit/Create Mode -->
          <form v-else-if="mode === 'edit' || mode === 'create'" id="category-form" @submit.prevent="onSave" novalidate class="space-y-6">
            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Left Column -->
              <div class="space-y-4">

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อหมวดหมู่</label>
                  <input
                    required
                    type="text"
                    v-model.trim="form.name"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                </div>
              </div>

              <!-- Right Column -->
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">จำนวนอุปกรณ์</label>
                  <div class="px-3 py-2 bg-gray-50 rounded-md border">
                    <span class="text-sm text-gray-900">
                      {{ categoryEquipments.length }} รายการ
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </form>

          <!-- Loading State -->
          <div v-else class="flex items-center justify-center py-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-end space-x-3 p-6 border-t border-gray-200 bg-gray-50">
          <button 
            @click="closeModal"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            {{ mode === 'view' ? 'ปิด' : 'ยกเลิก' }}
          </button>
          <button 
            v-if="mode === 'view' && userRole === 'admin'"
            @click="switchToEdit"
            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            แก้ไขข้อมูล
          </button>
          <button 
            v-if="mode === 'edit' || mode === 'create'"
            type="submit" 
            form="category-form"
            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center justify-center"
            :disabled="submitting || !canSave"
          >
            <svg v-if="submitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span v-if="submitting">กำลังบันทึก...</span>
            <span v-else>{{ mode === 'create' ? 'สร้างหมวดหมู่' : 'บันทึกการเปลี่ยนแปลง' }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CategoryModal',
  props: {
    isOpen: {
      type: Boolean,
      default: false
    },
    mode: {
      type: String,
      default: 'view', // 'view', 'edit', or 'create'
      validator: value => ['view', 'edit', 'create'].includes(value)
    },
    category: {
      type: Object,
      default: null
    },
    allEquipments: {
      type: Array,
      default: () => []
    },
    userRole: {
      type: String,
      default: 'user'
    }
  },
  data() {
    return {
      form: {},
      submitting: false,
      categoryEquipments: []
    }
  },
  computed: {
    canSave() {
      return !!(this.form && this.form.name);
    }
  },
  watch: {
    isOpen(newVal) {
      if (newVal) {
        if (this.mode === 'edit' && this.category) {
          this.setupForm();
        } else if (this.mode === 'create') {
          this.setupCreateForm();
        }
        if (this.mode === 'view' && this.category) {
          this.loadCategoryEquipments();
        }
      }
    },
    mode(newVal) {
      if (newVal === 'edit' && this.category) {
        this.setupForm();
      } else if (newVal === 'create') {
        this.setupCreateForm();
      }
    }
  },
  methods: {
    closeModal() {
      this.$emit('close');
    },
    
    switchToEdit() {
      this.$emit('edit', this.category);
    },
    
    setupCreateForm() {
      this.form = {
        name: ''
      };
      this.submitting = false;
    },
    
    setupForm() {
      this.form = {
        name: this.category.name
      };
      this.submitting = false;
    },
    
    onSave() {
      if (this.submitting || !this.canSave) return;
      this.submitting = true;
      
      const saveData = {
        name: this.form.name
      };
      
      if (this.mode === 'create') {
        this.$emit("create", saveData);
      } else {
        // For edit mode, include the id from the original category
        saveData.id = this.category.id;
        this.$emit("save", saveData);
      }
    },
    
    resetSubmitting() {
      this.submitting = false;
    },
    
    async loadCategoryEquipments() {
      this.categoryEquipments = [];
      
      if (!this.category || !this.category.id) return;
      
      // First try to filter from allEquipments prop
      if (this.allEquipments && this.allEquipments.length > 0) {
        this.categoryEquipments = this.allEquipments.filter(equipment => 
          equipment.categories_id == this.category.id || 
          equipment.category?.id == this.category.id
        );
        return;
      }
      
      // Fallback to API calls
      const endpoints = [
        `/api/equipments?category_id=${this.category.id}`,
        `/api/categories/${this.category.id}/equipments`,
        `/admin/categories/${this.category.id}/equipments`
      ];
      
      for (const endpoint of endpoints) {
        try {
          const response = await fetch(endpoint);
          if (response.ok) {
            const data = await response.json();
            this.categoryEquipments = Array.isArray(data) ? data : (data.data || []);
            return;
          }
        } catch (error) {
          console.log(`Failed to fetch from ${endpoint}:`, error);
        }
      }
      
      // Final fallback - empty array
      this.categoryEquipments = [];
    },
    
    viewEquipment(equipment) {
      // Close the category modal first
      this.closeModal();
      
      // Navigate to equipment page with the specific equipment ID
      const equipmentUrl = `/admin/equipment?equipment_id=${equipment.id}`;
      window.location.href = equipmentUrl;
    },
    
    getFirstPhoto(equipment) {
      if (!equipment || !equipment.photo_path) return null;
      
      try {
        const photos = JSON.parse(equipment.photo_path);
        if (Array.isArray(photos) && photos.length > 0) {
          return photos[0];
        }
      } catch (e) {
        return equipment.photo_path;
      }
      return null;
    },
    
    getStatusClass(status) {
      switch (status) {
        case "available":
          return "bg-green-100 text-green-800";
        case "unavailable":
          return "bg-gray-200 text-gray-600";
        case "retired":
          return "bg-red-100 text-red-800";
        case "maintenance":
          return "bg-yellow-100 text-yellow-800";
        default:
          return "bg-gray-100 text-gray-800";
      }
    },
    
    getStatusLabel(status) {
      switch (status) {
        case "available":
          return "พร้อมใช้งาน";
        case "unavailable":
          return "ไม่พร้อมใช้งาน";
        case "retired":
          return "รอคืน";
        case "maintenance":
          return "ซ่อมบำรุง";
        default:
          return "ไม่ระบุ";
      }
    },
    
    formatDate(dateString) {
      if (!dateString) return 'ไม่ระบุ';
      
      try {
        const date = new Date(dateString);
        return date.toLocaleDateString('th-TH', {
          year: 'numeric',
          month: 'long',
          day: 'numeric',
          hour: '2-digit',
          minute: '2-digit'
        });
      } catch (e) {
        return 'ไม่ระบุ';
      }
    }
  }
}
</script>
