<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>
    
    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
      <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col min-h-0">
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
                {{ mode === 'view' ? 'รายละเอียดผู้ใช้' : mode === 'edit' ? 'แก้ไขผู้ใช้' : 'เพิ่มผู้ใช้ใหม่' }}
              </h3>
              <p class="text-sm text-gray-500">
                {{ mode === 'view' ? user?.name : mode === 'edit' ? form?.name : 'ผู้ใช้ใหม่' }}
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
          <div v-if="mode === 'view' && user" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อผู้ใช้</label>
                <div class="px-3 py-2 bg-gray-50 rounded-md border">
                  <span class="text-sm text-gray-900">{{ user.name }}</span>
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">UID</label>
                <div class="px-3 py-2 bg-gray-50 rounded-md border">
                  <span class="text-sm font-mono text-gray-900">{{ user.uid }}</span>
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">อีเมล</label>
                <div class="px-3 py-2 bg-gray-50 rounded-md border">
                  <span class="text-sm text-gray-900">{{ user.email }}</span>
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">เบอร์โทร</label>
                <div class="px-3 py-2 bg-gray-50 rounded-md border">
                  <span class="text-sm text-gray-900">{{ user.phonenumber || 'ไม่ระบุ' }}</span>
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">บทบาท</label>
                <div class="px-3 py-2 bg-gray-50 rounded-md border">
                  <span 
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    :class="getRoleClass(user.role)"
                  >
                    {{ getRoleLabel(user.role) }}
                  </span>
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">วันที่สมัคร</label>
                <div class="px-3 py-2 bg-gray-50 rounded-md border">
                  <span class="text-sm text-gray-900">{{ formatDate(user.created_at) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Edit/Create Mode -->
          <form v-else-if="mode === 'edit' || mode === 'create'" @submit.prevent="onSave" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อผู้ใช้ *</label>
                <input
                  required
                  type="text"
                  v-model.trim="form.name"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="กรอกชื่อผู้ใช้"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">UID *</label>
                <input
                  required
                  type="text"
                  v-model.trim="form.uid"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="กรอก UID"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">อีเมล *</label>
                <input
                  required
                  type="email"
                  v-model.trim="form.email"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="กรอกอีเมล"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">เบอร์โทร</label>
                <input
                  type="tel"
                  v-model.trim="form.phonenumber"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="กรอกเบอร์โทร"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">บทบาท *</label>
                <select
                  required
                  v-model="form.role"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">เลือกบทบาท</option>
                  <option value="admin">ผู้ดูแลระบบ</option>
                  <option value="borrower">ผู้ยืม</option>
                  <option value="staff">เจ้าหน้าที่</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  รหัสผ่าน {{ mode === 'edit' ? '(เว้นว่างไว้หากไม่ต้องการเปลี่ยน)' : '*' }}
                </label>
                <input
                  :required="mode === 'create'"
                  type="password"
                  v-model.trim="form.password"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  :placeholder="mode === 'edit' ? 'กรอกรหัสผ่านใหม่ (ถ้าต้องการเปลี่ยน)' : 'กรอกรหัสผ่าน'"
                />
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
            form="user-form"
            @click="onSave"
            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center justify-center"
            :disabled="submitting"
          >
            <svg v-if="submitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span v-if="submitting">กำลังบันทึก...</span>
            <span v-else>{{ mode === 'create' ? 'สร้างผู้ใช้' : 'บันทึกการเปลี่ยนแปลง' }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'UserModal',
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
    user: {
      type: Object,
      default: null
    },
    userRole: {
      type: String,
      default: 'user'
    }
  },
  data() {
    return {
      form: {},
      submitting: false
    }
  },
  watch: {
    isOpen(newVal) {
      if (newVal) {
        if (this.mode === 'edit' && this.user) {
          this.setupForm();
        } else if (this.mode === 'create') {
          this.setupCreateForm();
        }
      }
    },
    mode(newVal) {
      if (newVal === 'edit' && this.user) {
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
      this.$emit('edit', this.user);
    },
    
    setupCreateForm() {
      this.form = {
        name: '',
        email: '',
        phonenumber: '',
        uid: '',
        password: '',
        role: 'borrower'
      };
      this.submitting = false;
    },
    
    setupForm() {
      this.form = {
        name: this.user.name,
        email: this.user.email,
        phonenumber: this.user.phonenumber || '',
        uid: this.user.uid,
        password: '',
        role: this.user.role
      };
      this.submitting = false;
    },
    
    onSave() {
      if (this.submitting) return;
      this.submitting = true;
      
      const saveData = { ...this.form };
      
      if (this.mode === 'create') {
        this.$emit("create", saveData);
      } else {
        // Include user ID for edit mode
        saveData.id = this.user.id;
        this.$emit("save", saveData);
      }
    },
    
    resetSubmitting() {
      this.submitting = false;
    },
    
    getRoleClass(role) {
      switch (role) {
        case "admin":
          return "bg-red-100 text-red-800";
        case "borrower":
          return "bg-green-100 text-green-800";
        case "staff":
          return "bg-yellow-100 text-yellow-800";
        default:
          return "bg-gray-100 text-gray-800";
      }
    },
    
    getRoleLabel(role) {
      switch (role) {
        case "admin":
          return "ผู้ดูแลระบบ";
        case "borrower":
          return "ผู้ยืม";
        case "staff":
          return "เจ้าหน้าที่";
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
