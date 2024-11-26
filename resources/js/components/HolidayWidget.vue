<template>
  <div class="p-4 bg-indigo-50 rounded-xl h-full">
    <div v-if="error" class="text-red-500">
      <p>{{ error }}</p>
    </div>
    <div v-else-if="holidays.length > 0">
      <h3 class="text-lg font-semibold text-indigo-700 mb-3">
        🎉 Belgian Holidays
      </h3>
      <div class="space-y-2">
        <div v-for="holiday in holidays" :key="holiday.date" class="flex items-center justify-between p-2 bg-white rounded-lg shadow-sm">
          <span class="font-medium text-gray-700 truncate mr-2 flex-shrink">{{ holiday.name }}</span>
          <span class="text-sm text-indigo-600 whitespace-nowrap flex-shrink-0">
            {{ formatDate(holiday.date) }}
          </span>
        </div>
      </div>
    </div>
    <div v-else class="flex items-center justify-center h-full">
      <p class="text-gray-500">Loading holidays...</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const holidays = ref([]);
const error = ref(null);

function formatDate(dateString) {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('en-US', { 
    month: 'short',
    day: 'numeric'
  }).format(date);
}

async function fetchHolidays() {
  try {
    error.value = null;
    const response = await axios.get('/api/holidays/upcoming');
    if (response.data?.data) {
      holidays.value = response.data.data;
    }
  } catch (err) {
    error.value = 'Failed to load holiday data';
    console.error('Error fetching holidays:', err);
  }
}

onMounted(() => {
  fetchHolidays();
});
</script>
