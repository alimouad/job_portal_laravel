<script setup>
import { computed, onMounted, ref } from 'vue';
import HomeLayout from '@/layouts/HomeLayout.vue';
import axiosClient from '@/axios';

const applications = ref([]);
const loading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');
const currentPage = ref(1);
const lastPage = ref(1);
const search = ref('');
const selectedStatus = ref('');

const statusOptions = [
  { value: '', label: 'All Statuses' },
  { value: 'pending', label: 'Pending' },
  { value: 'interview', label: 'Interview' },
  { value: 'accepted', label: 'Accepted' },
  { value: 'rejected', label: 'Rejected' },
];

const statusClass = {
  pending: 'bg-amber-100 text-amber-700',
  interview: 'bg-blue-100 text-blue-700',
  accepted: 'bg-green-100 text-green-700',
  rejected: 'bg-red-100 text-red-700',
};

const isEmpty = computed(() => !loading.value && !errorMessage.value && applications.value.length === 0);

const formatStatus = (status) => {
  if (!status) {
    return 'Unknown';
  }

  return status.charAt(0).toUpperCase() + status.slice(1);
};

const formatAppliedAt = (dateString) => {
  if (!dateString) {
    return 'Recently';
  }

  const created = new Date(dateString);
  const diffHours = Math.floor((Date.now() - created.getTime()) / (1000 * 60 * 60));

  if (diffHours < 1) return 'Just now';
  if (diffHours < 24) return `${diffHours}h ago`;

  const diffDays = Math.floor(diffHours / 24);
  return `${diffDays}d ago`;
};

const buildParams = (page = 1) => {
  const params = { page };

  if (search.value.trim()) {
    params.q = search.value.trim();
  }

  if (selectedStatus.value) {
    params.status = selectedStatus.value;
  }

  return params;
};

const loadApplications = async (page = 1) => {
  loading.value = true;
  errorMessage.value = '';

  try {
    const { data } = await axiosClient.get('/employer/applications', {
      params: buildParams(page),
    });

    applications.value = data?.data ?? [];
    currentPage.value = data?.current_page ?? 1;
    lastPage.value = data?.last_page ?? 1;
  } catch (error) {
    applications.value = [];
    errorMessage.value = error?.response?.data?.message || 'Failed to load applications.';
  } finally {
    loading.value = false;
  }
};

const updateStatus = async (application, status) => {
  if (!application?.id || !status || status === application.status) {
    return;
  }

  try {
    const { data } = await axiosClient.put(`/employer/applications/${application.id}`, { status });

    application.status = data?.application?.status ?? status;
    successMessage.value = `Application #${application.id} moved to ${formatStatus(application.status)}.`;
  } catch (error) {
    errorMessage.value = error?.response?.data?.message || 'Failed to update application status.';
  }
};

const applyFilters = () => {
  loadApplications(1);
};

const goToPage = (page) => {
  if (page < 1 || page > lastPage.value || page === currentPage.value) {
    return;
  }

  loadApplications(page);
};

onMounted(() => {
  loadApplications(1);
});
</script>

<template>
  <HomeLayout>
    <section class="relative min-h-screen overflow-hidden bg-[#F9F5E8] pb-20">
      <div class="pointer-events-none absolute inset-0 -z-10">
        <div class="absolute -left-24 top-0 h-150 w-150 rounded-full bg-[#FFCD1F]/10 blur-[120px]"></div>
        <div class="absolute right-0 top-32 h-125 w-125 rounded-full bg-[#E8D9A8]/30 blur-[100px]"></div>
      </div>

      <div class="relative mx-auto max-w-7xl px-4 py-10 md:px-8">
        <header class="mb-10 flex flex-col gap-6 border-b border-[#E9E1C8] pb-8 md:flex-row md:items-end md:justify-between">
          <div class="space-y-4">
            <div class="inline-flex items-center gap-2 rounded-full border border-[#E9E1C8] bg-white px-3 py-1 shadow-sm">
              <span class="h-2 w-2 animate-pulse rounded-full bg-[#FFCD1F]"></span>
              <span class="text-[10px] font-black uppercase tracking-[0.2em] text-[#8B7322]">Employer Hub</span>
            </div>
            <h1 class="text-5xl font-black tracking-tighter text-[#1A1A1A] md:text-6xl">
              Manage Applications<span class="text-[#FFCD1F]">.</span>
            </h1>
            <p class="max-w-2xl text-lg font-medium text-[#626262]">
              Review candidates across your posted jobs and move them through your hiring pipeline.
            </p>
          </div>

          <button
            class="rounded-2xl border border-[#E9E1C8] bg-white px-5 py-3 text-xs font-black uppercase tracking-[0.12em] text-[#1A1A1A] transition hover:border-[#FFCD1F]"
            @click="loadApplications(currentPage)"
          >
            Refresh
          </button>
        </header>

        <section class="mb-8 rounded-3xl border border-[#E9E1C8] bg-white p-5 shadow-sm">
          <div class="grid gap-4 md:grid-cols-[1fr_200px_auto] md:items-end">
            <div>
              <label class="ml-1 block text-[11px] font-black uppercase tracking-[0.2em] text-[#7B7B7B]">Search Candidate / Job</label>
              <input
                v-model="search"
                type="text"
                placeholder="e.g. Ahmed, Frontend Developer"
                class="mt-2 w-full rounded-2xl border-2 border-[#F2EDDB] bg-[#FFFDF6] px-5 py-3 text-sm font-bold text-[#1F1F1F] outline-none transition-all focus:border-[#FFCD1F] focus:bg-white"
              />
            </div>

            <div>
              <label class="ml-1 block text-[11px] font-black uppercase tracking-[0.2em] text-[#7B7B7B]">Status</label>
              <select
                v-model="selectedStatus"
                class="mt-2 w-full rounded-2xl border-2 border-[#F2EDDB] bg-[#FFFDF6] px-5 py-3 text-sm font-bold text-[#1F1F1F] outline-none transition-all focus:border-[#FFCD1F] focus:bg-white"
              >
                <option v-for="option in statusOptions" :key="option.value || 'all'" :value="option.value">{{ option.label }}</option>
              </select>
            </div>

            <button
              class="rounded-2xl bg-[#111111] px-5 py-3 text-xs font-black uppercase tracking-[0.12em] text-white transition hover:bg-[#FFCD1F] hover:text-black"
              @click="applyFilters"
            >
              Apply Filters
            </button>
          </div>
        </section>

        <p v-if="successMessage" class="mb-5 rounded-2xl border border-green-200 bg-green-50 p-4 text-sm font-semibold text-green-700">
          {{ successMessage }}
        </p>

        <p v-if="errorMessage" class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">
          {{ errorMessage }}
        </p>

        <div v-if="loading" class="space-y-4">
          <article v-for="i in 4" :key="`loading-${i}`" class="h-34 animate-pulse rounded-3xl border border-[#E9E1C8] bg-white"></article>
        </div>

        <div v-else-if="isEmpty" class="rounded-3xl border border-[#E9E1C8] bg-white p-10 text-center shadow-sm">
          <h2 class="text-2xl font-black text-[#1A1A1A]">No applications found</h2>
          <p class="mt-2 text-sm font-medium text-[#666]">Try changing your filters or post more jobs to receive candidates.</p>
        </div>

        <div v-else class="space-y-4">
          <article
            v-for="application in applications"
            :key="application.id"
            class="rounded-3xl border border-[#E9E1C8] bg-white p-6 shadow-sm"
          >
            <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
              <div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#8B7322]">
                  {{ application.job?.company?.name || 'Company' }}
                </p>
                <h3 class="mt-1 text-2xl font-black text-[#1A1A1A]">
                  {{ application.user?.full_name || 'Candidate' }}
                </h3>
                <p class="mt-1 text-sm font-semibold text-[#666]">{{ application.user?.email || 'No email' }}</p>
                <p class="mt-2 text-sm text-[#444]">
                  Applied for <span class="font-black">{{ application.job?.title || 'Job title' }}</span>
                  • {{ application.job?.location || 'Location not set' }}
                </p>
              </div>

              <div class="flex flex-col gap-3 md:items-end">
                <span
                  class="w-fit rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-[0.14em]"
                  :class="statusClass[application.status] || 'bg-slate-100 text-slate-700'"
                >
                  {{ formatStatus(application.status) }}
                </span>

                <select
                  class="rounded-xl border border-[#E9E1C8] bg-white px-3 py-2 text-xs font-black uppercase tracking-[0.08em] text-[#1A1A1A] outline-none"
                  :value="application.status"
                  @change="updateStatus(application, $event.target.value)"
                >
                  <option value="pending">Pending</option>
                  <option value="interview">Interview</option>
                  <option value="accepted">Accepted</option>
                  <option value="rejected">Rejected</option>
                </select>
              </div>
            </div>

            <div class="mt-5 grid gap-3 border-t border-[#F0E9D2] pt-4 text-sm text-[#555] md:grid-cols-3">
              <p><span class="font-black text-[#1A1A1A]">Applied:</span> {{ formatAppliedAt(application.created_at) }}</p>
              <p><span class="font-black text-[#1A1A1A]">Salary:</span> ${{ Number(application.job?.salary || 0).toLocaleString() }}</p>
              <p><span class="font-black text-[#1A1A1A]">Type:</span> {{ application.job?.type || 'N/A' }}</p>
            </div>

            <p v-if="application.cover_letter" class="mt-4 rounded-2xl bg-[#FFFEF9] p-4 text-sm font-medium text-[#4A4A4A]">
              "{{ application.cover_letter }}"
            </p>
          </article>

          <nav v-if="lastPage > 1" class="mt-8 flex items-center justify-center gap-2">
            <button
              class="h-10 rounded-xl border border-[#E9E1C8] bg-white px-3 text-xs font-black text-[#666] disabled:opacity-40"
              :disabled="currentPage === 1"
              @click="goToPage(currentPage - 1)"
            >
              Prev
            </button>

            <button
              v-for="page in lastPage"
              :key="page"
              class="h-10 min-w-10 rounded-xl border text-xs font-black"
              :class="page === currentPage ? 'border-[#111] bg-[#111] text-white' : 'border-[#E9E1C8] bg-white text-[#666]'"
              @click="goToPage(page)"
            >
              {{ page }}
            </button>

            <button
              class="h-10 rounded-xl border border-[#E9E1C8] bg-white px-3 text-xs font-black text-[#666] disabled:opacity-40"
              :disabled="currentPage === lastPage"
              @click="goToPage(currentPage + 1)"
            >
              Next
            </button>
          </nav>
        </div>
      </div>
    </section>
  </HomeLayout>
</template>

<style scoped>
section {
  font-family: 'Plus Jakarta Sans', sans-serif;
}
</style>
