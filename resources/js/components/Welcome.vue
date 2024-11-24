<template>
  <div class="min-h-screen bg-gray-100 flex flex-col items-center pt-4">
    <div class="relative sm:max-w-xl sm:mx-auto">
      <div class="relative px-4 py-10 bg-white shadow-lg sm:rounded-3xl sm:p-20">
        <div class="max-w-md mx-auto">
          <div class="divide-y divide-gray-200">
            <div class="py-8 text-base leading-6 space-y-4 text-gray-700 sm:text-lg sm:leading-7">
              <h2 class="text-3xl font-bold text-center mb-8 text-blue-600">Manage the Shizzle Bizzle</h2>
              <div class="mt-5 space-y-4">
                <div class="grid grid-cols-2 gap-6">
                  <router-link 
                    to="/lists" 
                    class="flex flex-col items-center justify-center aspect-square p-6 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                  >
                    <span class="text-4xl mb-2">📋</span>
                    <span class="text-lg">Lists</span>
                  </router-link>
                  <router-link 
                    to="/todos" 
                    class="flex flex-col items-center justify-center aspect-square p-6 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                  >
                    <span class="text-4xl mb-2">✅</span>
                    <span class="text-lg">Todos</span>
                  </router-link>
                  <router-link 
                    to="/people" 
                    class="flex flex-col items-center justify-center aspect-square p-6 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors"
                  >
                    <span class="text-4xl mb-2">👥</span>
                    <span class="text-lg">People</span>
                  </router-link>
                  <router-link 
                    to="/activities" 
                    class="flex flex-col items-center justify-center aspect-square p-6 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors"
                  >
                    <span class="text-4xl mb-2">🎯</span>
                    <span class="text-lg">Activities</span>
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
</script>
