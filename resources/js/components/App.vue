<template>
    <main class="min-h-screen bg-slate-950 px-6 py-16 text-slate-100">
        <section class="mx-auto max-w-5xl">
            <div class="flex flex-col gap-3 mt-6 mb-8 max-w-3xl">
                <h1 class="text-5xl font-semibold tracking-tight sm:text-7xl">
                    PersonalBoard
                </h1>
                <template v-if="boards && boards.length > 0">
                    <Dropdown :options="boards"
                        @getBoardTasks="retrieveTasks"
                    />
                </template>
            </div>
            <div class="flex justify-between items-center mb-2">
                <span v-if="activeBoard" class="text-3xl">
                    <strong>
                        {{ activeBoard }}
                    </strong>
                </span>
                <span class="text-cyan-400 cursor-pointer px-3 py-2 rounded-sm hover:bg-white/5 ms-auto"
                    @click="showModal = true"
                >
                    Create Board
                </span>
            </div>
            <Board class="w-full"
                :services="tasks"
                :loading="taskState.loading"
                :has-more="Boolean(taskState.nextCursor)"
                :selected="taskState.boardId !== null"
                :board-id="taskState.boardId"
                :error="taskState.error"
                @load-more="loadMoreTasks"
                @reload-list="fetchTasks(true)"
                @update:services="tasks = $event"
            />
        </section>
    </main>
    <template>
        <Modal v-model="showModal">
            <div class="flex flex-col gap-3">
                <p class="text-lg">
                    Create new board?
                </p>
                <div class="flex flex-col gap-1">
                    <Input v-model.trim="form.name" placeholder="Enter board name" />
                    <span v-if="formError.hasOwnProperty('name')" class="text-sm text-red-400">
                        {{ formError.name }}
                    </span>
                </div>
                <div class="flex justify-center mt-3">
                    <button class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900"
                        :disabled="formLoading"
                        @click="createBoard"
                    >
                        Submit
                    </button>
                </div>
            </div>
        </Modal>
    </template>
</template>

<script>
import Card from './Card.vue';
import Board from './Board.vue';
import Dropdown from './Dropdown.vue';
import api from '../api';
import Modal from './Modal.vue';
import Input from './Input.vue';

export default {
    components: {
        Board, Card, Dropdown, Modal, Input
    },
    mounted() {
        this.getBoards();
    },
    data() {
        return {
            boards: null,
            tasks: [],
            taskRequestId: 0,
            activeBoard: null,
            taskState: {
                boardId: null,
                nextCursor: null,
                loading: false,
                error: '',
            },
            showModal: false,
            formLoading: false,
            form: {},
            formError: {},
        }
    },
    methods: {
        async getBoards() {
            const response = await api.get('/boards');
            if (response.status == 200) {
                this.boards = response.data.boards;
            } else {
                this.boards = null;
            }
        },
        async retrieveTasks(boardId) {
            this.taskRequestId += 1;
            this.taskState.boardId = boardId;
            this.taskState.nextCursor = null;
            this.taskState.loading = false;
            this.taskState.error = '';
            const activeBoard = this.boards.find(board => board.id === boardId);
            this.activeBoard = activeBoard.name;
            this.tasks = [];

            await this.fetchTasks(true);
        },
        async loadMoreTasks() {
            if (!this.taskState.nextCursor || this.taskState.loading) {
                return;
            }

            await this.fetchTasks(false);
        },
        async fetchTasks(reset) {
            const requestId = this.taskRequestId;
            const boardId = this.taskState.boardId;

            if (boardId === null || this.taskState.loading) {
                return;
            }

            this.taskState.loading = true;
            this.taskState.error = '';

            try {
                const response = await api.get(`/tasks/${boardId}`, {
                    params: this.taskState.nextCursor
                        ? { cursor: this.taskState.nextCursor }
                        : {},
                });

                if (requestId !== this.taskRequestId) {
                    return;
                }

                const pagination = response.data.tasks;
                this.tasks = reset
                    ? pagination.data
                    : [...this.tasks, ...pagination.data];
                this.taskState.nextCursor = pagination.next_cursor;
            } catch (error) {
                if (requestId === this.taskRequestId) {
                    this.taskState.error = error.response?.data?.message ?? 'Unable to load tasks.';
                }
            } finally {
                if (requestId === this.taskRequestId) {
                    this.taskState.loading = false;
                }
            }
        },
        async createBoard() {
            if (!this.formLoading) {
                this.formError = {};
                if (!!this.form.name == false) {
                    this.formError.name = 'The name field is required.';
                }
    
                if (Object.keys(this.formError).length > 0) return;

                this.formLoading = true;
                try {
                    await api.post('/boards', this.form);
                    this.getBoards();
                    this.showModal = false;
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
                        alert(error.response?.data?.message ?? 'Something went wrong creating new board.');
                    }
                } finally {
                    this.formLoading = false;
                }
            }
        }
    }
}
</script>
