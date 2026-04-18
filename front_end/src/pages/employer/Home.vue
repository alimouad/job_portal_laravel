<script setup>
import HomeLayout from '@/layouts/HomeLayout.vue';
import axiosClient from '@/axios';
import { computed, onMounted, ref } from 'vue';

const company = ref(null);
const jobs = ref([]);
const dashboardLoading = ref(true);
const dashboardError = ref('');

const fetchPaginated = async (url, params = {}) => {
  let page = 1;
  let lastPage = 1;
  const rows = [];

  do {
    const { data } = await axiosClient.get(url, {
      params: { ...params, page },
    });

    const chunk = Array.isArray(data) ? data : Array.isArray(data?.data) ? data.data : [];

    if (chunk.length === 0) {
      break;
    }

    rows.push(...chunk);
    lastPage = Number(data?.last_page ?? 1);
    page += 1;
  } while (page <= lastPage);

  return rows;
};

const quickStats = computed(() => {
  const activeJobs = jobs.value.length;
  const categoriesCount = new Set(jobs.value.map((job) => job.category?.name).filter(Boolean)).size;
  const locationsCount = new Set(jobs.value.map((job) => job.location).filter(Boolean)).size;

  const salaryNumbers = jobs.value
    .map((job) => Number(job.salary))
    .filter((salary) => Number.isFinite(salary) && salary > 0);

  const averageSalary =
    salaryNumbers.length > 0
      ? Math.round(salaryNumbers.reduce((sum, salary) => sum + salary, 0) / salaryNumbers.length)
      : 0;

  return [
    { label: 'Active Jobs', value: String(activeJobs).padStart(2, '0') },
    { label: 'Categories', value: String(categoriesCount).padStart(2, '0') },
    { label: 'Locations', value: String(locationsCount).padStart(2, '0') },
    { label: 'Avg Salary', value: averageSalary ? `$${averageSalary.toLocaleString()}` : '$0' },
  ];
});

const recentJobs = computed(() => {
  return [...jobs.value]
    .sort((a, b) => new Date(b.created_at || 0).getTime() - new Date(a.created_at || 0).getTime())
    .slice(0, 4);
});

const activities = computed(() => {
  if (!company.value) {
    return ['Your employer dashboard will unlock after creating your company profile.'];
  }

  const totalJobs = jobs.value.length;
  const categoriesCount = new Set(jobs.value.map((job) => job.category?.name).filter(Boolean)).size;

  const latestJob = recentJobs.value[0];

  const latestJobText = latestJob
    ? `Latest post: ${latestJob.title} (${latestJob.type || 'type not specified'}).`
    : 'No jobs posted yet. Publish your first role today.';

  return [
    `${company.value?.name || 'Your company'} currently has ${totalJobs} active job post${totalJobs === 1 ? '' : 's'}.`,
    `You are hiring across ${categoriesCount} categor${categoriesCount === 1 ? 'y' : 'ies'}.`,
    latestJobText,
  ];
});

const companyDetails = computed(() => {
  if (!company.value) {
    return [];
  }

  return [
    { label: 'Industry', value: company.value.industry || 'Not set' },
    { label: 'Location', value: company.value.location || 'Not set' },
    { label: 'Company Size', value: company.value.size || company.value.companySize || 'Not set' },
    { label: 'Website', value: company.value.website || 'Not set' },
    { label: 'Email', value: company.value.email || 'Not set' },
    { label: 'Phone', value: company.value.phone || 'Not set' },
  ];
});

const formatJobType = (type) => {
  if (!type) {
    return 'Type not set';
  }

  return type
    .toString()
    .split('-')
    .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
    .join(' ');
};

const formatDate = (dateString) => {
  if (!dateString) {
    return 'Recently';
  }

  const date = new Date(dateString);

  if (Number.isNaN(date.getTime())) {
    return 'Recently';
  }

  return date.toLocaleDateString();
};

async function fetchDashboard() {
  dashboardLoading.value = true;
  dashboardError.value = '';

  try {
    const [{ data: user }, companies] = await Promise.all([
      axiosClient.get('/user'),
      fetchPaginated('/companies'),
    ]);

    company.value = companies.find((item) => Number(item.user_id) === Number(user?.id)) ?? null;

    if (!company.value) {
      jobs.value = [];
      return;
    }

    jobs.value = await fetchPaginated('/jobs', { company_id: company.value.id });
  } catch (error) {
    dashboardError.value = error?.response?.data?.message || 'Could not load employer dashboard.';
  } finally {
    dashboardLoading.value = false;
  }
}

onMounted(fetchDashboard);
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
              <router-link to="/jobs" class="text-[11px] font-black uppercase tracking-[0.16em] text-[#8B7322]">View all</router-link>
            </div>

            <div v-if="dashboardLoading" class="space-y-4">
              <article
                v-for="i in 3"
                :key="`job-loading-${i}`"
                class="h-29.5 animate-pulse rounded-2xl border border-[#F0E9D2] bg-[#FFFEF9] p-5"
              ></article>
            </div>

            <p v-else-if="dashboardError" class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">
              {{ dashboardError }}
            </p>

            <p v-else-if="recentJobs.length === 0" class="rounded-2xl border border-[#F0E9D2] bg-[#FFFEF9] p-4 text-sm font-semibold text-[#444]">
              No job posts yet. Create your first listing to start receiving candidates.
            </p>

            <div v-else class="space-y-4">
              <article
                v-for="job in recentJobs"
                :key="job.id"
                class="rounded-2xl border border-[#F0E9D2] bg-[#FFFEF9] p-5"
              >
                <div class="flex flex-wrap items-center justify-between gap-3">
                  <div>
                    <h3 class="text-lg font-black text-[#1A1A1A]">{{ job.title }}</h3>
                    <p class="mt-1 text-sm text-[#666]">{{ formatJobType(job.type) }} • {{ job.location || 'Location not set' }}</p>
                  </div>
                  <span class="rounded-full bg-[#fcf8ed] px-3 py-1 text-[10px] font-black uppercase tracking-[0.14em] text-[#8B7322]">
                    Active
                  </span>
                </div>
                <p class="mt-4 text-sm font-semibold text-[#333]">Posted {{ formatDate(job.created_at) }}</p>
              </article>
            </div>
          </section>

          <aside class="space-y-6">
            <section class="rounded-4xl border border-[#E9E1C8] bg-white p-6 shadow-sm md:p-8">
              <h3 class="text-xl font-black text-[#1A1A1A]">Company Details</h3>

              <p v-if="dashboardLoading" class="mt-5 rounded-2xl border border-[#F0E9D2] bg-[#FFFEF9] p-4 text-sm font-semibold text-[#444]">
                Loading company profile...
              </p>

              <p v-else-if="dashboardError" class="mt-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">
                {{ dashboardError }}
              </p>

              <div v-else-if="company" class="mt-5 space-y-3">
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
