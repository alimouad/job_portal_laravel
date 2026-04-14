<script setup>
import { onMounted, ref } from 'vue';
import HomeLayout from '@/layouts/HomeLayout.vue';
import axiosClient from '@/axios';

const applications = ref([]);
const loading = ref(false);
const error = ref('');

const statusStyles = {
  pending: 'bg-amber-100 text-amber-700',
  accepted: 'bg-green-100 text-green-700',
  rejected: 'bg-red-100 text-red-700',
  interview: 'bg-blue-100 text-blue-700',
};

const loadApplications = async () => {
  loading.value = true;
  error.value = '';

  try {
    const { data } = await axiosClient.get('/my-applications');
    applications.value = data?.data ?? [];
  } catch (err) {
    error.value = err?.response?.data?.message || 'Failed to load candidatures.';
    applications.value = [];
  } finally {
    loading.value = false;
  }
};

const formatCurrency = (val) => {
  const num = Number(val);
  return Number.isNaN(num) ? 'N/A' : `$${num.toLocaleString()}`;
};

const getStatusClass = (status) => {
  return statusStyles[status] || 'bg-slate-100 text-slate-700';
};

const formatAppliedAt = (date) => {
  if (!date) return 'recently';

  const created = new Date(date);
  const diffHours = Math.floor((Date.now() - created.getTime()) / (1000 * 60 * 60));

  if (diffHours < 1) return 'just now';
  if (diffHours < 24) return `${diffHours}h ago`;

  const diffDays = Math.floor(diffHours / 24);
  return `${diffDays}d ago`;
};

onMounted(loadApplications);
</script>

<template>
  <HomeLayout>
    <section class="min-h-screen bg-[#FFFDF5] relative overflow-hidden pb-20">
      <div class="pointer-events-none absolute inset-0 -z-10">
        <div class="absolute -right-20 top-0 h-96 w-96 rounded-full bg-blue-50/40 blur-[100px]"></div>
        <div class="absolute left-10 bottom-20 h-80 w-80 rounded-full bg-yellow-100/20 blur-[100px]"></div>
      </div>

      <div class="mx-auto max-w-6xl px-4 py-12 md:px-8">
        <header class="mb-12 flex flex-col gap-6 md:flex-row md:items-end md:justify-between border-b border-slate-100 pb-10">
          <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-slate-100 shadow-sm mb-3">
              <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
              </span>
              <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Application Tracker</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-black tracking-tighter text-slate-900">My Journey<span class="text-yellow-500">.</span></h1>
            <p class="mt-2 text-slate-500 font-medium">Tracking <span class="text-slate-900 font-bold">{{ applications.length }} active candidatures</span> across your network.</p>
          </div>
          
          <button 
            @click="loadApplications" 
            class="group flex items-center gap-2 rounded-2xl border-2 border-slate-100 bg-white px-6 py-3 text-xs font-black uppercase tracking-widest text-slate-600 transition-all hover:border-yellow-400 hover:text-yellow-700 active:scale-95 shadow-sm"
          >
            <svg class="w-4 h-4 transition-transform group-hover:rotate-180 duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Refresh Status
          </button>
        </header>

        <div v-if="loading" class="space-y-6">
          <div v-for="i in 3" :key="i" class="h-56 animate-pulse rounded-[2.5rem] border-2 border-slate-50 bg-white/50"></div>
        </div>

        <div v-else-if="error" class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">
          {{ error }}
        </div>

        <div v-else-if="applications.length === 0" class="rounded-[3rem] border-2 border-dashed border-slate-200 bg-white/50 p-20 text-center">
          <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-slate-50 text-slate-300">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <h3 class="text-xl font-black text-slate-900">No applications yet</h3>
          <p class="mt-2 text-slate-500 font-medium">Your next big move is just a click away.</p>
          <router-link to="/jobs" class="mt-8 inline-block rounded-2xl bg-slate-900 px-8 py-4 text-xs font-black uppercase tracking-widest text-white hover:bg-yellow-500 hover:text-slate-900 transition-all shadow-lg">Find Opportunities</router-link>
        </div>

        <div v-else class="space-y-6">
          <article 
            v-for="application in applications" 
            :key="application.id" 
            class="group relative rounded-[2.5rem] border-2 border-transparent bg-white p-8 transition-all hover:border-yellow-100 hover:shadow-[0_20px_50px_-20px_rgba(0,0,0,0.06)] shadow-sm"
          >
            <div class="flex flex-col md:flex-row items-start justify-between gap-6 mb-8">
              <div class="flex gap-6">
                <div class="h-16 w-16 shrink-0 rounded-[22px] bg-[#FFFDF5] border border-slate-100 flex items-center justify-center font-black text-xl text-slate-700 shadow-inner group-hover:bg-yellow-50 transition-colors">
                  {{ application.job?.company?.name?.substring(0, 1) }}
                </div>
                
                <div>
                  <p class="text-[11px] font-black uppercase tracking-[0.2em] text-blue-600 mb-1">
                    {{ application.job?.company?.name }}
                  </p>
                  <h2 class="text-2xl font-black tracking-tight text-slate-900 group-hover:text-yellow-700 transition-colors">
                    {{ application.job?.title }}
                  </h2>
                  <div class="mt-2 flex items-center gap-4 text-sm font-bold text-slate-400">
                    <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg> {{ application.job?.location }}</span>
                    <span class="h-1 w-1 rounded-full bg-slate-200"></span>
                    <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Applied {{ formatAppliedAt(application.created_at) }}</span>
                  </div>
                </div>
              </div>

              <div class="flex flex-col items-end gap-2">
                <span
                  class="rounded-xl px-4 py-2 text-[10px] font-black uppercase tracking-[0.15em] shadow-sm border"
                  :class="getStatusClass(application.status)"
                >
                  {{ application.status }}
                </span>
                <router-link :to="`/jobs/${application.job_id}/apply`" class="text-[10px] font-black text-slate-300 hover:text-yellow-700 uppercase tracking-widest transition-colors">View Job</router-link>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-[1fr_auto] items-center gap-6 pt-8 border-t border-slate-50">
              <div class="space-y-3">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-300">Message to Recruiter</p>
                <p v-if="application.cover_letter" class="text-sm font-medium text-slate-500 leading-relaxed italic line-clamp-2">
                  "{{ application.cover_letter }}"
                </p>
                <p v-else class="text-sm font-medium text-slate-300 italic">No additional note provided.</p>
              </div>

              <div class="flex flex-wrap items-center gap-3">
                <div class="px-4 py-2 rounded-xl bg-slate-50 text-[11px] font-black text-slate-600 border border-slate-100">
                  {{ formatCurrency(application.job?.salary) }}
                </div>
                <div class="px-4 py-2 rounded-xl bg-slate-50 text-[11px] font-black text-slate-600 border border-slate-100 uppercase">
                  {{ application.job?.type }}
                </div>
              </div>
            </div>
          </article>
        </div>
      </div>
    </section>
  </HomeLayout>
</template>

