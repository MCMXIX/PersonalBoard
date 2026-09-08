<template v-if="service">
    <div class="rounded-2xl border border-white/10 bg-white/5 p-5 shadow-xl shadow-black/10">
        <span :class="[service.color, 'mb-5 block h-2 w-10 rounded-full']"></span>
        <h2 class="text-lg font-semibold">{{ service.name }}</h2>
        <p class="mt-1 text-sm text-slate-400">{{ service.detail }}</p>
    </div>
</template>

<script>
export default {
    props: {
        service: {
            type: Object,
            required: true
        }
    },
    data() {

    }
}
</script>
