<template>
  <div class="min-h-screen bg-gray-100 flex flex-col items-center pt-4">
    <div class="relative w-full max-w-4xl mx-auto px-4 sm:px-6">
      <div class="relative py-8 px-6 sm:px-12 bg-white shadow-lg sm:rounded-3xl">
        <div class="mx-auto">
          <div class="divide-y divide-gray-200">
            <div class="py-4 sm:py-8 text-base leading-6 space-y-4 text-gray-700 sm:text-lg sm:leading-7">
              <h2 class="text-2xl sm:text-3xl font-bold text-center mb-4 text-blue-600">Manage the Shizzle Bizzle</h2>
              <p class="text-center text-sm sm:text-base text-gray-600">{{ currentDate }}</p>
              <div class="mb-6">
                <h3 class="text-lg font-semibold text-blue-700 mb-3">📅 Today's Activities</h3>
                <div v-if="todayActivities.length > 0" class="space-y-2">
                  <div v-for="activity in todayActivities" :key="activity.id" 
                       class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-3 bg-blue-50 rounded-lg shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3 mb-2 sm:mb-0">
                      <span class="font-medium text-gray-700">{{ activity.name }}</span>
                      <span class="text-sm text-gray-500">with {{ activity.person?.name }}</span>
                    </div>
                    <span class="text-sm text-blue-600 whitespace-nowrap">
                      {{ formatTime(activity.start_time) }} - {{ formatTime(activity.end_time) }}
                    </span>
                  </div>
                </div>
                <div v-else class="p-4 bg-blue-50 rounded-lg text-center">
                  <span class="text-2xl">🎉</span>
                  <p class="text-blue-700 font-medium mt-2">Hooray! No activities scheduled for today!</p>
                </div>
              </div>
              <div class="mt-5">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                  <router-link 
                    to="/checklists" 
                    class="flex flex-col items-center justify-center p-3 sm:p-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                  >
                    <span class="text-2xl sm:text-3xl mb-2">✅</span>
                    <span class="text-sm sm:text-base">Checklists</span>
                  </router-link>
                  <router-link 
                    to="/todos" 
                    class="flex flex-col items-center justify-center p-3 sm:p-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                  >
                    <span class="text-2xl sm:text-3xl mb-2">📋</span>
                    <span class="text-sm sm:text-base">Todos</span>
                  </router-link>
                  <router-link 
                    to="/people" 
                    class="flex flex-col items-center justify-center p-3 sm:p-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors"
                  >
                    <span class="text-2xl sm:text-3xl mb-2">👥</span>
                    <span class="text-sm sm:text-base">People</span>
                  </router-link>
                  <router-link 
                    to="/activities" 
                    class="flex flex-col items-center justify-center p-3 sm:p-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors"
                  >
                    <span class="text-2xl sm:text-3xl mb-2">🎯</span>
                    <span class="text-sm sm:text-base">Activities</span>
                  </router-link>
                </div>
              </div>
              <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
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
                <div class="col-span-1">
                  <HolidayWidget />
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
import HolidayWidget from './HolidayWidget.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const upcomingBirthdays = ref([]);
const currentDate = ref('');
const todayActivities = ref([]);

function formatDate(dateString) {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('en-US', { 
    month: 'short',
    day: 'numeric'
  }).format(date);
}

function formatTime(timeString) {
  return new Date(`2000-01-01T${timeString}`).toLocaleTimeString('en-US', {
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
  });
}

const updateDate = () => {
  const now = new Date();
  const options = { 
    weekday: 'long', 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  };
  currentDate.value = new Intl.DateTimeFormat('en-US', options).format(now);
};

const startClock = () => {
  updateDate();
  setInterval(updateDate, 60000);
};

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

const fetchTodayActivities = async () => {
  try {
    const response = await axios.get('/api/activities');
    const today = new Date().toLocaleDateString('en-US', { weekday: 'long' }); // Returns "Wednesday", "Thursday", etc.
    
    todayActivities.value = response.data.data.filter(activity => 
      activity.day_of_week === today
    ).sort((a, b) => a.start_time.localeCompare(b.start_time));
  } catch (error) {
    console.error('Error fetching activities:', error);
    todayActivities.value = [];
  }
};

onMounted(() => {
  startClock();
  fetchUpcomingBirthdays();
  fetchTodayActivities();
});
</script>
