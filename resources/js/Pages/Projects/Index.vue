<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ProjectCard from "@/Components/ProjectCard.vue";
import EmptyState from "@/Components/EmptyState.vue";
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
                    href="/projects/create"
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
                    <ProjectCard
                        v-for="project in filteredProjects"
                        :key="project.id"
                        :project="project"
                    />
                </div>

                <!-- Empty State -->
                <EmptyState
                    v-if="filteredProjects.length === 0"
                    icon="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                    title="No projects found"
                    :description="
                        searchQuery || selectedStatus !== 'all'
                            ? 'Try adjusting your search or filter criteria.'
                            : 'Get started by creating a new project.'
                    "
                    action-text="Create Project"
                    action-href="/projects/create"
                />
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
