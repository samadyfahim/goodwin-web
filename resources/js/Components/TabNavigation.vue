<script setup>
const props = defineProps({
    tabs: {
        type: Array,
        required: true,
    },
    activeTab: {
        type: String,
        required: true,
    },
});

const emit = defineEmits(["update:activeTab"]);

const setActiveTab = (tab) => {
    emit("update:activeTab", tab);
};
</script>

<template>
    <div class="border-b border-gray-200 mb-8">
        <nav class="-mb-px flex space-x-8">
            <button
                v-for="tab in tabs"
                :key="tab.id"
                @click="setActiveTab(tab.id)"
                :class="`py-2 px-1 border-b-2 font-medium text-sm ${
                    activeTab === tab.id
                        ? 'border-primary text-primary'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                }`"
            >
                {{ tab.label }}
                <span
                    v-if="tab.count !== undefined"
                    class="ml-1 text-xs text-gray-400"
                >
                    ({{ tab.count }})
                </span>
            </button>
        </nav>
    </div>
</template>
