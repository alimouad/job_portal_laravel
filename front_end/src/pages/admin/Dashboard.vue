
<script setup>
import { onMounted, ref } from 'vue';
import axiosClient from '@/axios';
import HomeLayout from '@/layouts/HomeLayout.vue';

const loading = ref(true);
const creatingCategory = ref(false);

const stats = ref([
  { label: 'Users', value: 0 },
  { label: 'Companies', value: 0 },
  { label: 'Jobs', value: 0 },
  { label: 'Categories', value: 0 }
]);

const users = ref([]);
const companies = ref([]);
const jobs = ref([]);
const categories = ref([]);

const categoryForm = ref({
  name: '',
  icon: '',
  description: ''
});

const error = ref('');
const categoryError = ref('');
const categorySuccess = ref('');

const fetchData = async () => {
  loading.value = true;
  error.value = '';

  try {
    const { data } = await axiosClient.get('/admin/overview');

    users.value = data?.users?.data ?? [];
    companies.value = data?.companies?.data ?? [];
    jobs.value = data?.jobs?.data ?? [];
    categories.value = data?.categories?.data ?? [];

    stats.value = [
      { label: 'Users', value: data?.users?.total ?? users.value.length },
      { label: 'Companies', value: data?.companies?.total ?? companies.value.length },
      { label: 'Jobs', value: data?.jobs?.total ?? jobs.value.length },
      { label: 'Categories', value: data?.categories?.total ?? categories.value.length }
    ];
  } catch (err) {
    error.value = 'Failed to load dashboard data.';
  } finally {
    loading.value = false;
  }
};

const createCategory = async () => {
  categoryError.value = '';
  categorySuccess.value = '';

  if (!categoryForm.value.name.trim()) {
    categoryError.value = 'Category name is required.';
    return;
  }

  creatingCategory.value = true;

  try {
    await axiosClient.post('/categories', {
      name: categoryForm.value.name.trim(),
      icon: categoryForm.value.icon?.trim() || null,
      description: categoryForm.value.description?.trim() || null
    });

    categoryForm.value = { name: '', icon: '', description: '' };
    categorySuccess.value = 'Category created successfully.';
    await fetchData();
  } catch (err) {
    categoryError.value = err?.response?.data?.message || 'Error creating category.';
  } finally {
    creatingCategory.value = false;
  }
};

const formatRole = (role) => {
  if (!role) return 'Unknown';
  return String(role).replace(/_/g, ' ').replace(/\b\w/g, (ch) => ch.toUpperCase());
};

const formatJobType = (type) => {
  if (!type) return 'Not specified';
  return String(type).replace(/_/g, ' ').replace(/\b\w/g, (ch) => ch.toUpperCase());
};

onMounted(fetchData);
</script>
<template>
  <HomeLayout>
    <section class="min-h-screen bg-[#F9F5E8] px-4 py-10 md:px-8">
      <div class="mx-auto max-w-7xl">
        <header class="mb-8 rounded-3xl border border-[#E9E1C8] bg-white p-6 md:p-8">
          <p class="text-[11px] font-black uppercase tracking-[0.2em] text-[#8B7322]">Admin Section</p>
          <h1 class="mt-3 text-4xl font-black tracking-tight text-[#1A1A1A] md:text-5xl">Platform Overview</h1>
          <p class="mt-3 text-sm font-semibold text-[#626262]">View core platform entities: users, companies, and jobs.</p>
        </header>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
          <article v-for="stat in stats" :key="stat.label" class="rounded-3xl border border-[#E9E1C8] bg-white p-6">
            <p class="text-[11px] font-black uppercase tracking-[0.16em] text-[#8B7322]">{{ stat.label }}</p>
            <p class="mt-2 text-4xl font-black text-[#1A1A1A]">{{ stat.value }}</p>
          </article>
        </div>

        <p v-if="error" class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">{{ error }}</p>

        <section class="mt-8 rounded-3xl border border-[#E9E1C8] bg-white p-5 md:p-6">
          <h2 class="text-xl font-black text-[#1A1A1A]">Add Category</h2>
          <form class="mt-4 grid gap-3 md:grid-cols-3" @submit.prevent="createCategory">
            <input
              v-model="categoryForm.name"
              type="text"
              placeholder="Category name"
              class="rounded-xl border border-[#E9E1C8] bg-[#FFFEF8] px-4 py-3 text-sm font-semibold text-[#1A1A1A] outline-none focus:border-[#FFCD1F]"
            />
            <input
              v-model="categoryForm.icon"
              type="text"
              placeholder="Icon (optional)"
              class="rounded-xl border border-[#E9E1C8] bg-[#FFFEF8] px-4 py-3 text-sm font-semibold text-[#1A1A1A] outline-none focus:border-[#FFCD1F]"
            />
            <button
              type="submit"
              :disabled="creatingCategory"
              class="rounded-xl bg-[#111] px-4 py-3 text-sm font-black uppercase tracking-widest text-white transition hover:bg-[#FFCD1F] hover:text-black disabled:cursor-not-allowed disabled:opacity-50"
            >
              {{ creatingCategory ? 'Creating...' : 'Create Category' }}
            </button>
            <textarea
              v-model="categoryForm.description"
              placeholder="Description (optional)"
              class="md:col-span-3 min-h-24 rounded-xl border border-[#E9E1C8] bg-[#FFFEF8] px-4 py-3 text-sm font-semibold text-[#1A1A1A] outline-none focus:border-[#FFCD1F]"
            ></textarea>
          </form>
          <p v-if="categoryError" class="mt-3 text-sm font-semibold text-red-600">{{ categoryError }}</p>
          <p v-if="categorySuccess" class="mt-3 text-sm font-semibold text-green-700">{{ categorySuccess }}</p>
        </section>

        <div class="mt-8 grid gap-8 xl:grid-cols-4">
          <section class="rounded-3xl border border-[#E9E1C8] bg-white p-5">
            <h2 class="text-xl font-black text-[#1A1A1A]">Users</h2>
            <div v-if="loading" class="mt-4 space-y-3">
              <div v-for="i in 5" :key="`user-loading-${i}`" class="h-14 animate-pulse rounded-xl bg-[#F7F2E1]"></div>
            </div>
            <div v-else class="mt-4 space-y-3">
              <article v-for="item in users" :key="item.id" class="rounded-xl border border-[#EFE8D4] bg-[#FFFEF8] p-3">
                <p class="font-black text-[#1A1A1A]">{{ item.full_name }}</p>
                <p class="text-xs font-semibold text-[#6B6B6B]">{{ item.email }}</p>
                <p class="mt-1 text-[11px] font-black uppercase tracking-widest text-[#8B7322]">{{ formatRole(item.role) }}</p>
              </article>
            </div>
          </section>

          <section class="rounded-3xl border border-[#E9E1C8] bg-white p-5">
            <h2 class="text-xl font-black text-[#1A1A1A]">Companies</h2>
            <div v-if="loading" class="mt-4 space-y-3">
              <div v-for="i in 5" :key="`company-loading-${i}`" class="h-14 animate-pulse rounded-xl bg-[#F7F2E1]"></div>
            </div>
            <div v-else class="mt-4 space-y-3">
              <article v-for="item in companies" :key="item.id" class="rounded-xl border border-[#EFE8D4] bg-[#FFFEF8] p-3">
                <p class="font-black text-[#1A1A1A]">{{ item.name }}</p>
                <p class="text-xs font-semibold text-[#6B6B6B]">{{ item.industry || 'No industry' }} • {{ item.location || 'No location' }}</p>
              </article>
            </div>
          </section>

          <section class="rounded-3xl border border-[#E9E1C8] bg-white p-5">
            <h2 class="text-xl font-black text-[#1A1A1A]">Jobs</h2>
            <div v-if="loading" class="mt-4 space-y-3">
              <div v-for="i in 5" :key="`job-loading-${i}`" class="h-14 animate-pulse rounded-xl bg-[#F7F2E1]"></div>
            </div>
            <div v-else class="mt-4 space-y-3">
              <article v-for="item in jobs" :key="item.id" class="rounded-xl border border-[#EFE8D4] bg-[#FFFEF8] p-3">
                <p class="font-black text-[#1A1A1A]">{{ item.title }}</p>
                <p class="text-xs font-semibold text-[#6B6B6B]">{{ item.company?.name || 'No company' }} • {{ item.location || 'No location' }}</p>
                <p class="mt-1 text-[11px] font-black uppercase tracking-widest text-[#8B7322]">{{ formatJobType(item.type) }}</p>
              </article>
            </div>
          </section>

          <section class="rounded-3xl border border-[#E9E1C8] bg-white p-5">
            <h2 class="text-xl font-black text-[#1A1A1A]">Categories</h2>
            <div v-if="loading" class="mt-4 space-y-3">
              <div v-for="i in 5" :key="`category-loading-${i}`" class="h-14 animate-pulse rounded-xl bg-[#F7F2E1]"></div>
            </div>
            <div v-else class="mt-4 space-y-3">
              <article v-for="item in categories" :key="item.id" class="rounded-xl border border-[#EFE8D4] bg-[#FFFEF8] p-3">
                <p class="font-black text-[#1A1A1A]">{{ item.name }}</p>
                <p class="text-xs font-semibold text-[#6B6B6B]">{{ item.description || 'No description' }}</p>
                <p class="mt-1 text-[11px] font-black uppercase tracking-widest text-[#8B7322]">{{ item.jobs_count || 0 }} jobs</p>
              </article>
            </div>
          </section>
        </div>
      </div>
    </section>
  </HomeLayout>
</template>