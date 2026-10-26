<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({ jobs: Array, payroll: Array });
const user = computed(() => usePage().props.auth.user);
const isManager = computed(() => user.value.role === 'manager');
const showPayroll = () => router.reload({ only: ['payroll'] });
</script>

<template>
    <h1>Hello {{ user.name }}</h1>
    <ul>
        <li v-for="job in jobs" :key="job.id">
            {{ job.scheduled_on }} · {{ job.title }} · <Link :href="`/clients/${job.client.id}`">{{ job.client.name }}</Link>
            <Link v-if="isManager" :href="`/jobs/${job.id}/edit`">Edit</Link>
        </li>
    </ul>
    <button v-if="isManager" @click="showPayroll">Payroll</button>
    <table v-if="payroll">
        <tr v-for="row in payroll" :key="row.name"><td>{{ row.name }}</td><td>{{ row.hourly_cost_cents / 100 }} €/h</td></tr>
    </table>
</template>
