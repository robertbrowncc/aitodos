<template>
  <div class="min-h-screen w-full">
    <div class="container mx-auto px-4 pt-0">
      <div v-if="isHomePage" class="text-center mb-12 content-overlay p-8 mt-8">
        <h1 class="text-4xl font-bold mb-4 text-blue-800">Welcome to Todo App 🐱</h1>
        <p class="text-gray-700 mb-8 text-lg">Manage your tasks efficiently with our beautiful beach view</p>
        <a href="/todos" class="inline-block px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors transform hover:scale-105">
          Go to Todo Manager
        </a>
      </div>
      <div v-else class="content-overlay p-8 mt-8">
        <div class="flex justify-between items-center mb-8">
          <h1 class="text-3xl font-bold text-blue-800">Todo Manager</h1>
          <a href="/" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors transform hover:scale-105">
            Back to Home
          </a>
        </div>
        <todo-list></todo-list>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import TodoList from './components/TodoList.vue'

const isHomePage = computed(() => window.location.pathname === '/')
</script>

<style>
/* Custom scrollbar styles are now in custom.css */
</style>
