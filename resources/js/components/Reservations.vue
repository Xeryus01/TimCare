<template>
    <div class="p-6">
        <h2 class="text-2xl font-bold mb-4">Reservations</h2>
        <div class="mb-4">
            <a :href="`${baseUrl}reservations/create`" class="px-4 py-2 bg-blue-600 text-white rounded">New Reservation</a>
        </div>
        <table class="min-w-full bg-white">
            <thead class="bg-gray-200">
                <tr>
                    <th class="py-2 px-4">Code</th>
                    <th class="py-2 px-4">Room</th>
                    <th class="py-2 px-4">Start</th>
                    <th class="py-2 px-4">End</th>
                    <th class="py-2 px-4">Status</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="r in reservations" :key="r.id" class="border-b">
                    <td class="py-2 px-4">{{ r.code }}</td>
                    <td class="py-2 px-4">{{ r.room_name }}</td>
                    <td class="py-2 px-4">{{ r.start_time }}</td>
                    <td class="py-2 px-4">{{ r.end_time }}</td>
                    <td class="py-2 px-4">{{ r.status }}</td>
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
            reservations: [],
            prevPage: null,
            nextPage: null,
        };
    },
    mounted() {
        this.load('api/reservations');
    },
    methods: {
        load(url) {
            if (!url) return;
            axios.get(url).then(res => {
                this.reservations = res.data.data;
                this.prevPage = res.data.prev_page_url;
                this.nextPage = res.data.next_page_url;
            });
        }
    }
};
</script>