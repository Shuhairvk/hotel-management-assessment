<script setup>
defineProps({ title: String });
defineEmits(['close']);
</script>

<template>
    <div class="overlay" @click.self="$emit('close')">
        <div class="modal">
            <h3>{{ title }}</h3>
            <slot />
        </div>
    </div>
</template>
