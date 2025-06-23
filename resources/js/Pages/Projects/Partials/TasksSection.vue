<script setup>
import StatusBadge from "@/Components/StatusBadge.vue";
import EmptyState from "@/Components/EmptyState.vue";
import Modal from "@/Components/Modal.vue";
import FormInput from "@/Components/FormInput.vue";
import { useForm, router } from "@inertiajs/vue3";
import { ref, computed, watch, toRaw } from "vue";

const props = defineProps({ project: Object });
const emit = defineEmits(["refresh"]);

const showTaskModal = ref(false);
const editingTask = ref(null);
const taskForm = useForm({
    name: "",
    description: "",
    status: "to_do",
    users: [],
    deadline: "",
});
const taskStatusOptions = [
    { value: "to_do", label: "To Do", icon: "📝" },
    { value: "in_progress", label: "In Progress", icon: "🔄" },
    { value: "done", label: "Done", icon: "✅" },
];
const showUserModal = ref(false);
const userSearch = ref("");
const userSuggestions = ref([]);
const selectedUsers = ref([]);
const userToAdd = ref(null);
const notTeamModal = ref(false);
const userToInvite = ref(null);

// Only allow team members
const teamMembers = computed(() => props.project.users || []);

watch(showTaskModal, (open) => {
    if (open && editingTask.value) {
        selectedUsers.value = editingTask.value.users
            ? [...editingTask.value.users]
            : [];
    } else if (open) {
        selectedUsers.value = [];
    }
    userSearch.value = "";
    userSuggestions.value = [];
});

watch(userSearch, (val) => {
    if (!val || val.length < 2) {
        userSuggestions.value = [];
        return;
    }
    // Filter team members by name/email
    userSuggestions.value = teamMembers.value
        .filter(
            (u) =>
                u.name.toLowerCase().includes(val.toLowerCase()) ||
                u.email.toLowerCase().includes(val.toLowerCase())
        )
        .filter((u) => !selectedUsers.value.some((su) => su.id === u.id));
});

function openTaskModal(task = null) {
    editingTask.value = task;
    if (task) {
        taskForm.name = task.name;
        taskForm.description = task.description;
        taskForm.status = task.status;
        selectedUsers.value = task.users ? [...task.users] : [];
        taskForm.deadline = task.deadline ? task.deadline.substring(0, 10) : "";
    } else {
        taskForm.reset();
        taskForm.status = "to_do";
        selectedUsers.value = [];
        taskForm.deadline = "";
    }
    showTaskModal.value = true;
}
function closeTaskModal() {
    showTaskModal.value = false;
    editingTask.value = null;
    taskForm.reset();
}
function addUserToTask(user) {
    if (!teamMembers.value.some((u) => u.id === user.id)) {
        userToInvite.value = user;
        notTeamModal.value = true;
        return;
    }
    selectedUsers.value.push(user);
    userSearch.value = "";
    userSuggestions.value = [];
}
async function confirmAddToTeamAndTask() {
    // Add to team
    await router.post(
        route("projects.team.store", { project: props.project.id }),
        { user_id: userToInvite.value.id },
        {
            onSuccess: () => {
                selectedUsers.value.push(userToInvite.value);
                notTeamModal.value = false;
                userToInvite.value = null;
            },
        }
    );
}
function removeUserFromTask(user) {
    selectedUsers.value = selectedUsers.value.filter((u) => u.id !== user.id);
}
function submitTask() {
    // Always use a plain array for users
    taskForm.users = selectedUsers.value.map((u) => u.id);
    console.log("Submitting task with users:", taskForm.users, taskForm);
    if (editingTask.value) {
        taskForm.put(
            route("projects.tasks.update", {
                project: props.project.id,
                task: editingTask.value.id,
            }),
            {
                onSuccess: () => {
                    closeTaskModal();
                    emit("refresh");
                },
                onFinish: () => {
                    taskForm.reset("users");
                },
            }
        );
    } else {
        taskForm.post(
            route("projects.tasks.store", { project: props.project.id }),
            {
                onSuccess: () => {
                    closeTaskModal();
                    emit("refresh");
                },
                onFinish: () => {
                    taskForm.reset("users");
                },
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
            }),
            {
                onSuccess: () => emit("refresh"),
            }
        );
    }
}
function removeUserFromTaskList(task, user) {
    if (confirm(`Remove ${user.name} from this task?`)) {
        router.delete(
            route("projects.tasks.users.destroy", {
                project: props.project.id,
                task: task.id,
                user: user.id,
            }),
            {
                onSuccess: () => emit("refresh"),
            }
        );
    }
}
</script>
<template>
    <div
        class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden"
    >
        <div
            class="flex items-center justify-between px-6 py-4 border-b border-gray-200"
        >
            <h3 class="text-lg font-semibold text-gray-900">Project Tasks</h3>
            <button
                @click="openTaskModal()"
                class="inline-flex items-center px-3 py-2 bg-primary border border-transparent rounded-md text-sm font-medium text-white hover:bg-primaryHover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors duration-200"
            >
                <svg
                    class="w-4 h-4 mr-1"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                    />
                </svg>
                Add Task
            </button>
        </div>
        <div
            v-if="project.tasks && project.tasks.length > 0"
            class="divide-y divide-gray-200"
        >
            <div
                v-for="task in project.tasks"
                :key="task.id"
                class="px-6 py-4 hover:bg-gray-50 transition-colors duration-200 flex justify-between items-center"
            >
                <div class="flex-1">
                    <h4 class="text-sm font-medium text-gray-900">
                        {{ task.name }}
                    </h4>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ task.description }}
                    </p>
                    <div class="flex items-center mt-2 space-x-4">
                        <StatusBadge :status="task.status" type="task" />
                        <span class="text-xs text-gray-500"
                            >Created by {{ task.creator?.name }}</span
                        >
                        <span v-if="task.deadline" class="text-xs text-gray-500"
                            >Deadline:
                            {{
                                new Date(task.deadline).toLocaleDateString()
                            }}</span
                        >
                        <div
                            v-if="task.users && task.users.length"
                            class="flex items-center space-x-1 ml-4"
                        >
                            <span
                                v-for="user in task.users"
                                :key="user.id"
                                class="flex items-center bg-primary/10 text-primary px-2 py-1 rounded text-xs"
                            >
                                <span class="font-bold mr-1">{{
                                    user.name
                                }}</span>
                                <button
                                    @click="removeUserFromTaskList(task, user)"
                                    class="text-red-500 ml-1"
                                    title="Remove"
                                >
                                    &times;
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button
                        @click="openTaskModal(task)"
                        class="text-primary hover:text-primaryHover p-2 rounded-full focus:outline-none"
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
                            />
                        </svg>
                    </button>
                    <button
                        @click="deleteTask(task)"
                        class="text-red-500 hover:text-red-700 p-2 rounded-full focus:outline-none"
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
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <EmptyState
            v-else
            icon="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
            title="No tasks"
            description="Get started by creating a new task."
            action-text="Create Task"
            :action-href="null"
            @click="openTaskModal()"
        />
    </div>
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
                :rows="3"
                :error="taskForm.errors.description"
            />
            <FormInput
                id="task-deadline"
                label="Deadline"
                v-model="taskForm.deadline"
                type="date"
                :error="taskForm.errors.deadline"
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
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2"
                    >Assign Users</label
                >
                <div class="mb-2">
                    <input
                        v-model="userSearch"
                        type="text"
                        placeholder="Search team members..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-200"
                    />
                    <ul
                        v-if="userSuggestions.length"
                        class="bg-white border border-gray-200 rounded-md mt-1 shadow-lg max-h-32 overflow-auto"
                    >
                        <li
                            v-for="user in userSuggestions"
                            :key="user.id"
                            @click="addUserToTask(user)"
                            class="px-4 py-2 cursor-pointer hover:bg-primary/10"
                        >
                            <span class="font-medium">{{ user.name }}</span>
                            <span class="text-gray-500 ml-2">{{
                                user.email
                            }}</span>
                        </li>
                    </ul>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span
                        v-for="user in selectedUsers"
                        :key="user.id"
                        class="bg-primary/10 text-primary px-2 py-1 rounded flex items-center"
                    >
                        {{ user.name }}
                        <button
                            @click="removeUserFromTask(user)"
                            class="ml-1 text-xs text-red-500"
                        >
                            &times;
                        </button>
                    </span>
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
    <Modal
        :open="notTeamModal"
        @close="
            () => {
                notTeamModal.value = false;
                userToInvite.value = null;
            }
        "
        title="User not in team"
    >
        <div class="p-4">
            <p>User is not a team member. Add to team and assign to task?</p>
            <div class="flex justify-end space-x-2 mt-4">
                <button
                    @click="
                        () => {
                            notTeamModal.value = false;
                            userToInvite.value = null;
                        }
                    "
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                >
                    Cancel
                </button>
                <button
                    @click="confirmAddToTeamAndTask"
                    class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-md"
                >
                    Add to Team & Assign
                </button>
            </div>
        </div>
    </Modal>
</template>
