<script setup>
import { onMounted, reactive, ref } from 'vue';
import api, { errMsg } from '../api';
import Pager from '../components/Pager.vue';
import Modal from '../components/Modal.vue';

const rows = ref([]), meta = ref({});
const f = reactive({ search: '', type: '', status: '' });
const show = ref(false), editId = ref(null), err = ref('');
const form = reactive({ room_number: '', type: 'Standard', price_per_night: '', status: 'available' });

async function load(page = 1) {
    const { data } = await api.get('/rooms', { params: { ...f, page } });
    rows.value = data.data; meta.value = data;
}
function openForm(r = null) {
    editId.value = r?.id || null; err.value = '';
    Object.assign(form, { room_number: r?.room_number || '', type: r?.type || 'Standard', price_per_night: r?.price_per_night || '', status: r?.status || 'available' });
    show.value = true;
}
async function save() {
    try {
        if (editId.value) await api.put('/rooms/' + editId.value, form);
        else await api.post('/rooms', form);
        show.value = false; load(meta.value.current_page || 1);
    } catch (e) { err.value = errMsg(e); }
}
async function remove(r) {
    if (!confirm('Delete room ' + r.room_number + '?')) return;
    await api.delete('/rooms/' + r.id);
    load(rows.value.length === 1 && meta.value.current_page > 1 ? meta.value.current_page - 1 : meta.value.current_page);
}
onMounted(() => load());
</script>

<template>
    <div class="top"><h1>Rooms Management</h1><button class="btn" @click="openForm()">+ Add Room</button></div>
    <div class="stats"><div class="stat"><span>TOTAL ROOMS</span><b>{{ meta.total || 0 }}</b></div></div>
    <div class="panel">
        <form class="filters" @submit.prevent="load()">
            <input v-model="f.search" placeholder="Search room number...">
            <select v-model="f.type"><option value="">All Types</option><option>Standard</option><option>Deluxe</option><option>Suite</option></select>
            <select v-model="f.status"><option value="">All Statuses</option><option value="available">Available</option><option value="booked">Booked</option></select>
            <button class="btn">Filter</button>
        </form>
        <table>
            <thead><tr><th>ROOM NUMBER</th><th>TYPE</th><th>PRICE PER NIGHT</th><th>STATUS</th><th>ACTIONS</th></tr></thead>
            <tbody>
                <tr v-for="r in rows" :key="r.id">
                    <td><b>Room {{ r.room_number }}</b></td><td><span class="tag">{{ r.type }}</span></td>
                    <td>${{ Number(r.price_per_night).toFixed(2) }}</td>
                    <td><span class="pill" :class="r.status">{{ r.status }}</span></td>
                    <td><button class="icon" @click="openForm(r)">Edit</button> <button class="icon" @click="remove(r)">Delete</button></td>
                </tr>
                <tr v-if="!rows.length"><td colspan="5">No rooms found.</td></tr>
            </tbody>
        </table>
        <Pager :meta="meta" @page="load" />
    </div>
    <Modal v-if="show" :title="editId ? 'Edit Room' : 'Add Room'" @close="show = false">
        <form @submit.prevent="save">
            <input v-model="form.room_number" placeholder="Room number" required>
            <select v-model="form.type"><option>Standard</option><option>Deluxe</option><option>Suite</option></select>
            <input v-model="form.price_per_night" type="number" step="0.01" min="0" placeholder="Price per night" required>
            <select v-model="form.status"><option value="available">Available</option><option value="booked">Booked</option></select>
            <p class="error" v-if="err">{{ err }}</p>
            <button class="btn">Save</button>
        </form>
    </Modal>
</template>
