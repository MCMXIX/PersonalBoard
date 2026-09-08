<template>
    <input
        v-bind="$attrs"
        :type="type"
        :value="modelValue"
        class="w-full rounded-lg border border-white/10 bg-slate-900 px-4 py-2.5 text-slate-100 outline-none placeholder:text-slate-500 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500/20"
        @input="$emit('update:modelValue', $event.target.value)"
    >
</template>

<script>
export default {
    inheritAttrs: false,
    props: {
        modelValue: {
            type: [String, Number],
            default: '',
        },
        type: {
            type: String,
            default: 'text',
        },
    },
    emits: ['update:modelValue'],
};
</script>
