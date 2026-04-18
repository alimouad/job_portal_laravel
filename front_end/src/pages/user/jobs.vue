<script setup>
import { computed, ref, onMounted, watch } from 'vue';
import axiosClient from '@/axios';
import HomeLayout from '@/layouts/HomeLayout.vue';

// State
const jobs = ref([]);
const categories = ref([]);
const loading = ref(false);
const search = ref('');
const selectedCategoryId = ref(null);
const error = ref('');
const favouriteJobIds = ref([]);
let searchTimer = null;

const quickTags = ['Project Manager', 'Safety Officer', 'Inspector', 'Foreman', 'Designer', 'Metallurgist'];

// Fetch jobs
const loadJobs = async () => {
    loading.value = true;
    error.value = '';
    try {
        const params = {};
        if (search.value.trim()) params.q = search.value.trim();
        if (selectedCategoryId.value) params.category_id = selectedCategoryId.value;
        
        const res = await axiosClient.get('/jobs', { params });
        jobs.value = res.data?.data ?? [];
    } catch (err) {
        error.value = 'Failed to load jobs';
        jobs.value = [];
    } finally {
        loading.value = false;
    }
};

const loadFavourites = async () => {
    try {
        const { data } = await axiosClient.get('/favourites');
        favouriteJobIds.value = (data?.data ?? []).map((item) => item.job_id);
    } catch {
        favouriteJobIds.value = [];
    }
};

// Fetch categories
const loadCategories = async () => {
    try {
        const res = await axiosClient.get('/categories');
        categories.value = res.data?.data ?? [];
    } catch {
        categories.value = [];
    }
};

// Init
onMounted(() => {
    loadCategories();
    loadJobs();
    loadFavourites();
});

// Reactive loading
watch([search, selectedCategoryId], () => {
    if (searchTimer) {
        clearTimeout(searchTimer);
    }

    searchTimer = setTimeout(() => {
        loadJobs();
    }, 300);
});

const toggleCategory = (id) => {
    selectedCategoryId.value = selectedCategoryId.value === id ? null : id;
};

const formatCurrency = (val) => {
    const num = Number(val);
    return isNaN(num) ? 'N/A' : `$${num.toLocaleString()}`;
};

const formatTime = (date) => {
    if (!date) return 'Recently';
    const d = new Date(date);
    const hours = Math.floor((Date.now() - d) / (1000 * 60 * 60));
    if (hours < 1) return 'Just now';
    if (hours < 24) return `${hours}h ago`;
    return Math.floor(hours / 24) + 'd ago';
};

const getInitials = (name) => {
    return name?.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase() || 'CO';
};

const isFavourite = (jobId) => favouriteJobIds.value.includes(jobId);

const toggleFavourite = async (jobId) => {
    try {
        if (isFavourite(jobId)) {
            await axiosClient.delete(`/favourites/${jobId}`);
            favouriteJobIds.value = favouriteJobIds.value.filter((id) => id !== jobId);
            return;
        }

        await axiosClient.post('/favourites', { job_id: jobId });
        favouriteJobIds.value.push(jobId);
    } catch (err) {
        error.value = err?.response?.data?.message || 'Failed to update favourite.';
    }
};
</script>

<template>
    <HomeLayout>
        <div class="min-h-screen text-slate-900 bg-[#FFFDF5]">
            <!-- Header -->
            <header class="bg-[#FFFDF5] py-12 px-4">
                <div class="max-w-4xl mx-auto text-center mb-8">
                    <h1 class="text-4xl md:text-5xl font-black mb-4">Find Your Next Job</h1>
                    <p class="text-slate-600 mb-8">Search from {{ jobs.length }} available positions</p>
                    
                    <!-- Search Bar -->
                    <div class="max-w-2xl mx-auto">
                        <input 
                            v-model="search"
                            type="text" 
                            placeholder="Search by role, company, or location..."
                            class="w-full px-6 py-4 rounded-full border-2 border-slate-200 focus:border-yellow-500 focus:outline-none shadow-sm"
                        />
                    </div>
                </div>

                <!-- Quick Tags -->
                <div class="max-w-4xl mx-auto text-center">
                    <p class="text-xs font-bold text-slate-400 mb-3 uppercase">Popular Searches</p>
                    <div class="flex flex-wrap justify-center gap-2">
                        <button 
                            v-for="tag in quickTags" 
                            :key="tag"
                            @click="search = tag"
                            class="px-4 py-2 text-xs font-bold rounded-full bg-white border border-slate-200 hover:bg-yellow-50 hover:border-yellow-400 transition"
                        >
                            {{ tag }}
                        </button>
                    </div>
                </div>
            </header>

            <main class="max-w-7xl mx-auto px-4 py-8 grid grid-cols-1 lg:grid-cols-4 gap-8 pb-20">
                
                <!-- Sidebar -->
                <aside class="space-y-6">
                    <div>
                        <h3 class="font-bold text-lg mb-4">Categories</h3>
                        <div class="space-y-3">
                            <button 
                                @click="toggleCategory(null)"
                                :class="!selectedCategoryId ? 'bg-yellow-100 text-slate-900 font-bold' : 'bg-white text-slate-600'"
                                class="w-full text-left px-4 py-2 rounded-lg border border-slate-200 transition text-sm"
                            >
                                All Jobs
                            </button>
                            <label v-for="cat in categories" :key="cat.id" class="flex items-center cursor-pointer p-2 hover:bg-slate-50 rounded">
                                <input 
                                    type="checkbox"
                                    :checked="selectedCategoryId === cat.id"
                                    @change="toggleCategory(cat.id)"
                                    class="w-4 h-4 rounded border-slate-300 text-yellow-500"
                                />
                                <span class="ml-3 text-sm font-medium">{{ cat.name }}</span>
                                <span class="ml-auto text-xs text-slate-400">({{ cat.jobs_count || 0 }})</span>
                            </label>
                        </div>
                    </div>
                </aside>

                <!-- Jobs List -->
                <section class="lg:col-span-3">
                    <div class="mb-6 flex justify-between items-center">
                        <h2 class="text-2xl font-bold">Latest Openings</h2>
                        <p class="text-sm text-slate-500">{{ jobs.length }} results</p>
                    </div>

                    <!-- Loading -->
                    <div v-if="loading" class="space-y-4">
                        <div v-for="i in 3" :key="i" class="h-56 bg-slate-100 rounded-4xl animate-pulse"></div>
                    </div>

                    <!-- Error -->
                    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-700 text-sm">
                        {{ error }}
                    </div>

                    <!-- Empty -->
                    <div v-else-if="jobs.length === 0" class="bg-white border-2 border-dashed border-slate-200 rounded-lg p-12 text-center">
                        <p class="text-slate-500 font-medium">No jobs found. Try adjusting your search.</p>
                    </div>

                    <!-- Jobs -->
                    <div v-else class="space-y-4">
                        <article 
                            v-for="job in jobs" 
                            :key="job.id"
                            class="bg-white border-2 border-slate-100 rounded-4xl p-6 hover:shadow-lg hover:-translate-y-0.5 transition"
                        >
                            <div class="flex justify-between items-start gap-4 mb-4">
                                <div class="flex-1">
                                    <p class="text-xs font-bold text-slate-400 uppercase mb-1">{{ job.company?.name || 'Company' }}</p>
                                    <h3 class="text-xl font-bold text-slate-900 mb-1">{{ job.title }}</h3>
                                    <p class="text-sm text-slate-600">{{ job.location }}</p>
                                </div>
                                <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center text-sm font-bold text-slate-700 shrink-0">
                                    {{ getInitials(job.company?.name) }}
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-4 text-sm text-slate-600 mb-4">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ formatCurrency(job.salary) }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    {{ (job.type || 'Full Time').split('-').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ') }}
                                </span>
                                <span class="flex items-center gap-1 ml-auto text-xs text-slate-400">
                                    {{ formatTime(job.created_at) }}
                                </span>
                            </div>

                            <div class="flex gap-3">
                                <router-link :to="`/jobs/${job.id}`" class="flex-1 px-4 py-2 border-2 border-slate-200 rounded-lg text-sm font-bold text-slate-700 hover:bg-slate-50 transition text-center">
                                    View Details
                                </router-link>
                                <router-link :to="`/jobs/${job.id}/apply`" class="flex-1 px-4 py-2 bg-slate-900 text-white rounded-lg text-sm font-bold hover:bg-black transition text-center">
                                    Apply Now
                                </router-link>
                                <button
                                    class="px-4 py-2 rounded-lg text-xs font-bold border transition"
                                    :class="isFavourite(job.id) ? 'bg-rose-50 text-rose-600 border-rose-200' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'"
                                    @click="toggleFavourite(job.id)"
                                >
                                    {{ isFavourite(job.id) ? 'Saved' : 'Save' }}
                                </button>
                            </div>
                        </article>
                    </div>
                </section>
            </main>
        </div>
    </HomeLayout>
</template>