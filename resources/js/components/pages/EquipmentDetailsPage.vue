<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Breadcrumb -->
    <nav class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center space-x-2 text-sm text-gray-500">
            <a href="/admin" class="hover:text-gray-700 hover:underline">แดชบอร์ด</a>
            <span>/</span>
            <a href="/admin/equipment" class="hover:text-gray-700 hover:underline">หน้าจัดการอุปกรณ์</a>
            <span>/</span>
            <span class="font-semibold text-gray-900">รายละเอียดอุปกรณ์</span>
          </div>
          <div class="flex items-center space-x-3">
            <button 
              @click="goBack"
              class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
              </svg>
              กลับ
            </button>
            <button 
              v-if="userRole === 'admin'"
              @click="editEquipment"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
              แก้ไขข้อมูล
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center min-h-[400px]">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <!-- Equipment Details -->
    <div v-else-if="equipment" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header Section -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="p-3 bg-blue-100 rounded-lg">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ equipment.name }}</h1>
                <p class="text-sm text-gray-500">รหัส: {{ equipment.code }}</p>
              </div>
            </div>
            <div class="flex items-center space-x-3">
              <span 
                class="inline-flex px-3 py-1 text-sm font-semibold rounded-full"
                :class="getStatusClass(equipment.status)"
              >
                {{ getStatusLabel(equipment.status) }}
              </span>
            </div>
          </div>
        </div>

        <!-- Equipment Photos -->
        <div v-if="equipmentPhotos.length > 0" class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900 mb-4">รูปภาพอุปกรณ์</h3>
          <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <div 
              v-for="(photo, index) in equipmentPhotos" 
              :key="index"
              class="relative group cursor-pointer"
              @click="openPhotoModal(photo)"
            >
              <img 
                :src="photo" 
                :alt="`Equipment photo ${index + 1}`"
                class="w-full h-32 object-cover rounded-lg border border-gray-200 hover:shadow-lg transition-shadow"
              />
              <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all rounded-lg flex items-center justify-center">
                <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Basic Information -->
        <div class="px-6 py-4">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">รหัสอุปกรณ์</label>
                <div class="px-3 py-2 bg-gray-50 rounded-md border">
                  <span class="text-sm font-mono text-gray-900">{{ equipment.code }}</span>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ชื่ออุปกรณ์</label>
                <div class="px-3 py-2 bg-gray-50 rounded-md border">
                  <span class="text-sm text-gray-900">{{ equipment.name }}</span>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">หมวดหมู่</label>
                <div class="px-3 py-2 bg-gray-50 rounded-md border">
                  <span class="text-sm text-gray-900">
                    {{ equipment.category ? equipment.category.name : 'ไม่ระบุ' }}
                  </span>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">สถานะ</label>
                <div class="px-3 py-2 bg-gray-50 rounded-md border">
                  <span 
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    :class="getStatusClass(equipment.status)"
                  >
                    {{ getStatusLabel(equipment.status) }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">รายละเอียด</label>
                <div class="px-3 py-2 bg-gray-50 rounded-md border min-h-[100px]">
                  <span class="text-sm text-gray-900 whitespace-pre-wrap">
                    {{ equipment.description || 'ไม่มีรายละเอียด' }}
                  </span>
                </div>
              </div>

              <div v-if="equipment.accessories">
                <label class="block text-sm font-medium text-gray-700 mb-2">อุปกรณ์เสริม</label>
                <div class="px-3 py-2 bg-gray-50 rounded-md border min-h-[80px]">
                  <span class="text-sm text-gray-900 whitespace-pre-wrap">
                    {{ equipment.accessories }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Additional Information -->
        <div class="px-6 py-4 bg-gray-50 rounded-b-lg">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">วันที่สร้าง</label>
              <div class="text-sm text-gray-900">
                {{ formatDate(equipment.created_at) }}
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">วันที่อัปเดตล่าสุด</label>
              <div class="text-sm text-gray-900">
                {{ formatDate(equipment.updated_at) }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Equipment History/Actions Section (Optional) -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">ประวัติการใช้งาน</h3>
        </div>
        <div class="px-6 py-4">
          <div class="text-center text-gray-500 py-8">
            <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <p class="text-sm">ไม่มีประวัติการใช้งาน</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else class="flex items-center justify-center min-h-[400px]">
      <div class="text-center">
        <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">ไม่พบข้อมูลอุปกรณ์</h3>
        <p class="text-sm text-gray-500 mb-4">อุปกรณ์ที่คุณกำลังมองหาอาจถูกลบหรือไม่ปรากฏในระบบ</p>
        <button 
          @click="goBack"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
        >
          กลับไปหน้าก่อนหน้า
        </button>
      </div>
    </div>

    <!-- Photo Modal -->
    <div v-if="photoModal.isOpen" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="fixed inset-0 bg-black bg-opacity-75" @click="closePhotoModal"></div>
      <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative max-w-4xl max-h-[90vh]">
          <img 
            :src="photoModal.url" 
            alt="Equipment photo"
            class="max-w-full max-h-[80vh] object-contain rounded-lg shadow-2xl"
          />
          <button 
            @click="closePhotoModal"
            class="absolute top-4 right-4 text-white bg-black bg-opacity-50 hover:bg-opacity-75 rounded-full p-2 transition-all"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'EquipmentDetailsPage',
  data() {
    return {
      equipment: null,
      loading: true,
      userRole: '',
      photoModal: {
        isOpen: false,
        url: ''
      }
    }
  },
  computed: {
    equipmentPhotos() {
      if (!this.equipment || !this.equipment.photo_path) return [];
      
      try {
        const photos = JSON.parse(this.equipment.photo_path);
        return Array.isArray(photos) ? photos : [];
      } catch (e) {
        return [this.equipment.photo_path];
      }
    }
  },
  methods: {
    async loadEquipment() {
      try {
        this.loading = true;
        const equipmentId = this.$route.params.id;
        const response = await fetch(`/admin/equipment/${equipmentId}`, {
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          }
        });
        
        if (response.ok) {
          const data = await response.json();
          this.equipment = data.equipment;
          this.userRole = data.userRole || 'user';
        } else {
          this.equipment = null;
        }
      } catch (error) {
        console.error('Error loading equipment:', error);
        this.equipment = null;
      } finally {
        this.loading = false;
      }
    },
    
    goBack() {
      this.$router.go(-1);
    },
    
    editEquipment() {
      this.$router.push(`/admin/equipment/edit/${this.equipment.id}`);
    },
    
    openPhotoModal(url) {
      this.photoModal.url = url;
      this.photoModal.isOpen = true;
    },
    
    closePhotoModal() {
      this.photoModal.url = '';
      this.photoModal.isOpen = false;
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
  },
  
  async mounted() {
    await this.loadEquipment();
  }
}
</script>
