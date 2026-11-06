<template>
    <div class="p-6">
        <h2 class="text-2xl font-bold mb-4">Tickets</h2>
        <div class="mb-4">
            <a :href="`${baseUrl}tickets/create`" class="px-4 py-2 bg-blue-600 text-white rounded">New Ticket</a>
        </div>
        <table class="min-w-full bg-white">
            <thead class="bg-gray-200">
                <tr>
                    <th class="py-2 px-4">Code</th>
                    <th class="py-2 px-4">Title</th>
                    <th class="py-2 px-4">Assignee</th>
                    <th class="py-2 px-4">Status</th>
                    <th class="py-2 px-4">Priority</th>
                    <th class="py-2 px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="t in tickets" :key="t.id" class="border-b">
                    <td class="py-2 px-4">{{ t.code }}</td>
                    <td class="py-2 px-4">{{ t.title }}</td>
                    <td class="py-2 px-4">{{ t.assignee ? t.assignee.name : '-' }}</td>
                    <td class="py-2 px-4">{{ t.status }}</td>
                    <td class="py-2 px-4">{{ t.priority }}</td>
                    <td class="py-2 px-4">
                        <a :href="`${baseUrl}tickets/${t.id}`" class="text-blue-500">View</a>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="mt-4">
            <button @click="load(prevPage)" :disabled="!prevPage">Previous</button>
            <button @click="load(nextPage)" :disabled="!nextPage">Next</button>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            baseUrl: import.meta.env.BASE_URL,
            tickets: [],
            prevPage: null,
            nextPage: null,
        };
    },
    mounted() {
        this.load('api/tickets');
    },
    methods: {
        load(url) {
            if (!url) return;
            axios.get(url).then(res => {
                this.tickets = res.data.data;
                this.prevPage = res.data.prev_page_url;
                this.nextPage = res.data.next_page_url;
            });
        }
    }
};
</script>
