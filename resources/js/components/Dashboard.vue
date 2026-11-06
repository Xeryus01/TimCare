<template>
    <div class="p-6">
        <h2 class="text-2xl font-bold mb-4">Dashboard</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-semibold">Assets</h3>
                <ul class="mt-2">
                    <li>Total: {{ summary.assets.total }}</li>
                    <li>Active: {{ summary.assets.active }}</li>
                    <li>Broken: {{ summary.assets.broken }}</li>
                    <li>Repair: {{ summary.assets.repair }}</li>
                </ul>
            </div>
            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-semibold">Tickets</h3>
                <ul class="mt-2">
                    <li>Open: {{ summary.tickets.open }}</li>
                    <li>Assigned/Detect: {{ summary.tickets.assigned_detect }}</li>
                    <li>Solved (with notes): {{ summary.tickets.solved_with_notes }}</li>
                    <li>Solved: {{ summary.tickets.solved }}</li>
                    <li>Rejected: {{ summary.tickets.rejected }}</li>
                </ul>
            </div>
        </div>
        <div class="mt-6 bg-white p-4 rounded shadow">
            <h3 class="font-semibold">Latest Tickets</h3>
            <ul class="mt-2">
                <li v-for="t in summary.latest_tickets" :key="t.id">
                    {{ t.code }} - {{ t.title }} ({{ t.status }})
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            summary: {
                assets: { total:0, active:0, broken:0, repair:0 },
                tickets: { open:0, assigned_detect:0, solved_with_notes:0, solved:0, rejected:0 },
                latest_tickets: [],
            }
        }
    },
    mounted() {
        axios.get('api/dashboard/summary')
            .then(res => { this.summary = res.data; })
            .catch(console.error);
    }
};
</script>
