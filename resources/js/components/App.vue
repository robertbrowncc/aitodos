<template>
  <div class="min-h-screen bg-gray-100">
    <KonamiCode />
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between h-auto sm:h-16">
          <div class="flex justify-center sm:justify-start py-4 sm:py-0">
            <router-link 
              to="/" 
              class="flex items-center px-4 text-lg font-medium text-blue-600 hover:text-blue-800"
            >
              <span class="text-4xl mr-1">🏠</span> 
              <span class="hidden sm:inline">Hizzle Management</span>
              <span class="sm:hidden">Hizzle</span>
            </router-link>
          </div>
          <div class="flex justify-center sm:justify-end space-x-2 sm:space-x-4 pb-4 sm:pb-0 overflow-x-auto">
            <router-link 
              to="/checklists" 
              class="whitespace-nowrap inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 hover:text-blue-800"
              :class="{ 'text-blue-800': $route.path === '/checklists' }"
            >
              <span class="sm:hidden">✅</span>
              <span class="hidden sm:inline">Checklists</span>
            </router-link>
            <router-link 
              to="/todos" 
              class="whitespace-nowrap inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 hover:text-blue-800"
              :class="{ 'text-blue-800': $route.path === '/todos' }"
            >
              <span class="sm:hidden">📋</span>
              <span class="hidden sm:inline">Todos</span>
            </router-link>
            <router-link 
              to="/people" 
              class="whitespace-nowrap inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 hover:text-blue-800"
              :class="{ 'text-blue-800': $route.path === '/people' }"
            >
              <span class="sm:hidden">👥</span>
              <span class="hidden sm:inline">People</span>
            </router-link>
            <router-link 
              to="/activities" 
              class="whitespace-nowrap inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 hover:text-blue-800"
              :class="{ 'text-blue-800': $route.path === '/activities' }"
            >
              <span class="sm:hidden">🎯</span>
              <span class="hidden sm:inline">Activities</span>
            </router-link>
          </div>
        </div>
      </div>
    </nav>

    <main class="py-4 sm:py-10">
      <router-view></router-view>
    </main>
  </div>
</template>

<script setup>
import KonamiCode from './KonamiCode.vue'
</script>
