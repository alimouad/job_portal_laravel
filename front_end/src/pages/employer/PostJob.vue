<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import HomeLayout from '@/layouts/HomeLayout.vue';
import axiosClient from '@/axios';

const loadingMeta = ref(true);
const submitting = ref(false);
const apiError = ref('');
const successMessage = ref('');

const categories = ref([]);
const myCompanies = ref([]);

const form = reactive({
  title: '',
  description: '',
  location: '',
  salary: '',
  company_id: '',
  category_id: '',
  type: '',
});

const jobTypes = [
  { value: 'full-time', label: 'Full Time' },
  { value: 'part-time', label: 'Part Time' },
  { value: 'remote', label: 'Remote' },
  { value: 'internship', label: 'Internship' },
  { value: 'freelance', label: 'Freelance' },
];

const canSubmit = computed(() => {
  return (
    form.title.trim().length >= 3 &&
    form.description.trim().length >= 20 &&
    form.location.trim().length >= 2 &&
    Number(form.salary) > 0 &&
    String(form.company_id).length > 0 &&
    String(form.category_id).length > 0 &&
    form.type.length > 0
  );
});

const hasCompany = computed(() => myCompanies.value.length > 0);

async function loadMeta() {
  loadingMeta.value = true;
  apiError.value = '';

  try {
    const [{ data: user }, { data: companiesResponse }, { data: categoriesResponse }] = await Promise.all([
      axiosClient.get('/user'),
      axiosClient.get('/companies'),
      axiosClient.get('/categories'),
    ]);

    const companies = companiesResponse?.data ?? [];
    const userId = user?.id;

    myCompanies.value = companies.filter((company) => company.user_id === userId);
    categories.value = categoriesResponse?.data ?? [];

    if (myCompanies.value.length === 1) {
      form.company_id = String(myCompanies.value[0].id);
    }
  } catch (error) {
    apiError.value = error?.response?.data?.message || 'Could not load form data.';
  } finally {
    loadingMeta.value = false;
  }
}

function resetForm() {
  form.title = '';
  form.description = '';
  form.location = '';
  form.salary = '';
  form.category_id = '';
  form.type = '';

  if (myCompanies.value.length !== 1) {
    form.company_id = '';
  }
}

async function submitJob() {
  if (!canSubmit.value) {
    return;
  }

  submitting.value = true;
  successMessage.value = '';
  apiError.value = '';

  try {
    const payload = {
      title: form.title.trim(),
      description: form.description.trim(),
      location: form.location.trim(),
      salary: Number(form.salary),
      company_id: Number(form.company_id),
      category_id: Number(form.category_id),
      type: form.type,
    };

    await axiosClient.post('/jobs', payload);
    successMessage.value = 'Job posted successfully.';
    resetForm();
  } catch (error) {
    const responseData = error?.response?.data;
    const firstValidationError = responseData?.errors ? Object.values(responseData.errors)?.[0]?.[0] : '';
    apiError.value = firstValidationError || responseData?.message || 'Failed to post job.';
  } finally {
    submitting.value = false;
  }
}

onMounted(loadMeta);
</script>

<template>
  <HomeLayout>
    <section class="relative min-h-screen overflow-hidden bg-[#F9F5E8] pb-20">
      <div class="pointer-events-none absolute inset-0 -z-10">
        <div class="absolute -left-24 top-0 h-150 w-150 rounded-full bg-[#FFCD1F]/10 blur-[120px]"></div>
        <div class="absolute right-0 top-32 h-125 w-125 rounded-full bg-[#E8D9A8]/30 blur-[100px]"></div>
      </div>

      <div class="relative mx-auto max-w-5xl px-4 py-10 md:px-8">
        <header class="mb-10 border-b border-[#E9E1C8] pb-8">
          <div class="inline-flex items-center gap-2 rounded-full border border-[#E9E1C8] bg-white px-3 py-1 shadow-sm">
            <span class="h-2 w-2 animate-pulse rounded-full bg-[#FFCD1F]"></span>
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-[#8B7322]">Employer Hub</span>
          </div>
          <h1 class="mt-4 text-4xl font-black tracking-tighter text-[#1A1A1A] md:text-5xl">
            Post a New Job<span class="text-[#FFCD1F]">.</span>
          </h1>
          <p class="mt-3 max-w-2xl text-base font-medium text-[#626262]">
            Add a clear and complete job description to attract the right candidates quickly.
          </p>
        </header>

        <div class="rounded-4xl border border-[#E9E1C8] bg-white p-6 shadow-sm md:p-8">
          <p v-if="loadingMeta" class="rounded-2xl border border-[#F0E9D2] bg-[#FFFEF9] p-4 text-sm font-semibold text-[#444]">
            Loading job form...
          </p>

          <p v-else-if="apiError && !hasCompany" class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">
            {{ apiError }}
          </p>

          <div v-else-if="!hasCompany" class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-800">
            You need a company profile before posting jobs.
            <router-link to="/employer/company" class="ml-1 font-black underline">Create company profile</router-link>
          </div>

          <form v-else class="space-y-6" @submit.prevent="submitJob">
            <div class="grid gap-4 md:grid-cols-2">
              <div class="space-y-2 md:col-span-2">
                <label class="ml-1 block text-[11px] font-black uppercase tracking-[0.2em] text-[#7B7B7B]">Job Title</label>
                <input v-model="form.title" type="text" placeholder="Senior Backend Engineer" class="w-full rounded-2xl border-2 border-[#F2EDDB] bg-[#FFFDF6] px-5 py-4 text-sm font-bold text-[#1F1F1F] outline-none transition-all placeholder:text-[#C4C0B0] focus:border-[#FFCD1F] focus:bg-white" />
              </div>

              <div class="space-y-2">
                <label class="ml-1 block text-[11px] font-black uppercase tracking-[0.2em] text-[#7B7B7B]">Company</label>
                <select v-model="form.company_id" class="w-full rounded-2xl border-2 border-[#F2EDDB] bg-[#FFFDF6] px-5 py-4 text-sm font-bold text-[#1F1F1F] outline-none transition-all focus:border-[#FFCD1F] focus:bg-white">
                  <option value="" disabled>Select company</option>
                  <option v-for="company in myCompanies" :key="company.id" :value="String(company.id)">
                    {{ company.name }}
                  </option>
                </select>
              </div>

              <div class="space-y-2">
                <label class="ml-1 block text-[11px] font-black uppercase tracking-[0.2em] text-[#7B7B7B]">Category</label>
                <select v-model="form.category_id" class="w-full rounded-2xl border-2 border-[#F2EDDB] bg-[#FFFDF6] px-5 py-4 text-sm font-bold text-[#1F1F1F] outline-none transition-all focus:border-[#FFCD1F] focus:bg-white">
                  <option value="" disabled>Select category</option>
                  <option v-for="category in categories" :key="category.id" :value="String(category.id)">
                    {{ category.name }}
                  </option>
                </select>
              </div>

              <div class="space-y-2">
                <label class="ml-1 block text-[11px] font-black uppercase tracking-[0.2em] text-[#7B7B7B]">Job Type</label>
                <select v-model="form.type" class="w-full rounded-2xl border-2 border-[#F2EDDB] bg-[#FFFDF6] px-5 py-4 text-sm font-bold text-[#1F1F1F] outline-none transition-all focus:border-[#FFCD1F] focus:bg-white">
                  <option value="" disabled>Select type</option>
                  <option v-for="type in jobTypes" :key="type.value" :value="type.value">{{ type.label }}</option>
                </select>
              </div>

              <div class="space-y-2">
                <label class="ml-1 block text-[11px] font-black uppercase tracking-[0.2em] text-[#7B7B7B]">Salary</label>
                <input v-model="form.salary" type="number" min="1" step="0.01" placeholder="4500" class="w-full rounded-2xl border-2 border-[#F2EDDB] bg-[#FFFDF6] px-5 py-4 text-sm font-bold text-[#1F1F1F] outline-none transition-all placeholder:text-[#C4C0B0] focus:border-[#FFCD1F] focus:bg-white" />
              </div>

              <div class="space-y-2 md:col-span-2">
                <label class="ml-1 block text-[11px] font-black uppercase tracking-[0.2em] text-[#7B7B7B]">Location</label>
                <input v-model="form.location" type="text" placeholder="Casablanca, Morocco" class="w-full rounded-2xl border-2 border-[#F2EDDB] bg-[#FFFDF6] px-5 py-4 text-sm font-bold text-[#1F1F1F] outline-none transition-all placeholder:text-[#C4C0B0] focus:border-[#FFCD1F] focus:bg-white" />
              </div>

              <div class="space-y-2 md:col-span-2">
                <label class="ml-1 block text-[11px] font-black uppercase tracking-[0.2em] text-[#7B7B7B]">Description</label>
                <textarea v-model="form.description" rows="7" placeholder="Write responsibilities, requirements, and benefits..." class="w-full rounded-3xl border-2 border-[#F2EDDB] bg-[#FFFDF6] px-5 py-4 text-sm font-semibold text-[#1F1F1F] outline-none transition-all placeholder:text-[#C4C0B0] focus:border-[#FFCD1F] focus:bg-white"></textarea>
              </div>
            </div>

            <div class="flex flex-col gap-3 pt-2 md:flex-row md:items-center md:justify-between">
              <p v-if="successMessage" class="text-sm font-bold text-green-700">{{ successMessage }}</p>
              <p v-else class="text-sm text-[#6b6b6b]">Make sure all fields are complete before publishing.</p>

              <button type="submit" :disabled="!canSubmit || submitting || loadingMeta" class="rounded-2xl bg-[#111111] px-6 py-4 text-sm font-black uppercase tracking-[0.12em] text-white transition hover:bg-[#FFCD1F] hover:text-black disabled:cursor-not-allowed disabled:opacity-40">
                {{ submitting ? 'Posting...' : 'Publish Job' }}
              </button>
            </div>

            <p v-if="apiError && hasCompany" class="text-sm font-bold text-red-600">{{ apiError }}</p>
          </form>
        </div>
      </div>
    </section>
  </HomeLayout>
</template>
