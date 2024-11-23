<template>
  <div class="min-h-screen w-full">
    <div class="container mx-auto px-4 pt-0">
      <div v-if="isHomePage" class="text-center mb-12 content-overlay p-8 mt-8">
        <h1 class="text-4xl font-bold mb-4 text-blue-800">Welcome to Todo App 🐱</h1>
        <p class="text-gray-700 mb-8 text-lg">Manage your tasks efficiently with our beautiful beach view</p>
        <div class="space-x-4">
          <a href="/todos" class="inline-block px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors transform hover:scale-105">
            Go to Todo Manager
          </a>
          <a href="/people" class="inline-block px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors transform hover:scale-105">
            Go to People Manager
          </a>
        </div>
      </div>
      <div v-else-if="isTodosPage" class="content-overlay p-8 mt-8">
        <todo-list></todo-list>
      </div>
      <div v-else-if="isPeoplePage" class="content-overlay p-8 mt-8">
        <people-list></people-list>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const isHomePage = computed(() => window.location.pathname === '/')
const isTodosPage = computed(() => window.location.pathname === '/todos')
const isPeoplePage = computed(() => window.location.pathname === '/people')
</script>

<style>
.content-overlay {
  background-color: rgba(255, 255, 255, 0.9);
  border-radius: 1rem;
  backdrop-filter: blur(10px);
}
</style>
