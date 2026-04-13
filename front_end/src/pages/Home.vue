<script setup>
import HomeLayout from '@/layouts/HomeLayout.vue';
const tags = [
  'Project Manager',
  'Safety Officer',
  'Inspector',
  'Foreman',
  'Designer',
  'Metalurgist',
  'Welding Specialist',
  'Supervisor',
  'Estimator',
  'Writing Engineer',
  'Technician',
];

const categories = [
  { name: 'Category (50)' },
  { name: 'Project Manager (20)' },
  { name: 'Safety Officer (34)' },
  { name: 'Foreman (14)' },
  { name: 'Inspector (26)' },
  { name: 'Welding Engineer (10)' },
  { name: 'Supervisor (18)' },
];

const openings = [
  {
    brand: 'Telegram',
    title: 'Senior Welding Inspector',
    salary: '$3500 - 10000',
    time: '3-5 years',
    vacancy: '20 Vacancy',
    place: 'San Francisco',
    type: 'Full Time',
    posted: 'Applied',
    accent: 'bg-sky-500',
  },
  {
    brand: 'Webflow',
    title: 'Project Manager',
    salary: '$3500 - 11000',
    time: '1-3 years',
    vacancy: '30 Vacancy',
    place: 'Sydney',
    type: 'Full Time',
    posted: '20 hours ago',
    accent: 'bg-blue-700',
  },
  {
    brand: 'Youtube',
    title: 'Designer',
    salary: '$4500 - 11000',
    time: '2-4 years',
    vacancy: '16 Vacancy',
    place: 'San Francisco',
    type: 'Contractual',
    posted: '1 day ago',
    accent: 'bg-red-500',
  },
  {
    brand: 'Verizon',
    title: 'Assistant Supervisor',
    salary: '$4000 - 10000',
    time: '1-3 years',
    vacancy: '18 Vacancy',
    place: 'San Francisco',
    type: 'Part Time',
    posted: '1 day ago',
    accent: 'bg-gray-800',
  },
  {
    brand: 'Zeplin',
    title: 'Inspector',
    salary: '$4000 - 10000',
    time: '3-5 years',
    vacancy: '10 Vacancy',
    place: 'San Francisco',
    type: 'Full Time',
    posted: '3 days ago',
    accent: 'bg-amber-500',
  },
];

const partners = ['Boltshift', 'Lightbox', 'FeatherDev', 'Spherule', 'GlobalBank', 'Nietzsche'];

const roleCards = [
  'Welding Industries',
  'Construction',
  'Manufacturing',
  'Plumbing Work',
  'Electrical Work',
  'Metal Welding',
];

const workSteps = [
  { title: 'Create Account', text: 'Open your account quickly and begin your journey.' },
  { title: 'Complete Your Profile', text: 'Add skills and details to attract employers.' },
  { title: 'Apply Job & Get Hire', text: 'Apply to matching jobs and get hired faster.' },
];
</script>

<template>

    <HomeLayout>

     <section class="relative pt-16 pb-20 overflow-hidden">
      <div class="mx-auto max-w-6xl px-4 md:px-8 text-center relative z-10">
        <div class="inline-block px-4 py-1.5 bg-white border border-[#e4dcc4] rounded-full mb-8 shadow-sm">
          <span class="text-[10px] font-black uppercase tracking-[0.2em] text-[#8b7322]">
            <i class="pi pi-sparkles mr-1"></i> Join 50,000+ Professionals
          </span>
        </div>

        <h1 class="text-5xl md:text-8xl font-[900] leading-[1.05] tracking-tight mb-8">
          Find the job that <br />
          <span class="italic text-[#8b7322] relative">
            shines your life
            <svg class="absolute -bottom-2 left-0 w-full h-3 text-[#ffcd1f]/40 -z-10" viewBox="0 0 100 10" preserveAspectRatio="none"><path d="M0 5 Q 25 0 50 5 T 100 5" stroke="currentColor" stroke-width="8" fill="none"/></svg>
          </span>
        </h1>

        <p class="mx-auto max-w-2xl text-base md:text-xl text-[#6b6b6b] leading-relaxed mb-12">
          The compliant, blazing-fast way to discover, hire and manage freelance talent. 
          Your dream career is just one search away.
        </p>

        <div class="mx-auto max-w-2xl flex items-center bg-white rounded-full p-2 shadow-[0_25px_50px_-12px_rgba(228,220,196,0.6)] border border-[#e4dcc4] focus-within:border-[#ffcd1f] transition-all">
          <div class="pl-4 pr-2 text-[#ffcd1f]">
            <i class="pi pi-search text-lg"></i>
          </div>
          <input type="text" placeholder="Job title, keywords, or company..." class="flex-1 bg-transparent py-3 outline-none text-sm placeholder:text-[#989898]" />
          <button class="bg-black text-white px-8 py-3.5 rounded-full font-black text-xs uppercase tracking-widest hover:bg-[#ffcd1f] hover:text-black transition-colors">
            Find Jobs
          </button>
        </div>
      </div>
    </section>

    <div class="mx-auto max-w-6xl px-4 md:px-8 -mt-10 mb-24 grid grid-cols-2 md:grid-cols-4 gap-4">
      <div v-for="stat in stats" :key="stat.label" class="bg-white border border-[#e9e1c8] p-6 rounded-3xl text-center shadow-sm">
        <div class="text-2xl font-black text-black">{{ stat.value }}</div>
        <div class="text-[10px] uppercase font-bold text-[#8b7322] tracking-widest mt-1">{{ stat.label }}</div>
      </div>
    </div>



      <section class="max-w-6xl mx-auto px-4 md:px-8 py-20">
        <div class="flex justify-between items-end mb-12">
          <div>
            <h2 class="text-3xl md:text-5xl font-black">Popular Categories</h2>
            <p class="text-[#6b6b6b] mt-2">Explore jobs by your area of expertise</p>
          </div>
          <button class="hidden md:flex items-center gap-2 text-xs font-black uppercase tracking-widest text-[#8b7322] hover:text-black">
            View All <i class="pi pi-arrow-right"></i>
          </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          <div v-for="cat in categories" :key="cat.name" class="bg-white border border-[#e9e1c8] p-8 rounded-[32px] hover:shadow-xl hover:-translate-y-1 transition-all cursor-pointer group">
            <div class="w-12 h-12 bg-[#f7efd8] rounded-2xl flex items-center justify-center mb-6 group-hover:bg-[#ffcd1f] transition-colors">
              <i :class="cat.icon" class="text-xl"></i>
            </div>
            <h4 class="text-lg font-black mb-1">{{ cat.name }}</h4>
            <p class="text-[10px] font-bold text-[#8b7322]">{{ cat.count }} Jobs Available</p>
          </div>
        </div>
      </section>

      <section class="bg-[#f7efd8]/40 py-24 border-y border-[#e9e1c8]">
        <div class="max-w-6xl mx-auto px-4 md:px-8">
          <div class="text-center mb-16">
            <h2 class="text-3xl md:text-5xl font-black mb-4">Latest Job Openings</h2>
            <p class="text-[#6b6b6b]">Apply to the most recent positions from top companies</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div v-for="i in 4" :key="i" class="bg-white p-6 rounded-[32px] border border-[#e9e1c8] flex items-center justify-between hover:shadow-md transition shadow-sm">
              <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-slate-100 rounded-2xl"></div>
                <div>
                  <h5 class="font-black text-lg">Senior Vue.js Developer</h5>
                  <p class="text-xs text-[#8b7322] font-bold">TechCorp • Remote • $80k - $120k</p>
                </div>
              </div>
              <button class="bg-[#fcf8ed] border border-[#e9e1c8] px-5 py-2 rounded-full text-[10px] font-black uppercase hover:bg-[#ffcd1f] hover:border-[#ffcd1f] transition">
                Apply
              </button>
            </div>
          </div>
        </div>
      </section>

      <section class="mx-auto mt-10 grid w-full max-w-6xl gap-6 px-4 md:grid-cols-[260px_1fr] md:px-8">
        <aside class="rounded-2xl border border-[#ece7d7] bg-white p-5">
          <h3 class="text-xl font-extrabold">All Filter</h3>

          <div class="mt-6 border-b border-[#f0ecd9] pb-4">
            <p class="text-sm font-bold">Category</p>
            <div class="mt-3 space-y-2">
              <label v-for="category in categories" :key="category.name" class="flex items-center gap-2 text-xs text-[#565656]">
                <input type="checkbox" class="h-3.5 w-3.5 rounded border-[#d0d0d0]" />
                {{ category.name }}
              </label>
            </div>
            <button class="mt-4 rounded-full bg-[#222] px-3 py-1.5 text-[11px] font-semibold text-white">View more</button>
          </div>

          <div class="space-y-4 pt-4 text-sm font-semibold text-[#2f2f2f]">
            <div class="flex items-center justify-between"><span>Location</span><span>+</span></div>
            <div class="flex items-center justify-between"><span>Job Type</span><span>+</span></div>
            <div class="flex items-center justify-between"><span>Experience level</span><span>+</span></div>
            <div class="flex items-center justify-between"><span>Salary Range</span><span>+</span></div>
          </div>
        </aside>

        <div>
          <div class="mb-4 flex items-end justify-between">
            <h2 class="text-2xl font-extrabold md:text-4xl">Browse our latest job opening</h2>
          </div>

          <div class="space-y-3">
            <article v-for="job in openings" :key="`${job.brand}-${job.title}`" class="rounded-2xl border border-[#ebe7d9] bg-white p-4 shadow-sm">
              <div class="flex items-start justify-between gap-3">
                <div>
                  <p class="text-xs font-semibold text-[#808080]">{{ job.brand }}</p>
                  <h3 class="text-lg font-extrabold leading-tight">{{ job.title }}</h3>
                </div>
                <span :class="job.accent" class="h-5 w-5 rounded-full"></span>
              </div>

              <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs text-[#666]">
                <span>{{ job.salary }}</span>
                <span>{{ job.time }}</span>
                <span>{{ job.vacancy }}</span>
                <span>{{ job.place }}</span>
              </div>

              <div class="mt-4 flex flex-wrap items-center justify-between gap-2">
                <div class="flex gap-2">
                  <button class="rounded-full border border-[#d9d4c3] px-4 py-1.5 text-xs font-semibold">View Job</button>
                  <button class="rounded-full bg-[#111] px-4 py-1.5 text-xs font-semibold text-white">Apply Job</button>
                </div>
                <div class="text-xs font-semibold text-[#666]">{{ job.type }} | {{ job.posted }}</div>
              </div>
            </article>
          </div>

          <button class="mt-5 rounded-full border border-[#ded9c9] bg-white px-4 py-2 text-xs font-semibold">View more jobs</button>
        </div>
      </section>

      <section class="mt-14 bg-white py-10">
        <div class="mx-auto w-full max-w-6xl px-4 text-center md:px-8">
          <h3 class="text-2xl font-extrabold">Trusted 2k+ company find best jobseekers</h3>
          <div class="mt-6 flex flex-wrap items-center justify-center gap-6 text-sm font-bold text-[#444]">
            <span v-for="partner in partners" :key="partner" class="opacity-85">{{ partner }}</span>
          </div>
        </div>
      </section>

      <section class="mx-auto mt-12 w-full max-w-6xl px-4 md:px-8">
        <div class="grid gap-5 rounded-3xl bg-[#f9edb9] p-6 md:grid-cols-[1fr_1fr] md:p-10">
          <div class="flex items-center">
            <div>
              <h3 class="text-3xl font-extrabold leading-tight">Discover jobs across popular roles</h3>
              <p class="mt-3 max-w-md text-sm text-[#555]">Select roles and we will show jobs that fit your profile and goals.</p>
            </div>
          </div>
          <div class="rounded-2xl bg-white p-5 shadow-lg">
            <div class="grid grid-cols-2 gap-3">
              <div v-for="role in roleCards" :key="role" class="rounded-xl border border-[#efead8] p-3">
                <p class="text-xs font-bold">{{ role }}</p>
                <p class="mt-1 text-[11px] text-[#666]">12k+ jobs</p>
              </div>
            </div>
          </div>
        </div>
      </section>

     

      <section class="mx-auto mt-12 w-full max-w-6xl px-4 md:px-8">
        <div class="overflow-hidden rounded-3xl bg-[linear-gradient(120deg,#bfe8f3_0%,#e5e6a2_100%)] p-10 text-center">
          <h3 class="text-4xl font-extrabold">Find great talent for your company.</h3>
          <p class="mx-auto mt-3 max-w-xl text-sm text-[#444]">Find exceptional candidates with ease on our streamlined job posting platform.</p>
          <button class="mt-5 rounded-full bg-[#101010] px-5 py-2 text-xs font-bold text-white">Post your job</button>
        </div>
      </section>

      <section class="mx-auto mt-12 grid w-full max-w-6xl gap-6 px-4 md:grid-cols-[1fr_1fr] md:px-8">
        <div class="rounded-3xl bg-white p-8">
          <h3 class="text-4xl font-extrabold leading-tight">Find your dream job and get hire</h3>
          <p class="mt-3 text-sm text-[#555]">Discover and explore opportunities that match your skills and ambitions.</p>
          <div class="mt-6 grid gap-3 text-xs text-[#666] md:grid-cols-3">
            <p>Access specialized talent and services across industries.</p>
            <p>Tailored workflows for organized recruitment.</p>
            <p>Connect with leading companies and preferred roles.</p>
          </div>
          <button class="mt-6 rounded-full bg-[#ffcd1f] px-5 py-2 text-xs font-extrabold">Find job & get hire</button>
        </div>

        <div class="overflow-hidden rounded-3xl border border-[#e9e3d2] bg-[linear-gradient(145deg,#dbd2bf,#9e957f)]">
          <div class="h-full min-h-[260px] bg-[radial-gradient(circle_at_70%_20%,rgba(255,255,255,0.5),rgba(255,255,255,0.1)_40%,transparent_60%)]"></div>
        </div>
      </section>

      <section class="mt-14 bg-[#f7efd8] py-14">
        <div class="mx-auto w-full max-w-6xl px-4 md:px-8">
          <h3 class="text-center text-4xl font-extrabold">How it works?</h3>
          <div class="mt-8 grid gap-4 md:grid-cols-3">
            <article v-for="step in workSteps" :key="step.title" class="rounded-2xl bg-white p-6 text-center">
              <div class="mx-auto grid h-10 w-10 place-items-center rounded-full bg-[#ffad17] text-white">●</div>
              <h4 class="mt-4 text-lg font-extrabold">{{ step.title }}</h4>
              <p class="mt-2 text-sm text-[#666]">{{ step.text }}</p>
            </article>
          </div>
        </div>
      </section>



  </HomeLayout>
</template>


