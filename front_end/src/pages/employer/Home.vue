<script setup>
import HomeLayout from '@/layouts/HomeLayout.vue';
import axiosClient from '@/axios';
import { computed, onMounted, ref } from 'vue';

const quickStats = [
  { label: 'Active Jobs', value: '08' },
  { label: 'Applications', value: '124' },
  { label: 'Interviews', value: '19' },
  { label: 'Hires', value: '06' },
];

const jobs = [
  { title: 'Site Engineer', type: 'Full Time', location: 'Dubai', applicants: 32, status: 'Open' },
  { title: 'Welding Inspector', type: 'Contract', location: 'Abu Dhabi', applicants: 21, status: 'Open' },
  { title: 'Project Coordinator', type: 'Full Time', location: 'Remote', applicants: 14, status: 'Reviewing' },
];

const activities = [
  '12 new applicants joined in the last 24 hours.',
  'Your company profile is visible to candidates.',
  '2 interviews are scheduled for tomorrow.',
];

const company = ref(null);
const companyLoading = ref(true);
const companyError = ref('');

const hasCompany = computed(() => !!company.value);

const companyDetails = computed(() => {
  if (!company.value) {
    return [];
  }

  return [
    { label: 'Industry', value: company.value.industry || 'Not set' },
    { label: 'Location', value: company.value.location || 'Not set' },
    { label: 'Company Size', value: company.value.size || 'Not set' },
    { label: 'Website', value: company.value.website || 'Not set' },
    { label: 'Email', value: company.value.email || 'Not set' },
    { label: 'Phone', value: company.value.phone || 'Not set' },
  ];
});

async function fetchMyCompany() {
  companyLoading.value = true;
  companyError.value = '';

  try {
    const { data } = await axiosClient.get('/companies/me');
    company.value = data?.company ?? null;
  } catch (error) {
    if (error?.response?.status === 404) {
      company.value = null;
      return;
    }

    companyError.value = error?.response?.data?.message || 'Could not load company details.';
  } finally {
    companyLoading.value = false;
  }
}

onMounted(fetchMyCompany);
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
              <span class="text-[10px] font-black uppercase tracking-[0.2em] text-[#8B7322]">Employer Home</span>
            </div>
            <h1 class="text-5xl font-black tracking-tighter text-[#1A1A1A] md:text-6xl">
              Company Dashboard<span class="text-[#FFCD1F]">.</span>
            </h1>
            <p class="max-w-2xl text-lg font-medium text-[#626262]">
              Manage your hiring activity, track candidate flow, and keep your company profile updated.
            </p>
          </div>

          <div class="flex gap-3">
            <router-link
              to="/employer/company"
              class="rounded-2xl border border-[#E9E1C8] bg-white px-5 py-3 text-xs font-black uppercase tracking-[0.12em] text-[#1A1A1A] transition hover:border-[#FFCD1F]"
            >
              Edit Company
            </router-link>
            <router-link
              to="/employer/jobs/create"
              class="rounded-2xl bg-[#111111] px-5 py-3 text-xs font-black uppercase tracking-[0.12em] text-white transition hover:bg-[#FFCD1F] hover:text-black"
            >
              Post a Job
            </router-link>
          </div>
        </header>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
          <article
            v-for="stat in quickStats"
            :key="stat.label"
            class="rounded-3xl border border-[#E9E1C8] bg-white p-6 shadow-sm"
          >
            <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#8B7322]">{{ stat.label }}</p>
            <p class="mt-3 text-4xl font-black text-[#1A1A1A]">{{ stat.value }}</p>
          </article>
        </div>

        <div class="mt-8 grid gap-8 lg:grid-cols-[1.25fr_0.75fr]">
          <section class="rounded-4xl border border-[#E9E1C8] bg-white p-6 shadow-sm md:p-8">
            <div class="mb-5 flex items-center justify-between">
              <h2 class="text-2xl font-black tracking-tight text-[#1A1A1A]">Recent Job Posts</h2>
              <a href="#" class="text-[11px] font-black uppercase tracking-[0.16em] text-[#8B7322]">View all</a>
            </div>

            <div class="space-y-4">
              <article
                v-for="job in jobs"
                :key="job.title"
                class="rounded-2xl border border-[#F0E9D2] bg-[#FFFEF9] p-5"
              >
                <div class="flex flex-wrap items-center justify-between gap-3">
                  <div>
                    <h3 class="text-lg font-black text-[#1A1A1A]">{{ job.title }}</h3>
                    <p class="mt-1 text-sm text-[#666]">{{ job.type }} • {{ job.location }}</p>
                  </div>
                  <span class="rounded-full bg-[#fcf8ed] px-3 py-1 text-[10px] font-black uppercase tracking-[0.14em] text-[#8B7322]">
                    {{ job.status }}
                  </span>
                </div>
                <p class="mt-4 text-sm font-semibold text-[#333]">{{ job.applicants }} applicants</p>
              </article>
            </div>
          </section>

          <aside class="space-y-6">
            <section class="rounded-4xl border border-[#E9E1C8] bg-white p-6 shadow-sm md:p-8">
              <h3 class="text-xl font-black text-[#1A1A1A]">Company Details</h3>

              <p v-if="companyLoading" class="mt-5 rounded-2xl border border-[#F0E9D2] bg-[#FFFEF9] p-4 text-sm font-semibold text-[#444]">
                Loading company profile...
              </p>

              <p v-else-if="companyError" class="mt-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">
                {{ companyError }}
              </p>

              <div v-else-if="hasCompany" class="mt-5 space-y-3">
                <article
                  v-for="detail in companyDetails"
                  :key="detail.label"
                  class="rounded-2xl border border-[#F0E9D2] bg-[#FFFEF9] p-4"
                >
                  <p class="text-[10px] font-black uppercase tracking-[0.16em] text-[#8B7322]">{{ detail.label }}</p>
                  <p class="mt-2 text-sm font-semibold text-[#1A1A1A]">{{ detail.value }}</p>
                </article>
              </div>

              <div v-else class="mt-5 rounded-2xl border border-[#F0E9D2] bg-[#FFFEF9] p-4 text-sm text-[#444]">
                No company profile found yet. Add your company details to show them here.
              </div>
            </section>

            <section class="rounded-4xl border border-[#E9E1C8] bg-white p-6 shadow-sm md:p-8">
              <h3 class="text-xl font-black text-[#1A1A1A]">Hiring Activity</h3>
              <div class="mt-5 space-y-3">
                <article
                  v-for="item in activities"
                  :key="item"
                  class="rounded-2xl border border-[#F0E9D2] bg-[#FFFEF9] p-4 text-sm font-semibold text-[#444]"
                >
                  {{ item }}
                </article>
              </div>
            </section>

            <section class="rounded-4xl border border-[#E9E1C8] bg-[#111111] p-6 text-white shadow-2xl md:p-8">
              <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#FFCD1F]">Company Profile</p>
              <h3 class="mt-3 text-2xl font-black">Keep it updated</h3>
              <p class="mt-3 text-sm text-white/75">
                Candidates are more likely to apply when your profile is complete with logo, story, and contact details.
              </p>
              <router-link
                to="/employer/company"
                class="mt-6 inline-flex rounded-2xl bg-[#FFCD1F] px-4 py-3 text-xs font-black uppercase tracking-[0.12em] text-black"
              >
                Manage Profile
              </router-link>
            </section>
          </aside>
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
