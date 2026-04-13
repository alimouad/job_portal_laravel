<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import axiosClient, { clearAuthToken } from '@/axios';

const user = ref(null);
const router = useRouter();

const isAuthenticated = computed(() => {
  return Boolean(localStorage.getItem('auth_token'));
});

const isEmployer = computed(() => {
  return user.value?.role === 'employer';
});

const isJobSeeker = computed(() => {
  return isAuthenticated.value && !isEmployer.value;
});

const loadCurrentUser = async () => {
  const storedUser = localStorage.getItem('auth_user');

  if (storedUser) {
    try {
      user.value = JSON.parse(storedUser);
    } catch {
      user.value = null;
    }
  }

  if (!isAuthenticated.value) {
    return;
  }

  try {
    const { data } = await axiosClient.get('/user');
    user.value = data;
    localStorage.setItem('auth_user', JSON.stringify(data));
  } catch {
    // Keep local user fallback if request fails.
  }
};

const handleLogout = async () => {
  try {
    await axiosClient.post('/logout');
  } catch {
    // Ignore backend logout errors and clear local state anyway.
  } finally {
    clearAuthToken();
    localStorage.removeItem('auth_user');
    user.value = null;
    router.push('/login');
  }
};

onMounted(loadCurrentUser);

</script>

<template>
   <header class="sticky top-0 z-50 border-b border-[#e9e1c8] bg-[#f7efd8]/95 backdrop-blur">
      <div class="mx-auto flex w-full max-w-6xl items-center justify-between px-4 py-4 md:px-8">
        <div class="flex items-center gap-2">
          <div class="h-8 w-9 rounded-md bg-[#ffcd1f] shadow-[inset_0_-3px_0_rgba(0,0,0,0.2)]"></div>
          <span class="text-base font-extrabold tracking-tight">Job Finder</span>
        </div>

        <nav class="hidden items-center gap-6 text-xs font-semibold text-[#2f2f2f] md:flex">
          <RouterLink to="/jobs" class="hover:text-black">Find Jobs</RouterLink>
          <RouterLink to="/home" class="hover:text-black">Home</RouterLink>
          <RouterLink to="/feed" class="hover:text-black">Feed</RouterLink>
          <RouterLink to="/home" class="hover:text-black">Category</RouterLink>
          <RouterLink to="/companies" class="hover:text-black">Companies</RouterLink>
        </nav>

        <div class="flex items-center gap-2">
          <RouterLink v-if="isJobSeeker" to="/favourites" class="rounded-full border border-[#d8d1b8] bg-white px-3 py-2 text-[11px] font-bold text-[#2f2f2f] hover:bg-[#fff8df]">
            Favourites
          </RouterLink>
          <RouterLink v-if="isJobSeeker" to="/my-candidatures" class="rounded-full border border-[#d8d1b8] bg-white px-3 py-2 text-[11px] font-bold text-[#2f2f2f] hover:bg-[#fff8df]">
            My Candidatures
          </RouterLink>
          <RouterLink v-if="!isAuthenticated" to="/login" class="rounded-full border border-[#d8d1b8] bg-white px-4 py-2 text-xs font-bold">Login</RouterLink>
          <button v-else class="rounded-full border border-[#d8d1b8] bg-white px-4 py-2 text-xs font-bold text-[#2f2f2f] hover:bg-[#fff8df]" @click="handleLogout">
            Logout
          </button>
          <RouterLink v-if="isEmployer" to="/employer/jobs/create" class="rounded-full bg-[#ffc61b] px-4 py-2 text-xs font-extrabold text-black">Post A Job</RouterLink>
        </div>
      </div>
    </header>
</template>

