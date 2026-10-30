<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import api, { errMsg } from '../api';

const router = useRouter();
const f = reactive({ role: 'admin', email: '', password: '' });
const err = ref('');

async function submit() {
    err.value = '';
    try {
        const { data } = await api.post('/login', f);
        localStorage.setItem('token', data.token);
        localStorage.setItem('user', JSON.stringify(data.user));
        router.push('/');
    } catch (e) { err.value = errMsg(e); }
}
</script>

<template>
    <div class="login">
        <form class="card" @submit.prevent="submit">
            <h2>Appcom Hotel Management</h2>
            <p>Sign in to access your dashboard</p>
            <label>Login Role</label>
            <div class="tabs">
                <button type="button" :class="{ on: f.role === 'admin' }" @click="f.role = 'admin'">Admin</button>
                <button type="button" :class="{ on: f.role === 'staff' }" @click="f.role = 'staff'">Staff</button>
            </div>
            <label>Email</label>
            <input v-model="f.email" type="email" placeholder="admin@appcom.com" required>
            <label>Password</label>
            <input v-model="f.password" type="password" required>
            <p class="error" v-if="err">{{ err }}</p>
            <button class="btn" type="submit">Sign In</button>
        </form>
    </div>
</template>
