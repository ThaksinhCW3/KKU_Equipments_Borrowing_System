<template>
  <BaseTable
    :data="categories"
    :columns="columns"
    :breadcrumbs="breadcrumbs"
    :user-role="userRole"
    :loading="loading"
    title="หมวดหมู่ทั้งหมด"
    search-placeholder="ค้นหาหมวดหมู่..."
    add-button-text="เพิ่มหมวดหมู่ใหม่"
    :show-pagination="false"
    :show-advanced-filters="false"
    :available-filters="availableFilters"
    :search-fields="['name', 'cate_id']"
    @create="openCreateModal"
    @edit="openEditModal"
    @delete="deleteCategory"
    @view="viewCategoryEquipments"
  >

    <!-- Custom Rows -->
    <template #rows="{ item: category, actions }">
      <td class="px-4 py-2">{{ category.cate_id }}</td>
      <td class="px-4 py-2">
        <button 
          @click="viewCategoryEquipments(category)" 
          class="text-blue-600 hover:text-blue-800 hover:underline cursor-pointer"
        >
          {{ category.name }}
        </button>
      </td>
      <td class="px-4 py-2">
        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
          {{ category.equipments_count || 0 }} รายการ
        </span>
      </td>
      <td v-if="userRole === 'admin'" class="px-4 py-2">
        <div class="flex items-center space-x-2">
          <button 
            @click="actions.edit()"
            class="bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded flex items-center justify-center"
            :disabled="updatingCategory === category.id"
            title="แก้ไขข้อมูล"
          >
            <svg v-if="updatingCategory === category.id" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
          </button>
          <button 
            @click="actions.delete()"
            class="bg-red-500 hover:bg-red-600 text-white p-2 rounded flex items-center justify-center"
            :disabled="deletingCategory === category.id"
            title="ลบรายการ"
          >
            <svg v-if="deletingCategory === category.id" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
          </button>
        </div>
      </td>
    </template>

    <!-- Custom Badges -->
    <template #badges="{ counts }">
      <span class="px-2 py-1 rounded text-sm font-medium bg-blue-100 text-blue-800">
        ทั้งหมด: {{ counts.all }}
      </span>
    </template>
  </BaseTable>

  <!-- Unified Modal -->
  <CategoryModal 
    ref="categoryModal"
    :isOpen="modal.isOpen" 
    :mode="modal.mode"
    :category="selectedCategory" 
    :all-equipments="allEquipments"
    :user-role="userRole"
    @close="closeModal" 
    @edit="openEditModal"
    @save="updateCategoryFromModal"
    @create="handleCreateCategory"
  />
</template>

<script>
import BaseTable from './BaseTable.vue';
import CategoryModal from '../modals/CategoryModal.vue';

export default {
  name: "CategoryTableNew",
  components: { 
    BaseTable,
    CategoryModal
  },
  
  data() {
    const el = document.getElementById("category-table");
    return {
      breadcrumbs: [
        { label: 'แดชบอร์ด', url: '/admin' },
        { label: 'หน้าจัดการหมวดหมู่' }
      ],
      userRole: el?.dataset?.role || "",
      categories: [],
      allEquipments: JSON.parse(el?.dataset?.equipments || "[]"),
      loading: false,
      selectedCategory: null,
      modal: {
        isOpen: false,
        mode: 'view' // 'view', 'edit', or 'create'
      },
      updatingCategory: null,
      deletingCategory: null,
      availableFilters: [
        {
          key: 'created_at',
          label: 'วันที่สร้าง',
          type: 'daterange',
          fromPlaceholder: 'จากวันที่',
          toPlaceholder: 'ถึงวันที่'
        }
      ]
    };
  },
  
  computed: {
    columns() {
      const cols = [
        { key: 'cate_id', label: 'รหัสหมวดหมู่' },
        { key: 'name', label: 'ชื่อหมวดหมู่' },
        { key: 'equipments_count', label: 'จํานวนอุปกรณ์' }
      ];
      
      // Only show actions column for admin
      if (this.userRole === 'admin') {
        cols.push({ key: 'actions', label: 'แอคชั่น' });
      }
      
      return cols;
    }
  },
  
  methods: {
    fetchCategories() {
      this.loading = true;
      fetch("/api/categories")
        .then(res => res.json())
        .then(data => {
          this.categories = data || [];
        })
        .catch(err => {
          console.error('Error fetching categories:', err);
        })
        .finally(() => {
          this.loading = false;
        });
    },
    
    async fetchEquipments() {
      if (this.allEquipments.length === 0) {
        console.log('No equipment data available, trying to fetch...');
        try {
          // Try multiple possible endpoints for equipment data
            const endpoints = [
              '/api/equipments',
              '/api/categories',
              '/admin/equipment',
              '/api/admin/equipment'
            ];
          
          for (const endpoint of endpoints) {
            try {
              const response = await fetch(endpoint, {
                headers: {
                  'Accept': 'application/json',
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
              });
              
              if (response.ok) {
                const data = await response.json();
                this.allEquipments = data.equipments || data.data || data || [];
                console.log(`Fetched equipments from ${endpoint}:`, this.allEquipments);
                if (this.allEquipments.length > 0) {
                  break;
                }
              }
            } catch (e) {
              console.log(`Failed to fetch from ${endpoint}:`, e);
            }
          }
          
          if (this.allEquipments.length === 0) {
            console.log('No equipment data found from any endpoint');
          }
        } catch (error) {
          console.error('Error fetching equipments:', error);
        }
      } else {
        console.log('Equipment data already available:', this.allEquipments.length, 'items');
      }
    },
    
    async     openModal(category, mode = 'view') {
      this.selectedCategory = category ? { ...category } : null;
      this.modal.mode = mode;
      this.modal.isOpen = true;
    },
    
    closeModal() {
      this.modal.isOpen = false;
      this.selectedCategory = null;
    },
    
    async openEditModal(category) {
      // Fetch equipment data before opening edit modal
      await this.fetchEquipments();
      this.openModal(category, 'edit');
    },
    
    async viewCategoryEquipments(category) {
      // Fetch equipment data before opening modal
      await this.fetchEquipments();
      this.openModal(category, 'view');
    },
    
    openCreateModal() {
      this.openModal(null, 'create');
    },
    
    
    
    updateCategoryFromModal(updatedCategory) {
      this.updatingCategory = updatedCategory.id;
      fetch(`/admin/category/update/${updatedCategory.id}`, {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ 
          name: updatedCategory.name
        })
      })
        .then(res => res.json())
        .then(data => {
          if (data.status) {
            const index = this.categories.findIndex(c => c.id === updatedCategory.id);
            if (index !== -1) {
              this.categories[index] = { ...this.categories[index], ...updatedCategory };
            }
            this.closeModal();
            Swal.fire('สำเร็จ!', 'แก้ไขหมวดหมู่เรียบร้อย', 'success');
          } else {
            Swal.fire('ผิดพลาด', 'ไม่สามารถแก้ไขหมวดหมู่ได้', 'error');
          }
        })
        .catch(() => Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการแก้ไขหมวดหมู่', 'error'))
        .finally(() => {
          this.updatingCategory = null;
          if (this.$refs.categoryModal && this.$refs.categoryModal.resetSubmitting) {
            this.$refs.categoryModal.resetSubmitting();
          }
        });
    },
    
    deleteCategory(category) {
      Swal.fire({
        title: 'คุณแน่ใจหรือไม่?',
        text: "รายการนี้จะถูกลบถาวร!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ลบเลย',
        cancelButtonText: 'ยกเลิก'
      }).then((result) => {
        if (result.isConfirmed) {
          this.deletingCategory = category.id;
          fetch(`/admin/category/destroy/${category.id}`, {
            method: "DELETE",
            headers: { 
              "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content 
            }
          })
            .then(() => {
              this.categories = this.categories.filter(c => c.id !== category.id);
              Swal.fire('ลบแล้ว!', 'หมวดหมู่ถูกลบเรียบร้อย', 'success');
            })
            .catch(() => {
              Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการลบหมวดหมู่', 'error');
            })
            .finally(() => {
              this.deletingCategory = null;
            });
        }
      });
    },
    
    handleCreateCategory(newCategory) {
      fetch('/admin/category/store', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(newCategory)
      })
        .then(async res => {
          if (!res.ok) throw new Error(await res.text() || 'Server error');
          return res.json();
        })
        .then(data => {
          if (data.status) {
            this.categories.push(data.category);
            this.closeModal();
            Swal.fire('สำเร็จ!', 'สร้างหมวดหมู่เรียบร้อย', 'success');
          } else {
            Swal.fire('ผิดพลาด', data.message || 'ไม่สามารถสร้างหมวดหมู่ได้', 'error');
          }
        })
        .catch(err => {
          console.error(err);
          Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการสร้างหมวดหมู่', 'error');
        })
        .finally(() => {
          if (this.$refs.categoryModal && this.$refs.categoryModal.resetSubmitting) {
            this.$refs.categoryModal.resetSubmitting();
          }
        });
    }
  },
  
  mounted() {
    const el = document.getElementById("category-table");
    if (el) {
      this.categories = JSON.parse(el.dataset.categories || "[]");
      this.userRole = el.dataset.role || '';
      // Also try to get equipments from dataset
      if (el.dataset.equipments) {
        this.allEquipments = JSON.parse(el.dataset.equipments);
      }
    }
    
    // Try to get equipment data from equipment table if it exists on the same page
    const equipmentEl = document.getElementById("equipment-table");
    if (equipmentEl && equipmentEl.dataset.equipments) {
      this.allEquipments = JSON.parse(equipmentEl.dataset.equipments);
      console.log('Found equipment data from equipment table:', this.allEquipments.length, 'items');
    }
    
    console.log('CategoryTable mounted - allEquipments:', this.allEquipments);
    this.fetchCategories();
    
    // Check for URL parameters to auto-open category modal
    const urlParams = new URLSearchParams(window.location.search);
    const categoryId = urlParams.get('category_id');
    
    if (categoryId) {
      // Wait for categories to load, then find and open the modal
      this.$nextTick(() => {
        const category = this.categories.find(c => c.id == categoryId);
        if (category) {
          // Small delay to ensure the page is fully loaded
          setTimeout(() => {
            this.viewCategoryEquipments(category);
          }, 100);
        }
      });
    }
  }
};
</script>
