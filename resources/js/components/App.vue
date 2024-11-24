<template>
  <div class="min-h-screen bg-gray-100">
    <KonamiCode />
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <router-link 
              to="/" 
              class="flex items-center px-4 text-lg font-medium text-blue-600 hover:text-blue-800"
            >
              🏠 Hizzle Management
            </router-link>
          </div>
          <div class="flex space-x-4">
            <router-link 
              to="/todos" 
              class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 hover:text-blue-800"
              :class="{ 'text-blue-800': $route.path === '/todos' }"
            >
              Todos
            </router-link>
            <router-link 
              to="/people" 
              class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 hover:text-blue-800"
              :class="{ 'text-blue-800': $route.path === '/people' }"
            >
              People
            </router-link>
            <router-link 
              to="/activities" 
              class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 hover:text-blue-800"
              :class="{ 'text-blue-800': $route.path === '/activities' }"
            >
              Activities
            </router-link>
          </div>
        </div>
      </div>
    </nav>

    <main class="py-10">
      <router-view></router-view>
    </main>
  </div>
</template>

<script setup>
import KonamiCode from './KonamiCode.vue'
</script>
