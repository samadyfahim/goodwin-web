<script setup>
import EmptyState from "@/Components/EmptyState.vue";
import { useForm, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps({ project: Object });
const emit = defineEmits(["refresh"]);

const customerForm = useForm({ customer_ids: [] });
const search = ref("");
const suggestions = ref([]);
const showSuggestions = ref(false);
const selectedCustomers = ref([]);

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
    console.log("Selected:", customer);
    if (!selectedCustomers.value.some((c) => c.id === customer.id)) {
        selectedCustomers.value.push(customer);
    }
    search.value = "";
    suggestions.value = [];
    showSuggestions.value = false;
}

function removeSelectedCustomer(customer) {
    selectedCustomers.value = selectedCustomers.value.filter(
        (c) => c.id !== customer.id
    );
}

function submitCustomers() {
    console.log("Submitting customers:", selectedCustomers.value);
    if (selectedCustomers.value.length === 0) {
        alert("Please select at least one customer.");
        return;
    }

    // Use the form to submit all customers at once
    customerForm.customer_ids = selectedCustomers.value.map((c) => c.id);

    customerForm.post(
        route("projects.customers.store", { project: props.project.id }),
        {
            onSuccess: () => {
                customerForm.reset();
                selectedCustomers.value = [];
                search.value = "";
                suggestions.value = [];
                showSuggestions.value = false;
                emit("refresh");
            },
            onError: () => {
                alert("Failed to add customers.");
            },
            onFinish: () => {
                customerForm.reset();
            },
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
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Customers</h3>
            </div>

            <!-- Add Customer Form -->
            <div class="space-y-4">
                <div class="relative">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search by name, company, or email..."
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
                            @mousedown="
                                console.log('Clicked', customer);
                                selectSuggestion(customer);
                            "
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

                <div v-if="selectedCustomers.length > 0">
                    <label class="block text-sm font-medium text-gray-700 mb-2"
                        >Selected Customers</label
                    >
                    <div class="flex flex-wrap gap-2">
                        <span
                            v-for="customer in selectedCustomers"
                            :key="customer.id"
                            class="bg-primary/10 text-primary px-2 py-1 rounded flex items-center"
                        >
                            {{ customer.name }}
                            <button
                                @click="removeSelectedCustomer(customer)"
                                class="ml-1 text-xs text-red-500"
                            >
                                &times;
                            </button>
                        </span>
                    </div>
                </div>

                <div class="flex justify-start space-x-2">
                    <button
                        type="button"
                        @click="submitCustomers"
                        :disabled="
                            selectedCustomers.length === 0 ||
                            customerForm.processing
                        "
                        class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primaryHover focus:bg-primaryHover active:bg-primaryHover focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg
                            v-if="customerForm.processing"
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
                        Add Customers
                    </button>
                </div>
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
            action-text="Add Customers"
            :action-href="null"
        />
    </div>
</template>
