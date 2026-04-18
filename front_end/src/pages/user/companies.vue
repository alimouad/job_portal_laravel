<script setup>
import { computed, onMounted, ref } from 'vue';
import HomeLayout from '@/layouts/HomeLayout.vue';
import axiosClient from '@/axios';

const companies = ref([]);
const loading = ref(false);
const errorMessage = ref('');
const search = ref('');
const currentPage = ref(1);
const lastPage = ref(1);
const total = ref(0);

const loadCompanies = async (page = 1) => {
  loading.value = true;
  errorMessage.value = '';

  try {
    const response = await axiosClient.get('/companies', {
      params: { page },
    });

    companies.value = response.data?.data ?? [];
    currentPage.value = response.data?.current_page ?? page;
    lastPage.value = response.data?.last_page ?? 1;
    total.value = response.data?.total ?? companies.value.length;
  } catch (error) {
    errorMessage.value = error?.response?.data?.message || 'Failed to load companies.';
    companies.value = [];
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadCompanies();
});

const filteredCompanies = computed(() => {
  const query = search.value.trim().toLowerCase();

  if (!query) {
    return companies.value;
  }

  return companies.value.filter((company) => {
    return [company.name, company.industry, company.location, company.size, company.website]
      .filter(Boolean)
      .some((field) => String(field).toLowerCase().includes(query));
  });
});

const getInitials = (name = '') => {
  return name
    .split(' ')
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part[0]?.toUpperCase())
    .join('') || 'C';
};

const goToPage = (page) => {
  if (page < 1 || page > lastPage.value || page === currentPage.value) {
    return;
  }

  loadCompanies(page);
};

const summaryText = computed(() => {
  const shown = filteredCompanies.value.length;
  const filtered = search.value.trim() ? ` filtered by “${search.value.trim()}”` : '';
  return `${shown} of ${total.value} companies${filtered}`;
});
</script>

<template>
  <HomeLayout>
    <section class="relative overflow-hidden bg-[#FFFDF5] pb-20 pt-16">
      <div class="pointer-events-none absolute inset-0 -z-10">
        <div class="absolute -left-20 top-0 h-96 w-96 rounded-full bg-[#ffcd1f]/20 blur-[120px]"></div>
        <div class="absolute right-0 top-20 h-80 w-80 rounded-full bg-white/60 blur-[120px]"></div>
      </div>

      <div class="mx-auto max-w-6xl px-4 md:px-8">
        <div class="mb-10 text-center">
          <div class="mx-auto mb-6 inline-flex items-center gap-2 rounded-full border border-[#e9e1c8] bg-white px-4 py-1 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-[#ffcd1f]"></span>
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-[#8b7322]">Company Directory</span>
          </div>

          <h1 class="text-4xl font-black tracking-tight md:text-6xl">
            Browse all companies
          </h1>
          <p class="mx-auto mt-4 max-w-2xl text-sm text-[#626262] md:text-lg">
            Explore companies, industries, and open opportunities from the job platform.
          </p>
        </div>

        <div class="mx-auto mb-12 max-w-2xl rounded-full border border-[#e4dcc4] bg-white p-2 shadow-[0_25px_50px_-12px_rgba(228,220,196,0.6)]">
          <div class="flex items-center gap-3 px-4">
            <i class="pi pi-search text-[#8b7322]"></i>
            <input
              v-model="search"
              type="text"
              placeholder="Search by company, industry, location..."
              class="w-full bg-transparent py-3 outline-none text-sm placeholder:text-[#999]"
            />
          </div>
        </div>

        <div class="mb-8 flex flex-wrap items-center justify-between gap-3">
          <p class="text-sm font-semibold text-[#6b6b6b]">{{ summaryText }}</p>
          <button
            class="rounded-full border border-[#e9e1c8] bg-white px-4 py-2 text-xs font-black uppercase tracking-widest text-[#8b7322] hover:bg-[#ffcd1f] hover:text-black transition"
            @click="loadCompanies(currentPage)"
          >
            Refresh
          </button>
        </div>

        <div v-if="loading" class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
          <div v-for="i in 6" :key="i" class="animate-pulse rounded-[28px] border border-[#e9e1c8] bg-white p-6 shadow-sm">
            <div class="h-14 w-14 rounded-2xl bg-[#f3ead0]"></div>
            <div class="mt-4 h-4 w-32 rounded bg-[#f1e7c8]"></div>
            <div class="mt-3 h-3 w-24 rounded bg-[#f1e7c8]"></div>
            <div class="mt-6 space-y-2">
              <div class="h-3 w-full rounded bg-[#f6edd5]"></div>
              <div class="h-3 w-5/6 rounded bg-[#f6edd5]"></div>
            </div>
          </div>
        </div>

        <div v-else-if="errorMessage" class="rounded-3xl border border-red-200 bg-red-50 px-6 py-5 text-sm font-semibold text-red-700">
          {{ errorMessage }}
        </div>

        <div v-else class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
          <article
            v-for="company in filteredCompanies"
            :key="company.id"
            class="rounded-[28px] border border-[#e9e1c8] bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-xl"
          >
            <div class="flex items-start gap-4">
              <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-[#ffcd1f] text-lg font-black text-black shadow-sm overflow-hidden">
                <img
                  v-if="company.logo"
                  :src="company.logo"
                  :alt="company.name"
                  class="h-full w-full object-cover"
                />
                <span v-else>{{ getInitials(company.name) }}</span>
              </div>

              <div class="min-w-0 flex-1">
                <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#8b7322]">
                  {{ company.industry || 'Industry not set' }}
                </p>
                <h3 class="mt-1 truncate text-xl font-black text-[#1a1a1a]">{{ company.name }}</h3>
                <p class="mt-1 text-sm font-medium text-[#666]">
                  {{ company.location || 'Location not set' }}
                </p>
              </div>
            </div>

            <p class="mt-5 line-clamp-3 text-sm leading-6 text-[#626262]">
              {{ company.description || 'No company description available yet.' }}
            </p>

            <div class="mt-5 flex flex-wrap gap-2 text-[11px] font-bold uppercase tracking-widest text-[#6b6b6b]">
              <span class="rounded-full bg-[#fcf8ed] px-3 py-1">{{ company.size || 'Size N/A' }}</span>
              <span class="rounded-full bg-[#fcf8ed] px-3 py-1">{{ company.jobs_count ?? 0 }} jobs</span>
            </div>

            <div class="mt-6 space-y-2 border-t border-[#f0ecd9] pt-5 text-sm text-[#4a4a4a]">
              <div class="flex items-center justify-between gap-3">
                <span class="font-semibold text-[#7a7a7a]">Email</span>
                <span class="truncate font-bold">{{ company.email || '—' }}</span>
              </div>
              <div class="flex items-center justify-between gap-3">
                <span class="font-semibold text-[#7a7a7a]">Phone</span>
                <span class="truncate font-bold">{{ company.phone || '—' }}</span>
              </div>
            </div>

            <div class="mt-6 flex items-center justify-between gap-3">
              <a
                v-if="company.website"
                :href="company.website"
                target="_blank"
                rel="noreferrer"
                class="rounded-full border border-[#ded9c9] px-4 py-2 text-xs font-bold text-[#1a1a1a] transition hover:border-[#ffcd1f] hover:bg-[#ffcd1f]"
              >
                Visit Website
              </a>
              <button class="rounded-full bg-[#111111] px-4 py-2 text-xs font-black uppercase tracking-widest text-white transition hover:bg-[#ffcd1f] hover:text-black">
                View Company
              </button>
            </div>
          </article>
        </div>

        <div v-if="!loading && !errorMessage && filteredCompanies.length === 0" class="rounded-3xl border border-[#e9e1c8] bg-white px-6 py-10 text-center shadow-sm">
          <p class="text-lg font-black text-[#1a1a1a]">No companies found</p>
          <p class="mt-2 text-sm text-[#666]">Try a different search term or clear the filter.</p>
        </div>

        <div class="mt-10 flex flex-wrap items-center justify-center gap-3" v-if="lastPage > 1">
          <button
            class="rounded-full border border-[#e9e1c8] bg-white px-4 py-2 text-xs font-bold disabled:cursor-not-allowed disabled:opacity-40"
            :disabled="currentPage === 1"
            @click="goToPage(currentPage - 1)"
          >
            Previous
          </button>

          <button
            v-for="page in lastPage"
            :key="page"
            class="h-10 min-w-10 rounded-full px-4 text-xs font-black transition"
            :class="page === currentPage ? 'bg-[#111111] text-white' : 'border border-[#e9e1c8] bg-white text-[#1a1a1a] hover:bg-[#ffcd1f]'"
            @click="goToPage(page)"
          >
            {{ page }}
          </button>

          <button
            class="rounded-full border border-[#e9e1c8] bg-white px-4 py-2 text-xs font-bold disabled:cursor-not-allowed disabled:opacity-40"
            :disabled="currentPage === lastPage"
            @click="goToPage(currentPage + 1)"
          >
            Next
          </button>
        </div>
      </div>
    </section>
  </HomeLayout>
</template>
