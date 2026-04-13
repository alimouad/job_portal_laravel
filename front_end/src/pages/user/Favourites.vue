<script setup>
import { onMounted, ref } from 'vue';
import HomeLayout from '@/layouts/HomeLayout.vue';
import axiosClient from '@/axios';

const favourites = ref([]);
const loading = ref(false);
const error = ref('');

const loadFavourites = async () => {
  loading.value = true;
  error.value = '';

  try {
    const { data } = await axiosClient.get('/favourites');
    favourites.value = data?.data ?? [];
  } catch (err) {
    error.value = err?.response?.data?.message || 'Failed to load favourites.';
    favourites.value = [];
  } finally {
    loading.value = false;
  }
};

const removeFavourite = async (jobId) => {
  try {
    await axiosClient.delete(`/favourites/${jobId}`);
    favourites.value = favourites.value.filter((item) => item.job_id !== jobId);
  } catch (err) {
    error.value = err?.response?.data?.message || 'Failed to remove favourite.';
  }
};

const formatCurrency = (val) => {
  const num = Number(val);
  return Number.isNaN(num) ? 'N/A' : `$${num.toLocaleString()}`;
};

onMounted(loadFavourites);
</script>

<template>
  <HomeLayout>
    <section class="min-h-screen bg-[#FFFDF5] relative overflow-hidden pb-20">
      <div class="pointer-events-none absolute inset-0 -z-10">
        <div class="absolute -left-20 top-0 h-80 w-80 rounded-full bg-yellow-200/20 blur-[100px]"></div>
        <div class="absolute right-0 bottom-20 h-80 w-80 rounded-full bg-orange-100/30 blur-[100px]"></div>
      </div>

      <div class="mx-auto max-w-5xl px-4 py-12 md:px-8">
        <header class="mb-12 flex flex-col gap-4 md:flex-row md:items-end md:justify-between border-b border-slate-100 pb-10">
          <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-slate-100 shadow-sm mb-3">
              <svg class="w-3 h-3 text-red-500 fill-current" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
              <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Personal Collection</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-black tracking-tighter text-slate-900">Saved Jobs<span class="text-yellow-500">.</span></h1>
            <p class="mt-2 text-slate-500 font-medium">You have <span class="text-slate-900 font-bold">{{ favourites.length }} positions</span> bookmarked for review.</p>
          </div>
          
          <button 
            @click="loadFavourites" 
            class="group flex items-center gap-2 rounded-2xl border-2 border-slate-100 bg-white px-6 py-3 text-xs font-black uppercase tracking-widest text-slate-600 transition-all hover:border-yellow-400 hover:text-yellow-600 active:scale-95 shadow-sm"
          >
            <svg class="w-4 h-4 transition-transform group-hover:rotate-180 duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Sync List
          </button>
        </header>

        <div v-if="loading" class="space-y-6">
          <div v-for="i in 3" :key="i" class="h-48 animate-pulse rounded-[2.5rem] border-2 border-slate-50 bg-white/50"></div>
        </div>

        <div v-else-if="error" class="rounded-3xl border-2 border-red-100 bg-white p-8 text-center shadow-sm">
          <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-red-500">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <p class="font-black text-slate-900">Oops! Something went wrong</p>
          <p class="text-sm text-slate-500 mt-1">{{ error }}</p>
        </div>

        <div v-else-if="favourites.length === 0" class="rounded-[3rem] border-2 border-dashed border-slate-200 bg-white/50 p-20 text-center">
          <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-slate-50 text-slate-300">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
          </div>
          <h3 class="text-xl font-black text-slate-900">No Favourites Yet</h3>
          <p class="mt-2 text-slate-500 font-medium">Start exploring and save jobs that catch your eye.</p>
          <router-link to="/jobs" class="mt-8 inline-block rounded-2xl bg-slate-900 px-8 py-4 text-xs font-black uppercase tracking-widest text-white hover:bg-yellow-500 hover:text-slate-900 transition-all shadow-lg">Browse Jobs</router-link>
        </div>

        <div v-else class="space-y-6">
          <article 
            v-for="favourite in favourites" 
            :key="favourite.id" 
            class="group relative rounded-[2.5rem] border-2 border-transparent bg-white p-8 transition-all hover:border-yellow-100 hover:shadow-[0_20px_50px_-20px_rgba(0,0,0,0.06)] shadow-sm"
          >
            <div class="flex flex-col md:flex-row items-start justify-between gap-6">
              <div class="flex gap-6">
                <div class="h-16 w-16 shrink-0 rounded-[22px] bg-[#FFFDF5] border border-slate-100 flex items-center justify-center font-black text-xl text-slate-700 shadow-inner group-hover:bg-yellow-50 transition-colors">
                  {{ favourite.job?.company?.name?.substring(0, 1) || 'C' }}
                </div>
                
                <div>
                  <p class="text-[11px] font-black uppercase tracking-[0.2em] text-yellow-600 mb-1">
                    {{ favourite.job?.company?.name || 'Unknown Company' }}
                  </p>
                  <h2 class="text-2xl font-black tracking-tight text-slate-900 group-hover:text-yellow-600 transition-colors">
                    {{ favourite.job?.title }}
                  </h2>
                  <div class="mt-2 flex items-center gap-2 text-sm font-medium text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                    {{ favourite.job?.location }}
                  </div>
                </div>
              </div>

              <div class="flex w-full md:w-auto gap-3">
                <router-link 
                  :to="`/jobs/${favourite.job_id}/apply`" 
                  class="flex-1 md:flex-none text-center rounded-2xl bg-slate-900 px-8 py-4 text-xs font-black uppercase tracking-widest text-white hover:bg-black transition-all shadow-lg active:scale-95"
                >
                  Apply Now
                </router-link>
                <button 
                  @click="removeFavourite(favourite.job_id)" 
                  class="group/btn flex h-12 w-12 items-center justify-center rounded-2xl border-2 border-slate-50 bg-slate-50 text-slate-400 transition-all hover:border-red-100 hover:bg-red-50 hover:text-red-500"
                >
                  <svg class="w-5 h-5 fill-current transition-transform group-hover/btn:scale-110" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                </button>
              </div>
            </div>

            <div class="mt-8 flex flex-wrap items-center gap-6 pt-6 border-t border-slate-50">
              <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 text-[11px] font-black text-slate-600">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ formatCurrency(favourite.job?.salary) }}
              </div>
              <div class="flex items-center gap-2 text-[11px] font-bold text-slate-400">
                <span class="h-1.5 w-1.5 rounded-full bg-slate-200"></span>
                {{ favourite.job?.type }}
              </div>
              <div class="flex items-center gap-2 text-[11px] font-bold text-slate-400">
                <span class="h-1.5 w-1.5 rounded-full bg-slate-200"></span>
                {{ favourite.job?.category?.name || 'General' }}
              </div>
              <div class="ml-auto text-[10px] font-bold text-slate-300 italic uppercase tracking-widest">
                Added {{ favourite.created_at_human || 'recently' }}
              </div>
            </div>
          </article>
        </div>
      </div>
    </section>
  </HomeLayout>
</template>