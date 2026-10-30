<script setup>
import { useRouter } from 'vue-router';
import api, { can, user } from '../api';

const router = useRouter();
const u = user();

async function logout() {
    try { await api.post('/logout'); } catch (e) {}
    localStorage.clear();
    router.push('/login');
}
</script>

<template>
    <div class="shell">
        <aside>
            <div class="brand">Appcom Hotel<small>Management System</small></div>
            <nav>
                <small>MAIN MENU</small>
                <router-link v-if="can('bookings')" to="/" exact-active-class="on">Bookings</router-link>
                <router-link v-if="can('rooms')" to="/rooms" active-class="on">Rooms</router-link>
                <router-link v-if="can('guests')" to="/guests" active-class="on">Guests</router-link>
                <template v-if="can('admin')">
                    <small>ADMINISTRATION</small>
                    <router-link to="/staff" active-class="on">Staff Users</router-link>
                </template>
            </nav>
        </aside>
        <main>
            <header>
                <span>{{ u?.name }} ({{ u?.role }})</span>
                <button class="btn ghost" @click="logout">Logout</button>
            </header>
            <router-view />
        </main>
    </div>
</template>
