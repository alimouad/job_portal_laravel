<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axiosClient from '@/axios';
import HomeLayout from '@/layouts/HomeLayout.vue';

const route = useRoute();
const router = useRouter();

const job = ref(null);
const loading = ref(true);
const submitting = ref(false);
const error = ref('');
const successMessage = ref('');

const form = ref({
    cover_letter: '',
});

const jobId = computed(() => route.params.id);

// Fetch job details
const loadJob = async () => {
    loading.value = true;
    error.value = '';
    try {
        const res = await axiosClient.get(`/jobs/${jobId.value}`);
        job.value = res.data?.data || res.data;
    } catch (err) {
        error.value = 'Failed to load job details';
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    loadJob();
});

// Submit application
const submitApplication = async () => {
    if (!form.value.cover_letter.trim()) {
        error.value = 'Please write a cover letter';
        return;
    }

    submitting.value = true;
    error.value = '';
    successMessage.value = '';

    try {
        const res = await axiosClient.post(`/jobs/${jobId.value}/apply`, {
            cover_letter: form.value.cover_letter.trim(),
        });

        successMessage.value = res.data?.message || 'Application submitted successfully!';
        
        setTimeout(() => {
            router.push('/jobs');
        }, 2000);
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to submit application';
    } finally {
        submitting.value = false;
    }
};

const formatCurrency = (val) => {
    const num = Number(val);
    return isNaN(num) ? 'N/A' : `$${num.toLocaleString()}`;
};

const formatTime = (date) => {
    if (!date) return 'Recently';
    const d = new Date(date);
    const hours = Math.floor((Date.now() - d) / (1000 * 60 * 60));
    if (hours < 1) return 'Just now';
    if (hours < 24) return `${hours}h ago`;
    return Math.floor(hours / 24) + 'd ago';
};

const getInitials = (name) => {
    return name?.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase() || 'CO';
};
</script>

<template>
    <HomeLayout>
        <div class="min-h-screen bg-white py-12 px-4">
            <div class="max-w-4xl mx-auto">
                <!-- Back Button -->
                <button
                    @click="$router.back()"
                    class="mb-8 text-slate-600 hover:text-slate-900 font-semibold text-sm flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Jobs
                </button>

                <!-- Loading -->
                <div v-if="loading" class="space-y-4">
                    <div class="h-10 w-64 bg-slate-100 rounded animate-pulse"></div>
                    <div class="h-8 w-80 bg-slate-100 rounded animate-pulse"></div>
                    <div class="h-96 bg-slate-100 rounded-lg animate-pulse"></div>
                </div>

                <!-- Error -->
                <div v-else-if="error && !job" class="bg-red-50 border border-red-200 rounded-lg p-6 text-red-700">
                    {{ error }}
                </div>

                <!-- Content -->
                <div v-else-if="job" class="space-y-8">
                    <!-- Job Header -->
                    <div class="border-b border-slate-200 pb-8">
                        <div class="flex justify-between items-start gap-4 mb-6">
                            <div class="flex-1">
                                <p class="text-sm font-bold text-slate-400 uppercase mb-2">{{ job.company?.name || 'Company' }}</p>
                                <h1 class="text-4xl font-black text-slate-900 mb-2">{{ job.title }}</h1>
                                <p class="text-lg text-slate-600">{{ job.location }}</p>
                            </div>
                            <div class="w-16 h-16 rounded-full bg-yellow-100 flex items-center justify-center text-lg font-bold text-slate-700 shrink-0">
                                {{ getInitials(job.company?.name) }}
                            </div>
                        </div>

                        <!-- Job Details -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="bg-slate-50 rounded-lg p-4">
                                <p class="text-xs font-bold text-slate-500 uppercase mb-1">Salary</p>
                                <p class="text-lg font-bold text-slate-900">{{ formatCurrency(job.salary) }}</p>
                            </div>
                            <div class="bg-slate-50 rounded-lg p-4">
                                <p class="text-xs font-bold text-slate-500 uppercase mb-1">Job Type</p>
                                <p class="text-lg font-bold text-slate-900">{{ (job.type || 'Full Time').split('-').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ') }}</p>
                            </div>
                            <div class="bg-slate-50 rounded-lg p-4">
                                <p class="text-xs font-bold text-slate-500 uppercase mb-1">Category</p>
                                <p class="text-lg font-bold text-slate-900">{{ job.category?.name || 'General' }}</p>
                            </div>
                            <div class="bg-slate-50 rounded-lg p-4">
                                <p class="text-xs font-bold text-slate-500 uppercase mb-1">Posted</p>
                                <p class="text-lg font-bold text-slate-900">{{ formatTime(job.created_at) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Job Description -->
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900 mb-4">About This Position</h2>
                        <div class="bg-slate-50 rounded-lg p-6">
                            <p class="text-slate-700 leading-relaxed whitespace-pre-wrap">{{ job.description }}</p>
                        </div>
                    </div>

                    <!-- Application Form -->
                    <div class="bg-slate-50 rounded-lg p-8">
                        <h2 class="text-2xl font-bold text-slate-900 mb-6">Submit Your Application</h2>

                        <!-- Success Message -->
                        <div v-if="successMessage" class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 text-green-700">
                            ✓ {{ successMessage }}
                        </div>

                        <!-- Error Message -->
                        <div v-if="error" class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4 text-red-700">
                            {{ error }}
                        </div>

                        <form @submit.prevent="submitApplication" class="space-y-6">
                            <!-- Cover Letter -->
                            <div>
                                <label class="block text-sm font-bold text-slate-900 mb-2">
                                    Cover Letter
                                </label>
                                <textarea
                                    v-model="form.cover_letter"
                                    placeholder="Tell us why you're interested in this position and what makes you a great fit..."
                                    rows="8"
                                    class="w-full px-4 py-3 border-2 border-slate-200 rounded-lg focus:border-yellow-500 focus:outline-none resize-none text-slate-900"
                                ></textarea>
                                <p class="mt-2 text-xs text-slate-500">
                                    {{ form.cover_letter.length }} / 500 characters
                                </p>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex gap-4">
                                <button
                                    type="button"
                                    @click="$router.back()"
                                    class="flex-1 px-6 py-3 border-2 border-slate-200 rounded-lg font-bold text-slate-700 hover:bg-slate-100 transition"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    :disabled="submitting || !form.cover_letter.trim()"
                                    class="flex-1 px-6 py-3 bg-slate-900 text-white rounded-lg font-bold hover:bg-black transition disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    {{ submitting ? 'Submitting...' : 'Submit Application' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </HomeLayout>
</template>
