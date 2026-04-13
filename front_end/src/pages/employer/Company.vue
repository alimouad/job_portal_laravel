<script setup>
import HomeLayout from '@/layouts/HomeLayout.vue';
import axiosClient from '@/axios';
import { computed, reactive, ref } from 'vue';

const form = reactive({
  companyName: '',
  industry: '',
  companySize: '',
  location: '',
  website: '',
  email: '',
  phone: '',
  logo: null,
  description: '',
});

const isSubmitting = ref(false);
const saved = ref(false);
const apiError = ref('');
const successMessage = ref('');

const canSubmit = computed(() => {
  return (
    form.companyName.trim().length >= 2 &&
    form.industry.trim().length > 0 &&
    form.companySize.trim().length > 0 &&
    form.location.trim().length > 0 &&
    form.email.trim().length > 0 &&
    form.description.trim().length >= 20
  );
});

function handleLogoChange(event) {
  const file = event.target.files?.[0] ?? null;
  form.logo = file;
}

async function handleSave() {
  if (!canSubmit.value) {
    return;
  }

  isSubmitting.value = true;
  saved.value = false;
  apiError.value = '';
  successMessage.value = '';

  try {
    const payload = {
      name: form.companyName.trim(),
      industry: form.industry,
      companySize: form.companySize,
      location: form.location.trim(),
      website: form.website.trim() || null,
      email: form.email.trim(),
      phone: form.phone.trim() || null,
      logo: form.logo ? form.logo.name : null,
      description: form.description.trim(),
    };

    await axiosClient.post('/companies', payload);

    successMessage.value = 'Company profile saved successfully.';
  } catch (error) {
    const responseData = error?.response?.data;
    const firstValidationError = responseData?.errors ? Object.values(responseData.errors)?.[0]?.[0] : '';
    apiError.value = firstValidationError || responseData?.message || 'Failed to save company profile.';
  }

  saved.value = !apiError.value;
  isSubmitting.value = false;
}
</script>

<template>
  <HomeLayout>
    <section class="relative min-h-screen overflow-hidden bg-[#F9F5E8] pb-20">
      <div class="pointer-events-none absolute inset-0 -z-10">
        <div class="absolute -left-24 top-0 h-150 w-150 rounded-full bg-[#FFCD1F]/10 blur-[120px]"></div>
        <div class="absolute right-0 top-32 h-125 w-125 rounded-full bg-[#E8D9A8]/30 blur-[100px]"></div>
      </div>

      <div class="relative mx-auto max-w-7xl px-4 py-10 md:px-8">
        <header class="mb-12 flex flex-col gap-6 border-b border-[#E9E1C8] pb-10 md:flex-row md:items-end md:justify-between">
          <div class="space-y-4">
            <div class="inline-flex items-center gap-2 rounded-full border border-[#E9E1C8] bg-white px-3 py-1 shadow-sm">
              <span class="h-2 w-2 animate-pulse rounded-full bg-[#FFCD1F]"></span>
              <span class="text-[10px] font-black uppercase tracking-[0.2em] text-[#8B7322]">Employer Hub</span>
            </div>
            <h1 class="text-5xl font-black tracking-tighter text-[#1A1A1A] md:text-6xl">
              Add Company<span class="text-[#FFCD1F]">.</span>
            </h1>
            <p class="max-w-2xl text-lg font-medium text-[#626262]">
              Create your company profile first so recruiters can start posting jobs and building your employer brand.
            </p>
          </div>

          <div class="rounded-2xl border border-[#E9E1C8] bg-white px-5 py-4 shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#8B7322]">Profile Status</p>
            <p class="mt-1 text-sm font-bold text-[#1A1A1A]">{{ saved ? 'Saved' : 'Draft' }}</p>
          </div>
        </header>

        <div class="grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
          <div class="rounded-[40px] border border-[#E9E1C8] bg-white p-6 shadow-sm md:p-8">
            <div class="mb-8 flex items-center justify-between">
              <div>
                <h2 class="text-2xl font-black tracking-tight text-[#1A1A1A]">Company Profile</h2>
                <p class="mt-1 text-sm text-[#6b6b6b]">Fill in the details below to create your company page.</p>
              </div>
              <span class="rounded-full bg-[#fcf8ed] px-3 py-1 text-[10px] font-black uppercase tracking-[0.15em] text-[#8B7322]">
                Employer
              </span>
            </div>

            <form class="space-y-6" @submit.prevent="handleSave">
              <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-2 md:col-span-2">
                  <label class="ml-1 block text-[11px] font-black uppercase tracking-[0.2em] text-[#7B7B7B]">Company Logo</label>
                  <label class="flex cursor-pointer items-center justify-center rounded-3xl border-2 border-dashed border-[#E9E1C8] bg-[#FFFDF5] px-6 py-8 text-center transition-all hover:border-[#FFCD1F] hover:bg-[#fffaf0]">
                    <input type="file" class="hidden" accept="image/*" @change="handleLogoChange" />
                    <div>
                      <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#FFCD1F] text-xl font-black text-black shadow-sm">+</div>
                      <p class="mt-3 text-sm font-black text-[#1A1A1A]">Upload company logo</p>
                      <p class="mt-1 text-xs text-[#6b6b6b]">PNG, JPG or SVG recommended</p>
                    </div>
                  </label>
                  <p v-if="form.logo" class="ml-1 text-xs font-semibold text-green-700">Selected: {{ form.logo.name }}</p>
                </div>

                <div class="space-y-2">
                  <label class="ml-1 block text-[11px] font-black uppercase tracking-[0.2em] text-[#7B7B7B]">Company Name</label>
                  <input v-model="form.companyName" type="text" placeholder="Atlas Build Co." class="w-full rounded-2xl border-2 border-[#F2EDDB] bg-[#FFFDF6] px-5 py-4 text-sm font-bold text-[#1F1F1F] outline-none transition-all placeholder:text-[#C4C0B0] focus:border-[#FFCD1F] focus:bg-white focus:shadow-[0_0_0_4px_rgba(255,205,31,0.1)]" />
                </div>

                <div class="space-y-2">
                  <label class="ml-1 block text-[11px] font-black uppercase tracking-[0.2em] text-[#7B7B7B]">Industry</label>
                  <select v-model="form.industry" class="w-full rounded-2xl border-2 border-[#F2EDDB] bg-[#FFFDF6] px-5 py-4 text-sm font-bold text-[#1F1F1F] outline-none transition-all focus:border-[#FFCD1F] focus:bg-white focus:shadow-[0_0_0_4px_rgba(255,205,31,0.1)]">
                    <option value="" disabled>Select industry</option>
                    <option>Construction</option>
                    <option>Manufacturing</option>
                    <option>Engineering</option>
                    <option>Technology</option>
                    <option>Healthcare</option>
                  </select>
                </div>

                <div class="space-y-2">
                  <label class="ml-1 block text-[11px] font-black uppercase tracking-[0.2em] text-[#7B7B7B]">Company Size</label>
                  <select v-model="form.companySize" class="w-full rounded-2xl border-2 border-[#F2EDDB] bg-[#FFFDF6] px-5 py-4 text-sm font-bold text-[#1F1F1F] outline-none transition-all focus:border-[#FFCD1F] focus:bg-white focus:shadow-[0_0_0_4px_rgba(255,205,31,0.1)]">
                    <option value="" disabled>Select size</option>
                    <option>1-10 Employees</option>
                    <option>11-50 Employees</option>
                    <option>51-200 Employees</option>
                    <option>200+ Employees</option>
                  </select>
                </div>

                <div class="space-y-2">
                  <label class="ml-1 block text-[11px] font-black uppercase tracking-[0.2em] text-[#7B7B7B]">Location</label>
                  <input v-model="form.location" type="text" placeholder="Dubai, UAE" class="w-full rounded-2xl border-2 border-[#F2EDDB] bg-[#FFFDF6] px-5 py-4 text-sm font-bold text-[#1F1F1F] outline-none transition-all placeholder:text-[#C4C0B0] focus:border-[#FFCD1F] focus:bg-white focus:shadow-[0_0_0_4px_rgba(255,205,31,0.1)]" />
                </div>

                <div class="space-y-2 md:col-span-2">
                  <label class="ml-1 block text-[11px] font-black uppercase tracking-[0.2em] text-[#7B7B7B]">Website</label>
                  <input v-model="form.website" type="url" placeholder="https://atlasbuild.io" class="w-full rounded-2xl border-2 border-[#F2EDDB] bg-[#FFFDF6] px-5 py-4 text-sm font-bold text-[#1F1F1F] outline-none transition-all placeholder:text-[#C4C0B0] focus:border-[#FFCD1F] focus:bg-white focus:shadow-[0_0_0_4px_rgba(255,205,31,0.1)]" />
                </div>

                <div class="space-y-2">
                  <label class="ml-1 block text-[11px] font-black uppercase tracking-[0.2em] text-[#7B7B7B]">Contact Email</label>
                  <input v-model="form.email" type="email" placeholder="hr@company.com" class="w-full rounded-2xl border-2 border-[#F2EDDB] bg-[#FFFDF6] px-5 py-4 text-sm font-bold text-[#1F1F1F] outline-none transition-all placeholder:text-[#C4C0B0] focus:border-[#FFCD1F] focus:bg-white focus:shadow-[0_0_0_4px_rgba(255,205,31,0.1)]" />
                </div>

                <div class="space-y-2">
                  <label class="ml-1 block text-[11px] font-black uppercase tracking-[0.2em] text-[#7B7B7B]">Phone</label>
                  <input v-model="form.phone" type="text" placeholder="+971 50 123 4567" class="w-full rounded-2xl border-2 border-[#F2EDDB] bg-[#FFFDF6] px-5 py-4 text-sm font-bold text-[#1F1F1F] outline-none transition-all placeholder:text-[#C4C0B0] focus:border-[#FFCD1F] focus:bg-white focus:shadow-[0_0_0_4px_rgba(255,205,31,0.1)]" />
                </div>

                <div class="space-y-2 md:col-span-2">
                  <label class="ml-1 block text-[11px] font-black uppercase tracking-[0.2em] text-[#7B7B7B]">Company Description</label>
                  <textarea v-model="form.description" rows="6" placeholder="Tell candidates what your company does, your values, and why they should join you..." class="w-full rounded-3xl border-2 border-[#F2EDDB] bg-[#FFFDF6] px-5 py-4 text-sm font-semibold text-[#1F1F1F] outline-none transition-all placeholder:text-[#C4C0B0] focus:border-[#FFCD1F] focus:bg-white focus:shadow-[0_0_0_4px_rgba(255,205,31,0.1)]"></textarea>
                  <p class="ml-1 text-xs text-[#6b6b6b]">Minimum 20 characters.</p>
                </div>
              </div>

              <div class="flex flex-col gap-3 pt-2 md:flex-row md:items-center md:justify-between">
                <p v-if="saved" class="text-sm font-bold text-green-700">{{ successMessage }}</p>
                <p v-else class="text-sm text-[#6b6b6b]">Complete the company details before posting your first job.</p>

                <button type="submit" :disabled="!canSubmit || isSubmitting" class="rounded-2xl bg-[#111111] px-6 py-4 text-sm font-black uppercase tracking-[0.12em] text-white transition hover:bg-[#FFCD1F] hover:text-black disabled:cursor-not-allowed disabled:opacity-40">
                  {{ isSubmitting ? 'Saving...' : 'Save Company Profile' }}
                </button>
              </div>

              <p v-if="apiError" class="text-sm font-bold text-red-600">{{ apiError }}</p>
            </form>
          </div>

          <aside class="space-y-8">
            <div class="rounded-[40px] border border-[#E9E1C8] bg-white p-8 shadow-sm">
              <p class="inline-flex rounded-full border border-[#E9E1C8] bg-[#fcf8ed] px-4 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-[#8B7322]">
                Profile Preview
              </p>
              <h3 class="mt-5 text-3xl font-black text-[#1A1A1A]">Your company card</h3>
              <p class="mt-2 text-sm text-[#6b6b6b]">This is how candidates will see your profile.</p>

              <div class="mt-8 rounded-4xl border border-[#E9E1C8] bg-[#FFFDF5] p-6">
                <div class="flex items-start gap-4">
                  <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#FFCD1F] text-sm font-black text-black shadow-sm">
                    {{ form.companyName ? form.companyName.charAt(0).toUpperCase() : 'A' }}
                  </div>
                  <div>
                    <p class="text-[11px] font-black uppercase tracking-[0.2em] text-[#8B7322]">{{ form.industry || 'Industry' }}</p>
                    <h4 class="mt-1 text-xl font-black text-[#1A1A1A]">
                      {{ form.companyName || 'Atlas Build Co.' }}
                    </h4>
                    <p class="mt-2 text-sm text-[#626262]">
                      {{ form.location || 'Dubai, UAE' }} • {{ form.companySize || 'Company Size' }}
                    </p>
                  </div>
                </div>

                <div class="mt-6 space-y-3 text-sm text-[#343434]">
                  <div class="flex items-center justify-between rounded-2xl bg-white px-4 py-3">
                    <span class="font-semibold text-[#6b6b6b]">Website</span>
                    <span class="font-black">{{ form.website || 'atlasbuild.io' }}</span>
                  </div>
                  <div class="flex items-center justify-between rounded-2xl bg-white px-4 py-3">
                    <span class="font-semibold text-[#6b6b6b]">Email</span>
                    <span class="font-black">{{ form.email || 'hr@company.com' }}</span>
                  </div>
                  <div class="flex items-center justify-between rounded-2xl bg-white px-4 py-3">
                    <span class="font-semibold text-[#6b6b6b]">Phone</span>
                    <span class="font-black">{{ form.phone || '+971 50 123 4567' }}</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="rounded-[40px] border border-[#E9E1C8] bg-[#111111] p-8 text-white shadow-2xl">
              <h3 class="text-2xl font-black">Why complete your profile?</h3>
              <div class="mt-6 space-y-4">
                <div class="rounded-3xl border border-white/10 bg-white/10 p-4">
                  <p class="text-sm font-black text-[#FFCD1F]">Better visibility</p>
                  <p class="mt-1 text-sm text-white/75">A complete profile helps candidates trust your company.</p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/10 p-4">
                  <p class="text-sm font-black text-[#FFCD1F]">Faster hiring</p>
                  <p class="mt-1 text-sm text-white/75">Add company details once and reuse them for every job post.</p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/10 p-4">
                  <p class="text-sm font-black text-[#FFCD1F]">Professional brand</p>
                  <p class="mt-1 text-sm text-white/75">Show your company story, contact info, and culture.</p>
                </div>
              </div>
            </div>
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
