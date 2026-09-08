<template>
    <Teleport to="body">
        <div v-if="modelValue"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4"
            role="dialog"
            aria-modal="true"
            @click.self="close"
        >
            <div class="relative max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-2xl border border-white/10 bg-slate-900 p-6 text-slate-100 shadow-2xl">
                <button type="button"
                    class="absolute right-4 top-4 rounded-lg p-2 text-slate-400 transition hover:bg-white/10 hover:text-white focus:outline-none focus:ring-2 focus:ring-slate-400"
                    aria-label="Close modal"
                    @click="close"
                >
                    <span aria-hidden="true">&times;</span>
                </button>

                <div class="min-h-24 pt-8">
                    <slot></slot>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script>
export default {
    props: {
        modelValue: {
            type: Boolean,
            default: false,
        },
    },
    emits: ['update:modelValue', 'close'],
    mounted() {
        window.addEventListener('keydown', this.handleKeydown);
    },
    beforeUnmount() {
        window.removeEventListener('keydown', this.handleKeydown);
    },
    methods: {
        close() {
            this.$emit('update:modelValue', false);
            this.$emit('close');
        },
        handleKeydown(event) {
            if (this.modelValue && event.key === 'Escape') {
                this.close();
            }
        },
    },
};
</script>
