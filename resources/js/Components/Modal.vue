<!--
Modal.vue
Reusable modal dialog component for displaying overlays and forms.
-->
<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from "vue";

const props = defineProps({
    open: {
        type: Boolean,
        required: true,
    },
    title: {
        type: String,
        default: "",
    },
    maxWidth: {
        type: String,
        default: "max-w-lg",
    },
    persistent: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["close"]);

const close = () => {
    if (!props.persistent) emit("close");
};

const closeOnEscape = (e) => {
    if (e.key === "Escape") {
        e.preventDefault();

        if (props.open) {
            close();
        }
    }
};

onMounted(() => document.addEventListener("keydown", closeOnEscape));

onUnmounted(() => {
    document.removeEventListener("keydown", closeOnEscape);
});

const maxWidthClass = computed(() => {
    return {
        sm: "sm:max-w-sm",
        md: "sm:max-w-md",
        lg: "sm:max-w-lg",
        xl: "sm:max-w-xl",
        "2xl": "sm:max-w-2xl",
    }[props.maxWidth];
});
</script>

<template>
    <transition name="fade">
        <div
            v-if="open"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40"
        >
            <div
                :class="`bg-white rounded-lg shadow-xl w-full ${maxWidth} mx-4 relative`"
                @click.stop
                role="dialog"
                aria-modal="true"
            >
                <!-- Header -->
                <div
                    class="flex items-center justify-between px-6 py-4 border-b border-gray-200"
                >
                    <h3 class="text-lg font-semibold text-gray-900">
                        <slot name="title">{{ title }}</slot>
                    </h3>
                    <button
                        v-if="!persistent"
                        @click="close"
                        class="text-gray-400 hover:text-gray-600 focus:outline-none"
                        aria-label="Close"
                    >
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
                <!-- Content -->
                <div class="px-6 py-4">
                    <slot />
                </div>
                <!-- Actions -->
                <div
                    class="flex justify-end space-x-2 px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-lg"
                >
                    <slot name="actions" />
                </div>
            </div>
        </div>
    </transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
