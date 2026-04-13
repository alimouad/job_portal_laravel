<script setup>

import AuthLayout from '@/layouts/AuthLayout.vue';
import { computed, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import axiosClient, { setAuthToken } from '@/axios';

const features = [
    { 
        title: 'Fast Setup', 
        desc: 'Build your profile and start applying in under 2 minutes.',
        iconPath: 'M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12V4.5A2.25 2.25 0 0 0 19.5 2.25h-2.25a14.98 14.98 0 0 0-12.12 6.16M15.59 14.37l-2.636-2.636m0 0a14.98 14.98 0 0 0-6.16 12.12v2.25A2.25 2.25 0 0 0 9.044 28.5h2.25a14.98 14.98 0 0 0 12.12-6.16m-10.46-10.606 2.122-2.122a3 3 0 1 1 4.243 4.243l-2.122 2.122m-4.243-4.243 4.243 4.243' 
    },
    { 
        title: 'Curated Matches', 
        desc: 'Receive roles tailored specifically to your craft and goals.',
        iconPath: 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z' 
    },
    { 
        title: 'Verified Only', 
        desc: 'Connect with screened employers and legitimate opportunities.',
        iconPath: 'M9 12.75 11.25 15 15 9.75m6 0c0 5.25-3.75 9.75-9 11.25-5.25-1.5-9-6-9-11.25V6.75A2.25 2.25 0 0 1 5.25 4.5h13.5A2.25 2.25 0 0 1 21 6.75v3Z' 
    }
];

const router = useRouter();

const form = reactive({
    fullName: '',
    email: '',
    password: '',
    confirmPassword: '',
    role: 'job_seeker',
    agree: false,
});

const isSubmitting = ref(false);
const apiError = ref('');

const emailError = computed(() => {
    if (!form.email) {
        return false;
    }
    return !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email);
});

const passwordMismatch = computed(() => {
    if (!form.confirmPassword) {
        return false;
    }
    return form.password !== form.confirmPassword;
});

const canSubmit = computed(() => {
    return (
        form.fullName.trim().length >= 3 &&
        form.role.trim().length > 0 &&
        form.password.length >= 8 &&
        !emailError.value &&
        !passwordMismatch.value &&
        form.agree
    );
});

async function handleRegister() {
    if (!canSubmit.value) {
        return;
    }

    isSubmitting.value = true;
    apiError.value = '';

    try {
        const { data } = await axiosClient.post('/register', {
            full_name: form.fullName,
            email: form.email,
            password: form.password,
            password_confirmation: form.confirmPassword,
            role: form.role,
        });

        if (data?.token) {
            setAuthToken(data.token);
        }

        if (data?.user) {
            localStorage.setItem('auth_user', JSON.stringify(data.user));
        }

        router.push('/login');
    } catch (error) {
        apiError.value = error?.response?.data?.message || 'Registration failed. Please verify your details.';
    } finally {
        isSubmitting.value = false;
    }
}
</script>

<template>
    <AuthLayout>
        <section class="relative isolate min-h-screen overflow-hidden bg-[#F9F5E8] px-4 py-8 md:px-8 md:py-12 flex items-center justify-center">
            <div class="pointer-events-none absolute inset-0 -z-10">
                <div class="absolute right-0 top-0 h-125 w-125 rounded-full bg-[#FFCD1F]/20 blur-[120px]"></div>
                <div class="absolute bottom-0 left-0 h-100 w-100 rounded-full bg-[#FFE59E]/40 blur-[100px]"></div>
            </div>

            <div class="relative mx-auto grid w-full max-w-6xl items-stretch gap-0 overflow-hidden rounded-[40px] border border-[#E9E1C8] bg-white shadow-[0_32px_64px_-16px_rgba(107,83,17,0.12)] md:grid-cols-[1.05fr_0.95fr]">
                
                <div class="p-8 md:p-14 border-r border-[#F2EDDB]">
                    <div class="flex items-center justify-between">
                        <router-link to="/home" class="group inline-flex items-center gap-2 text-[11px] font-black uppercase tracking-[0.2em] text-[#8B7322] hover:text-black transition-colors">
                            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                            Back to Home
                        </router-link>
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-300">Step 01/02</span>
                    </div>

                    <div class="mt-10">
                        <h1 class="text-4xl font-black tracking-tight text-[#1A1A1A]">Create Account</h1>
                        <p class="mt-3 text-base font-medium text-[#6B6B6B]">Build your professional profile in minutes.</p>
                    </div>

                    <form class="mt-10 space-y-5" @submit.prevent="handleRegister">
                        <div class="space-y-2">
                            <label class="block text-[11px] font-black uppercase tracking-[0.2em] text-[#7B7B7B] ml-1">Full Name</label>
                            <input
                                v-model="form.fullName"
                                type="text"
                                placeholder="John Doe"
                                class="w-full rounded-2xl border-2 border-[#F2EDDB] bg-[#FFFDF6] px-6 py-4 text-sm font-bold text-[#1F1F1F] outline-none transition-all focus:border-[#FFCD1F] focus:bg-white focus:shadow-[0_0_0_4px_rgba(255,205,31,0.1)]"
                            />
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[11px] font-black uppercase tracking-[0.2em] text-[#7B7B7B] ml-1">Email Address</label>
                            <input
                                v-model="form.email"
                                type="email"
                                placeholder="john@example.com"
                                class="w-full rounded-2xl border-2 border-[#F2EDDB] bg-[#FFFDF6] px-6 py-4 text-sm font-bold text-[#1F1F1F] outline-none transition-all focus:border-[#FFCD1F] focus:bg-white focus:shadow-[0_0_0_4px_rgba(255,205,31,0.1)]"
                            />
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <label class="block text-[11px] font-black uppercase tracking-[0.2em] text-[#7B7B7B] ml-1">Password</label>
                                <input
                                    v-model="form.password"
                                    type="password"
                                    placeholder="••••••••"
                                    class="w-full rounded-2xl border-2 border-[#F2EDDB] bg-[#FFFDF6] px-6 py-4 text-sm font-bold text-[#1F1F1F] outline-none transition-all focus:border-[#FFCD1F] focus:bg-white"
                                />
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[11px] font-black uppercase tracking-[0.2em] text-[#7B7B7B] ml-1">Confirm</label>
                                <input
                                    v-model="form.confirmPassword"
                                    type="password"
                                    placeholder="••••••••"
                                    class="w-full rounded-2xl border-2 border-[#F2EDDB] bg-[#FFFDF6] px-6 py-4 text-sm font-bold text-[#1F1F1F] outline-none transition-all focus:border-[#FFCD1F] focus:bg-white"
                                />
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="block text-[11px] font-black uppercase tracking-[0.2em] text-[#7B7B7B] ml-1">Account Type</label>
                            <div class="grid gap-3 md:grid-cols-2">
                                <label
                                    class="flex cursor-pointer items-start gap-3 rounded-[20px] border-2 p-4 transition-all"
                                    :class="form.role === 'job_seeker' ? 'border-[#FFCD1F] bg-[#FFF9E5] shadow-[0_0_0_4px_rgba(255,205,31,0.08)]' : 'border-[#F2EDDB] bg-[#FFFDF6] hover:border-[#FFCD1F]/30'"
                                >
                                    <input v-model="form.role" type="radio" value="job_seeker" class="mt-1 h-4 w-4 accent-[#111111]" />
                                    <div>
                                        <p class="text-sm font-black text-[#1A1A1A]">Job Seeker</p>
                                        <p class="mt-1 text-xs font-medium text-[#6B6B6B]">Find and apply to jobs.</p>
                                    </div>
                                </label>

                                <label
                                    class="flex cursor-pointer items-start gap-3 rounded-[20px] border-2 p-4 transition-all"
                                    :class="form.role === 'employer' ? 'border-[#FFCD1F] bg-[#FFF9E5] shadow-[0_0_0_4px_rgba(255,205,31,0.08)]' : 'border-[#F2EDDB] bg-[#FFFDF6] hover:border-[#FFCD1F]/30'"
                                >
                                    <input v-model="form.role" type="radio" value="employer" class="mt-1 h-4 w-4 accent-[#111111]" />
                                    <div>
                                        <p class="text-sm font-black text-[#1A1A1A]">Employer</p>
                                        <p class="mt-1 text-xs font-medium text-[#6B6B6B]">Post jobs and review applicants.</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <label class="flex cursor-pointer items-start gap-4 rounded-[20px] border-2 border-[#F2EDDB] bg-[#FFFDF6] p-4 group transition-all hover:border-[#FFCD1F]/30">
                            <div class="relative flex items-center mt-0.5">
                                <input v-model="form.agree" type="checkbox" class="peer appearance-none h-5 w-5 rounded-lg border-2 border-[#DCD3B6] checked:bg-[#111111] checked:border-[#111111] transition-all cursor-pointer" />
                                <svg class="absolute w-3.5 h-3.5 text-white opacity-0 peer-checked:opacity-100 left-0.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="text-xs font-medium leading-relaxed text-[#5F5F5F] group-hover:text-black">
                                I agree to the <a href="#" class="font-black text-black underline decoration-[#FFCD1F] decoration-2 underline-offset-2">Terms of Service</a> and 
                                <a href="#" class="font-black text-black underline decoration-[#FFCD1F] decoration-2 underline-offset-2">Privacy Policy</a>.
                            </span>
                        </label>

                        <button
                            type="submit"
                            :disabled="!canSubmit || isSubmitting"
                            class="group relative w-full overflow-hidden rounded-2xl bg-[#FFCD1F] px-6 py-5 text-sm font-black uppercase tracking-[0.15em] text-black transition-all hover:bg-[#111111] hover:text-white active:scale-[0.98] disabled:opacity-30 shadow-xl shadow-[#FFCD1F]/20"
                        >
                            <span class="relative z-10">{{ isSubmitting ? 'Creating Account...' : 'Create My Account' }}</span>
                        </button>

                        <p v-if="apiError" class="text-sm font-bold text-red-500">{{ apiError }}</p>
                    </form>

                    <p class="mt-8 text-center text-sm font-medium text-[#666]">
                        Already part of the network?
                        <router-link to="/login" class="ml-1 font-black text-black underline decoration-[#FFCD1F] decoration-[3px] underline-offset-[6px] hover:bg-[#FFCD1F]/10 transition-all px-1">
                            Sign in here
                        </router-link>
                    </p>
                </div>

                <div class="hidden relative bg-[#FFFDF7] p-12 md:flex md:flex-col md:justify-between overflow-hidden">
                    <div class="absolute -right-20 top-1/4 opacity-[0.05] pointer-events-none rotate-12">
                        <svg width="400" height="400" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="50" cy="50" r="48" stroke="currentColor" stroke-width="4"/>
                            <path d="M50 20V80M20 50H80" stroke="currentColor" stroke-width="4"/>
                        </svg>
                    </div>

                    <div class="relative z-10">
                        <p class="inline-flex rounded-full border border-[#E9E1C8] bg-white px-4 py-1.5 text-[10px] font-black uppercase tracking-[0.2em] text-[#8B7322] shadow-sm">
                            Career Acceleration Hub
                        </p>
                        <h2 class="mt-8 text-5xl font-black leading-[1.05] tracking-tighter text-[#1A1A1A]">
                            Be discovered by <br/>
                            <span class="text-[#8B7322] italic">leading teams.</span>
                        </h2>
                        <p class="mt-6 max-w-sm text-base font-medium leading-relaxed text-[#626262]">
                            Complete your profile once and get matched with verified opportunities in <span class="text-black font-bold">Industrial & Tech</span> sectors.
                        </p>
                    </div>

                    <div class="relative z-10 space-y-4">
                        <div v-for="(feature, index) in features" :key="index" 
                             class="flex items-start gap-4 rounded-3xl border border-[#ECE5CE] bg-white p-5 transition-all hover:translate-x-2 hover:shadow-lg hover:border-[#FFCD1F]">
                            <div class="h-10 w-10 shrink-0 bg-[#F9F5E8] rounded-xl flex items-center justify-center text-[#8B7322]">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" :d="feature.iconPath"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[11px] font-black uppercase tracking-[0.15em] text-[#8B7322]">{{ feature.title }}</p>
                                <p class="mt-1 text-sm font-bold text-[#343434] leading-snug">{{ feature.desc }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AuthLayout>
</template>

