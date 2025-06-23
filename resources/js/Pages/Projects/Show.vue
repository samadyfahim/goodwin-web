<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import StatusBadge from "@/Components/StatusBadge.vue";
import StatCard from "@/Components/StatCard.vue";
import TabNavigation from "@/Components/TabNavigation.vue";
import EmptyState from "@/Components/EmptyState.vue";
import Modal from "@/Components/Modal.vue";
import FormInput from "@/Components/FormInput.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import TasksSection from "./Partials/TasksSection.vue";
import NotesSection from "./Partials/NotesSection.vue";
import TeamSection from "./Partials/TeamSection.vue";
import FilesSection from "./Partials/FilesSection.vue";
import CustomersSection from "./Partials/CustomersSection.vue";

const props = defineProps({
    project: Object,
    comments: Array,
});

const activeTab = ref("details");

const formatDate = (date) => {
    if (!date) return "No deadline";
    return new Date(date).toLocaleDateString();
};

const tabs = computed(() => [
    { id: "details", label: "Details" },
    { id: "tasks", label: "Tasks", count: props.project.tasks?.length || 0 },
    { id: "notes", label: "Notes", count: props.project.notes?.length || 0 },
    { id: "team", label: "Team", count: props.project.users?.length || 0 },
    { id: "files", label: "Files", count: props.project.files?.length || 0 },
    {
        id: "customers",
        label: "Customers",
        count: props.project.customers?.length || 0,
    },
]);

const stats = computed(() => [
    {
        title: "Total Tasks",
        value: props.project.tasks?.length || 0,
        icon: "M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2",
        iconColor: "blue",
        iconBgColor: "blue",
    },
    {
        title: "Team Members",
        value: props.project.users?.length || 0,
        icon: "M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z",
        iconColor: "green",
        iconBgColor: "green",
    },
    {
        title: "Customers",
        value: props.project.customers?.length || 0,
        icon: "M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z",
        iconColor: "purple",
        iconBgColor: "purple",
    },
    {
        title: "Deadline",
        value: formatDate(props.project.deadline),
        icon: "M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z",
        iconColor: "yellow",
        iconBgColor: "yellow",
    },
]);

// --- Task Modal State ---
const showTaskModal = ref(false);
const editingTask = ref(null);
const taskForm = useForm({
    name: "",
    description: "",
    status: "pending",
});
const taskStatusOptions = [
    { value: "pending", label: "Pending", icon: "⏳" },
    { value: "in_progress", label: "In Progress", icon: "🔄" },
    { value: "completed", label: "Completed", icon: "✅" },
    { value: "cancelled", label: "Cancelled", icon: "❌" },
];
function openTaskModal(task = null) {
    editingTask.value = task;
    if (task) {
        taskForm.name = task.name;
        taskForm.description = task.description;
        taskForm.status = task.status;
    } else {
        taskForm.reset();
        taskForm.status = "pending";
    }
    showTaskModal.value = true;
}
function closeTaskModal() {
    showTaskModal.value = false;
    editingTask.value = null;
    taskForm.reset();
}
function submitTask() {
    if (editingTask.value) {
        taskForm.put(
            route("projects.tasks.update", {
                project: props.project.id,
                task: editingTask.value.id,
            }),
            {
                onSuccess: closeTaskModal,
            }
        );
    } else {
        taskForm.post(
            route("projects.tasks.store", { project: props.project.id }),
            {
                onSuccess: closeTaskModal,
            }
        );
    }
}
function deleteTask(task) {
    if (confirm("Delete this task?")) {
        router.delete(
            route("projects.tasks.destroy", {
                project: props.project.id,
                task: task.id,
            })
        );
    }
}
// --- End Task Modal State ---

// --- Note Modal State ---
const showNoteModal = ref(false);
const editingNote = ref(null);
const noteForm = useForm({
    content: "",
});
function openNoteModal(note = null) {
    editingNote.value = note;
    if (note) {
        noteForm.content = note.content;
    } else {
        noteForm.reset();
    }
    showNoteModal.value = true;
}
function closeNoteModal() {
    showNoteModal.value = false;
    editingNote.value = null;
    noteForm.reset();
}
function submitNote() {
    if (editingNote.value) {
        noteForm.put(
            route("projects.notes.update", {
                project: props.project.id,
                note: editingNote.value.id,
            }),
            {
                onSuccess: closeNoteModal,
            }
        );
    } else {
        noteForm.post(
            route("projects.notes.store", { project: props.project.id }),
            {
                onSuccess: closeNoteModal,
            }
        );
    }
}
function deleteNote(note) {
    if (confirm("Delete this note?")) {
        router.delete(
            route("projects.notes.destroy", {
                project: props.project.id,
                note: note.id,
            })
        );
    }
}
// --- End Note Modal State ---

// --- Team Modal State ---
const showTeamModal = ref(false);
const teamForm = useForm({
    user_id: "",
});
const allUsers = ref([]); // You may want to fetch this from the backend for real app
function openTeamModal() {
    showTeamModal.value = true;
}
function closeTeamModal() {
    showTeamModal.value = false;
    teamForm.reset();
}
function submitTeam() {
    teamForm.post(route("projects.team.store", { project: props.project.id }), {
        onSuccess: closeTeamModal,
    });
}
function removeTeamMember(user) {
    if (confirm("Remove this team member?")) {
        router.delete(
            route("projects.team.destroy", {
                project: props.project.id,
                user: user.id,
            })
        );
    }
}
// --- End Team Modal State ---

// --- File Modal State ---
const showFileModal = ref(false);
const fileForm = useForm({
    file: null,
});
function openFileModal() {
    showFileModal.value = true;
}
function closeFileModal() {
    showFileModal.value = false;
    fileForm.reset();
}
function submitFile() {
    fileForm.post(
        route("projects.files.store", { project: props.project.id }),
        {
            onSuccess: closeFileModal,
            forceFormData: true,
        }
    );
}
function deleteFile(file) {
    if (confirm("Delete this file?")) {
        router.delete(
            route("projects.files.destroy", {
                project: props.project.id,
                file: file.id,
            })
        );
    }
}
// --- End File Modal State ---

// --- Edit Project Modal State ---
const showEditModal = ref(false);
const editForm = useForm({
    name: props.project.name,
    description: props.project.description,
    status: props.project.status,
});
const projectStatusOptions = [
    { value: "planned", label: "Planned" },
    { value: "active", label: "Active" },
    { value: "completed", label: "Completed" },
    { value: "delayed", label: "Delayed" },
];
function openEditModal() {
    showEditModal.value = true;
}
function closeEditModal() {
    showEditModal.value = false;
    editForm.reset();
}
function submitEdit() {
    editForm.patch(route("projects.update", { project: props.project.id }), {
        onSuccess: closeEditModal,
    });
}
// --- End Edit Project Modal State ---

function refresh() {
    router.reload({ only: ["project"] });
}
</script>

<template>
    <Head :title="`${project.name} - Project Details`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <Link
                        href="/projects"
                        class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
                    >
                        <svg
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 19l-7-7 7-7"
                            ></path>
                        </svg>
                    </Link>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">
                            {{ project.name }}
                        </h2>
                        <p class="text-sm text-gray-500">
                            Created by {{ project.creator?.name }} on
                            {{ formatDate(project.created_at) }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="text-sm text-gray-500 mr-5">
                        <span>Deadline: </span>
                        <span>{{ formatDate(project.deadline) }}</span>
                    </div>
                    <StatusBadge :status="project.status" type="project" />
                    <button
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors duration-200"
                        @click="openEditModal"
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
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                            ></path>
                        </svg>
                    </button>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <TabNavigation
                    :tabs="tabs"
                    :active-tab="activeTab"
                    @update:active-tab="activeTab = $event"
                />

                <!-- Details Tab -->
                <div v-if="activeTab === 'details'" class="space-y-6">
                    <div
                        class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6"
                    >
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            Description
                        </h3>
                        <p class="text-gray-600">
                            {{
                                project.description ||
                                "No description available for this project."
                            }}
                        </p>
                    </div>
                </div>

                <!-- Tasks Tab -->
                <div v-if="activeTab === 'tasks'" class="space-y-6">
                    <TasksSection :project="project" @refresh="refresh" />
                </div>

                <!-- Notes Tab -->
                <div v-if="activeTab === 'notes'" class="space-y-6">
                    <NotesSection
                        :project="project"
                        :comments="comments"
                        @refresh="refresh"
                    />
                </div>

                <!-- Team Tab -->
                <div v-if="activeTab === 'team'" class="space-y-6">
                    <TeamSection :project="project" @refresh="refresh" />
                </div>

                <!-- Files Tab -->
                <div v-if="activeTab === 'files'" class="space-y-6">
                    <FilesSection :project="project" @refresh="refresh" />
                </div>

                <!-- Customers Tab -->
                <div v-if="activeTab === 'customers'" class="space-y-6">
                    <CustomersSection :project="project" @refresh="refresh" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <!-- Edit Project Modal -->
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

    <!-- Task Modal -->
    <Modal
        :open="showTaskModal"
        @close="closeTaskModal"
        :title="editingTask ? 'Edit Task' : 'Add Task'"
    >
        <form @submit.prevent="submitTask" class="space-y-4">
            <FormInput
                id="task-name"
                label="Task Name"
                v-model="taskForm.name"
                required
                :error="taskForm.errors.name"
            />
            <FormInput
                id="task-description"
                label="Description"
                v-model="taskForm.description"
                type="textarea"
                rows="3"
                :error="taskForm.errors.description"
            />
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2"
                    >Status</label
                >
                <div class="flex space-x-2">
                    <label
                        v-for="option in taskStatusOptions"
                        :key="option.value"
                        class="flex items-center cursor-pointer"
                    >
                        <input
                            type="radio"
                            v-model="taskForm.status"
                            :value="option.value"
                            class="form-radio text-primary focus:ring-primary"
                        />
                        <span class="ml-2"
                            >{{ option.icon }} {{ option.label }}</span
                        >
                    </label>
                </div>
            </div>
            <div class="flex justify-end space-x-2 pt-4">
                <button
                    type="button"
                    @click="closeTaskModal"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors duration-200"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    :disabled="taskForm.processing"
                    class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primaryHover focus:bg-primaryHover active:bg-primaryHover focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <svg
                        v-if="taskForm.processing"
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
                    {{ editingTask ? "Update Task" : "Create Task" }}
                </button>
            </div>
        </form>
    </Modal>
</template>
