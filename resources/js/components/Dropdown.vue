<template>
    <div  class="relative w-64">
        <button type="button"
            class="flex w-full items-center justify-between rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-left text-sm text-gray-700 shadow-sm transition hover:border-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"
            @click="optionValues.isOpen = !optionValues.isOpen"
        >
            <span>
                <strong>Select Board</strong>
            </span>
            <svg class="h-4 w-4 text-gray-400 transition-transform"
                :class="{ 'rotate-180': optionValues.isOpen }"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"
                />
            </svg>
        </button>
        <div v-if="optionValues.isOpen"
            class="absolute z-50 mt-2 w-full overflow-hidden rounded-lg border border-gray-200 bg-white shadow-lg"
        >
            <div class="max-h-60 overflow-y-auto py-1">
                <button v-for="option in options"
                    :key="option.id"
                    type="button"
                    class="flex w-full items-center justify-between px-4 py-2.5 text-left text-sm transition hover:bg-gray-50"
                    :class="{
                        'bg-indigo-50 text-indigo-600': optionValues.selected === option.id,
                        'text-gray-700': optionValues.selected !== option.id,
                    }"
                    @click="selectOption(option)"
                >
                    <span>{{ option.name }}</span>

                    <svg v-if="optionValues.selected === option.id"
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7"
                        />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        options: {
            type: Object,
            required: true
        }
    },
    computed: {
        selected() {
            return this.options.find(option => option.id === this.optionValues.selected);
        },
    },
    data() {
        return {
            optionValues: {
                isOpen: false,
                selected: null
            }
        }
    },
    methods: {
        selectOption(option) {
            this.optionValues.selected = option.id;
            this.optionValues.isOpen = false;
            this.$emit('getBoardTasks', option.id);
        }
    }
}
</script>
