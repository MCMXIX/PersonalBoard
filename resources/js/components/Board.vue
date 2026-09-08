<template>
    <div class="overflow-hidden rounded-lg border border-white/10 p-5 shadow-xl shadow-black/10">
        <div class="flex min-h-0 flex-col gap-5">
            <div class="flex justify-between">
                <p class="text-lg leading-8">
                    Manage your tasks
                </p>
                <button v-if="selected"
                    class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900"
                    @click="showModal = true"
                >
                    Add Task
                </button>
            </div>

            <div ref="taskScroller"
                class="min-h-0 max-h-[60vh] overflow-x-hidden overflow-y-auto pr-2"
                @scroll.passive="handleScroll"
            >
                <draggable v-model="draggableServices"
                    item-key="id"
                    handle=".drag-handle"
                    class="min-w-0 space-y-3"
                    @end="savePositions"
                >
                    <template #item="{ element }">
                        <div class="drag-handle flex justify-between items-center w-full min-w-0 cursor-move gap-2 rounded-2xl border border-white/10 bg-white/5 p-5 shadow-xl shadow-black/10">
                            <div class="flex min-w-0 flex-col gap-2">
                                <template v-if="(!!editId == false || editId != element.id)">
                                    <div class="flex gap-2 items-center">
                                        <div :class="getTypeColor(element.type)"
                                            class="h-3 w-3 rounded-full"
                                        ></div>
                                        <span class="break-words">
                                            {{ element.name }}
                                        </span>
                                    </div>
                                    <span class="text-sm text-slate-400">
                                        {{ element.created_at }}
                                    </span>
                                </template>
                                <template v-if="(editId == element.id)">
                                    <Input v-model.trim="selectedTask.name" placeholder="Enter a task name" />
                                    <span v-if="editError.hasOwnProperty('name')" class="text-sm text-red-400">
                                        {{ editError.name }}
                                    </span>
                                    <div class="flex gap-6">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input v-model.trim="selectedTask.type"
                                                type="radio"
                                                value="high"
                                                class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                                            />
                                            <span class="text-sm">High</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input v-model.trim="selectedTask.type"
                                                type="radio"
                                                value="medium"
                                                class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                                            />
                                            <span class="text-sm">Medium</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input v-model.trim="selectedTask.type"
                                                type="radio"
                                                value="low"
                                                class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                                            />
                                            <span class="text-sm">Low</span>
                                        </label>
                                    </div>
                                    <span v-if="editError.hasOwnProperty('type')" class="text-sm text-red-400">
                                        {{ editError.type }}
                                    </span>
                                </template>
                            </div>
                            <div class="flex align-items-center gap-3">
                                <span v-if="editId != element.id" class="text-cyan-600 cursor-pointer px-3 py-2 rounded-sm hover:bg-white/5"
                                    @click="enableEdit(element)"
                                >
                                    Edit
                                </span>
                                <span v-if="editId == element.id" class="text-green-600 cursor-pointer px-3 py-2 rounded-sm hover:bg-white/5"
                                    @click="saveTaskChanges()"
                                >
                                    Save
                                </span>
                                <span v-if="(!!editId == false || editId != element.id)" class="text-red-600 cursor-pointer px-3 py-2 rounded-sm hover:bg-white/5"
                                    @click="removeTask(element.id)"
                                >
                                    Delete
                                </span>
                            </div>
                        </div>
                    </template>
                </draggable>

                <p v-if="loading" class="py-4 text-center text-sm text-slate-400">
                    Loading tasks...
                </p>
                <p v-else-if="error" class="py-4 text-center text-sm text-red-400">
                    {{ error }}
                </p>
                <p v-else-if="selected && services.length == 0" class="py-4 text-center text-sm text-slate-400">
                    No tasks found for this board.
                </p>
                <p v-else-if="!selected" class="py-4 text-center text-sm text-slate-400">
                    Select a board to view its tasks.
                </p>
            </div>
        </div>
    </div>
    <template>
        <Modal v-model="showModal">
            <div class="flex flex-col gap-3">
                <p class="text-lg">
                    Create new tasks?
                </p>
                <div class="flex flex-col gap-1">
                    <Input v-model.trim="form.name" placeholder="Enter a task name" />
                    <span v-if="formError.hasOwnProperty('name')" class="text-sm text-red-400">
                        {{ formError.name }}
                    </span>
                </div>
                <div class="flex flex-col gap-1">
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input v-model.trim="form.type"
                                type="radio"
                                value="high"
                                class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                            />
                            <span class="text-sm">High</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input v-model.trim="form.type"
                                type="radio"
                                value="medium"
                                class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                            />
                            <span class="text-sm">Medium</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input v-model.trim="form.type"
                                type="radio"
                                value="low"
                                class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                            />
                            <span class="text-sm">Low</span>
                        </label>
                    </div>
                    <span v-if="formError.hasOwnProperty('type')" class="text-sm text-red-400">
                        {{ formError.type }}
                    </span>
                </div>
                <div class="flex justify-center mt-3">
                    <button class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900"
                        :disabled="formLoading"
                        @click="createTask"
                    >
                        Submit
                    </button>
                </div>
            </div>
        </Modal>
    </template>
</template>

<script>
import draggable from 'vuedraggable';
import Modal from './Modal.vue';
import Input from './Input.vue';
import api from '../api';

export default {
    components: {
        draggable,
        Modal,
        Input
    },
    props: {
        services: {
            type: Array,
            default: () => [],
        },
        loading: {
            type: Boolean,
            default: false,
        },
        hasMore: {
            type: Boolean,
            default: false,
        },
        selected: {
            type: Boolean,
            default: false,
        },
        boardId: {
            type: Number,
            default: 0
        },
        error: {
            type: String,
            default: '',
        },
    },
    emits: ['load-more', 'update:services'],
    computed: {
        draggableServices: {
            get() {
                return this.services;
            },
            set(value) {
                this.$emit('update:services', value);
            },
        },
    },
    data() {
        return {
            showModal: false,
            form: {
                name: null,
                type: null
            },
            formError: {},
            formLoading: false,
            deleteLoading:false,
            editId: null,
            selectedTask: null,
            editError: {}
        }
    },
    watch: {
        services() {
            this.$nextTick(this.loadMoreIfNeeded);
        },
    },
    methods: {
        handleScroll(event) {
            const scroller = event.currentTarget;
            const distanceFromBottom = scroller.scrollHeight - scroller.scrollTop - scroller.clientHeight;

            if (distanceFromBottom <= 120) {
                this.requestMoreTasks();
            }
        },
        loadMoreIfNeeded() {
            const scroller = this.$refs.taskScroller;

            if (scroller && scroller.scrollHeight <= scroller.clientHeight) {
                this.requestMoreTasks();
            }
        },
        requestMoreTasks() {
            if (this.hasMore && !this.loading) {
                this.$emit('load-more');
            }
        },
        async savePositions() {
            const positions = this.services.map((item, index) => ({
                id: item.id,
                priority: index + 1,
            }));

            try {
                await api.post('/tasks/reorder', positions);
            } catch (error) {
                console.log(error);
            }
        },
        getTypeColor(label) {
            switch (label) {
                case 'high':
                    return 'bg-red-800';
                case 'medium':
                    return 'bg-orange-400';
                case 'low':
                    return 'bg-gray-800';
                default:
                    return 'bg-slate-500';
            }
        },
        async createTask() {
            if (!this.formLoading) {
                if (!this.boardId) {
                    alert('Please select a board/project to create new task.');
                }

                this.formError = {};
                if (!!this.form.name == false) {
                    this.formError.name = 'The name field is required.'
                }

                if (!!this.form.type == false) {
                    this.formError.type = 'The type field is required';
                }

                if (Object.keys(this.formError).length > 0) return;

                this.formLoading = true;
                try {
                    await api.post('/tasks', {
                        board_id: this.boardId,
                        name: this.form.name,
                        type: this.form.type
                    })
                    
                    this.form = {
                        name: null,
                        type: null
                    };
                    this.showModal = false;
                    this.$emit('reload-list');
                } catch (error) {
                    if (error.response.status == 422) {
                        const data = error.response.data.errors;
                        for (const key in data) {
                            if (!Object.hasOwn(data, key)) continue;
                            
                            const element = data[key];
                            if (Array.isArray(element)) {
                                this.formError[key] = element[0];
                            } else {
                                this.formError[key] = element;
                            }
                        }
                    } else {
                        alert(error.response?.data?.message ?? 'Something went wrong creating new task.');
                    }
                } finally {
                    this.formLoading = false;
                }
            }
        },
        async removeTask(taskId) {
            if (!this.deleteLoading) {
                this.deleteLoading = true;
                try {
                    await api.delete('/tasks/' + taskId);
                    this.$emit('reload-list');
                } catch (error) {
                    alert(error.response?.data?.message ?? 'Something went wrong removing the task.');
                } finally {
                    this.deleteLoading = false;
                }
            }
        },
        enableEdit(task) {
            this.editId = task.id;
            this.selectedTask = task;
        },
        async saveTaskChanges() {
            if (!this.formLoading) {
                if (!this.boardId) {
                    alert('Please select a board/project to create new task.');
                }
    
                this.editError = {};
                if (!!this.selectedTask.name == false) {
                    this.editError.name = 'The name field is required.'
                }
    
                if (!!this.selectedTask.type == false) {
                    this.editError.type = 'The type field is required';
                }
                
                if (Object.keys(this.editError).length > 0) return;

                this.formLoading = true;
                try {
                    await api.post('/tasks/update', {
                        task_id: this.selectedTask.id,
                        name: this.selectedTask.name,
                        type: this.selectedTask.type
                    });
                    this.$emit('reload-list');
                    this.editId = null;
                    this.selectedTask = null;
                } catch (error) {
                    if (error.response.status == 422) {
                        const data = error.response.data.errors;
                        for (const key in data) {
                            if (!Object.hasOwn(data, key)) continue;
                            
                            const element = data[key];
                            if (Array.isArray(element)) {
                                this.editError[key] = element[0];
                            } else {
                                this.editError[key] = element;
                            }
                        }
                    } else {
                        this.editId = null;
                        this.selectedTask = null;
                        alert(error.response?.data?.message ?? 'Something went wrong updating the task.');
                    }
                } finally {
                    this.formLoading = false;
                }
            }
        }
    },
};
</script>
