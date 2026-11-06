<template>
    <div class="p-6">
        <h2 class="text-2xl font-bold mb-4">Assets</h2>
        <div class="mb-4" v-if="canManage">
            <a :href="`${baseUrl}assets/create`" class="px-4 py-2 bg-blue-600 text-white rounded">New Asset</a>
        </div>
        <table class="min-w-full bg-white">
            <thead class="bg-gray-200">
                <tr>
                    <th class="py-2 px-4">Code</th>
                    <th class="py-2 px-4">Name</th>
                    <th class="py-2 px-4">Status</th>
                    <th class="py-2 px-4">Location</th>
                    <th class="py-2 px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="a in assets" :key="a.id" class="border-b">
                    <td class="py-2 px-4">{{ a.asset_code }}</td>
                    <td class="py-2 px-4">{{ a.name }}</td>
                    <td class="py-2 px-4">{{ a.status }}</td>
                    <td class="py-2 px-4">{{ a.location }}</td>
                    <td class="py-2 px-4"><a :href="`${baseUrl}assets/${a.id}`" class="text-blue-500">View</a></td>
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
    props: ['canManage'],
    data() {
        return {
            baseUrl: import.meta.env.BASE_URL,
            assets: [],
            prevPage: null,
            nextPage: null,
        };
    },
    mounted() {
        this.load('api/assets');
    },
    methods: {
        load(url) {
            if (!url) return;
            axios.get(url).then(res => {
                this.assets = res.data.data;
                this.prevPage = res.data.prev_page_url;
                this.nextPage = res.data.next_page_url;
            });
        }
    }
};
</script>