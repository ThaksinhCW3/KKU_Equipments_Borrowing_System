<template>
  <!-- Breadcrumb -->
  <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
    <a href="/admin" class="hover:text-gray-700">แดชบอร์ด</a>
    <span>/</span>
    <span class="font-semibold text-gray-900">หน้าจัดการหมวดหมู่</span>
  </nav>

  <div class="bg-white p-6 rounded-lg shadow">
    <!-- Search Bar -->
    <div class="relative mb-4">
      <input type="text" v-model="searchQuery" placeholder="Search"
        class="pl-10 pr-3 py-2 text-sm border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
      <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
      </svg>
    </div>

    <!-- Header + Counts -->
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-4">
      <h2 class="text-lg font-semibold">
        หมวดหมู่ทั้งหมด: {{ filteredCategories.length }}
      </h2>
      <div class="flex flex-wrap gap-2 items-center">
        <!-- Sort toggle -->
        <button @click="toggleSort"
          class="border border-gray-300 rounded-md px-5 py-2 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
          {{ sortDirection.toUpperCase() }}
        </button>
        <!-- Add button (only admin) -->
        <button v-if="userRole === 'admin'" @click="createModalOpen = true"
          class="ml-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
          เพิ่มหมวดหมู่ใหม่
        </button>
      </div>
    </div>

    <!-- Table -->
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 border-b">
        <tr>
          <th class="px-4 py-2 text-left">รหัสหมวดหมู่</th>
          <th class="px-4 py-2 text-left">ชื่อหมวดหมู่</th>
          <th class="px-4 py-2 text-left">จํานวนอุปกรณ์</th>
          <th v-if="userRole === 'admin'" class="px-4 py-2 text-left">แอคชั่น</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="category in filteredCategories" :key="category.id" class="border-b">
          <td class="px-4 py-2">{{ category.cate_id }}</td>
          <td class="px-4 py-2">
            <button @click="viewCategoryEquipments(category)" 
              class="text-blue-600 hover:text-blue-800 hover:underline cursor-pointer">
              {{ category.name }}
            </button>
          </td>
          <td class="px-4 py-2">{{ category.equipments_count }}</td>
          <td class="px-4 py-2">
            <div class="flex items-center space-x-2">
              <button v-if="userRole === 'admin'" @click="openModal(category)"
                class="bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded flex items-center justify-center"
                :disabled="updatingCategory === category.id"
                title="แก้ไขข้อมูล">
                <svg v-if="updatingCategory === category.id" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </button>
              <button v-if="userRole === 'admin'" @click="deleteCategory(category.id)"
                class="bg-red-500 hover:bg-red-600 text-white p-2 rounded flex items-center justify-center"
                :disabled="deletingCategory === category.id"
                title="ลบรายการ">
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
        </tr>
      </tbody>
    </table>

    <!-- Edit Modal -->
    <CategoryEditModal ref="editModal" :isOpen="isOpen" :category="selectedCategory" @close="isOpen = false"
      @save="updateCategoryFromModal" />

    <!-- Create Modal -->
    <CategoryCreateModal ref="createModal" :isOpen="createModalOpen" @close="createModalOpen = false" @create="handleCreateCategory" />
  </div>
</template>

<script>
import CategoryEditModal from '../modals/CategoryEditModal.vue';
import CategoryCreateModal from '../modals/CategoryCreateModal.vue';

export default {
  name: "CategoriesTable",
  components: { CategoryEditModal, CategoryCreateModal },
  data() {
    const el = document.getElementById("category-table");
    return {
      userRole: el?.dataset?.role || "",
      equipments: [],
      categories: [],
      searchQuery: "",
      isOpen: false,
      createModalOpen: false,
      selectedCategory: null,
      categoryTypes: [],
      sortDirection: "asc",
      updatingCategory: null,
      deletingCategory: null,
    };
  },
  computed: {
    filteredCategories() {
      let list = this.categories;
      if (this.searchQuery) {
        const q = this.searchQuery.toLowerCase();
        list = list.filter(c => c.name.toLowerCase().includes(q));
      }
      // Sort by created_at
      list = [...list].sort((a, b) => {
        const da = new Date(a.created_at);
        const db = new Date(b.created_at);
        return this.sortDirection === "asc" ? da - db : db - da;
      });
      return list;
    },
    typeCounts() {
      const counts = {};
      for (const cat of this.categories) {
        const type = cat.type || 'Unknown';
        counts[type] = (counts[type] || 0) + 1;
      }
      return counts;
    }
  },
  methods: {
    toggleSort() {
      this.sortDirection = this.sortDirection === "asc" ? "desc" : "asc";
    },
    typeClass(type) {
      return type === 'Unknown'
        ? 'bg-gray-100 text-gray-800'
        : 'bg-green-100 text-green-800';
    },
    fetchCategories() {
      fetch("/api/categories")
        .then(res => res.json())
        .then(data => {
          this.categories = data || [];
          this.categoryTypes = [...new Set(this.categories.map(c => c.type || 'Unknown'))];
        });
    },
    fetchEquipments() {
      fetch("/api/equipments")
        .then(res => res.json())
        .then(data => {
          this.equipments = data || [];
        });
    },
    openModal(category) {
      this.selectedCategory = { ...category };
      this.isOpen = true;
    },
    viewCategoryEquipments(category) {
      // Redirect to equipment page with category filter
      window.location.href = `/admin/equipment?category=${encodeURIComponent(category.name)}&category_id=${category.id}`;
    },
    updateCategoryFromModal(updatedCategory) {
      this.updatingCategory = updatedCategory.id;
      fetch(`/admin/category/update/${updatedCategory.id}`, {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ name: updatedCategory.name, type: updatedCategory.type })
      })
        .then(res => res.json())
        .then(data => {
          if (data.status) {
            const index = this.categories.findIndex(c => c.id === updatedCategory.id);
            if (index !== -1) this.categories[index] = { ...this.categories[index], ...updatedCategory };
            this.isOpen = false;
            Swal.fire('สำเร็จ!', 'แก้ไขหมวดหมู่เรียบร้อย', 'success');
          } else {
            Swal.fire('ผิดพลาด', 'ไม่สามารถแก้ไขหมวดหมู่ได้', 'error');
          }
        })
        .catch(() => Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการแก้ไขหมวดหมู่', 'error'))
        .finally(() => {
          this.updatingCategory = null;
          this.$refs.editModal.resetSubmitting();
        });
    },
    deleteCategory(id) {
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
          this.deletingCategory = id;
          fetch(`/admin/category/destroy/${id}`, {
            method: "DELETE",
            headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content }
          }).then(() => {
            this.categories = this.categories.filter(c => c.id !== id);
            Swal.fire('ลบแล้ว!', 'หมวดหมู่ถูกลบเรียบร้อย', 'success');
          }).catch(() => {
            Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการลบหมวดหมู่', 'error');
          }).finally(() => {
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
            this.categoryTypes = [...new Set(this.categories.map(c => c.type || 'Unknown'))];
            this.createModalOpen = false;
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
          this.$refs.createModal.resetSubmitting();
        });
    }
  },
  mounted() {
    const el = document.getElementById("category-table");
    if (el) {
      this.categories = JSON.parse(el.dataset.categories || "[]");
      this.userRole = el.dataset.role || '';
    }
    this.fetchCategories();
    this.fetchEquipments();
  }
};
</script>