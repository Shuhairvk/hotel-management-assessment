import { createRouter, createWebHistory } from 'vue-router';
import { can, user } from './api';
import Layout from './components/Layout.vue';
import Login from './pages/Login.vue';
import Bookings from './pages/Bookings.vue';
import Rooms from './pages/Rooms.vue';
import Guests from './pages/Guests.vue';
import Staff from './pages/Staff.vue';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/login', component: Login },
        {
            path: '/', component: Layout, children: [
                { path: '', component: Bookings, meta: { perm: 'bookings' } },
                { path: 'rooms', component: Rooms, meta: { perm: 'rooms' } },
                { path: 'guests', component: Guests, meta: { perm: 'guests' } },
                { path: 'staff', component: Staff, meta: { perm: 'admin' } },
            ],
        },
    ],
});

router.beforeEach((to) => {
    const loggedIn = !!localStorage.getItem('token');
    if (to.path === '/login') return loggedIn ? '/' : true;
    if (!loggedIn) return '/login';
    const perm = to.meta.perm;
    if (perm && !can(perm)) {
        // send staff to the first module they can use
        const first = [['bookings', '/'], ['rooms', '/rooms'], ['guests', '/guests']].find(([m]) => can(m));
        return first && first[1] !== to.path ? first[1] : false;
    }
    return true;
});

export default router;
