<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({ job: Object });
const form = useForm({ ...props.job });
</script>

<template>
    <form @submit.prevent="form.put(`/jobs/${job.id}`)">
        <input v-model="form.title">
        <input v-model="form.scheduled_on" type="date">
        <input v-model.number="form.price_cents" type="number">
        <button>Save</button>
    </form>
</template>
