<script setup>
const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    value: {
        type: [String, Number],
        required: true,
    },
    icon: {
        type: String,
        required: true,
    },
    iconColor: {
        type: String,
        default: "blue",
    },
    iconBgColor: {
        type: String,
        default: "blue",
    },
});

const getIconColorClass = (color) => {
    const colors = {
        blue: "text-blue-600",
        green: "text-green-600",
        purple: "text-purple-600",
        yellow: "text-yellow-600",
        red: "text-red-600",
        gray: "text-gray-600",
    };
    return colors[color] || colors.blue;
};

const getIconBgColorClass = (color) => {
    const colors = {
        blue: "bg-blue-100",
        green: "bg-green-100",
        purple: "bg-purple-100",
        yellow: "bg-yellow-100",
        red: "bg-red-100",
        gray: "bg-gray-100",
    };
    return colors[color] || colors.blue;
};
</script>

<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div
                    :class="`w-8 h-8 ${getIconBgColorClass(
                        iconBgColor
                    )} rounded-lg flex items-center justify-center`"
                >
                    <svg
                        :class="`w-5 h-5 ${getIconColorClass(iconColor)}`"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            :d="icon"
                        ></path>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">{{ title }}</p>
                <p class="text-2xl font-semibold text-gray-900">{{ value }}</p>
            </div>
        </div>
    </div>
</template>
