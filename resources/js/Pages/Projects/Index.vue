<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    projects: Array,
});

const searchQuery = ref("");
const selectedStatus = ref("all");

const filteredProjects = computed(() => {
    return props.projects.filter((project) => {
        const matchesSearch =
            project.name
                .toLowerCase()
                .includes(searchQuery.value.toLowerCase()) ||
            project.description
                ?.toLowerCase()
                .includes(searchQuery.value.toLowerCase());
        const matchesStatus =
            selectedStatus.value === "all" ||
            project.status === selectedStatus.value;
        return matchesSearch && matchesStatus;
    });
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
    <Head title="Projects" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-900">Projects</h2>
                <Link
                    href="#"
                    class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primaryHover focus:bg-primary active:bg-primaryHover focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition ease-in-out duration-150"
                >
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
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                        ></path>
                    </svg>
                    New Project
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Search and Filter Section -->
                <div
                    class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6"
                >
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                                >Search Projects</label
                            >
                            <div class="relative">
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Search by name or description..."
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary"
                                />
                                <svg
                                    class="absolute left-3 top-2.5 h-5 w-5 text-gray-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                    ></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                                >Filter by Status</label
                            >
                            <select
                                v-model="selectedStatus"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary"
                            >
                                <option value="all">All Statuses</option>
                                <option value="planned">Planned</option>
                                <option value="active">Active</option>
                                <option value="completed">Completed</option>
                                <option value="delayed">Delayed</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Projects Grid -->
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <div
                        v-for="project in filteredProjects"
                        :key="project.id"
                        class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200 overflow-hidden flex flex-col"
                    >
                        <div class="p-6 flex-1 flex flex-col">
                            <!-- Project Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3
                                        class="text-lg font-semibold text-gray-900 mb-1"
                                    >
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
                                                project.status
                                                    .charAt(0)
                                                    .toUpperCase() +
                                                project.status.slice(1)
                                            }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Project Description -->
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                {{
                                    project.description ||
                                    "No description available"
                                }}
                            </p>

                            <!-- Project Details -->
                            <div class="space-y-2 mb-4">
                                <div
                                    class="flex items-center text-sm text-gray-500"
                                >
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
                                <div
                                    class="flex items-center text-sm text-gray-500"
                                >
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
                                <div
                                    class="flex items-center text-sm text-gray-500"
                                >
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
                </div>

                <!-- Empty State -->
                <div
                    v-if="filteredProjects.length === 0"
                    class="text-center py-12"
                >
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
                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                        ></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">
                        No projects found
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{
                            searchQuery || selectedStatus !== "all"
                                ? "Try adjusting your search or filter criteria."
                                : "Get started by creating a new project."
                        }}
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
