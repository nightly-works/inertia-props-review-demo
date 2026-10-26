<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({ job: Object, technicians: Array });
const form = useForm({ ...props.job });
</script>

<template>
    <form @submit.prevent="form.put(`/jobs/${job.id}`)">
        <input v-model="form.title">
        <input v-model="form.scheduled_on" type="date">
        <input v-model.number="form.price_cents" type="number">
        <select v-model="form.technician_id">
            <option v-for="tech in technicians" :key="tech.id" :value="tech.id">{{ tech.name }}</option>
        </select>
        <button>Save</button>
    </form>
</template>
