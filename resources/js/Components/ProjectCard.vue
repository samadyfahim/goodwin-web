<!--
ProjectCard.vue
Card component for displaying project summary information.
Props:
- project: Project object
-->
<script setup>
import { Link, router } from "@inertiajs/vue3";
import { ref, onMounted, onBeforeUnmount, watch } from "vue";

const props = defineProps({
    project: {
        type: Object,
        required: true,
    },
    authUserId: Number,
});

const showMenu = ref(false);
const showDeleteModal = ref(false);
let clickOutsideHandler = null;

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

function handleEdit() {
    // Emit to parent to open edit modal for this project
    // (Parent must listen for 'edit-project' event)
    // $emit is only available in Options API, so use defineEmits
    emit("edit-project", props.project);
    showMenu.value = false;
}

function handleDelete() {
    showDeleteModal.value = true;
    showMenu.value = false;
}

function confirmDelete() {
    router.delete(route("projects.destroy", { project: props.project.id }), {
        onSuccess: () => {
            showDeleteModal.value = false;
        },
    });
}

const emit = defineEmits(["edit-project"]);

watch(showMenu, (val) => {
    if (val) {
        clickOutsideHandler = (e) => {
            if (!e.target.closest(".project-card-menu")) {
                showMenu.value = false;
            }
        };
        document.addEventListener("mousedown", clickOutsideHandler);
    } else if (clickOutsideHandler) {
        document.removeEventListener("mousedown", clickOutsideHandler);
        clickOutsideHandler = null;
    }
});

onBeforeUnmount(() => {
    if (clickOutsideHandler) {
        document.removeEventListener("mousedown", clickOutsideHandler);
    }
});
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
                <div
                    v-if="String(authUserId) === String(project.created_by)"
                    class="relative project-card-menu"
                >
                    <button
                        @click="showMenu = !showMenu"
                        class="p-2 text-gray-700 hover:text-primary focus:outline-none"
                        aria-label="Project actions"
                    >
                        <svg
                            class="w-7 h-7"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <circle cx="12" cy="6" r="1.5" />
                            <circle cx="12" cy="12" r="1.5" />
                            <circle cx="12" cy="18" r="1.5" />
                        </svg>
                    </button>
                    <div
                        v-if="showMenu"
                        class="absolute right-0 mt-2 w-32 bg-white border border-gray-200 rounded-md shadow-lg z-10"
                    >
                        <button
                            @click="$emit('edit-project', project)"
                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                        >
                            Edit
                        </button>
                        <button
                            @click="handleDelete"
                            class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                        >
                            Delete
                        </button>
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
            </div>
        </div>
        <!-- Delete Confirmation Modal -->
        <div
            v-if="showDeleteModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40"
        >
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-sm">
                <h3 class="text-lg font-semibold mb-4">Delete Project</h3>
                <p class="mb-6">
                    Are you sure you want to delete this project and all related
                    data? This action cannot be undone.
                </p>
                <div class="flex justify-end space-x-2">
                    <button
                        @click="showDeleteModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                    >
                        Cancel
                    </button>
                    <button
                        @click="confirmDelete"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700"
                    >
                        Delete
                    </button>
                </div>
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
