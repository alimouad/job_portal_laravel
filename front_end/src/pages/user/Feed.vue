<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import HomeLayout from '@/layouts/HomeLayout.vue';
import axiosClient from '@/axios';

const posts = ref([]);
const loading = ref(false);
const submitting = ref(false);
const errorMessage = ref('');
const successMessage = ref('');
const currentPage = ref(1);
const lastPage = ref(1);

const form = reactive({
  title: '',
  content: '',
});

const isAuthenticated = computed(() => Boolean(localStorage.getItem('auth_token')));
const currentUser = computed(() => {
  try {
    return JSON.parse(localStorage.getItem('auth_user') || 'null');
  } catch {
    return null;
  }
});

const canSubmit = computed(() => {
  return isAuthenticated.value && form.title.trim().length >= 3 && form.content.trim().length >= 10;
});

const formatPostedAt = (dateString) => {
  if (!dateString) return 'Recently';

  const created = new Date(dateString);
  const diffHours = Math.floor((Date.now() - created.getTime()) / (1000 * 60 * 60));

  if (diffHours < 1) return 'Just now';
  if (diffHours < 24) return `${diffHours}h ago`;

  const diffDays = Math.floor(diffHours / 24);
  return `${diffDays}d ago`;
};

const loadPosts = async (page = 1) => {
  loading.value = true;
  errorMessage.value = '';

  try {
    const { data } = await axiosClient.get('/blog-posts', { params: { page } });
    posts.value = data?.data ?? [];
    currentPage.value = data?.current_page ?? 1;
    lastPage.value = data?.last_page ?? 1;
  } catch (err) {
    posts.value = [];
    errorMessage.value = err?.response?.data?.message || 'Failed to load feed posts.';
  } finally {
    loading.value = false;
  }
};

const submitPost = async () => {
  if (!canSubmit.value) return;

  submitting.value = true;
  successMessage.value = '';
  errorMessage.value = '';

  try {
    const { data } = await axiosClient.post('/blog-posts', {
      title: form.title.trim(),
      content: form.content.trim(),
    });

    if (currentPage.value === 1 && data?.post) {
      posts.value = [data.post, ...posts.value];
    } else {
      await loadPosts(1);
    }

    form.title = '';
    form.content = '';
    successMessage.value = 'Post published successfully.';
  } catch (err) {
    const validationError = err?.response?.data?.errors
      ? Object.values(err.response.data.errors)?.[0]?.[0]
      : null;

    errorMessage.value = validationError || err?.response?.data?.message || 'Failed to publish post.';
  } finally {
    submitting.value = false;
  }
};

const goToPage = (page) => {
  if (page < 1 || page > lastPage.value || page === currentPage.value) return;
  loadPosts(page);
};

onMounted(() => {
  loadPosts(1);
});
</script>

<template>
  <HomeLayout>
    <section class="min-h-screen bg-[#FFFDF5] relative overflow-hidden pb-24">
      <div class="pointer-events-none absolute inset-0 -z-10">
        <div class="absolute -left-24 top-0 h-[500px] w-[500px] rounded-full bg-yellow-200/20 blur-[120px]"></div>
        <div class="absolute right-0 top-1/2 h-[400px] w-[400px] rounded-full bg-orange-100/30 blur-[100px]"></div>
      </div>

      <div class="mx-auto max-w-6xl px-4 pt-12 md:px-8">
        <header class="mb-12 border-b border-[#E9E1C8] pb-10">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-[#E9E1C8] shadow-sm mb-4">
            <span class="relative flex h-2 w-2">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span>
            </span>
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-[#8B7322]">Knowledge Exchange</span>
          </div>
          <h1 class="text-5xl md:text-6xl font-black tracking-tighter text-[#1A1A1A]">
            Community Feed<span class="text-yellow-500">.</span>
          </h1>
          <p class="mt-4 max-w-2xl text-lg font-medium text-[#626262]">
            Join the conversation. Share career milestones, industry insights, or seeking advice from fellow professionals.
          </p>
        </header>

        <div class="grid gap-10 lg:grid-cols-[380px_1fr]">
          
          <aside class="lg:sticky lg:top-8 h-fit space-y-6">
            <div class="rounded-[2.5rem] border-2 border-transparent bg-white p-8 shadow-[0_20px_50px_-20px_rgba(0,0,0,0.06)] ring-1 ring-black/5">
              <div class="flex items-center gap-4 mb-8">
                <div class="h-12 w-12 rounded-2xl bg-yellow-400 flex items-center justify-center font-black text-white shadow-lg shadow-yellow-200 rotate-3">
                  {{ currentUser?.full_name?.substring(0, 1) || '?' }}
                </div>
                <div>
                  <h2 class="text-lg font-black text-[#1A1A1A]">Create Post</h2>
                  <p class="text-[10px] font-bold text-[#8B7322] uppercase tracking-widest">
                    {{ isAuthenticated ? 'Express yourself' : 'Auth Required' }}
                  </p>
                </div>
              </div>

              <form v-if="isAuthenticated" class="space-y-5" @submit.prevent="submitPost">
                <div class="group">
                  <label class="mb-2 block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 transition-colors group-focus-within:text-yellow-600">Post Headline</label>
                  <input
                    v-model="form.title"
                    type="text"
                    placeholder="Capture attention..."
                    class="w-full rounded-2xl border-2 border-slate-50 bg-slate-50/50 px-5 py-4 text-sm font-bold outline-none transition-all focus:bg-white focus:border-yellow-400 focus:shadow-inner"
                  />
                </div>

                <div class="group">
                  <label class="mb-2 block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 transition-colors group-focus-within:text-yellow-600">Insights & Details</label>
                  <textarea
                    v-model="form.content"
                    rows="5"
                    placeholder="What's on your mind?"
                    class="w-full rounded-2xl border-2 border-slate-50 bg-slate-50/50 px-5 py-4 text-sm font-bold outline-none transition-all focus:bg-white focus:border-yellow-400 focus:shadow-inner resize-none"
                  ></textarea>
                </div>

                <button
                  type="submit"
                  :disabled="!canSubmit || submitting"
                  class="relative group w-full overflow-hidden rounded-2xl bg-[#111] px-6 py-4 text-xs font-black uppercase tracking-[0.2em] text-white transition-all active:scale-[0.98] disabled:opacity-30 shadow-xl shadow-black/10"
                >
                  <div class="absolute inset-0 w-0 bg-yellow-500 transition-all duration-300 group-hover:w-full"></div>
                  <span class="relative z-10 group-hover:text-black">
                    {{ submitting ? 'Publishing...' : 'Broadcast Post' }}
                  </span>
                </button>
              </form>

              <div v-else class="text-center py-6">
                <p class="text-sm font-medium text-slate-500 mb-6">Log in to contribute to the community discussion.</p>
                <RouterLink
                  to="/login"
                  class="inline-block w-full rounded-2xl bg-yellow-500 px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-900 shadow-lg shadow-yellow-100 transition-transform hover:scale-[1.02] active:scale-[0.98]"
                >
                  Log in to Post
                </RouterLink>
              </div>

              <Transition name="fade">
                <p v-if="successMessage" class="mt-4 rounded-xl bg-green-50 p-4 text-xs font-bold text-green-700 border border-green-100">
                  ✨ {{ successMessage }}
                </p>
              </Transition>
            </div>

            <div class="rounded-[2rem] bg-slate-900 p-8 text-white hidden lg:block">
              <p class="text-[10px] font-black uppercase tracking-[0.2em] text-yellow-500 mb-4">Feed Guidelines</p>
              <ul class="space-y-3 text-xs font-medium text-slate-400">
                <li class="flex gap-2"><span>•</span> Be professional and respectful.</li>
                <li class="flex gap-2"><span>•</span> Share value-driven content.</li>
                <li class="flex gap-2"><span>•</span> No spam or self-promotion.</li>
              </ul>
            </div>
          </aside>

          <div class="space-y-8">
            <div v-if="loading" class="space-y-6">
              <div v-for="i in 3" :key="i" class="h-64 animate-pulse rounded-[2.5rem] bg-white border border-slate-100"></div>
            </div>

            <div v-else-if="errorMessage" class="rounded-[2.5rem] border-2 border-red-100 bg-white p-10 text-center">
               <div class="h-12 w-12 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4 text-red-500 font-bold">!</div>
               <p class="text-lg font-black text-slate-900">Communication Error</p>
               <p class="text-sm font-medium text-slate-500">{{ errorMessage }}</p>
            </div>

            <div v-else-if="posts.length === 0" class="rounded-[3rem] border-2 border-dashed border-[#DFD7BF] bg-white/40 p-20 text-center">
              <div class="h-20 w-20 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
                <svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
              </div>
              <h3 class="text-xl font-black text-slate-900">The feed is quiet...</h3>
              <p class="text-sm font-medium text-slate-500">Be the first one to start a professional discussion.</p>
            </div>

            <div v-else class="space-y-6">
              <article
                v-for="post in posts"
                :key="post.id"
                class="group relative rounded-[2.5rem] border-2 border-transparent bg-white p-10 transition-all hover:border-yellow-100 hover:shadow-[0_30px_60px_-20px_rgba(0,0,0,0.06)] shadow-sm"
              >
                <div class="mb-8 flex items-center justify-between gap-4">
                  <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-[18px] bg-[#FFFDF5] border border-slate-100 flex items-center justify-center font-black text-slate-700 shadow-inner group-hover:bg-yellow-50 transition-colors">
                      {{ post.user?.full_name?.substring(0, 1) || 'A' }}
                    </div>
                    <div>
                      <p class="text-base font-black text-[#1A1A1A]">{{ post.user?.full_name || 'Anonymous User' }}</p>
                      <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ post.user?.email || 'verified member' }}</p>
                    </div>
                  </div>
                  <span class="px-4 py-1.5 rounded-full bg-slate-50 text-[10px] font-black uppercase tracking-widest text-slate-400 group-hover:text-yellow-600 transition-colors">
                    {{ formatPostedAt(post.created_at) }}
                  </span>
                </div>

                <h3 class="text-2xl font-black text-[#151515] tracking-tight group-hover:text-yellow-600 transition-colors mb-4">{{ post.title }}</h3>
                <div class="relative">
                  <div class="absolute left-0 top-0 bottom-0 w-1 bg-yellow-50 rounded-full group-hover:bg-yellow-400 transition-colors"></div>
                  <p class="pl-6 whitespace-pre-line text-base leading-relaxed text-[#4D4D4D]">{{ post.content }}</p>
                </div>
                
                <div class="mt-8 flex items-center gap-6 pt-8 border-t border-slate-50">
                   <button class="flex items-center gap-2 text-xs font-black text-slate-400 hover:text-red-500 transition-colors">
                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                     Support
                   </button>
                   <button class="flex items-center gap-2 text-xs font-black text-slate-400 hover:text-blue-500 transition-colors">
                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                     Comment
                   </button>
                </div>
              </article>
            </div>

            <nav v-if="lastPage > 1" class="mt-12 flex items-center justify-center gap-3">
              <button
                class="h-12 w-12 flex items-center justify-center rounded-2xl border-2 border-slate-100 bg-white text-slate-600 transition-all hover:border-yellow-400 hover:text-yellow-600 disabled:opacity-30 disabled:hover:border-slate-100 disabled:hover:text-slate-600"
                :disabled="currentPage === 1"
                @click="goToPage(currentPage - 1)"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
              </button>

              <div class="flex items-center gap-2">
                <button
                  v-for="page in lastPage"
                  :key="page"
                  class="h-12 min-w-12 rounded-2xl text-xs font-black transition-all"
                  :class="page === currentPage ? 'bg-slate-900 text-white shadow-lg' : 'bg-white border-2 border-slate-50 text-slate-400 hover:border-slate-100'"
                  @click="goToPage(page)"
                >
                  {{ page }}
                </button>
              </div>

              <button
                class="h-12 w-12 flex items-center justify-center rounded-2xl border-2 border-slate-100 bg-white text-slate-600 transition-all hover:border-yellow-400 hover:text-yellow-600 disabled:opacity-30"
                :disabled="currentPage === lastPage"
                @click="goToPage(currentPage + 1)"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
              </button>
            </nav>
          </div>
        </div>
      </div>
    </section>
  </HomeLayout>
</template>