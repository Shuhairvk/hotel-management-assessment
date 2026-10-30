<script setup>
import { onMounted, reactive, ref } from 'vue';
import api, { errMsg } from '../api';
import Pager from '../components/Pager.vue';
import Modal from '../components/Modal.vue';

const rows = ref([]), meta = ref({}), stats = ref({}), opts = ref({ guests: [], rooms: [] });
const f = reactive({ search: '', status: '', type: '', date_from: '', date_to: '' });
const show = ref(false), editId = ref(null), err = ref('');
const form = reactive({ guest_id: '', room_id: '', check_in: '', check_out: '', status: 'confirmed' });
const money = (n) => '$' + Number(n || 0).toLocaleString(undefined, { minimumFractionDigits: 2 });

async function load(page = 1) {
    const { data } = await api.get('/bookings', { params: { ...f, page } });
    rows.value = data.data; meta.value = data;
    api.get('/bookings/stats').then((r) => (stats.value = r.data));
}
function reset() { Object.assign(f, { search: '', status: '', type: '', date_from: '', date_to: '' }); load(); }
function openForm(b = null) {
    editId.value = b?.id || null; err.value = '';
    Object.assign(form, { guest_id: b?.guest_id || '', room_id: b?.room_id || '', check_in: b?.check_in || '', check_out: b?.check_out || '', status: b?.status || 'confirmed' });
    show.value = true;
}
async function save() {
    try {
        if (editId.value) await api.put('/bookings/' + editId.value, form);
        else await api.post('/bookings', form);
        show.value = false; load(meta.value.current_page || 1);
    } catch (e) { err.value = errMsg(e); }
}
async function remove(b) {
    if (!confirm('Delete booking #BK-' + b.id + '?')) return;
    await api.delete('/bookings/' + b.id);
    load(rows.value.length === 1 && meta.value.current_page > 1 ? meta.value.current_page - 1 : meta.value.current_page);
}
onMounted(async () => {
    opts.value = (await api.get('/bookings/options')).data;
    load();
});
</script>

<template>
    <div class="top"><h1>Dashboard Overview</h1><button class="btn" @click="openForm()">+ New Booking</button></div>
    <div class="stats">
        <div class="stat"><span>TOTAL BOOKINGS</span><b>{{ stats.total }}</b></div>
        <div class="stat"><span>OCCUPANCY RATE</span><b>{{ stats.occupancy }}%</b></div>
        <div class="stat"><span>ACTIVE GUESTS</span><b>{{ stats.active_guests }}</b></div>
        <div class="stat"><span>TOTAL REVENUE</span><b>{{ money(stats.revenue) }}</b></div>
    </div>
    <div class="panel">
        <h2>Bookings Management</h2>
        <form class="filters" @submit.prevent="load()">
            <input v-model="f.search" placeholder="Name, email, or phone...">
            <select v-model="f.status"><option value="">All Statuses</option><option value="confirmed">Confirmed</option><option value="checked-in">Checked-in</option><option value="checked-out">Checked-out</option><option value="cancelled">Cancelled</option></select>
            <select v-model="f.type"><option value="">All Types</option><option>Standard</option><option>Deluxe</option><option>Suite</option></select>
            <input v-model="f.date_from" type="date"><input v-model="f.date_to" type="date">
            <button class="btn">Filter</button>
            <button class="btn ghost" type="button" @click="reset">Reset</button>
        </form>
        <table>
            <thead><tr><th>BOOKING ID</th><th>GUEST</th><th>ROOM</th><th>CHECK-IN</th><th>CHECK-OUT</th><th>STATUS</th><th>TOTAL</th><th>ACTIONS</th></tr></thead>
            <tbody>
                <tr v-for="b in rows" :key="b.id">
                    <td><b>#BK-{{ b.id }}</b></td>
                    <td><b>{{ b.guest?.name }}</b><br><small>{{ b.guest?.email }}</small></td>
                    <td>Room {{ b.room?.room_number }}<br><span class="tag">{{ b.room?.type }}</span></td>
                    <td>{{ b.check_in }}</td><td>{{ b.check_out }}</td>
                    <td><span class="pill" :class="b.status">{{ b.status }}</span></td>
                    <td><b>{{ money(b.total_amount) }}</b></td>
                    <td><button class="icon" @click="openForm(b)">Edit</button> <button class="icon" @click="remove(b)">Delete</button></td>
                </tr>
                <tr v-if="!rows.length"><td colspan="8">No bookings found.</td></tr>
            </tbody>
        </table>
        <Pager :meta="meta" @page="load" />
    </div>
    <Modal v-if="show" :title="editId ? 'Edit Booking' : 'New Booking'" @close="show = false">
        <form @submit.prevent="save">
            <select v-model="form.guest_id" required><option value="">Select guest</option><option v-for="g in opts.guests" :key="g.id" :value="g.id">{{ g.name }}</option></select>
            <select v-model="form.room_id" required><option value="">Select room</option><option v-for="r in opts.rooms" :key="r.id" :value="r.id">Room {{ r.room_number }} ({{ r.type }})</option></select>
            <input v-model="form.check_in" type="date" required>
            <input v-model="form.check_out" type="date" required>
            <select v-model="form.status"><option value="confirmed">Confirmed</option><option value="checked-in">Checked-in</option><option value="checked-out">Checked-out</option><option value="cancelled">Cancelled</option></select>
            <p class="error" v-if="err">{{ err }}</p>
            <button class="btn">Save</button>
        </form>
    </Modal>
</template>
