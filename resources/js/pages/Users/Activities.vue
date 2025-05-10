<template>
    <AppLayout>
        <div class="container p-6 space-y-6">
            <h1 class="text-2xl font-bold text-gray-900">User Activity Logs</h1>

            <!-- Search and User Filter -->
            <div class="flex flex-col md:flex-row justify-between space-y-4 md:space-y-0 md:space-x-4">
                <Input v-model="searchQuery" type="text" placeholder="Search activities"
                    class="p-3 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200 w-full md:w-1/2" />

                <select v-model="selectedUser"
                    class="p-3 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200 w-full md:w-1/3">
                    <option value="">All Users</option>
                    <option v-for="user in uniqueUsers" :key="user" :value="user">{{ user }}</option>
                </select>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="text-center">
                <p class="text-gray-500">Loading...</p>
            </div>

            <!-- No Results State -->
            <div v-if="!loading && paginatedActivities.length === 0" class="text-center text-gray-500">
                <p>No activity logs found.</p>
            </div>

            <!-- Activity Log Table -->
            <table v-if="!loading && paginatedActivities.length"
                class="min-w-full border-collapse bg-white shadow-md rounded-lg overflow-hidden">
                <thead class="bg-blue-600 text-white">
                    <tr>
                        <th class="p-3 text-left">Date</th>
                        <th class="p-3 text-left">User</th>
                        <th class="p-3 text-left">Event</th>
                        <th class="p-3 text-left">IP</th>
                        <th class="p-3 text-left">URL</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="activity in paginatedActivities" :key="activity.id" class="border-b hover:bg-gray-100">
                        <td class="p-3">{{ formatDate(activity.created_at) }}</td>
                        <td class="p-3">{{ activity.user_name }}</td>
                        <td class="p-3">{{ activity.description }}</td>
                        <td class="p-3">{{ activity.properties?.ip }}</td>
                        <td class="p-3">{{ activity.properties?.url }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination Controls -->
            <div v-if="!loading && totalPages > 1" class="mt-4 flex items-center justify-center gap-2">
                <button @click="currentPage = 1" :disabled="currentPage === 1"
                    class="px-3 py-1 rounded border disabled:opacity-50 disabled:cursor-not-allowed">
                    First
                </button>
                <button @click="currentPage--" :disabled="currentPage === 1"
                    class="px-3 py-1 rounded border disabled:opacity-50 disabled:cursor-not-allowed">
                    Previous
                </button>

                <div class="flex gap-1">
                    <button v-for="pageNumber in displayedPages" :key="pageNumber" @click="currentPage = pageNumber"
                        :class="[
                            'px-3 py-1 rounded border',
                            currentPage === pageNumber ? 'bg-blue-600 text-white' : 'hover:bg-gray-100'
                        ]">
                        {{ pageNumber }}
                    </button>
                </div>

                <button @click="currentPage++" :disabled="currentPage === totalPages"
                    class="px-3 py-1 rounded border disabled:opacity-50 disabled:cursor-not-allowed">
                    Next
                </button>
                <button @click="currentPage = totalPages" :disabled="currentPage === totalPages"
                    class="px-3 py-1 rounded border disabled:opacity-50 disabled:cursor-not-allowed">
                    Last
                </button>

                <span class="text-sm text-gray-600">
                    Page {{ currentPage }} of {{ totalPages }}
                </span>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Input } from '@/components/ui/input';
import { Pagination } from '@/components/ui/pagination';

const page = usePage();
const activities = ref(Array.isArray(page.props.activities) ? page.props.activities : []);
const searchQuery = ref('');
const selectedUser = ref('');
const loading = ref(false);
const currentPage = ref(1);
const itemsPerPage = 10;

// Unique users for the user filter
const uniqueUsers = computed(() => [...new Set(activities.value.map(activity => activity.user_name))]);

// Computed filtered activities
const filteredActivities = computed(() => {
    let filtered = activities.value;
    if (searchQuery.value) {
        filtered = filtered.filter(activity =>
            activity.description?.toLowerCase().includes(searchQuery.value.toLowerCase())
        );
    }
    if (selectedUser.value) {
        filtered = filtered.filter(activity => activity.user_name === selectedUser.value);
    }
    return filtered;
});

// Pagination logic
const totalPages = computed(() => Math.ceil(filteredActivities.value.length / itemsPerPage));
const paginatedActivities = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredActivities.value.slice(start, end);
});

// Reset to first page when filters change
watch([searchQuery, selectedUser], () => {
    currentPage.value = 1;
});

// Pagination handler
function onPageChange(page) {
    currentPage.value = page;
}

const formatDate = (dateString) => new Date(dateString).toLocaleString();
const displayedPages = computed(() => {
    const delta = 2;
    const range = [];
    const rangeWithDots = [];
    let l;

    range.push(1);

    for (let i = currentPage.value - delta; i <= currentPage.value + delta; i++) {
        if (i < totalPages.value && i > 1) {
            range.push(i);
        }
    }

    range.push(totalPages.value);

    for (let i of range) {
        if (l) {
            if (i - l === 2) {
                rangeWithDots.push(l + 1);
            } else if (i - l !== 1) {
                rangeWithDots.push('...');
            }
        }
        rangeWithDots.push(i);
        l = i;
    }

    return rangeWithDots;
});

</script>

<style scoped>
.shadow-md {
    box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.08);
}

.rounded-lg {
    border-radius: 12px;
}

input:focus,
select:focus {
    border-color: #2563EB;
    outline: none;
}

table {
    border-radius: 12px;
    overflow: hidden;
}

th {
    text-align: left;
}

tr:hover {
    background-color: #f8fafc;
}
</style>