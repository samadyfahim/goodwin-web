<script setup>
import EmptyState from "@/Components/EmptyState.vue";
import Modal from "@/Components/Modal.vue";
import FormInput from "@/Components/FormInput.vue";
import { useForm, router } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";

const props = defineProps({ project: Object, comments: Array });
const emit = defineEmits(["refresh"]);

const showNoteModal = ref(false);
const editingNote = ref(null);
const noteForm = useForm({ content: "" });

const newNote = ref("");
const replyOpen = ref({}); // { [noteId]: true/false }
const commentContent = ref({}); // { [noteId]: '' }

const localComments = ref([...props.comments]);
watch(
    () => props.comments,
    (newVal) => {
        localComments.value = [...newVal];
    }
);

const commentsByNote = computed(() => {
    const map = {};
    (localComments.value || []).forEach((comment) => {
        if (!map[comment.note_id]) map[comment.note_id] = [];
        map[comment.note_id].push(comment);
    });
    return map;
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
    if (!newNote.value.trim()) return;
    noteForm.content = newNote.value;
    noteForm.post(
        route("projects.notes.store", { project: props.project.id }),
        {
            onSuccess: () => {
                newNote.value = "";
                emit("refresh");
            },
        }
    );
}
function handleNoteKey(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === "Enter") submitNote();
    if (e.key === "Enter" && !e.shiftKey) {
        e.preventDefault();
        submitNote();
    }
}
function toggleReply(noteId) {
    replyOpen.value[noteId] = !replyOpen.value[noteId];
}
async function submitComment(noteId) {
    if (!commentContent.value[noteId]?.trim()) return;
    const content = commentContent.value[noteId];
    // Try AJAX first
    try {
        const res = await fetch(
            route("projects.notes.comments.store", {
                project: props.project.id,
                note: noteId,
            }),
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        "meta[name=csrf-token]"
                    )?.content,
                },
                body: JSON.stringify({ content }),
            }
        );
        if (res.ok) {
            const newComment = await res.json();
            localComments.value.push(newComment);
            commentContent.value[noteId] = "";
            return;
        }
    } catch (e) {
        // fallback
    }
    // Fallback to Inertia
    router.post(
        route("projects.notes.comments.store", {
            project: props.project.id,
            note: noteId,
        }),
        { content },
        {
            onSuccess: () => {
                commentContent.value[noteId] = "";
            },
        }
    );
}
function deleteNote(note) {
    if (confirm("Delete this note?")) {
        router.delete(
            route("projects.notes.destroy", {
                project: props.project.id,
                note: note.id,
            }),
            {
                onSuccess: () => emit("refresh"),
            }
        );
    }
}
function handleCommentKey(e, noteId) {
    if (e.key === "Enter" && !e.shiftKey) {
        e.preventDefault();
        submitComment(noteId);
    }
}
</script>
<template>
    <div
        class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden"
    >
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                Project Notes
            </h3>
            <div class="flex items-end space-x-2 mb-2">
                <textarea
                    v-model="newNote"
                    @keydown="handleNoteKey"
                    rows="2"
                    placeholder="Add a note..."
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-200 resize-none"
                />
                <button
                    @click="submitNote"
                    :disabled="noteForm.processing || !newNote.trim()"
                    class="inline-flex items-center px-3 py-2 bg-primary border border-transparent rounded-md text-sm font-medium text-white hover:bg-primaryHover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <svg
                        class="w-5 h-5 mr-1"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7"
                        />
                    </svg>
                    Send
                </button>
            </div>
        </div>
        <div
            v-if="project.notes && project.notes.length > 0"
            class="divide-y divide-gray-200"
        >
            <div v-for="note in project.notes" :key="note.id" class="px-6 py-6">
                <div class="flex items-center space-x-3 mb-1">
                    <div
                        class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-base"
                    >
                        {{ note.creator?.name?.charAt(0) }}
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">
                            {{ note.creator?.name }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ new Date(note.created_at).toLocaleString() }}
                        </div>
                    </div>
                </div>
                <div class="ml-11 mt-1 text-gray-800 whitespace-pre-line">
                    {{ note.content }}
                </div>
                <!-- Comments thread -->
                <div
                    v-if="
                        commentsByNote[note.id] &&
                        commentsByNote[note.id].length
                    "
                    class="ml-11 mt-4 space-y-3"
                >
                    <div
                        v-for="comment in commentsByNote[note.id]"
                        :key="comment.id"
                        class="flex items-start space-x-2"
                    >
                        <div
                            class="w-7 h-7 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-xs font-bold"
                        >
                            {{ comment.creator?.name?.charAt(0) }}
                        </div>
                        <div>
                            <div class="font-medium text-gray-800 text-sm">
                                {{ comment.creator?.name }}
                                <span class="text-xs text-gray-400 ml-2">{{
                                    new Date(
                                        comment.created_at
                                    ).toLocaleString()
                                }}</span>
                            </div>
                            <div
                                class="text-gray-700 text-sm whitespace-pre-line"
                            >
                                {{ comment.content }}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Reply input -->
                <div class="ml-11 mt-4 flex items-end space-x-2">
                    <input
                        v-model="commentContent[note.id]"
                        @keydown="(e) => handleCommentKey(e, note.id)"
                        type="text"
                        placeholder="Reply..."
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-200"
                    />
                    <button
                        @click="() => submitComment(note.id)"
                        :disabled="!commentContent[note.id]?.trim()"
                        class="inline-flex items-center px-2 py-1 bg-primary border border-transparent rounded-md text-xs font-medium text-white hover:bg-primaryHover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50 disabled:cursor-not-allowed"
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
                                d="M5 13l4 4L19 7"
                            />
                        </svg>
                        Send
                    </button>
                </div>
            </div>
        </div>
        <EmptyState
            v-else
            icon="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
            title="No notes"
            description="Get started by adding a note."
            action-text="Add Note"
            :action-href="null"
            @click="() => {}"
        />
    </div>
    <Modal
        :open="showNoteModal"
        @close="closeNoteModal"
        :title="editingNote ? 'Edit Note' : 'Add Note'"
    >
        <form @submit.prevent="submitNote" class="space-y-4">
            <FormInput
                id="note-content"
                label="Note"
                v-model="noteForm.content"
                type="textarea"
                rows="4"
                required
                :error="noteForm.errors.content"
            />
            <div class="flex justify-end space-x-2 pt-4">
                <button
                    type="button"
                    @click="closeNoteModal"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors duration-200"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    :disabled="noteForm.processing"
                    class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primaryHover focus:bg-primaryHover active:bg-primaryHover focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <svg
                        v-if="noteForm.processing"
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
                    {{ editingNote ? "Update Note" : "Add Note" }}
                </button>
            </div>
        </form>
    </Modal>
</template>
