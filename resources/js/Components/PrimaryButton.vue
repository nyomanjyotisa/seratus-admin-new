<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <button 
        :type="type" 
        :disabled="disabled"
        :class="[
            'inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150',
            disabled ? 'bg-gray-400 dark:bg-gray-600 text-gray-300 cursor-not-allowed' : 'bg-primary dark:bg-primary text-white dark:text-white hover:bg-primary/80 dark:hover:bg-primary/90 focus:bg-primary/80 dark:focus:bg-primary/80 active:bg-primary dark:active:bg-primary/80 focus:ring-primary dark:focus:ring-offset-primary'
        ]"
    >
        <slot />
    </button>
</template>
