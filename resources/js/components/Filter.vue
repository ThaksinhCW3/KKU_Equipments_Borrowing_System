<template>
    <div class="relative">
        <div class="sticky top-0 z-40 bg-white py-3 max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-end">
            <div class="flex space-x-2 items-center">
                <!-- applied-filters -->
                <div class="relative inline-block text-left" v-if="appliedCount > 0">
                    <button @click="appliedOpen = !appliedOpen"
                        class="inline-flex items-center px-3 py-1.5 text-sm bg-gray-100 text-gray-900 rounded-full border border-black">
                        <span>ตัวกรอง {{ appliedCount }} ตัว</span>
                        <svg :class="['ml-2 h-3 w-3 transition-transform', appliedOpen ? 'rotate-180' : 'rotate-0']"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div v-if="appliedOpen"
                        class="absolute left-0 mt-2 w-64 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 p-3"
                        style="z-index:9999;">
                        <div class="space-y-2">
                            <div v-for="(label, idx) in appliedLabels" :key="idx"
                                class="flex items-center justify-between px-3 py-2 bg-gray-100 rounded-full border border-black">
                                <span class="text-sm">{{ label }}</span>
                                <button @click="removeFilter(idx)" class="ml-3 text-gray-700">×</button>
                            </div>
                            <button @click="clearAll"
                                class="text-orange-600 text-sm hover:underline">ล้างทั้งหมด</button>
                        </div>
                    </div>
                </div>

                <!-- Open modal button -->
                <button @click="openFilterModal"
                    class="px-3 py-2 text-sm sm:px-4 sm:py-2 sm:text-base mx-2 bg-orange-600 text-white rounded-full hover:bg-orange-700 flex items-center gap-1.5 sm:gap-2 shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4h18M7 8h10M10 12h4" />
                    </svg>
                    ตัวกรอง
                </button>
            </div>
        </div>

        <!-- Modal -->
        <transition name="modal-fade" enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95">
            <div v-if="modalOpen" class="fixed inset-0 z-[9999] flex items-start sm:items-center justify-center p-4">
                <!-- backdrop -->
                <div class="absolute inset-0 bg-black bg-opacity-40" @click="closeFilterModal"></div>

                <!-- panel -->
                <div class="relative bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[85vh] flex flex-col z-10">
                    <!-- Fixed Header -->
                    <div class="flex items-center justify-between px-4 py-4 border-b flex-shrink-0">
                        <h2 class="text-lg font-semibold">ตัวกรอง</h2>
                        <button @click="closeFilterModal" class="text-2xl leading-none" aria-label="ปิด">×</button>
                    </div>

                    <!-- Scrollable Content -->
                    <div class="flex-1 overflow-y-auto px-4 py-4 space-y-4">
                        <!-- Availability filter -->
                        <div>
                            <button @click="accCondition = !accCondition"
                                class="w-full flex items-center justify-between py-3">
                                <span class="font-medium">สถานะ</span>
                                <span class="text-gray-500">▾</span>
                            </button>
                            <div v-show="accCondition" class="space-y-2 pl-1">
                                <label class="flex items-center space-x-2 p-2 hover:bg-gray-50 rounded cursor-pointer">
                                    <input type="radio" name="availability_panel" class="form-radio text-orange-600"
                                        v-model="selectedAvailability" value="" />
                                    <span>ทั้งหมด</span>
                                </label>
                                <label class="flex items-center space-x-2 p-2 hover:bg-gray-50 rounded cursor-pointer">
                                    <input type="radio" name="availability_panel" class="form-radio text-orange-600"
                                        v-model="selectedAvailability" value="available" />
                                    <span>อุปกรณ์พร้อมใช้งาน</span>
                                </label>
                                <label class="flex items-center space-x-2 p-2 hover:bg-gray-50 rounded cursor-pointer">
                                    <input type="radio" name="availability_panel" class="form-radio text-orange-600"
                                        v-model="selectedAvailability" value="unavailable" />
                                    <span>อุปกรณ์ที่ถูกยืม</span>
                                </label>
                                <label class="flex items-center space-x-2 p-2 hover:bg-gray-50 rounded cursor-pointer">
                                    <input type="radio" name="availability_panel" class="form-radio text-orange-600"
                                        v-model="selectedAvailability" value="maintenance" />
                                    <span>อยู่ระหว่างซ่อมบำรุง</span>
                                </label>
                            </div>
                        </div>

                        <!-- Category filter -->
                        <div>
                            <button @click="accPrice = !accPrice" class="w-full flex items-center justify-between py-3">
                                <span class="font-medium">หมวดหมู่</span>
                                <span class="text-gray-500">▾</span>
                            </button>
                            <div v-show="accPrice" class="space-y-2 pl-1 max-h-60 overflow-y-auto">
                                <label class="flex items-center space-x-2 p-2 hover:bg-gray-50 rounded cursor-pointer">
                                    <input type="radio" name="category_panel" class="form-radio text-orange-600"
                                        v-model="selectedCategory" value="" />
                                    <span>ทั้งหมด</span>
                                </label>
                                <label v-for="cat in categories" :key="cat.cate_id"
                                    class="flex items-center space-x-2 p-2 hover:bg-gray-50 rounded cursor-pointer">
                                    <input type="radio" name="category_panel" class="form-radio text-orange-600"
                                        v-model="selectedCategory" :value="cat.cate_id" />
                                    <span>{{ cat.name }}</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Fixed Footer -->
                    <div class="px-4 py-4 border-t flex items-center gap-3 flex-shrink-0 bg-white">
                        <button @click="resetFilters"
                            class="px-4 py-2 rounded-full bg-gray-100 hover:bg-gray-200">รีเซ็ต</button>
                        <button @click="applyFilters"
                            class="ml-auto px-5 py-2 rounded-full bg-orange-600 text-white hover:bg-orange-700">ใช้ตัวกรอง</button>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
export default {
    data() {
        return {
            // panel / modal state
            modalOpen: false,

            // accordions inside modal
            accCondition: true,
            accPrice: true,

            // filters
            categories: [],
            selectedCategory: '',
            selectedAvailability: '',

            // applied filters popover
            appliedOpen: false,
        };
    },
    computed: {
        appliedLabels() {
            const labels = [];
            if (this.selectedAvailability) {
                const statusLabels = {
                    'available': 'เฉพาะที่มีให้ยืม',
                    'unavailable': 'เฉพาะที่ไม่มีให้ยืม',
                    'maintenance': 'อยู่ระหว่างซ่อมบำรุง'
                };
                labels.push(`ความพร้อมใช้งาน: ${statusLabels[this.selectedAvailability] || this.selectedAvailability}`);
            }
            if (this.selectedCategory) {
                const cat = this.categories.find(c => c.cate_id === this.selectedCategory);
                labels.push(`หมวดหมู่: ${cat ? cat.name : this.selectedCategory}`);
            }
            return labels;
        },
        appliedCount() {
            return this.appliedLabels.length;
        },
    },
    mounted() {
        document.addEventListener('click', this.handleClickOutside);
        const mountEl = document.getElementById('filter');
        if (mountEl) {
            try {
                this.categories = JSON.parse(mountEl.dataset.categories || '[]');
            } catch (e) { this.categories = []; }
            this.selectedCategory = mountEl.dataset.currentCategory || '';
            this.selectedAvailability = mountEl.dataset.currentAvailability || '';
        }
    },
    beforeUnmount() {
        document.removeEventListener('click', this.handleClickOutside);
        // ensure body class cleaned up
        document.body.classList.remove('overflow-hidden');
    },
    methods: {
        handleClickOutside(event) {
            if (!this.$el.contains(event.target)) {
                this.accCondition = false;
                this.accPrice = false;
                this.appliedOpen = false;
            }
        },
        openFilterModal() {
            this.modalOpen = true;
            this.appliedOpen = false;
            // prevent background scrolling while modal is open
            document.body.classList.add('overflow-hidden');
        },
        closeFilterModal() {
            this.modalOpen = false;
            document.body.classList.remove('overflow-hidden');
        },
        applyFilters() {
            const url = new URL(window.location);
            if (this.selectedCategory) url.searchParams.set('category', this.selectedCategory); else url.searchParams.delete('category');
            if (this.selectedAvailability) url.searchParams.set('availability', this.selectedAvailability); else url.searchParams.delete('availability');
            url.searchParams.delete('page');
            window.location.href = url.toString();
        },
        resetFilters() {
            this.selectedCategory = '';
            this.selectedAvailability = '';
        },
        clearAll() {
            this.selectedCategory = '';
            this.selectedAvailability = '';
            this.appliedOpen = false;
            const url = new URL(window.location);
            url.searchParams.delete('category');
            url.searchParams.delete('availability');
            url.searchParams.delete('page');
            window.location.href = url.toString();
        },
        removeFilter(index) {
            const label = this.appliedLabels[index];
            if (!label) return;
            if (label.startsWith('หมวดหมู่:')) this.selectedCategory = '';
            if (label.startsWith('ความพร้อมใช้งาน:')) this.selectedAvailability = '';
            this.applyFilters();
        },
    },
};
</script>

<style scoped>
.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
    transform: scale(0.95);
}

.modal-fade-enter-to,
.modal-fade-leave-from {
    opacity: 1;
    transform: scale(1);
}
</style>
