<!--
UploadedFiles.vue
Displays a table of all uploaded files, with download links and uploader/project info.
Props:
- files: Array of file objects
-->
<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

const props = defineProps({
    files: Array,
});
</script>

<template>
    <Head title="Uploaded Files" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-bold text-gray-900">Uploaded Files</h2>
        </template>
        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white rounded-lg shadow-sm border border-gray-200 p-6"
                >
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    File Name
                                </th>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Project
                                </th>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Uploaded By
                                </th>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Uploaded At
                                </th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="file in files" :key="file.id">
                                <td class="px-4 py-2">
                                    <span class="font-medium text-blue-700">{{
                                        file.filename
                                    }}</span>
                                </td>
                                <td class="px-4 py-2">
                                    <span v-if="file.project">
                                        <Link
                                            :href="`/projects/${file.project.id}`"
                                            class="text-primary hover:underline"
                                        >
                                            {{ file.project.name }}
                                        </Link>
                                    </span>
                                    <span v-else class="text-gray-400">-</span>
                                </td>
                                <td class="px-4 py-2">
                                    <span v-if="file.creator">
                                        {{ file.creator.name }}
                                    </span>
                                    <span v-else class="text-gray-400">-</span>
                                </td>
                                <td class="px-4 py-2">
                                    {{
                                        new Date(
                                            file.created_at
                                        ).toLocaleString()
                                    }}
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <a
                                        :href="`/projects/${file.project?.id}/files/${file.id}/download`"
                                        class="text-primary hover:underline"
                                        target="_blank"
                                        download
                                        >Download</a
                                    >
                                </td>
                            </tr>
                            <tr v-if="files.length === 0">
                                <td
                                    colspan="5"
                                    class="text-center text-gray-400 py-8"
                                >
                                    No files uploaded yet.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
