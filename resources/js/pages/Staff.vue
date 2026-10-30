<script setup>
import { onMounted, reactive, ref } from 'vue';
import api, { errMsg } from '../api';
import Pager from '../components/Pager.vue';
import Modal from '../components/Modal.vue';

const rows = ref([]), meta = ref({});
const show = ref(false), editId = ref(null), err = ref('');
const modules = ['bookings', 'rooms', 'guests'];
const form = reactive({ name: '', email: '', password: '', permissions: [], is_active: true });

async function load(page = 1) {
    const { data } = await api.get('/staff', { params: { page } });
    rows.value = data.data; meta.value = data;
}
function openForm(s = null) {
    editId.value = s?.id || null; err.value = '';
    Object.assign(form, { name: s?.name || '', email: s?.email || '', password: '', permissions: [...(s?.permissions || [])], is_active: s ? s.is_active : true });
    show.value = true;
}
async function save() {
    try {
        if (editId.value) await api.put('/staff/' + editId.value, form);
        else await api.post('/staff', form);
        show.value = false; load(meta.value.current_page || 1);
    } catch (e) { err.value = errMsg(e); }
}
async function remove(s) {
    if (!confirm('Delete ' + s.name + '?')) return;
    await api.delete('/staff/' + s.id);
    load(rows.value.length === 1 && meta.value.current_page > 1 ? meta.value.current_page - 1 : meta.value.current_page);
}
onMounted(() => load());
</script>

<template>
    <div class="top"><h1>Staff Accounts &amp; Permissions</h1><button class="btn" @click="openForm()">+ Create Staff Account</button></div>
    <div class="panel">
        <table>
            <thead><tr><th>STAFF NAME</th><th>EMAIL</th><th>GRANTED ACCESS MODULES</th><th>STATUS</th><th>ACTIONS</th></tr></thead>
            <tbody>
                <tr v-for="s in rows" :key="s.id">
                    <td><b>{{ s.name }}</b></td><td>{{ s.email }}</td>
                    <td><span class="tag" v-for="p in s.permissions" :key="p">{{ p }}</span></td>
                    <td><span class="pill" :class="s.is_active ? 'available' : 'cancelled'">{{ s.is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td><button class="icon" @click="openForm(s)">Edit</button> <button class="icon" @click="remove(s)">Delete</button></td>
                </tr>
                <tr v-if="!rows.length"><td colspan="5">No staff yet.</td></tr>
            </tbody>
        </table>
        <Pager :meta="meta" @page="load" />
    </div>
    <Modal v-if="show" :title="editId ? 'Edit Staff' : 'Create Staff Account'" @close="show = false">
        <form @submit.prevent="save">
            <input v-model="form.name" placeholder="Name" required>
            <input v-model="form.email" type="email" placeholder="Email" required>
            <input v-model="form.password" type="password" :placeholder="editId ? 'New password (leave blank to keep)' : 'Password'" :required="!editId">
            <div class="checks"><label v-for="m in modules" :key="m"><input type="checkbox" :value="m" v-model="form.permissions"> {{ m }}</label></div>
            <label><input type="checkbox" v-model="form.is_active"> Active</label>
            <p class="error" v-if="err">{{ err }}</p>
            <button class="btn">Save</button>
        </form>
    </Modal>
</template>
