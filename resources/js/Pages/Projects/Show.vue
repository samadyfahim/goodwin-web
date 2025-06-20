<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    project: Object,
});

const activeTab = ref("overview");

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

const getTaskStatusColor = (status) => {
    const colors = {
        pending: "bg-yellow-100 text-yellow-800",
        in_progress: "bg-blue-100 text-blue-800",
        completed: "bg-green-100 text-green-800",
        cancelled: "bg-red-100 text-red-800",
    };
    return colors[status] || "bg-gray-100 text-gray-800";
};
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
                    <span
                        :class="`inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${getStatusColor(
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
                    <button
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors duration-200"
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
                <!-- Tab Navigation -->
                <div class="border-b border-gray-200 mb-8">
                    <nav class="-mb-px flex space-x-8">
                        <button
                            @click="activeTab = 'overview'"
                            :class="`py-2 px-1 border-b-2 font-medium text-sm ${
                                activeTab === 'overview'
                                    ? 'border-primary text-primary'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                            }`"
                        >
                            Overview
                        </button>
                        <button
                            @click="activeTab = 'tasks'"
                            :class="`py-2 px-1 border-b-2 font-medium text-sm ${
                                activeTab === 'tasks'
                                    ? 'border-primary text-primary'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                            }`"
                        >
                            Tasks ({{ project.tasks?.length || 0 }})
                        </button>
                        <button
                            @click="activeTab = 'team'"
                            :class="`py-2 px-1 border-b-2 font-medium text-sm ${
                                activeTab === 'team'
                                    ? 'border-primary text-primary'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                            }`"
                        >
                            Team ({{ project.users?.length || 0 }})
                        </button>
                        <button
                            @click="activeTab = 'files'"
                            :class="`py-2 px-1 border-b-2 font-medium text-sm ${
                                activeTab === 'files'
                                    ? 'border-primary text-primary'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                            }`"
                        >
                            Files ({{ project.files?.length || 0 }})
                        </button>
                    </nav>
                </div>

                <!-- Overview Tab -->
                <div v-if="activeTab === 'overview'" class="space-y-6">
                    <!-- Project Description -->
                    <div
                        class="bg-white rounded-lg shadow-sm border border-gray-200 p-6"
                    >
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Description
                        </h3>
                        <p class="text-gray-600">
                            {{
                                project.description ||
                                "No description available for this project."
                            }}
                        </p>
                    </div>

                    <!-- Project Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div
                            class="bg-white rounded-lg shadow-sm border border-gray-200 p-6"
                        >
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center"
                                    >
                                        <svg
                                            class="w-5 h-5 text-blue-600"
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
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Total Tasks
                                    </p>
                                    <p
                                        class="text-2xl font-semibold text-gray-900"
                                    >
                                        {{ project.tasks?.length || 0 }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-white rounded-lg shadow-sm border border-gray-200 p-6"
                        >
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center"
                                    >
                                        <svg
                                            class="w-5 h-5 text-green-600"
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
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Team Members
                                    </p>
                                    <p
                                        class="text-2xl font-semibold text-gray-900"
                                    >
                                        {{ project.users?.length || 0 }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-white rounded-lg shadow-sm border border-gray-200 p-6"
                        >
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center"
                                    >
                                        <svg
                                            class="w-5 h-5 text-purple-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                            ></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Customers
                                    </p>
                                    <p
                                        class="text-2xl font-semibold text-gray-900"
                                    >
                                        {{ project.customers?.length || 0 }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-white rounded-lg shadow-sm border border-gray-200 p-6"
                        >
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center"
                                    >
                                        <svg
                                            class="w-5 h-5 text-yellow-600"
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
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Deadline
                                    </p>
                                    <p
                                        class="text-2xl font-semibold text-gray-900"
                                    >
                                        {{ formatDate(project.deadline) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customers -->
                    <div
                        v-if="project.customers && project.customers.length > 0"
                        class="bg-white rounded-lg shadow-sm border border-gray-200 p-6"
                    >
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Customers
                        </h3>
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"
                        >
                            <div
                                v-for="customer in project.customers"
                                :key="customer.id"
                                class="border border-gray-200 rounded-lg p-4"
                            >
                                <h4 class="font-medium text-gray-900">
                                    {{ customer.name }}
                                </h4>
                                <p class="text-sm text-gray-500">
                                    {{ customer.company_name }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ customer.email }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tasks Tab -->
                <div v-if="activeTab === 'tasks'" class="space-y-6">
                    <div
                        class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden"
                    >
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Project Tasks
                            </h3>
                        </div>
                        <div
                            v-if="project.tasks && project.tasks.length > 0"
                            class="divide-y divide-gray-200"
                        >
                            <div
                                v-for="task in project.tasks"
                                :key="task.id"
                                class="px-6 py-4 hover:bg-gray-50 transition-colors duration-200"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h4
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            {{ task.name }}
                                        </h4>
                                        <p class="text-sm text-gray-500 mt-1">
                                            {{ task.description }}
                                        </p>
                                        <div
                                            class="flex items-center mt-2 space-x-4"
                                        >
                                            <span
                                                :class="`inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${getTaskStatusColor(
                                                    task.status
                                                )}`"
                                            >
                                                {{
                                                    task.status
                                                        .replace("_", " ")
                                                        .charAt(0)
                                                        .toUpperCase() +
                                                    task.status
                                                        .replace("_", " ")
                                                        .slice(1)
                                                }}
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                Created by
                                                {{ task.creator?.name }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs text-gray-500">
                                            {{ task.users?.length || 0 }}
                                            assigned
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="px-6 py-12 text-center">
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
                                No tasks
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Get started by creating a new task.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Team Tab -->
                <div v-if="activeTab === 'team'" class="space-y-6">
                    <div
                        class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden"
                    >
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Team Members
                            </h3>
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
                                        <span
                                            class="text-sm font-medium text-gray-700"
                                        >
                                            {{
                                                user.name
                                                    .charAt(0)
                                                    .toUpperCase()
                                            }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h4
                                        class="text-sm font-medium text-gray-900"
                                    >
                                        {{ user.name }}
                                    </h4>
                                    <p class="text-sm text-gray-500">
                                        {{ user.email }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div v-else class="px-6 py-12 text-center">
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
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"
                                ></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">
                                No team members
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Add team members to get started.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Files Tab -->
                <div v-if="activeTab === 'files'" class="space-y-6">
                    <div
                        class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden"
                    >
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Project Files
                            </h3>
                        </div>
                        <div
                            v-if="project.files && project.files.length > 0"
                            class="divide-y divide-gray-200"
                        >
                            <div
                                v-for="file in project.files"
                                :key="file.id"
                                class="px-6 py-4 flex items-center space-x-4"
                            >
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center"
                                    >
                                        <svg
                                            class="w-6 h-6 text-gray-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                                            ></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h4
                                        class="text-sm font-medium text-gray-900"
                                    >
                                        {{ file.name }}
                                    </h4>
                                    <p class="text-sm text-gray-500">
                                        {{ file.size }} bytes
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button
                                        class="text-primary hover:text-primaryHover text-sm font-medium"
                                    >
                                        Download
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div v-else class="px-6 py-12 text-center">
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
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                                ></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">
                                No files
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Upload files to get started.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
