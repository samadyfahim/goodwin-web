<!--
Projects/Create.vue
Form for creating a new project, with fields for name, description, status, and deadline.
-->
<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import FormInput from "@/Components/FormInput.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ref } from "vue";

const form = useForm({
    name: "",
    description: "",
    status: "planned",
    deadline: "",
});

const statusOptions = [
    { value: "planned", label: "Planned", icon: "📋" },
    { value: "active", label: "Active", icon: "⚡" },
    { value: "completed", label: "Completed", icon: "✅" },
    { value: "delayed", label: "Delayed", icon: "⚠️" },
];

const submit = () => {
    form.post(route("projects.store"));
};
</script>

<template>
    <Head title="Create New Project" />

    <AuthenticatedLayout>
        <template #header>
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
                <h2 class="text-2xl font-bold text-gray-900">
                    Create New Project
                </h2>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden"
                >
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Project Information
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Fill in the details below to create a new project.
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Project Name -->
                        <FormInput
                            id="name"
                            label="Project Name"
                            v-model="form.name"
                            type="text"
                            required
                            placeholder="Enter project name"
                            :error="form.errors.name"
                        />

                        <!-- Project Description -->
                        <FormInput
                            id="description"
                            label="Description"
                            v-model="form.description"
                            type="textarea"
                            rows="4"
                            placeholder="Describe your project..."
                            :error="form.errors.description"
                        />

                        <!-- Project Status -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Status *
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <label
                                    v-for="option in statusOptions"
                                    :key="option.value"
                                    class="relative flex cursor-pointer rounded-lg border border-gray-300 bg-white p-4 shadow-sm focus:outline-none hover:border-primary transition-colors duration-200"
                                    :class="{
                                        'border-primary bg-primary/5':
                                            form.status === option.value,
                                    }"
                                >
                                    <input
                                        type="radio"
                                        :value="option.value"
                                        v-model="form.status"
                                        class="sr-only"
                                    />
                                    <div
                                        class="flex w-full items-center justify-between"
                                    >
                                        <div class="flex items-center">
                                            <div class="text-sm">
                                                <span class="text-lg mr-2">{{
                                                    option.icon
                                                }}</span>
                                                <span
                                                    class="font-medium text-gray-900"
                                                    >{{ option.label }}</span
                                                >
                                            </div>
                                        </div>
                                        <div
                                            v-if="form.status === option.value"
                                            class="shrink-0 text-primary"
                                        >
                                            <svg
                                                class="h-6 w-6"
                                                fill="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M20.707 5.293a1 1 0 010 1.414l-11 11a1 1 0 01-1.414 0l-5-5a1 1 0 011.414-1.414L9 15.586 19.293 5.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <p
                                v-if="form.errors.status"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.status }}
                            </p>
                        </div>

                        <!-- Project Deadline -->
                        <FormInput
                            id="deadline"
                            label="Deadline"
                            v-model="form.deadline"
                            type="date"
                            :error="form.errors.deadline"
                        />
                        <p class="mt-1 text-xs text-gray-500">
                            Leave empty if no specific deadline is set
                        </p>

                        <!-- Form Actions -->
                        <div
                            class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200"
                        >
                            <Link
                                href="/projects"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors duration-200"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primaryHover focus:bg-primaryHover active:bg-primaryHover focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <svg
                                    v-if="form.processing"
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
                                {{
                                    form.processing
                                        ? "Creating..."
                                        : "Create Project"
                                }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
