<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({ client: Object });
const isManager = computed(() => usePage().props.auth.user.role === 'manager');
</script>

<template>
    <h1>{{ client.name }}</h1>
    <p>Contact: {{ client.contact_email }}</p>
    <p v-if="isManager">Internal notes: {{ client.internal_notes }}</p>
    <ul>
        <li v-for="job in client.site_jobs" :key="job.id">{{ job.scheduled_on }} · {{ job.title }}</li>
    </ul>
</template>
