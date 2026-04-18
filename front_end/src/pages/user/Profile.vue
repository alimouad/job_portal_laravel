<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import HomeLayout from '@/layouts/HomeLayout.vue';
import axiosClient from '@/axios';

const router = useRouter();

const loading = ref(true);
const submitting = ref(false);
const errorMessage = ref('');
const successMessage = ref('');
const profile = ref(null);
const user = ref(null);

const form = reactive({
  full_name: '',
  email: '',
  phone: '',
  location: '',
  bio: '',
  photo: '',
});

const isAuthenticated = computed(() => Boolean(localStorage.getItem('auth_token')));

const initial = computed(() => {
  const name = form.full_name?.trim() || user.value?.full_name || 'User';
  return name
    .split(' ')
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part[0]?.toUpperCase())
    .join('') || 'U';
});

const loadProfile = async () => {
  if (!isAuthenticated.value) {
    router.push('/login');
    return;
  }

  loading.value = true;
  errorMessage.value = '';

  try {
    const { data } = await axiosClient.get('/profile');
    user.value = data?.user || null;
    profile.value = data?.profile || null;

    form.full_name = user.value?.full_name || '';
    form.email = user.value?.email || '';
    form.phone = profile.value?.phone || '';
    form.location = profile.value?.location || '';
    form.bio = profile.value?.bio || '';
    form.photo = profile.value?.photo || '';
  } catch (error) {
    errorMessage.value = error?.response?.data?.message || 'Failed to load your profile.';
  } finally {
    loading.value = false;
  }
};

const submitProfile = async () => {
  if (!form.full_name.trim() || !form.email.trim()) {
    errorMessage.value = 'Full name and email are required.';
    return;
  }

  submitting.value = true;
  errorMessage.value = '';
  successMessage.value = '';

  try {
    const { data } = await axiosClient.put('/profile', {
      full_name: form.full_name.trim(),
      email: form.email.trim(),
      phone: form.phone.trim() || null,
      location: form.location.trim() || null,
      bio: form.bio.trim() || null,
      photo: form.photo.trim() || null,
    });

    user.value = data?.user || user.value;
    profile.value = data?.profile || profile.value;

    localStorage.setItem('auth_user', JSON.stringify(user.value));
    successMessage.value = 'Profile updated successfully.';
  } catch (error) {
    const validationError = error?.response?.data?.errors
      ? Object.values(error.response.data.errors)?.[0]?.[0]
      : null;

    errorMessage.value = validationError || error?.response?.data?.message || 'Failed to update profile.';
  } finally {
    submitting.value = false;
  }
};

onMounted(loadProfile);
</script>

<template>
  <HomeLayout>
    <section class="min-h-screen bg-[#FFFDF5] pb-20 pt-10">
      <div class="mx-auto max-w-6xl px-4 md:px-8">
        <div class="mb-8 rounded-4xl border border-[#e9e1c8] bg-white p-6 shadow-sm md:p-8">
          <p class="text-[11px] font-black uppercase tracking-[0.18em] text-[#8b7322]">My Profile</p>
          <h1 class="mt-2 text-3xl font-black text-[#1a1a1a] md:text-4xl">Edit your personal profile</h1>
          <p class="mt-3 max-w-2xl text-sm font-medium text-[#666]">
            Keep your account information, photo, and bio up to date so employers and the community can recognize you.
          </p>
        </div>

        <div v-if="loading" class="grid gap-6 lg:grid-cols-[320px_1fr]">
          <div class="h-72 animate-pulse rounded-4xl border border-[#e9e1c8] bg-white"></div>
          <div class="h-136 animate-pulse rounded-4xl border border-[#e9e1c8] bg-white"></div>
        </div>

        <div v-else-if="errorMessage" class="rounded-3xl border border-red-200 bg-red-50 px-6 py-5 text-sm font-semibold text-red-700">
          {{ errorMessage }}
        </div>

        <div v-else class="grid gap-6 lg:grid-cols-[320px_1fr]">
          <aside class="rounded-4xl border border-[#e9e1c8] bg-white p-6 shadow-sm">
            <div class="flex flex-col items-center text-center">
              <div class="flex h-28 w-28 items-center justify-center overflow-hidden rounded-full bg-[#ffcd1f] text-2xl font-black text-black shadow-sm ring-8 ring-[#fff8df]">
                <img
                  v-if="form.photo"
                  :src="form.photo"
                  :alt="form.full_name"
                  class="h-full w-full object-cover"
                />
                <span v-else>{{ initial }}</span>
              </div>

              <h2 class="mt-5 text-2xl font-black text-[#1a1a1a]">
                {{ form.full_name || 'Your Name' }}
              </h2>
              <p class="mt-1 text-sm font-semibold text-[#777]">{{ form.email || 'you@example.com' }}</p>

              <div class="mt-5 flex flex-wrap justify-center gap-2 text-[11px] font-black uppercase tracking-widest text-[#6b6b6b]">
                <span class="rounded-full bg-[#fcf8ed] px-3 py-1">{{ user?.role || 'job_seeker' }}</span>
                <span class="rounded-full bg-[#fcf8ed] px-3 py-1">{{ profile?.phone ? 'Phone Added' : 'No Phone Yet' }}</span>
              </div>
            </div>

            <div class="mt-6 rounded-2xl bg-[#fffdf3] p-4 text-sm leading-6 text-[#555]">
              <p class="font-black text-[#1a1a1a]">Profile tips</p>
              <ul class="mt-3 space-y-2 text-xs font-semibold">
                <li>• Add a real photo or avatar link.</li>
                <li>• Write a short bio with your strengths.</li>
                <li>• Keep your contact details current.</li>
              </ul>
            </div>
          </aside>

          <div class="rounded-4xl border border-[#e9e1c8] bg-white p-6 shadow-sm md:p-8">
            <div class="flex flex-col gap-2 border-b border-[#f0ecd9] pb-5 md:flex-row md:items-end md:justify-between">
              <div>
                <h2 class="text-2xl font-black text-[#1a1a1a]">Account Details</h2>
                <p class="mt-1 text-sm text-[#666]">Update your name, email, and profile information.</p>
              </div>
              <button
                class="rounded-full border border-[#e9e1c8] bg-white px-4 py-2 text-xs font-black uppercase tracking-widest text-[#8b7322] hover:bg-[#ffcd1f] hover:text-black"
                type="button"
                @click="loadProfile"
              >
                Refresh
              </button>
            </div>

            <form class="mt-6 grid gap-5 md:grid-cols-2" @submit.prevent="submitProfile">
              <div class="space-y-2">
                <label class="ml-1 block text-[11px] font-black uppercase tracking-[0.18em] text-[#7b7b7b]">Full Name</label>
                <input
                  v-model="form.full_name"
                  type="text"
                  class="w-full rounded-2xl border-2 border-[#f2eddb] bg-[#fffdf6] px-5 py-4 text-sm font-bold text-[#1f1f1f] outline-none transition-all focus:border-[#ffcd1f] focus:bg-white"
                  placeholder="John Doe"
                />
              </div>

              <div class="space-y-2">
                <label class="ml-1 block text-[11px] font-black uppercase tracking-[0.18em] text-[#7b7b7b]">Email</label>
                <input
                  v-model="form.email"
                  type="email"
                  class="w-full rounded-2xl border-2 border-[#f2eddb] bg-[#fffdf6] px-5 py-4 text-sm font-bold text-[#1f1f1f] outline-none transition-all focus:border-[#ffcd1f] focus:bg-white"
                  placeholder="name@example.com"
                />
              </div>

              <div class="space-y-2">
                <label class="ml-1 block text-[11px] font-black uppercase tracking-[0.18em] text-[#7b7b7b]">Phone</label>
                <input
                  v-model="form.phone"
                  type="text"
                  class="w-full rounded-2xl border-2 border-[#f2eddb] bg-[#fffdf6] px-5 py-4 text-sm font-bold text-[#1f1f1f] outline-none transition-all focus:border-[#ffcd1f] focus:bg-white"
                  placeholder="+1 555 000 000"
                />
              </div>

              <div class="space-y-2">
                <label class="ml-1 block text-[11px] font-black uppercase tracking-[0.18em] text-[#7b7b7b]">Location</label>
                <input
                  v-model="form.location"
                  type="text"
                  class="w-full rounded-2xl border-2 border-[#f2eddb] bg-[#fffdf6] px-5 py-4 text-sm font-bold text-[#1f1f1f] outline-none transition-all focus:border-[#ffcd1f] focus:bg-white"
                  placeholder="Casablanca, Morocco"
                />
              </div>

              <div class="space-y-2 md:col-span-2">
                <label class="ml-1 block text-[11px] font-black uppercase tracking-[0.18em] text-[#7b7b7b]">Photo URL</label>
                <input
                  v-model="form.photo"
                  type="text"
                  class="w-full rounded-2xl border-2 border-[#f2eddb] bg-[#fffdf6] px-5 py-4 text-sm font-bold text-[#1f1f1f] outline-none transition-all focus:border-[#ffcd1f] focus:bg-white"
                  placeholder="https://example.com/avatar.jpg"
                />
              </div>

              <div class="space-y-2 md:col-span-2">
                <label class="ml-1 block text-[11px] font-black uppercase tracking-[0.18em] text-[#7b7b7b]">Bio</label>
                <textarea
                  v-model="form.bio"
                  rows="6"
                  class="w-full resize-none rounded-2xl border-2 border-[#f2eddb] bg-[#fffdf6] px-5 py-4 text-sm font-semibold text-[#1f1f1f] outline-none transition-all focus:border-[#ffcd1f] focus:bg-white"
                  placeholder="Tell people about your experience, skills, and goals..."
                ></textarea>
              </div>

              <div class="md:col-span-2 flex flex-wrap items-center gap-3 pt-2">
                <button
                  type="submit"
                  :disabled="submitting"
                  class="rounded-2xl bg-[#111] px-6 py-4 text-xs font-black uppercase tracking-[0.18em] text-white transition hover:bg-[#ffcd1f] hover:text-black disabled:cursor-not-allowed disabled:opacity-40"
                >
                  {{ submitting ? 'Saving...' : 'Save Profile' }}
                </button>

                <p v-if="successMessage" class="rounded-2xl bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">
                  {{ successMessage }}
                </p>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </HomeLayout>
</template>
