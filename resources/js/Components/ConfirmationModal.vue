<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-slate-800 shadow p-6 rounded-lg shadow-lg">
            <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">{{ title }}</h3>
            <p class="mt-2 text-sm text-gray-800 dark:text-gray-400">{{ message }}</p>
            <div class="mt-4 flex justify-end space-x-4">
                <button @click="onCancel" class="text-gray-400 hover:text-gray-700">Cancel</button>
                <button @click="onConfirm" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Confirm</button>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    show: Boolean,
    title: String,
    message: String,
});
const emit = defineEmits(['confirm', 'cancel']);

const onConfirm = () => emit('confirm');
const onCancel = () => emit('cancel');
</script>
