<template>
    <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6 relative">
            <!-- Close button -->
            <button @click="$emit('close')" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600" :disabled="submitting">
                ✕
            </button>

            <h2 class="text-xl font-bold text-gray-800 mb-4">Create Category</h2>
            <form @submit.prevent="onCreate">
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1">Category Name</label>
                    <input type="text" v-model="name"
                        class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required :disabled="submitting">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="$emit('close')"
                        class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300" :disabled="submitting">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700 disabled:opacity-60 flex items-center justify-center"
                        :disabled="submitting || !name.trim()">
                        <svg v-if="submitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span v-if="submitting">กำลังสร้าง...</span>
                        <span v-else>Create</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    name: "CategoryCreateModal",
    props: {
        isOpen: Boolean
    },
    data() {
        return {
            name: "",
            submitting: false
        };
    },
    methods: {
        onCreate() {
            this.submitting = true;
            this.$emit('create', { name: this.name });
        },
        resetSubmitting() {
            this.submitting = false;
        }
    },
    watch: {
        isOpen(newVal) {
            if (!newVal) {
                this.submitting = false;
                this.name = "";
            }
        }
    }
};
</script>