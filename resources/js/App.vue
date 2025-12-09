<script setup>
import { ref } from 'vue';

const count = ref(0);
</script>

<template>
    <div class="min-h-screen bg-zinc-900 text-zinc-100 flex items-center justify-center">
        <div class="text-center space-y-4">
            <h1 class="text-4xl font-bold text-emerald-500">
                Laravel <span class="text-zinc-100">+</span> Vue 3
            </h1>
            <p class="text-zinc-400">Kanban Board Setup</p>

            <button
                @click="count++"
                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 rounded-lg font-medium transition"
            >
                Counter: {{ count }}
            </button>
        </div>
    </div>
</template>
