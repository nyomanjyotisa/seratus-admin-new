<template>
    <div v-if="show" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50">
      <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg p-6 max-w-sm">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200 mb-4">{{ title }}</h3>
        <p class="text-sm text-gray-700 dark:text-gray-300 mb-6">{{ message }}</p>
        <div class="text-right space-x-2">
          <button
            v-if="showCancel"
            @click="handleCancel"
            class="px-4 py-2 text-gray-400 hover:text-gray-700"
          >
            {{ cancelText || 'Cancel' }}
          </button>
          <button
            @click="handleConfirm"
            class="px-4 py-2 bg-blue-500 text-white rounded-lg"
          >
            {{ okText || 'OK' }}
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  const props = defineProps({
    show: Boolean,
    title: String,
    message: String,
    okText: String,
    cancelText: String,
    showCancel: { type: Boolean, default: true },
  });
  
  const emit = defineEmits(['confirm', 'cancel']);
  
  const handleConfirm = () => {
    emit('confirm');
  };
  
  const handleCancel = () => {
    emit('cancel');
  };
  </script>
  