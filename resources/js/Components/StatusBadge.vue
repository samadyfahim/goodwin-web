<script setup>
const props = defineProps({
    status: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        default: "project", // 'project' or 'task'
        validator: (value) => ["project", "task"].includes(value),
    },
});

const getStatusColor = (status, type) => {
    if (type === "task") {
        const colors = {
            pending: "bg-yellow-100 text-yellow-800",
            in_progress: "bg-blue-100 text-blue-800",
            completed: "bg-green-100 text-green-800",
            cancelled: "bg-red-100 text-red-800",
        };
        return colors[status] || "bg-gray-100 text-gray-800";
    } else {
        const colors = {
            planned: "bg-blue-100 text-blue-800",
            active: "bg-green-100 text-green-800",
            completed: "bg-purple-100 text-purple-800",
            delayed: "bg-red-100 text-red-800",
        };
        return colors[status] || "bg-gray-100 text-gray-800";
    }
};

const getStatusIcon = (status, type) => {
    if (type === "task") {
        const icons = {
            pending: "⏳",
            in_progress: "🔄",
            completed: "✅",
            cancelled: "❌",
        };
        return icons[status] || "📄";
    } else {
        const icons = {
            planned: "📋",
            active: "⚡",
            completed: "✅",
            delayed: "⚠️",
        };
        return icons[status] || "📄";
    }
};

const formatStatus = (status) => {
    return (
        status.replace("_", " ").charAt(0).toUpperCase() +
        status.replace("_", " ").slice(1)
    );
};
</script>

<template>
    <span
        :class="`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusColor(
            status,
            type
        )}`"
    >
        <span class="mr-1">{{ getStatusIcon(status, type) }}</span>
        {{ formatStatus(status) }}
    </span>
</template>
