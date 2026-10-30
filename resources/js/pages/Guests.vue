<script setup>
import { onMounted, reactive, ref } from 'vue';
import api, { errMsg } from '../api';
import Pager from '../components/Pager.vue';
import Modal from '../components/Modal.vue';

const rows = ref([]), meta = ref({}), search = ref('');
const show = ref(false), editId = ref(null), err = ref(''), file = ref(null);
const form = reactive({ name: '', email: '', phone: '', nationality: '' });

async function load(page = 1) {
    const { data } = await api.get('/guests', { params: { search: search.value, page } });
    rows.value = data.data; meta.value = data;
}
function openForm(g = null) {
    editId.value = g?.id || null; file.value = null; err.value = '';
    Object.assign(form, { name: g?.name || '', email: g?.email || '', phone: g?.phone || '', nationality: g?.nationality || '' });
    show.value = true;
}
async function save() {
    const fd = new FormData();
    Object.entries(form).forEach(([k, v]) => fd.append(k, v));
    if (file.value) fd.append('photo', file.value);
    try {
        if (editId.value) { fd.append('_method', 'PUT'); await api.post('/guests/' + editId.value, fd); }
        else await api.post('/guests', fd);
        show.value = false; load(meta.value.current_page || 1);
    } catch (e) { err.value = errMsg(e); }
}
async function remove(g) {
    if (!confirm('Delete ' + g.name + '?')) return;
    await api.delete('/guests/' + g.id);
    load(rows.value.length === 1 && meta.value.current_page > 1 ? meta.value.current_page - 1 : meta.value.current_page);
}
onMounted(() => load());
</script>

<template>
    <div class="top"><h1>Guests Directory</h1><button class="btn" @click="openForm()">+ Add Guest</button></div>
    <div class="stats"><div class="stat"><span>TOTAL REGISTERED GUESTS</span><b>{{ meta.total || 0 }}</b></div></div>
    <div class="panel">
        <form class="filters" @submit.prevent="load()">
            <input v-model="search" placeholder="Search guest by name, email, phone or nationality...">
            <button class="btn">Search Guests</button>
        </form>
        <table>
            <thead><tr><th>PHOTO</th><th>GUEST NAME</th><th>EMAIL</th><th>PHONE</th><th>NATIONALITY</th><th>ACTIONS</th></tr></thead>
            <tbody>
                <tr v-for="g in rows" :key="g.id">
                    <td><img v-if="g.photo_url" :src="g.photo_url" class="avatar"><span v-else class="avatar">{{ g.name[0] }}</span></td>
                    <td><b>{{ g.name }}</b></td><td>{{ g.email }}</td><td>{{ g.phone }}</td><td>{{ g.nationality }}</td>
                    <td><button class="icon" @click="openForm(g)">Edit</button> <button class="icon" @click="remove(g)">Delete</button></td>
                </tr>
                <tr v-if="!rows.length"><td colspan="6">No guests found.</td></tr>
            </tbody>
        </table>
        <Pager :meta="meta" @page="load" />
    </div>
    <Modal v-if="show" :title="editId ? 'Edit Guest' : 'Add Guest'" @close="show = false">
        <form @submit.prevent="save">
            <input v-model="form.name" placeholder="Full name" required>
            <input v-model="form.email" type="email" placeholder="Email" required>
            <input v-model="form.phone" placeholder="Phone" required>
            <input v-model="form.nationality" placeholder="Nationality" required>
            <input type="file" accept="image/*" @change="file = $event.target.files[0]">
            <p class="error" v-if="err">{{ err }}</p>
            <button class="btn">Save</button>
        </form>
    </Modal>
</template>
