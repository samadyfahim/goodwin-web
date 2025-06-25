<!--
EmptyState.vue
Component for displaying an empty state with icon, title, and description.
Props:
- icon: String
- title: String
- description: String
- actionText: String
- actionHref: String|null
-->
<script setup>
const props = defineProps({
    icon: {
        type: String,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        required: true,
    },
    actionText: {
        type: String,
        default: null,
    },
    actionHref: {
        type: String,
        default: null,
    },
});
</script>

<template>
    <div class="text-center py-12">
        <svg
            class="mx-auto h-12 w-12 text-gray-400"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                :d="icon"
            ></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">{{ title }}</h3>
        <p class="mt-1 text-sm text-gray-500">{{ description }}</p>
        <div v-if="actionText && actionHref" class="mt-6">
            <a
                :href="actionHref"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primaryHover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
            >
                {{ actionText }}
            </a>
        </div>
    </div>
</template>
