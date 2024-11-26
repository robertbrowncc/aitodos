<template>
  <div class="min-h-screen bg-gray-100 flex flex-col items-center pt-4">
    <div class="relative sm:max-w-4xl sm:mx-auto">
      <div class="relative px-4 py-10 bg-white shadow-lg sm:rounded-3xl sm:p-20">
        <div class="mx-auto">
          <div class="divide-y divide-gray-200">
            <div class="py-8 text-base leading-6 space-y-4 text-gray-700 sm:text-lg sm:leading-7">
              <h2 class="text-3xl font-bold text-center mb-4 text-blue-600">Manage the Shizzle Bizzle</h2>
              <p class="text-center text-gray-600 mb-8">Your all-in-one solution for managing tasks, checklists, and team activities.</p>
              <div class="mt-5">
                <div class="grid grid-cols-4 gap-4">
                  <router-link 
                    to="/checklists" 
                    class="flex flex-col items-center justify-center p-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                  >
                    <span class="text-3xl mb-2">✅</span>
                    <span class="text-base">Checklists</span>
                  </router-link>
                  <router-link 
                    to="/todos" 
                    class="flex flex-col items-center justify-center p-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                  >
                    <span class="text-3xl mb-2">📋</span>
                    <span class="text-base">Todos</span>
                  </router-link>
                  <router-link 
                    to="/people" 
                    class="flex flex-col items-center justify-center p-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors"
                  >
                    <span class="text-3xl mb-2">👥</span>
                    <span class="text-base">People</span>
                  </router-link>
                  <router-link 
                    to="/activities" 
                    class="flex flex-col items-center justify-center p-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors"
                  >
                    <span class="text-3xl mb-2">🎯</span>
                    <span class="text-base">Activities</span>
                  </router-link>
                </div>
              </div>
              <div class="mt-8 grid grid-cols-2 gap-4">
                <div class="col-span-1">
                  <WeatherWidget />
                </div>
                <div v-if="upcomingBirthdays.length > 0" class="col-span-1 p-4 bg-pink-50 rounded-xl h-full">
                  <h3 class="text-lg font-semibold text-pink-700 mb-3">🎂 Upcoming Birthdays</h3>
                  <div class="space-y-2">
                    <div v-for="person in upcomingBirthdays" :key="person.id" class="flex items-center justify-between p-2 bg-white rounded-lg shadow-sm">
                      <span class="font-medium text-gray-700 truncate mr-2 flex-shrink">{{ person.name }}</span>
                      <span class="text-sm text-pink-600 whitespace-nowrap flex-shrink-0">
                        {{ formatDate(person.date_of_birth) }}
                      </span>
                    </div>
                  </div>
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
import WeatherWidget from './WeatherWidget.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const upcomingBirthdays = ref([]);

function formatDate(dateString) {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('en-US', { 
    month: 'short',
    day: 'numeric'
  }).format(date);
}

async function fetchUpcomingBirthdays() {
  try {
    const response = await axios.get('/api/people/upcoming/birthdays');
    if (response.data?.data) {
      upcomingBirthdays.value = response.data.data;
    }
  } catch (error) {
    console.error('Error fetching upcoming birthdays:', error);
  }
}

onMounted(() => {
  fetchUpcomingBirthdays();
});
</script>
