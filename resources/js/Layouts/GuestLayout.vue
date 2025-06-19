<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center pt-6 sm:justify-center sm:pt-0"
    >
        <div
            class="mt-6 w-full overflow-hidden px-16 py-10 shadow-md sm:max-w-md sm:rounded-lg"
        >
            <div class="flex justify-center mb-4">
                <ApplicationLogo class="h-14 w-auto fill-current" />
            </div>

            <slot />
        </div>
    </div>
</template>
