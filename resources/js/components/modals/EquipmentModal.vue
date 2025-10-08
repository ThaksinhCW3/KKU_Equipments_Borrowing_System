<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>
    
    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
      <div class="relative bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col min-h-0">
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
                {{ mode === 'view' ? 'รายละเอียดอุปกรณ์' : mode === 'edit' ? 'แก้ไขอุปกรณ์' : 'เพิ่มอุปกรณ์ใหม่' }}
              </h3>
              <p class="text-sm text-gray-500">
                {{ mode === 'view' ? equipment?.name : mode === 'edit' ? form?.name : 'อุปกรณ์ใหม่' }} 
                {{ mode === 'view' ? `(${equipment?.code})` : mode === 'edit' ? `(${form?.code})` : '' }}
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
        <div class="p-6 overflow-y-auto flex-1 min-h-0">
          <!-- View Mode -->
          <div v-if="mode === 'view' && equipment" class="space-y-6">
            <!-- Equipment Photos -->
            <div v-if="equipmentPhotos.length > 0" class="space-y-4">
              <h4 class="text-lg font-medium text-gray-900">รูปภาพอุปกรณ์</h4>
              <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Left Column -->
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">รหัสอุปกรณ์</label>
                  <div class="px-3 py-2 bg-gray-50 rounded-md border">
                    <span class="text-sm font-mono text-gray-900">{{ equipment.code }}</span>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">ชื่ออุปกรณ์</label>
                  <div class="px-3 py-2 bg-gray-50 rounded-md border">
                    <span class="text-sm text-gray-900">{{ equipment.name }}</span>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">หมวดหมู่</label>
                  <div class="px-3 py-2 bg-gray-50 rounded-md border">
                    <span class="text-sm text-gray-900">
                      {{ equipment.category ? equipment.category.name : 'ไม่ระบุ' }}
                    </span>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">สถานะ</label>
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
                  <label class="block text-sm font-medium text-gray-700 mb-1">รายละเอียด</label>
                  <div class="px-3 py-2 bg-gray-50 rounded-md border min-h-[80px]">
                    <span class="text-sm text-gray-900 whitespace-pre-wrap">
                      {{ equipment.description || 'ไม่มีรายละเอียด' }}
                    </span>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">อุปกรณ์เสริม</label>
                  <div class="px-3 py-2 bg-gray-50 rounded-md border min-h-[60px]">
                    <div v-if="formattedAccessories.length > 0" class="space-y-1">
                      <div v-for="(accessory, index) in formattedAccessories" :key="index" class="flex items-center">
                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                        <span class="text-sm text-gray-900">{{ accessory }}</span>
                      </div>
                    </div>
                    <div v-else class="text-sm text-gray-500">
                      ไม่มีอุปกรณ์เสริม
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Additional Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-200">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">วันที่สร้าง</label>
                <div class="px-3 py-2 bg-gray-50 rounded-md border">
                  <span class="text-sm text-gray-900">
                    {{ formatDate(equipment.created_at) }}
                  </span>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">วันที่อัปเดตล่าสุด</label>
                <div class="px-3 py-2 bg-gray-50 rounded-md border">
                  <span class="text-sm text-gray-900">
                    {{ formatDate(equipment.updated_at) }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Edit/Create Mode -->
          <form v-else-if="mode === 'edit' || mode === 'create'" id="equipment-form" @submit.prevent="onSave" novalidate class="space-y-6">
            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Left Column -->
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">รหัสอุปกรณ์</label>
                  <input
                    required
                    type="text"
                    v-model.trim="form.code"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">ชื่ออุปกรณ์</label>
                  <input
                    required
                    type="text"
                    v-model.trim="form.name"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">หมวดหมู่</label>
                  <select
                    required
                    v-model="form.categories_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option value="">เลือกหมวดหมู่</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                      {{ cat.name }}
                    </option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">สถานะ</label>
                  <select
                    required
                    v-model="form.status"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option v-for="s in statuses" :key="s" :value="s">
                      {{ capitalize(s) }}
                    </option>
                  </select>
                </div>
              </div>

              <!-- Right Column -->
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">รายละเอียด</label>
                  <textarea
                    v-model.trim="form.description"
                    class="w-full h-24 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                    placeholder="กรอกรายละเอียดอุปกรณ์"
                  ></textarea>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">อุปกรณ์เสริม</label>
                  <textarea
                    v-model.trim="form.accessories"
                    class="w-full h-20 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                    placeholder="กรอกรายการอุปกรณ์ที่ติดมากับเครื่อง (เช่น สายไฟ, แบตเตอรี่, คู่มือ)&#10;สามารถแยกด้วยเครื่องหมายจุลภาค (,) หรือขึ้นบรรทัดใหม่"
                  ></textarea>
                  <p class="text-xs text-gray-500 mt-1">ตัวอย่าง: สายไฟ, แบตเตอรี่, คู่มือ หรือแยกแต่ละรายการในบรรทัดใหม่</p>
                </div>
              </div>
            </div>

            <!-- Image Management Section (Edit Mode Only) -->
            <div v-if="mode === 'edit'" class="pt-4 border-t border-gray-200">
              <h4 class="text-lg font-medium text-gray-900 mb-4">จัดการรูปภาพ</h4>

              <!-- Existing Images -->
              <div v-if="existingImages.length > 0" class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">รูปภาพปัจจุบัน</label>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                  <div
                    v-for="image in existingImages"
                    :key="image.id"
                    class="relative group"
                  >
                    <img
                      :src="image.url"
                      alt="Existing image"
                      class="w-full h-32 object-cover rounded-lg border-2 transition-all duration-200"
                      :class="{ 
                        'opacity-30': imagesToDelete.includes(image.url),
                        'border-blue-500 shadow-lg': selectedMainIdentifier === image.url && !imagesToDelete.includes(image.url),
                        'border-gray-200': selectedMainIdentifier !== image.url
                      }"
                    />
                    <div v-if="imagesToDelete.includes(image.url)" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 rounded-lg">
                        <span class="text-white font-bold text-sm">ลบ</span>
                    </div>
                    <div v-if="!imagesToDelete.includes(image.url)" class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 flex items-center justify-center gap-2 transition-opacity rounded-lg">
                        <button type="button" @click="setAsMain(image.url)" class="w-8 h-8 flex items-center justify-center bg-white/80 rounded-full text-lg hover:bg-white" title="ตั้งเป็นภาพหลัก">★</button>
                        <button type="button" @click="toggleDeletion(image)" class="w-8 h-8 flex items-center justify-center bg-white/80 rounded-full text-lg hover:bg-white" title="ลบรูปภาพ">×</button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Add New Images -->
              <div>
                <div class="flex items-center justify-between mb-3">
                  <label class="block text-sm font-medium text-gray-700">เพิ่มรูปภาพใหม่</label>
                  <span class="text-xs text-gray-500">
                    {{ existingImages.length + newImageFiles.length }}/10 รูป
                  </span>
                </div>
                <input type="file" accept="image/*" multiple @change="onImageChange" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" :disabled="processingImages || (existingImages.length + newImageFiles.length >= 10)"/>
                <p v-if="processingImages" class="text-sm text-blue-600 mt-1">กำลังประมวลผลรูปภาพ...</p>
                <p v-if="imageError" class="text-sm text-red-600 mt-1">{{ imageError }}</p>
                <p v-if="existingImages.length + newImageFiles.length >= 10" class="text-sm text-orange-600 mt-1">ถึงขีดจำกัดสูงสุด 10 รูปแล้ว</p>

                <div v-if="newImagePreviewUrls.length > 0" class="mt-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                  <div v-for="(url, index) in newImagePreviewUrls" :key="index" class="relative group">
                    <img :src="url" alt="New preview" class="w-full h-32 object-cover rounded-lg border-2" :class="selectedMainIdentifier === newImageFiles[index].name ? 'border-blue-500 shadow-lg' : 'border-gray-200'"/>
                    <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 flex items-center justify-center gap-2 transition-opacity rounded-lg">
                        <button type="button" @click="setAsMain(newImageFiles[index].name)" class="w-8 h-8 flex items-center justify-center bg-white/80 rounded-full text-lg hover:bg-white" title="ตั้งเป็นภาพหลัก">★</button>
                        <button type="button" @click="removeNewImage(index)" class="w-8 h-8 flex items-center justify-center bg-white/80 rounded-full text-lg hover:bg-white" title="ลบรูปภาพ">×</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Image Upload Section (Create Mode Only) -->
            <div v-if="mode === 'create'" class="pt-4 border-t border-gray-200">
              <h4 class="text-lg font-medium text-gray-900 mb-4">รูปภาพอุปกรณ์</h4>
              <div>
                <div class="flex items-center justify-between mb-3">
                  <label class="block text-sm font-medium text-gray-700">เพิ่มรูปภาพ</label>
                  <span class="text-xs text-gray-500">
                    {{ newImageFiles.length }}/10 รูป
                  </span>
                </div>
                <input type="file" accept="image/*" multiple @change="onImageChange" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" :disabled="processingImages || newImageFiles.length >= 10"/>
                <p v-if="processingImages" class="text-sm text-blue-600 mt-1">กำลังประมวลผลรูปภาพ...</p>
                <p v-if="imageError" class="text-sm text-red-600 mt-1">{{ imageError }}</p>
                <p v-if="newImageFiles.length >= 10" class="text-sm text-orange-600 mt-1">ถึงขีดจำกัดสูงสุด 10 รูปแล้ว</p>

                <div v-if="newImagePreviewUrls.length > 0" class="mt-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                  <div v-for="(url, index) in newImagePreviewUrls" :key="index" class="relative group">
                    <img :src="url" alt="New preview" class="w-full h-32 object-cover rounded-lg border-2" :class="selectedMainIdentifier === newImageFiles[index].name ? 'border-blue-500 shadow-lg' : 'border-gray-200'"/>
                    <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 flex items-center justify-center gap-2 transition-opacity rounded-lg">
                        <button type="button" @click="setAsMain(newImageFiles[index].name)" class="w-8 h-8 flex items-center justify-center bg-white/80 rounded-full text-lg hover:bg-white" title="ตั้งเป็นภาพหลัก">★</button>
                        <button type="button" @click="removeNewImage(index)" class="w-8 h-8 flex items-center justify-center bg-white/80 rounded-full text-lg hover:bg-white" title="ลบรูปภาพ">×</button>
                    </div>
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
        <div class="flex items-center justify-end space-x-3 p-6 border-t border-gray-200 bg-gray-50 flex-shrink-0">
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
            form="equipment-form"
            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center justify-center"
            :disabled="submitting || processingImages || !canSave"
          >
            <svg v-if="submitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span v-if="submitting">กำลังบันทึก...</span>
            <span v-else>{{ mode === 'create' ? 'สร้างอุปกรณ์' : 'บันทึกการเปลี่ยนแปลง' }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Photo Modal -->
    <div v-if="photoModal.isOpen" class="fixed inset-0 z-60 overflow-y-auto">
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
  name: 'EquipmentModal',
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
    equipment: {
      type: Object,
      default: null
    },
    categories: {
      type: Array,
      default: () => []
    },
    statuses: {
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
      existingImages: [],
      imagesToDelete: [], 
      newImageFiles: [],
      newImagePreviewUrls: [],
      imageError: "",
      processingImages: false,
      submitting: false,
      selectedMainIdentifier: null,
      photoModal: {
        isOpen: false,
        url: ''
      },
      validationErrors: {},
      showValidationErrors: false
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
    },
    formattedAccessories() {
      if (!this.equipment || !this.equipment.accessories) return [];
      
      try {
        // Handle different formats
        if (Array.isArray(this.equipment.accessories)) {
          return this.equipment.accessories;
        } else if (typeof this.equipment.accessories === 'string') {
          const parsed = JSON.parse(this.equipment.accessories);
          return Array.isArray(parsed) ? parsed : [];
        }
        return [];
      } catch (e) {
        // If JSON parsing fails, try to split by comma
        if (typeof this.equipment.accessories === 'string') {
          return this.equipment.accessories.split(',').map(item => item.trim()).filter(item => item);
        }
        return [];
      }
    },
    canSave() {
      const hasBasicInfo = !!(this.form && this.form.code && this.form.name && this.form.categories_id && this.form.status);
      const hasNoImageError = !this.imageError;
      const withinImageLimit = this.mode === 'create' ? 
        this.newImageFiles.length <= 10 : 
        (this.existingImages.length - this.imagesToDelete.length + this.newImageFiles.length) <= 10;
      
      return hasBasicInfo && hasNoImageError && withinImageLimit;
    },
    hasValidationError(field) {
      return this.showValidationErrors && this.validationErrors[field];
    },
    getFieldClass(field) {
      const baseClass = "w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2";
      if (this.hasValidationError(field)) {
        return `${baseClass} border-red-500 focus:ring-red-500`;
      }
      return `${baseClass} border-gray-300 focus:ring-blue-500`;
    }
  },
  watch: {
    isOpen(newVal) {
      if (newVal) {
        if (this.mode === 'edit' && this.equipment) {
          this.setupForm();
        } else if (this.mode === 'create') {
          this.setupCreateForm();
        }
      }
    },
    mode(newVal) {
      if (newVal === 'edit' && this.equipment) {
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
      this.$emit('edit', this.equipment);
    },
    
    setupCreateForm() {
      this.form = {
        code: '',
        name: '',
        description: '',
        accessories: '',
        categories_id: '',
        status: 'available'
      };
      
      this.existingImages = [];
      this.imagesToDelete = [];
      this.newImageFiles = [];
      this.newImagePreviewUrls = [];
      this.imageError = "";
      this.submitting = false;
      this.processingImages = false;
      this.selectedMainIdentifier = null;
    },
    
    setupForm() {
      this.form = JSON.parse(JSON.stringify(this.equipment));
      this.form.categories_id = this.equipment.category?.id || null;
      
      // Handle accessories - convert from JSON array to string for editing
      if (this.equipment.accessories) {
        if (Array.isArray(this.equipment.accessories)) {
          this.form.accessories = this.equipment.accessories.join(', ');
        } else if (typeof this.equipment.accessories === 'string') {
          try {
            const parsed = JSON.parse(this.equipment.accessories);
            if (Array.isArray(parsed)) {
              this.form.accessories = parsed.join(', ');
            } else {
              this.form.accessories = this.equipment.accessories;
            }
          } catch (e) {
            this.form.accessories = this.equipment.accessories;
          }
        } else {
          this.form.accessories = this.equipment.accessories;
        }
      } else {
        this.form.accessories = '';
      }
      
      this.existingImages = [];
      this.selectedMainIdentifier = null;

      if (this.equipment.photo_path) {
        try {
          const photos = JSON.parse(this.equipment.photo_path);
          if (Array.isArray(photos) && photos.length > 0) {
            this.existingImages = photos.map(url => ({ id: url, url: url }));
            this.selectedMainIdentifier = photos[0];
          }
        } catch (e) {
          this.existingImages = [{ id: this.equipment.photo_path, url: this.equipment.photo_path }];
          this.selectedMainIdentifier = this.equipment.photo_path;
        }
      }

      this.imagesToDelete = [];
      this.newImageFiles = [];
      this.newImagePreviewUrls = [];
      this.imageError = "";
      this.submitting = false;
      this.processingImages = false;
    },
    
    onSave() {
      if (this.submitting || !this.canSave) return;
      this.submitting = true;
      
      const saveData = {
        ...this.form,
        newImageFiles: this.newImageFiles,
        imagesToDelete: this.imagesToDelete, 
        selectedMainIdentifier: this.selectedMainIdentifier, 
      };
      
      if (this.mode === 'create') {
        this.$emit("create", saveData);
      } else {
        this.$emit("save", saveData);
      }
    },
    
    resetSubmitting() {
      this.submitting = false;
    },
    
    // Image management methods (same as EquipmentEditModal)
    setAsMain(identifier) {
      this.selectedMainIdentifier = identifier;
    },
    
    toggleDeletion(image) {
      const imageUrl = image.url;
      const index = this.imagesToDelete.indexOf(imageUrl);
      if (index > -1) {
        this.imagesToDelete.splice(index, 1);
      } else {
        this.imagesToDelete.push(imageUrl);
        if (this.selectedMainIdentifier === imageUrl) {
          this.selectedMainIdentifier = null;
        }
      }
    },
    
    removeNewImage(index) {
      const removedFile = this.newImageFiles[index];
      this.newImageFiles.splice(index, 1);
      this.newImagePreviewUrls.splice(index, 1);
      
      if (this.selectedMainIdentifier === removedFile.name) {
        this.selectedMainIdentifier = null;
      }
    },
    
    async onImageChange(event) {
      const files = event.target.files;
      if (!files || files.length === 0) return;
      this.imageError = "";
      this.processingImages = true;
      
      // Check total image limit (existing + new images)
      const currentTotalImages = this.existingImages.length + this.newImageFiles.length;
      const newFilesCount = files.length;
      
      if (currentTotalImages + newFilesCount > 10) {
        this.imageError = `ไม่สามารถเพิ่มรูปภาพได้เกิน 10 รูป (ปัจจุบัน: ${currentTotalImages} รูป, พยายามเพิ่ม: ${newFilesCount} รูป)`;
        this.processingImages = false;
        event.target.value = null;
        return;
      }
      
      const resizePromises = [];
      for (const file of files) {
        if (file.size > 5 * 1024 * 1024) {
          this.imageError = `ไฟล์ '${file.name}' ใหญ่เกินไป (จำกัด 5MB)`;
          this.processingImages = false;
          return;
        }
        resizePromises.push(this.resizeImage(file));
      }
      try {
        const resizedImages = await Promise.all(resizePromises);
        resizedImages.forEach(({ blob, dataUrl, originalName }) => {
          const newFile = new File([blob], originalName, { type: blob.type });
          this.newImageFiles.push(newFile);
          this.newImagePreviewUrls.push(dataUrl);
        });
      } catch (error) {
        this.imageError = "เกิดข้อผิดพลาดขณะประมวลผลรูปภาพ";
      } finally {
        this.processingImages = false;
        event.target.value = null;
      }
    },
    
    resizeImage(file, { maxWidth = 1280, quality = 0.85 } = {}) {
      return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = (e) => {
          const img = new Image();
          img.src = e.target.result;
          img.onload = () => {
            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");
            const ratio = Math.min(maxWidth / img.width, 1);
            canvas.width = img.width * ratio;
            canvas.height = img.height * ratio;
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            canvas.toBlob(
              (blob) => {
                if (!blob) return reject(new Error("Canvas to Blob failed."));
                const dataUrl = canvas.toDataURL("image/jpeg", quality);
                resolve({ blob, dataUrl, originalName: file.name });
              },
              "image/jpeg",
              quality
            );
          };
          img.onerror = reject;
        };
        reader.onerror = reject;
      });
    },
    
    // Photo modal methods
    openPhotoModal(url) {
      this.photoModal.url = url;
      this.photoModal.isOpen = true;
    },
    
    closePhotoModal() {
      this.photoModal.url = '';
      this.photoModal.isOpen = false;
    },
    
    // Utility methods
    capitalize(str) {
      return str ? str.charAt(0).toUpperCase() + str.slice(1) : "";
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
          return "ถูกยืม";
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
