<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import StatCard from "@/Components/StatCard.vue";
import { Head, Link } from "@inertiajs/vue3";

const props = defineProps({
    upcomingTasks: Array,
    teamProjects: Array,
});

const taskStatusColors = {
    to_do: "bg-yellow-100 text-yellow-800",
    in_progress: "bg-blue-100 text-blue-800",
    done: "bg-green-100 text-green-800",
};
const taskStatusLabels = {
    to_do: "To do",
    in_progress: "In progress",
    done: "Done",
};
const projectStatusColors = {
    planned: "bg-blue-100 text-blue-800",
    active: "bg-green-100 text-green-800",
    completed: "bg-purple-100 text-purple-800",
    delayed: "bg-red-100 text-red-800",
};
const projectStatusLabels = {
    planned: "Planned",
    active: "Active",
    completed: "Completed",
    delayed: "Delayed",
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-bold text-gray-900">Dashboard</h2>
        </template>

        <div class="py-12 min-h-screen">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-10">
                <!-- Welcome Message -->
                <div
                    class="bg-gradient-to-r from-primary to-blue-400 rounded-xl shadow-lg p-8 flex flex-col md:flex-row items-center justify-between"
                >
                    <div>
                        <h3 class="text-2xl font-bold text-white mb-2">
                            Welcome to Goodwin Web!
                        </h3>
                        <p class="text-white/90 text-lg">
                            Your project management dashboard. See your tasks
                            and projects at a glance.
                        </p>
                    </div>
                    <img
                        src="/images/logo.png"
                        alt="Logo"
                        class="h-16 w-auto mt-6 md:mt-0 md:ml-8 drop-shadow-xl"
                    />
                </div>
                <div class="flex flex-row gap-8">
                    <!-- Upcoming Tasks -->
                    <div class="bg-white rounded-xl shadow-md p-6 w-1/2 h-fit">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-xl font-semibold text-gray-900">
                                My Upcoming Tasks
                            </h4>
                            <Link
                                href="/projects"
                                class="text-primary font-medium hover:underline"
                                >View All Tasks</Link
                            >
                        </div>
                        <div
                            v-if="props.upcomingTasks.length > 0"
                            class="divide-y divide-gray-100"
                        >
                            <div
                                v-for="task in props.upcomingTasks"
                                :key="task.id"
                                class="py-4 flex flex-col md:flex-row md:items-center md:justify-between group hover:bg-primary/5 rounded-lg transition"
                            >
                                <div class="flex-1 p-4 min-w-0">
                                    <span
                                        :class="`px-2 py-0.5 rounded-full text-xs font-semibold ${
                                            taskStatusColors[task.status]
                                        }`"
                                    >
                                        {{
                                            taskStatusLabels[task.status] ||
                                            task.status
                                        }}
                                    </span>
                                    <div class="items-center mt-2 mb-1">
                                        <span
                                            class="font-semibold text-lg text-gray-800"
                                            >{{ task.name }}</span
                                        >
                                    </div>
                                    <div
                                        class="text-gray-500 text-sm mb-1 line-clamp-2"
                                    >
                                        {{ task.description }}
                                    </div>
                                    <div
                                        class="flex items-center space-x-4 text-xs text-gray-400 mt-1"
                                    >
                                        <span v-if="task.project">
                                            <Link
                                                :href="`/projects/${task.project.id}`"
                                                class="hover:underline text-primary font-medium"
                                            >
                                                {{ task.project.name }}
                                            </Link>
                                        </span>
                                        <span v-if="task.deadline">
                                            <svg
                                                class="inline w-4 h-4 mr-1"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                />
                                            </svg>
                                            Deadline:
                                            {{
                                                new Date(
                                                    task.deadline
                                                ).toLocaleDateString()
                                            }}
                                        </span>
                                        <span>
                                            Created:
                                            {{
                                                new Date(
                                                    task.created_at
                                                ).toLocaleDateString()
                                            }}
                                        </span>
                                    </div>
                                </div>
                                <div
                                    class="mt-2 md:mt-0 md:ml-4 mr-4 flex-shrink-0"
                                >
                                    <Link
                                        :href="`/projects/${task.project?.id}`"
                                        class="inline-flex items-center px-3 py-1.5 bg-primary text-white rounded-md text-xs font-semibold shadow hover:bg-primaryHover transition"
                                    >
                                        View Project
                                    </Link>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-gray-400 text-center py-8">
                            No upcoming tasks assigned to you.
                        </div>
                    </div>

                    <!-- Projects as Team Member -->
                    <div class="bg-white rounded-xl shadow-md p-6 w-1/2 h-fit">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-xl font-semibold text-gray-900">
                                Projects I'm a Team Member
                            </h4>
                            <Link
                                href="/projects"
                                class="text-primary font-medium hover:underline"
                                >View All Projects</Link
                            >
                        </div>
                        <div
                            v-if="props.teamProjects.length > 0"
                            class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-6"
                        >
                            <div
                                v-for="project in props.teamProjects"
                                :key="project.id"
                                class="bg-gradient-to-br from-primary/10 to-blue-100 rounded-lg p-5 shadow group hover:shadow-lg transition flex flex-col"
                            >
                                <div class="flex items-center mb-2">
                                    <span
                                        :class="`px-2 py-0.5 rounded-full text-xs font-semibold ${
                                            projectStatusColors[project.status]
                                        }`"
                                    >
                                        {{
                                            projectStatusLabels[
                                                project.status
                                            ] || project.status
                                        }}
                                    </span>
                                    <span
                                        class="ml-auto text-xs text-gray-400"
                                        >{{
                                            new Date(
                                                project.created_at
                                            ).toLocaleDateString()
                                        }}</span
                                    >
                                </div>
                                <h5
                                    class="font-bold text-lg text-gray-800 mb-1 truncate"
                                >
                                    {{ project.name }}
                                </h5>
                                <div
                                    class="text-gray-500 text-sm mb-2 line-clamp-2"
                                >
                                    {{
                                        project.description || "No description"
                                    }}
                                </div>
                                <div class="flex-1"></div>
                                <div
                                    class="flex items-center justify-between mt-4"
                                >
                                    <div
                                        class="flex items-center text-xs text-gray-500"
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
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                            />
                                        </svg>
                                        {{ project.creator?.name || "Unknown" }}
                                    </div>
                                    <Link
                                        :href="`/projects/${project.id}`"
                                        class="inline-flex items-center px-3 py-1.5 bg-primary text-white rounded-md text-xs font-semibold shadow hover:bg-primaryHover transition"
                                    >
                                        View Project
                                    </Link>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-gray-400 text-center py-8">
                            You are not a team member in any projects yet.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.bg-primary {
    background-color: #6366f1;
}
.bg-primaryHover {
    background-color: #4f46e5;
}
.text-primary {
    color: #6366f1;
}
</style>
