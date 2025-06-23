<script setup>
import EmptyState from "@/Components/EmptyState.vue";
import { useForm, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps({ project: Object });
const emit = defineEmits(["refresh"]);

const customerForm = useForm({ customer_id: "" });
const search = ref("");
const suggestions = ref([]);
const showSuggestions = ref(false);

let debounceTimeout = null;

watch(search, async (val) => {
    clearTimeout(debounceTimeout);
    if (val.length < 2) {
        suggestions.value = [];
        showSuggestions.value = false;
        return;
    }
    debounceTimeout = setTimeout(async () => {
        const res = await fetch(`/api/customers?q=${encodeURIComponent(val)}`);
        let customers = await res.json();
        // Filter out already-added customers
        customers = customers.filter(
            (c) => !props.project.customers.some((pc) => pc.id === c.id)
        );
        suggestions.value = customers;
        showSuggestions.value = customers.length > 0;
    }, 200);
});

function selectSuggestion(customer) {
    customerForm.customer_id = customer.id;
    search.value = "";
    suggestions.value = [];
    showSuggestions.value = false;
    customerForm.post(
        route("projects.customers.store", { project: props.project.id }),
        {
            onSuccess: () => emit("refresh"),
            onError: () =>
                alert(
                    customerForm.errors.customer_id || "Failed to add customer."
                ),
        }
    );
}

function removeCustomer(customer) {
    if (confirm("Remove this customer?")) {
        router.delete(
            route("projects.customers.destroy", {
                project: props.project.id,
                customer: customer.id,
            }),
            {
                onSuccess: () => emit("refresh"),
            }
        );
    }
}

function onBlur() {
    setTimeout(() => {
        showSuggestions.value = false;
    }, 150);
}
</script>
<template>
    <div
        class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden"
    >
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Customers</h3>
            <div class="mb-4 relative">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Add by name, company, or email..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-200"
                    @blur="onBlur"
                />
                <ul
                    v-if="showSuggestions && suggestions.length"
                    class="absolute z-10 w-full bg-white border border-gray-200 rounded-md mt-1 shadow-lg max-h-48 overflow-auto"
                >
                    <li
                        v-for="customer in suggestions"
                        :key="customer.id"
                        @click="selectSuggestion(customer)"
                        class="px-4 py-2 cursor-pointer hover:bg-primary/10"
                    >
                        <span class="font-medium">{{ customer.name }}</span>
                        <span class="text-gray-500 ml-2">{{
                            customer.company_name
                        }}</span>
                        <span class="text-gray-400 ml-2">{{
                            customer.email
                        }}</span>
                    </li>
                </ul>
            </div>
        </div>
        <div
            v-if="project.customers && project.customers.length > 0"
            class="divide-y divide-gray-200"
        >
            <div
                v-for="customer in project.customers"
                :key="customer.id"
                class="px-6 py-4 flex items-center space-x-4"
            >
                <div class="flex-shrink-0">
                    <div
                        class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center"
                    >
                        <span class="text-sm font-medium text-gray-700">
                            {{ customer.name.charAt(0).toUpperCase() }}
                        </span>
                    </div>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-medium text-gray-900">
                        {{ customer.name }}
                    </h4>
                    <p class="text-sm text-gray-500">
                        {{ customer.company_name }}
                    </p>
                    <p class="text-xs text-gray-400">{{ customer.email }}</p>
                </div>
                <div>
                    <button
                        @click="removeCustomer(customer)"
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
            icon="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"
            title="No customers"
            description="Add customers to get started."
            action-text="Add Customer"
            :action-href="null"
        />
    </div>
</template>
