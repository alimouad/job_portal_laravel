import { createRouter, createWebHistory } from 'vue-router';
import axiosClient from '@/axios.js';
// import HomeView from '@/views/HomeView.vue';
// import JobsView from '@/views/JobsView.vue';
// import NotFoundView from '@/views/NotFound.vue';
import Login from '@/pages/auth/Login.vue';  
import Register from '@/pages/auth/Register.vue';
import Home from '@/pages/Home.vue';
import CompaniesIndex from '@/pages/user/companies.vue';
import UserJobs from '@/pages/user/jobs.vue';
import ApplyJob from '@/pages/user/ApplyJob.vue';
import Favourites from '@/pages/user/Favourites.vue';
import MyCandidatures from '@/pages/user/MyCandidatures.vue';
import EmployerCompany from '@/pages/employer/Company.vue';
import EmployerHome from '@/pages/employer/Home.vue';
import EmployerPostJob from '@/pages/employer/PostJob.vue';


const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/home',
            name: 'home',
            component: Home,
        },
        {
            path: '/jobs',
            name: 'jobs',
            component: UserJobs,
        },
        {
            path: '/jobs/:id/apply',
            name: 'apply-job',
            component: ApplyJob,
        },
        {
            path: '/favourites',
            name: 'favourites',
            component: Favourites,
        },
        {
            path: '/my-candidatures',
            name: 'my-candidatures',
            component: MyCandidatures,
        },
        {
            path: '/companies',
            name: 'companies',
            component: CompaniesIndex,
        },
        {
            path:'/login',
            name: 'login',
            component: Login,
        },
        {
            path:'/register',
            name: 'register',
            component: Register,
        },
        {
            path:'/employer/home',
            name: 'employer-home',
            component: EmployerHome,
        },
        {
            path:'/employer/company',
            name: 'employer-company',
            component: EmployerCompany,
        },
        {
            path:'/employer/jobs/create',
            name: 'employer-post-job',
            component: EmployerPostJob,
        },
    ],
});

// router.beforeEach(async (to) => {
//   const isAdminRoute = to.path.startsWith('/admin');

//   if (!isAdminRoute) {
//     return true;
//   }

//   try {
//     const response = await axiosClient.get('/user');
//     const role = response.data?.role ?? 'USER';

//     if (role !== 'ADMIN') {
//       return { name: 'home' };
//     }
//     return true;
//   } catch {
//     return { name: 'login' };
//   }
// });

export default router;