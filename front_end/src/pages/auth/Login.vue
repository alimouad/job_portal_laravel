<script setup>
import AuthLayout from '@/layouts/AuthLayout.vue';
import { useRouter } from 'vue-router';
import { computed, reactive, ref } from 'vue';
import axiosClient, { setAuthToken } from '@/axios';

const stats = [
    { value: '12k+', label: 'Open Jobs' },
    { value: '4k+', label: 'Companies' },
    { value: '90%', label: 'Hire Rate' }
];

const router = useRouter();

const form = reactive({
    email: '',
    password: '',
    remember: false,
});

const isSubmitting = ref(false);
const apiError = ref('');

const emailError = computed(() => {
    if (!form.email) {
        return false;
    }
    return !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email);
});

const canSubmit = computed(() => {
    return !emailError.value && form.password.trim().length >= 6;
});

async function handleLogin() {
    if (!canSubmit.value) {
        return;
    }

    isSubmitting.value = true;
    apiError.value = '';

    try {
        const { data } = await axiosClient.post('/login', {
            email: form.email,
            password: form.password,
        });

        if (data?.token) {
            setAuthToken(data.token);
        }

        if (data?.user) {
            localStorage.setItem('auth_user', JSON.stringify(data.user));
        }

        router.push('/');
    } catch (error) {
        apiError.value = error?.response?.data?.message || 'Login failed. Please check your credentials.';
    } finally {
        isSubmitting.value = false;
    }
}
</script>
<template>
    <AuthLayout>
        <section class="relative isolate min-h-screen overflow-hidden bg-[#F9F5E8] px-4 py-8 md:px-8 md:py-12 flex items-center justify-center">
            <div class="pointer-events-none absolute inset-0 -z-10">
                <div class="absolute -left-20 top-10 h-[500px] w-[500px] rounded-full bg-[#FFD645]/20 blur-[120px] animate-pulse"></div>
                <div class="absolute -right-10 bottom-10 h-[400px] w-[400px] rounded-full bg-[#E8D9A8]/40 blur-[100px]"></div>
            </div>

            <div class="relative mx-auto grid w-full max-w-6xl items-stretch gap-0 overflow-hidden rounded-[40px] border border-[#E9E1C8] bg-white shadow-[0_32px_64px_-16px_rgba(107,83,17,0.12)] md:grid-cols-[1.1fr_0.9fr]">
                
                <div class="hidden relative overflow-hidden bg-[#FFFDF7] p-12 md:flex md:flex-col md:justify-between border-r border-[#F2EDDB]">
                    <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png');"></div>
                    
                    <div class="relative z-10">
                        <div class="inline-flex items-center gap-2 rounded-full border border-[#E9E1C8] bg-white px-4 py-1.5 shadow-sm">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#FFCD1F] opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-[#FFCD1F]"></span>
                            </span>
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#8B7322]">
                                Professional Hiring Network
                            </p>
                        </div>
                        
                        <h1 class="mt-10 text-6xl font-black leading-[1.1] tracking-tighter text-[#1A1A1A]">
                            Build your <br/> 
                            <span class="text-[#8B7322] relative">
                                future
                                <svg class="absolute -bottom-2 left-0 w-full h-2 text-[#FFCD1F]/40" viewBox="0 0 100 10" preserveAspectRatio="none"><path d="M0 5 Q 25 0 50 5 T 100 5" stroke="currentColor" stroke-width="4" fill="none"/></svg>
                            </span> 
                            with us.
                        </h1>
                        <p class="mt-6 max-w-sm text-base font-medium leading-relaxed text-[#626262]">
                            Join a community of <span class="text-black font-bold">500k+</span> experts. Sign in to access your personalized dashboard and track applications.
                        </p>
                    </div>

                    <div class="relative z-10 grid grid-cols-3 gap-4">
                        <div v-for="(stat, index) in stats" :key="index" class="group rounded-3xl border border-[#ECE5CE] bg-white p-5 transition-all hover:border-[#FFCD1F] hover:shadow-md">
                            <p class="text-3xl font-black tracking-tighter group-hover:scale-110 transition-transform">{{ stat.value }}</p>
                            <p class="mt-1 text-[9px] font-black uppercase tracking-[0.15em] text-[#8B7322]">{{ stat.label }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-8 md:p-14">
                    <div class="flex items-center justify-between">
                        <router-link to="/home" class="group inline-flex items-center gap-2 text-[11px] font-black uppercase tracking-[0.2em] text-[#8B7322] hover:text-black transition-colors">
                            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                            Back
                        </router-link>
                        <div class="h-10 w-10 bg-[#FFCD1F] rounded-xl flex items-center justify-center shadow-lg shadow-[#FFCD1F]/20 rotate-3">
                             <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                    </div>

                    <div class="mt-10">
                        <h2 class="text-4xl font-black tracking-tight text-[#1A1A1A]">Sign In</h2>
                        <p class="mt-3 text-base font-medium text-[#6B6B6B]">Welcome back! Please enter your details.</p>
                    </div>

                    <form class="mt-10 space-y-6" @submit.prevent="handleLogin">
                        <div class="space-y-2">
                            <label class="block text-[11px] font-black uppercase tracking-[0.2em] text-[#7B7B7B] ml-1">Email Address</label>
                            <input
                                v-model="form.email"
                                type="email"
                                placeholder="name@company.com"
                                class="w-full rounded-2xl border-2 border-[#F2EDDB] bg-[#FFFDF6] px-6 py-4 text-sm font-bold text-[#1F1F1F] outline-none transition-all focus:border-[#FFCD1F] focus:bg-white focus:shadow-[0_0_0_4px_rgba(255,205,31,0.1)] placeholder:text-[#C4C0B0]"
                            />
                            <p v-if="emailError" class="mt-1 text-xs font-bold text-red-500 animate-shake">Please enter a valid email address.</p>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between px-1">
                                <label class="text-[11px] font-black uppercase tracking-[0.2em] text-[#7B7B7B]">Password</label>
                                <a href="#" class="text-[11px] font-black uppercase tracking-widest text-[#8B7322] hover:text-black underline underline-offset-4 decoration-[#FFCD1F]/40">Forgot?</a>
                            </div>
                            <input
                                v-model="form.password"
                                type="password"
                                placeholder="••••••••"
                                class="w-full rounded-2xl border-2 border-[#F2EDDB] bg-[#FFFDF6] px-6 py-4 text-sm font-bold text-[#1F1F1F] outline-none transition-all focus:border-[#FFCD1F] focus:bg-white focus:shadow-[0_0_0_4px_rgba(255,205,31,0.1)] placeholder:text-[#C4C0B0]"
                            />
                        </div>

                        <div class="flex items-center justify-between py-2">
                            <label class="flex cursor-pointer items-center gap-3 text-sm font-bold text-[#5F5F5F] group">
                                <div class="relative flex items-center">
                                    <input v-model="form.remember" type="checkbox" class="peer appearance-none h-5 w-5 rounded-lg border-2 border-[#DCD3B6] checked:bg-[#111111] checked:border-[#111111] transition-all cursor-pointer" />
                                    <svg class="absolute w-3.5 h-3.5 text-white opacity-0 peer-checked:opacity-100 left-0.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <span class="group-hover:text-black transition-colors">Keep me signed in</span>
                            </label>
                        </div>

                        <button
                            type="submit"
                            :disabled="!canSubmit || isSubmitting"
                            class="relative w-full overflow-hidden rounded-2xl bg-[#111111] px-6 py-5 text-sm font-black uppercase tracking-[0.15em] text-white transition-all hover:bg-[#FFCD1F] hover:text-black active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-30 shadow-xl shadow-black/10 hover:shadow-[#FFCD1F]/20"
                        >
                            <span class="relative z-10">{{ isSubmitting ? 'Signing In...' : 'Access Account' }}</span>
                        </button>

                        <p v-if="apiError" class="text-sm font-bold text-red-500">{{ apiError }}</p>
                    </form>

                    <div class="mt-10 pt-8 border-t border-[#F2EDDB] text-center">
                        <p class="text-sm font-medium text-[#666]">
                            Don't have an account yet?
                            <router-link to="/register" class="ml-2 font-black text-black underline decoration-[#FFCD1F] decoration-[3px] underline-offset-[6px] hover:bg-[#FFCD1F]/10 transition-all">
                                Join our network
                            </router-link>
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </AuthLayout>
</template>



<style scoped>
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-4px); }
    75% { transform: translateX(4px); }
}
.animate-shake {
    animation: shake 0.2s ease-in-out 0s 2;
}
</style>