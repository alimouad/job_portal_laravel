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
  <header class="sticky top-0 z-50 border-b border-[#E9E1C8] bg-[#FFFDF5]/80 backdrop-blur-xl">
    <div class="mx-auto flex w-full max-w-7xl items-center justify-between px-6 py-4 md:px-10">
      
      <RouterLink to="/" class="group flex items-center gap-3 transition-transform active:scale-95">
        <div class="relative h-9 w-10 rounded-xl bg-[#FFCD1F] shadow-[0_4px_12px_-4px_rgba(250,204,21,0.4)] flex items-center justify-center overflow-hidden">
          <div class="absolute inset-0 bg-gradient-to-tr from-black/10 to-transparent"></div>
          <svg class="w-5 h-5 text-white relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
          </svg>
        </div>
        <span class="text-lg font-black tracking-tighter text-[#1A1A1A]">JobFinder<span class="text-[#FFCD1F]">.</span></span>
      </RouterLink>

      <nav class="hidden items-center gap-8 text-[11px] font-black uppercase tracking-[0.2em] text-[#8B7322] md:flex">
        <RouterLink to="/jobs" class="transition-colors hover:text-black">Find Jobs</RouterLink>
        <RouterLink to="/feed" class="transition-colors hover:text-black">Community</RouterLink>
        <RouterLink to="/companies" class="transition-colors hover:text-black">Companies</RouterLink>
      </nav>

      <div class="flex items-center gap-3">
        
        <template v-if="isAuthenticated">
          <div class="flex items-center gap-1 mr-2 border-r border-[#E9E1C8] pr-4">
            <template v-if="isJobSeeker">
              <RouterLink to="/favourites" class="p-2 text-slate-400 hover:text-red-500 transition-colors" title="Favourites">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
              </RouterLink>
              <RouterLink to="/my-candidatures" class="p-2 text-slate-400 hover:text-blue-500 transition-colors" title="My Applications">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
              </RouterLink>
            </template>
            
            <RouterLink v-if="isEmployer" to="/employer/jobs/create" class="hidden lg:flex items-center gap-2 rounded-xl bg-[#111] px-4 py-2 text-[10px] font-black uppercase tracking-widest text-white hover:bg-[#FFCD1F] hover:text-black transition-all">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
              Post Job
            </RouterLink>

          </div>

          <RouterLink v-if="isEmployer" to="/employer/home" class="flex items-center gap-3 pl-2 group">
            <div class="h-9 w-9 rounded-full bg-[#FFFDF5] border-2 border-[#FFCD1F] p-0.5 transition-transform group-hover:scale-110">
              <div class="h-full w-full rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-black text-slate-500">
                {{ currentUser?.full_name?.substring(0, 1) || 'U' }}
              </div>
            </div>
            <button @click="handleLogout" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-red-500 transition-colors">
              Exit
            </button>
          </RouterLink>
            <RouterLink v-if="isJobSeeker" to="/profile" class="flex items-center gap-3 pl-2 group">
            <div class="h-9 w-9 rounded-full bg-[#FFFDF5] border-2 border-[#FFCD1F] p-0.5 transition-transform group-hover:scale-110">
              <div class="h-full w-full rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-black text-slate-500">
                {{ currentUser?.full_name?.substring(0, 1) || 'U' }}
              </div>
            </div>
            <button @click="handleLogout" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-red-500 transition-colors">
              Exit
            </button>
          </RouterLink>
        </template>

        <template v-else>
          <RouterLink to="/login" class="text-[11px] font-black uppercase tracking-[0.2em] text-[#1A1A1A] px-4 py-2 hover:text-[#8B7322] transition-colors">
            Login
          </RouterLink>
          <RouterLink to="/register" class="rounded-2xl bg-[#111] px-6 py-3 text-[11px] font-black uppercase tracking-[0.2em] text-white shadow-xl shadow-black/10 hover:bg-[#FFCD1F] hover:text-black transition-all active:scale-95">
            Join Now
          </RouterLink>
        </template>

      </div>
    </div>
  </header>
</template>
