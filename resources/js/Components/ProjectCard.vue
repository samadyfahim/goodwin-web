<script setup>
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    project: {
        type: Object,
        required: true,
    },
});

const getStatusColor = (status) => {
    const colors = {
        planned: "bg-blue-100 text-blue-800",
        active: "bg-green-100 text-green-800",
        completed: "bg-purple-100 text-purple-800",
        delayed: "bg-red-100 text-red-800",
    };
    return colors[status] || "bg-gray-100 text-gray-800";
};

const getStatusIcon = (status) => {
    const icons = {
        planned: "📋",
        active: "⚡",
        completed: "✅",
        delayed: "⚠️",
    };
    return icons[status] || "📄";
};

const formatDate = (date) => {
    if (!date) return "No deadline";
    return new Date(date).toLocaleDateString();
};
</script>

<template>
    <div
        class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200 overflow-hidden flex flex-col"
    >
        <div class="p-6 flex-1 flex flex-col">
            <!-- Project Header -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">
                        {{ project.name }}
                    </h3>
                    <div class="flex items-center space-x-2">
                        <span
                            :class="`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusColor(
                                project.status
                            )}`"
                        >
                            <span class="mr-1">{{
                                getStatusIcon(project.status)
                            }}</span>
                            {{
                                project.status.charAt(0).toUpperCase() +
                                project.status.slice(1)
                            }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Project Description -->
            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                {{ project.description || "No description available" }}
            </p>

            <!-- Project Details -->
            <div class="space-y-2 mb-4">
                <div class="flex items-center text-sm text-gray-500">
                    <svg
                        class="w-4 h-4 mr-2"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                        ></path>
                    </svg>
                    {{ project.creator?.name || "Unknown" }}
                </div>
                <div class="flex items-center text-sm text-gray-500">
                    <svg
                        class="w-4 h-4 mr-2"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                        ></path>
                    </svg>
                    {{ formatDate(project.deadline) }}
                </div>
                <div class="flex items-center text-sm text-gray-500">
                    <svg
                        class="w-4 h-4 mr-2"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                        ></path>
                    </svg>
                    {{ project.tasks?.length || 0 }} tasks
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-2 mt-auto pt-4">
                <Link
                    :href="`/projects/${project.id}`"
                    class="flex-1 inline-flex justify-center items-center px-3 py-2 bg-primary border border-transparent rounded-md text-sm font-medium text-white hover:bg-primaryHover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors duration-200"
                >
                    View Details
                </Link>
                <button
                    class="px-3 py-2 text-gray-400 hover:text-gray-600 transition-colors duration-200"
                >
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"
                        ></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
