<script setup>
defineProps({ meta: Object });
defineEmits(['page']);
</script>

<template>
    <div class="pager" v-if="meta && meta.total !== undefined">
        <span>Showing {{ meta.from || 0 }}–{{ meta.to || 0 }} of {{ meta.total }}</span>
        <div>
            <button v-for="p in meta.last_page" :key="p" :class="{ on: p === meta.current_page }" @click="$emit('page', p)">{{ p }}</button>
        </div>
    </div>
</template>
