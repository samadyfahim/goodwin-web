<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ProjectCard from "@/Components/ProjectCard.vue";
import EmptyState from "@/Components/EmptyState.vue";
import { Head, Link } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import Modal from "@/Components/Modal.vue";
import FormInput from "@/Components/FormInput.vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    projects: Array,
    authUserId: Number,
});

const searchQuery = ref("");
const selectedStatus = ref("all");
const showMineOnly = ref(false);
const showEditModal = ref(false);
const editingProject = ref(null);
const editForm = useForm({
    name: "",
    description: "",
    status: "planned",
});
const projectStatusOptions = [
    { value: "planned", label: "Planned" },
    { value: "active", label: "Active" },
    { value: "completed", label: "Completed" },
    { value: "delayed", label: "Delayed" },
];

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
        const matchesMine =
            !showMineOnly.value || project.created_by === props.authUserId;
        return matchesSearch && matchesStatus && matchesMine;
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

function openEditModal(project) {
    editingProject.value = project;
    editForm.name = project.name;
    editForm.description = project.description;
    editForm.status = project.status;
    showEditModal.value = true;
}

function closeEditModal() {
    showEditModal.value = false;
    editingProject.value = null;
    editForm.reset();
}

function submitEdit() {
    if (!editingProject.value) return;
    editForm.patch(
        route("projects.update", { project: editingProject.value.id }),
        {
            onSuccess: closeEditModal,
        }
    );
}
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
                            <label class="inline-flex items-center mt-3">
                                <input
                                    type="checkbox"
                                    v-model="showMineOnly"
                                    class="form-checkbox text-primary"
                                />
                                <span class="ml-2 text-sm text-gray-700"
                                    >Created by me</span
                                >
                            </label>
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
                        :auth-user-id="props.authUserId"
                        @edit-project="openEditModal"
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

    <Modal :open="showEditModal" @close="closeEditModal" title="Edit Project">
        <form @submit.prevent="submitEdit" class="space-y-4">
            <FormInput
                id="project-name"
                label="Project Name"
                v-model="editForm.name"
                required
                :error="editForm.errors.name"
            />
            <FormInput
                id="project-description"
                label="Description"
                v-model="editForm.description"
                type="textarea"
                rows="3"
                :error="editForm.errors.description"
            />
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2"
                    >Status</label
                >
                <div class="flex space-x-2">
                    <label
                        v-for="option in projectStatusOptions"
                        :key="option.value"
                        class="flex items-center cursor-pointer"
                    >
                        <input
                            type="radio"
                            v-model="editForm.status"
                            :value="option.value"
                            class="form-radio text-primary focus:ring-primary"
                        />
                        <span class="ml-2">{{ option.label }}</span>
                    </label>
                </div>
            </div>
            <div class="flex justify-end space-x-2 pt-4">
                <button
                    type="button"
                    @click="closeEditModal"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors duration-200"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    :disabled="editForm.processing"
                    class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primaryHover focus:bg-primaryHover active:bg-primaryHover focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <svg
                        v-if="editForm.processing"
                        class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        ></circle>
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        ></path>
                    </svg>
                    Update Project
                </button>
            </div>
        </form>
    </Modal>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
