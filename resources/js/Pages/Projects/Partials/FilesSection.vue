<script setup>
import EmptyState from "@/Components/EmptyState.vue";
import Modal from "@/Components/Modal.vue";
import { useForm, router } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({ project: Object });
const emit = defineEmits(["refresh"]);

const showFileModal = ref(false);
const fileForm = useForm({ file: null });
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
            onSuccess: () => {
                closeFileModal();
                emit("refresh");
            },
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
            <h3 class="text-lg font-semibold text-gray-900">Project Files</h3>
            <button
                @click="openFileModal()"
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
                Upload File
            </button>
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
                            />
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-medium text-gray-900">
                        {{ file.filename }}
                    </h4>
                    <p class="text-sm text-gray-500">
                        {{ file.size ? file.size + " bytes" : "" }}
                    </p>
                    <div class="flex space-x-2 mt-2">
                        <a
                            :href="
                                route('projects.files.view', {
                                    project: props.project.id,
                                    file: file.id,
                                })
                            "
                            target="_blank"
                            class="text-primary hover:underline text-xs"
                            >View</a
                        >
                        <a
                            :href="
                                route('projects.files.download', {
                                    project: props.project.id,
                                    file: file.id,
                                })
                            "
                            class="text-primary hover:underline text-xs"
                            >Download</a
                        >
                    </div>
                    <div
                        v-if="
                            file.filename.match(/\.(jpg|jpeg|png|gif|webp)$/i)
                        "
                        class="mt-2"
                    >
                        <img
                            :src="
                                route('projects.files.view', {
                                    project: props.project.id,
                                    file: file.id,
                                })
                            "
                            :alt="file.filename"
                            class="w-24 h-24 object-cover rounded border"
                        />
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button
                        @click="deleteFile(file)"
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
            icon="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"
            title="No files"
            description="Upload files to get started."
            action-text="Upload File"
            :action-href="null"
            @click="openFileModal()"
        />
    </div>
    <Modal :open="showFileModal" @close="closeFileModal" title="Upload File">
        <form @submit.prevent="submitFile" class="space-y-4">
            <input
                type="file"
                @change="(e) => (fileForm.file = e.target.files[0])"
                class="block w-full text-sm text-gray-700 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-200"
            />
            <div class="flex justify-end space-x-2 pt-4">
                <button
                    type="button"
                    @click="closeFileModal"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors duration-200"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    :disabled="fileForm.processing"
                    class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primaryHover focus:bg-primaryHover active:bg-primaryHover focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <svg
                        v-if="fileForm.processing"
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
                    Upload
                </button>
            </div>
        </form>
    </Modal>
</template>
