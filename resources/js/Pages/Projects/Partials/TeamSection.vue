<script setup>
import EmptyState from "@/Components/EmptyState.vue";
import { useForm, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps({ project: Object });
const emit = defineEmits(["refresh"]);

const teamForm = useForm({ user_id: "" });
const search = ref("");
const suggestions = ref([]);
const showSuggestions = ref(false);

let debounceTimeout = null;

watch(search, async (val) => {
    clearTimeout(debounceTimeout);
    if (val.length < 2) {
        suggestions.value = [];
        showSuggestions.value = false;
        return;
    }
    debounceTimeout = setTimeout(async () => {
        const res = await fetch(`/api/users?q=${encodeURIComponent(val)}`);
        let users = await res.json();
        // Filter out already-added users
        users = users.filter(
            (u) => !props.project.users.some((pu) => pu.id === u.id)
        );
        suggestions.value = users;
        showSuggestions.value = users.length > 0;
    }, 200);
});

function selectSuggestion(user) {
    teamForm.user_id = user.id;
    search.value = "";
    suggestions.value = [];
    showSuggestions.value = false;
    teamForm.post(route("projects.team.store", { project: props.project.id }), {
        onSuccess: () => emit("refresh"),
        onError: () => alert(teamForm.errors.user_id || "Failed to add user."),
    });
}

function removeTeamMember(user) {
    if (confirm("Remove this team member?")) {
        router.delete(
            route("projects.team.destroy", {
                project: props.project.id,
                user: user.id,
            }),
            {
                onSuccess: () => emit("refresh"),
            }
        );
    }
}

function onBlur() {
    setTimeout(() => {
        showSuggestions.value = false;
    }, 150);
}
</script>
<template>
    <div
        class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden"
    >
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                Team Members
            </h3>
            <div class="mb-4 relative">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Add by name or email..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-200"
                    @blur="onBlur"
                />
                <ul
                    v-if="showSuggestions && suggestions.length"
                    class="absolute z-10 w-full bg-white border border-gray-200 rounded-md mt-1 shadow-lg max-h-48 overflow-auto"
                >
                    <li
                        v-for="user in suggestions"
                        :key="user.id"
                        @click="selectSuggestion(user)"
                        class="px-4 py-2 cursor-pointer hover:bg-primary/10"
                    >
                        <span class="font-medium">{{ user.name }}</span>
                        <span class="text-gray-500 ml-2">{{ user.email }}</span>
                    </li>
                </ul>
            </div>
        </div>
        <div
            v-if="project.users && project.users.length > 0"
            class="divide-y divide-gray-200"
        >
            <div
                v-for="user in project.users"
                :key="user.id"
                class="px-6 py-4 flex items-center space-x-4"
            >
                <div class="flex-shrink-0">
                    <div
                        class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center"
                    >
                        <span class="text-sm font-medium text-gray-700">
                            {{ user.name.charAt(0).toUpperCase() }}
                        </span>
                    </div>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-medium text-gray-900">
                        {{ user.name }}
                    </h4>
                    <p class="text-sm text-gray-500">{{ user.email }}</p>
                </div>
                <div>
                    <button
                        @click="removeTeamMember(user)"
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
            icon="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"
            title="No team members"
            description="Add team members to get started."
            action-text="Invite Member"
            :action-href="null"
        />
    </div>
</template>
