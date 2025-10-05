<template>
  <!-- Breadcrumb -->
  <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
    <a href="/admin" class="hover:text-gray-700 hover:underline">แดชบอร์ด</a>
    <span>/</span>
    <span class="font-semibold text-gray-900">หน้าจัดการอุปกรณ์</span>
  </nav>

  <BaseTable
    :data="equipments"
    :columns="columns"
    :user-role="userRole"
    :loading="loading"
    title="อุปกรณ์รวมกันทั้งหมด"
    search-placeholder="ค้นหาอุปกรณ์..."
    add-button-text="เพิ่มอุปกรณ์ใหม่"
    :show-pagination="true"
    :show-advanced-filters="true"
    :page-size="15"
    :search-fields="['name', 'code', 'description', 'status']"
    :available-filters="availableFilters"
    @create="openCreateModal"
    @edit="openEditModal"
    @delete="deleteEquipment"
    @view="viewEquipmentDetails"
  >

    <!-- Custom Rows -->
    <template #rows="{ item: equipment, actions }">
      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
        <div class="flex items-center space-x-2">
        <img 
          v-if="getFirstPhoto(equipment)" 
          :src="getFirstPhoto(equipment)" 
          alt="Equipment Photo"
          class="w-8 h-8 object-cover rounded cursor-pointer"
          @click="viewEquipmentDetails(equipment)" 
        />
        </div>
      </td>
      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
        <span 
          class="text-blue-600 hover:text-blue-800 cursor-pointer font-medium" 
          @click="viewEquipmentDetails(equipment)"
        >
          {{ equipment.code }}
        </span>
      </td>
      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
        <span 
          class="text-blue-600 hover:text-blue-800 cursor-pointer hover:underline font-medium" 
          @click="viewEquipmentDetails(equipment)"
        >
          {{ equipment.name }}
        </span>
      </td>
      <td class="px-6 py-4 text-sm text-gray-900 max-w-[200px]">
        <div class="truncate">{{ equipment.description }}</div>
      </td>
      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
        <span 
          v-if="equipment.category" 
          class="text-blue-600 hover:text-blue-800 cursor-pointer hover:underline" 
          @click="viewCategory(equipment.category)"
        >
          {{ equipment.category.name }}
        </span>
        <span v-else class="text-gray-400">N/A</span>
      </td>
      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
        <span 
          class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
          :class="statusClass(equipment.status)"
        >
          {{ getStatusLabel(equipment.status) }}
        </span>
      </td>
      <td v-if="userRole === 'admin'" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
        <div class="flex items-center space-x-2">
          <button 
            @click="actions.edit()"
            class="bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded flex items-center justify-center"
            :disabled="updatingEquipment === equipment.id"
            title="แก้ไขข้อมูล"
          >
            <svg v-if="updatingEquipment === equipment.id" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
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
            :disabled="deletingEquipment === equipment.id"
            title="ลบรายการ"
          >
            <svg v-if="deletingEquipment === equipment.id" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
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
      <span 
        v-for="status in statuses" 
        :key="status" 
        class="px-2 py-1 rounded text-sm font-medium"
        :class="statusClass(status)"
      >
        {{ capitalize(status) }}: {{ counts[status] || 0 }}
      </span>
    </template>

  </BaseTable>

  <!-- Unified Modal -->
  <EquipmentModal 
    ref="equipmentModal"
    :isOpen="modal.isOpen" 
    :mode="modal.mode"
    :equipment="selectedEquipment" 
    :categories="categories"
    :statuses="statuses"
    :user-role="userRole"
    @close="closeModal" 
    @edit="openEditModal"
    @save="updateEquipment"
    @create="createEquipment"
  />
</template>

<script>
import BaseTable from './BaseTable.vue';
import EquipmentModal from "../modals/EquipmentModal.vue";

export default {
  name: "EquipmentTableNew",
  components: {
    BaseTable,
    EquipmentModal,
  },
  
  data() {
    const el = document.getElementById("equipment-table");
    return {
      userRole: el?.dataset?.role || "",
      equipments: JSON.parse(el?.dataset?.equipments || "[]"),
      categories: JSON.parse(el?.dataset?.categories || "[]"),
      statuses: ["available", "unavailable", "retired", "maintenance"],
      loading: false,
      selectedEquipment: {},
      modal: {
        isOpen: false,
        mode: 'view' // 'view', 'edit', or 'create'
      },
      deletingEquipment: null,
      updatingEquipment: null,
      columns: [
        { key: 'photo', label: 'รูป' },
        { key: 'code', label: 'เลขหมายครุภัณฑ์' },
        { key: 'name', label: 'ชื่ออุปกรณ์' },
        { key: 'description', label: 'รายละเอียด' },
        { key: 'category.name', label: 'หมวดหมู่' },
        { key: 'status', label: 'สถานะ' }
      ],
      availableFilters: [
        {
          key: 'status',
          label: 'สถานะ',
          type: 'select',
          placeholder: 'เลือกสถานะ',
          options: [
            { value: 'available', label: 'พร้อมใช้งาน' },
            { value: 'unavailable', label: 'ไม่พร้อมใช้งาน' },
            { value: 'retired', label: 'รอคืน' },
            { value: 'maintenance', label: 'ซ่อมบำรุง' }
          ]
        },
        {
          key: 'category.id',
          label: 'หมวดหมู่',
          type: 'select',
          placeholder: 'เลือกหมวดหมู่',
          dynamicOptions: true,
          getOptions: () => {
            return this.validCategories.map(cat => ({
              value: cat.id,
              label: cat.name
            }));
          }
        },
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
    validCategories() {
      return (this.categories || []).filter((c) => c && c.id != null);
    }
  },
  
  methods: {
    capitalize(str) {
      if (!str) return "";
      return str.charAt(0).toUpperCase() + str.slice(1);
    },
    
    statusClass(status) {
      switch (status) {
        case "available":
          return "bg-green-100 text-green-800";
        case "unavailable":
          return "bg-red-200 text-red-600";
        case "retired":
          return "bg-purple-200 text-purple-600";
        case "maintenance":
          return "bg-yellow-100 text-yellow-800";
        default:
          return "bg-gray-200 text-gray-600";
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
    
    getFirstPhoto(equipment) {
      if (!equipment.photo_path) return null;
      try {
        const photos = JSON.parse(equipment.photo_path);
        return Array.isArray(photos) && photos.length > 0 ? photos[0] : null;
      } catch (e) {
        return equipment.photo_path;
      }
    },
    
    
    openCreateModal() {
      this.openModal(null, 'create');
    },
    
    
    openModal(equipment, mode = 'view') {
      this.selectedEquipment = equipment ? { ...equipment } : null;
      this.modal.mode = mode;
      this.modal.isOpen = true;
    },
    
    closeModal() {
      this.modal.isOpen = false;
      this.selectedEquipment = null;
    },
    
    openEditModal(equipment) {
      this.openModal(equipment, 'edit');
    },
    
    viewEquipmentDetails(equipment) {
      this.openModal(equipment, 'view');
    },
    
    
    viewCategory(category) {
      // Navigate to category page with the specific category ID
      const categoryUrl = `/admin/category?category_id=${category.id}`;
      window.location.href = categoryUrl;
    },
    
    updateEquipment(payload) {
      this.updatingEquipment = payload.id;
      const formData = new FormData();
      formData.append("code", payload.code || "");
      formData.append("name", payload.name || "");
      formData.append("description", payload.description || "");
      formData.append("accessories", payload.accessories || "");
      formData.append("categories_id", String(payload.categories_id ?? ""));
      formData.append("status", payload.status || "available");

      if (payload.imagesToDelete && payload.imagesToDelete.length > 0) {
        payload.imagesToDelete.forEach((url) => {
          formData.append("images_to_delete[]", url);
        });
      }
      if (payload.newImageFiles && payload.newImageFiles.length > 0) {
        payload.newImageFiles.forEach((file) => {
          formData.append("images[]", file);
        });
      }
      if (payload.selectedMainIdentifier) {
        formData.append("selected_main_identifier", payload.selectedMainIdentifier);
      }

      formData.append("_method", "PUT");
      fetch(`/admin/equipment/update/${payload.id}`, {
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
          Accept: "application/json",
        },
        body: formData,
      })
        .then(async (res) => {
          if (!res.ok) {
            let msg = "Update failed";
            try {
              const errorData = await res.json();
              msg = errorData.message || JSON.stringify(errorData.errors || errorData);
            } catch (e) {
              msg = `HTTP ${res.status}: ${res.statusText}`;
            }
            throw new Error(msg);
          }
          return res.json();
        })
        .then((data) => {
          const updated = data.data;
          const idx = this.equipments.findIndex((e) => e.id === updated.id);
          if (idx !== -1) {
            this.equipments.splice(idx, 1, updated);
          }
          this.closeModal();
          this.ensureSwal().then(() => {
            window.Swal.fire({
              title: "อัปเดตสำเร็จ",
              text: `ทำการอัพเดทเรียบร้อย ${payload.name} (ID: ${payload.code})`,
              icon: "success",
              timer: 1200,
              showConfirmButton: false,
            });
          });
        })
        .catch((err) => {
          this.notifyError(err.message || "ไม่สามารถอัปเดตได้");
          // Reset the modal's submitting state on error
          if (this.$refs.equipmentModal && this.$refs.equipmentModal.resetSubmitting) {
            this.$refs.equipmentModal.resetSubmitting();
          }
        })
        .finally(() => {
          this.updatingEquipment = null;
        });
    },
    
    deleteEquipment(equipment) {
      this.ensureSwal().then(() => {
        window.Swal.fire({
          title: "ลบรายการ?",
          text: `คุณกำลังจะลบ: ${equipment.name} (ID: ${equipment.code})`,
          icon: "warning",
          imageUrl: this.getFirstPhoto(equipment),
          imageWidth: 120,
          imageHeight: 120,
          imageAlt: equipment.name,
          showCancelButton: true,
          confirmButtonText: "ลบ",
          cancelButtonText: "ยกเลิก",
          confirmButtonColor: "#ef4444",
        }).then((result) => {
          if (result.isConfirmed) {
            this.deletingEquipment = equipment.id;
            fetch(`/admin/equipment/destroy/${equipment.id}`, {
              method: "DELETE",
              headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                Accept: "application/json",
              },
            })
              .then(async (res) => {
                if (!res.ok) {
                  let msg = "Delete failed";
                  try {
                    const j = await res.json();
                    msg = j.message || JSON.stringify(j);
                  } catch (e) { }
                  throw new Error(msg);
                }
                return res.json();
              })
              .then(() => {
                this.equipments = this.equipments.filter((e) => e.id !== equipment.id);
                window.Swal.fire({
                  title: "ลบแล้ว",
                  text: `${equipment.name} ถูกลบเรียบร้อย`,
                  icon: "success",
                  timer: 1200,
                  showConfirmButton: false,
                });
              })
              .catch((err) => {
                this.notifyError(err.message || "ลบไม่สำเร็จ");
              })
              .finally(() => {
                this.deletingEquipment = null;
              });
          }
        });
      });
    },
    
    createEquipment(payload) {
      const formData = new FormData();
      formData.append("code", payload.code || "");
      formData.append("name", payload.name || "");
      formData.append("description", payload.description || "");
      formData.append("accessories", payload.accessories || "");
      formData.append("categories_id", payload.categories_id || "");
      formData.append("status", payload.status || "available");

      if (payload.newImageFiles && payload.newImageFiles.length > 0) {
        for (const file of payload.newImageFiles) {
          formData.append("images[]", file);
        }
      }
      if (payload.selectedMainIdentifier !== null && payload.selectedMainIdentifier !== undefined) {
        formData.append("selectedMainIdentifier", payload.selectedMainIdentifier);
      }

      fetch(`/admin/equipment/store`, {
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
          Accept: "application/json",
        },
        body: formData,
      })
        .then(async (res) => {
          if (!res.ok) {
            let msg = "Create failed";
            try {
              const j = await res.json();
              msg = j.message || JSON.stringify(j);
            } catch (e) { }
            throw new Error(msg);
          }
          return res.json();
        })
        .then((data) => {
          let created = data.data;
          if (!created.category && created.categories_id) {
            const found = this.categories.find((c) => c.id === created.categories_id);
            if (found) {
              created = { ...created, category: found };
            }
          }
          this.equipments.unshift(created);
          this.closeModal();
          this.ensureSwal().then(() => {
            window.Swal.fire({
              title: "เพิ่มข้อมูลสำเร็จ",
              text: `ทำการเพี่มข้อมูลเรียบร้อย ${payload.name} (ID: ${payload.code})`,
              icon: "success",
              timer: 1200,
              showConfirmButton: false,
            });
          });
        })
        .catch((err) => {
          this.notifyError(err.message || "ไม่สามารถเพิ่มข้อมูลได้");
          if (this.$refs.equipmentModal && this.$refs.equipmentModal.resetSubmitting) {
            this.$refs.equipmentModal.resetSubmitting();
          }
        });
    },
    
    
    ensureSwal() {
      return new Promise((resolve) => {
        if (window.Swal) return resolve();
        const script = document.createElement("script");
        script.src = "https://cdn.jsdelivr.net/npm/sweetalert2@11";
        script.onload = () => resolve();
        document.head.appendChild(script);
      });
    },
    
    notifyError(message) {
      let errorMessage = message;
      if (typeof message === "object") {
        if (message.message) {
          errorMessage = message.message;
        } else {
          errorMessage = JSON.stringify(message);
        }
      }

      if (window.Swal) {
        window.Swal.fire({
          title: "เกิดข้อผิดพลาด",
          text: errorMessage,
          icon: "error",
        });
      } else {
        alert(errorMessage);
      }
    }
  },
  
  mounted() {
    // Check for URL parameters to filter by category
    const urlParams = new URLSearchParams(window.location.search);
    const categoryId = urlParams.get('category_id');
    const categoryName = urlParams.get('category');
    const equipmentId = urlParams.get('equipment_id');
    
    if (categoryId) {
      // Auto-filter by category if needed
    }
    
    if (categoryName) {
      document.title = `อุปกรณ์ - ${decodeURIComponent(categoryName)}`;
    }
    
    // Auto-open equipment modal if equipment_id is provided
    if (equipmentId) {
      const equipment = this.equipments.find(e => e.id == equipmentId);
      if (equipment) {
        // Small delay to ensure the page is fully loaded
        setTimeout(() => {
          this.viewEquipmentDetails(equipment);
        }, 100);
      }
    }
  }
};
</script>
