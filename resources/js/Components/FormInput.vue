<!--
FormInput.vue
Reusable form input component for text, textarea, and date fields.
Props:
- id: String
- label: String
- modelValue: String
- type: String
- error: String
-->
<script setup>
const props = defineProps({
    id: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        default: "text",
    },
    modelValue: {
        type: [String, Number],
        default: "",
    },
    required: {
        type: Boolean,
        default: false,
    },
    placeholder: {
        type: String,
        default: "",
    },
    error: {
        type: String,
        default: "",
    },
    rows: {
        type: Number,
        default: undefined,
    },
});

const emit = defineEmits(["update:modelValue"]);

const updateValue = (event) => {
    emit("update:modelValue", event.target.value);
};
</script>

<template>
    <div>
        <label :for="id" class="block text-sm font-medium text-gray-700 mb-2">
            {{ label }}<span v-if="required" class="text-red-500">*</span>
        </label>

        <textarea
            v-if="type === 'textarea'"
            :id="id"
            :value="modelValue"
            @input="updateValue"
            :rows="rows"
            :required="required"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-200"
            :class="{
                'border-red-500 focus:border-red-500 focus:ring-red-500': error,
            }"
            :placeholder="placeholder"
        ></textarea>

        <input
            v-else
            :id="id"
            :type="type"
            :value="modelValue"
            @input="updateValue"
            :required="required"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-200"
            :class="{
                'border-red-500 focus:border-red-500 focus:ring-red-500': error,
            }"
            :placeholder="placeholder"
        />

        <p v-if="error" class="mt-1 text-sm text-red-600">
            {{ error }}
        </p>
    </div>
</template>
