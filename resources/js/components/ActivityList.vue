<template>
  <div class="max-w-3xl mx-auto">
    <div v-if="error" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
      {{ error }}
    </div>

    <div class="mb-6">
      <button 
        @click="showAddForm = !showAddForm"
        class="w-full flex justify-between items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors mb-2"
      >
        <span class="text-lg font-medium text-purple-800">Add New Activity</span>
        <svg 
          class="w-6 h-6 text-purple-800 transform transition-transform"
          :class="{ 'rotate-180': showAddForm }"
          fill="none" 
          stroke="currentColor" 
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>

      <form 
        v-show="showAddForm"
        @submit.prevent="addActivity" 
        class="space-y-4 bg-white p-6 rounded-lg shadow-md"
      >
        <div>
          <label for="activityName" class="block text-sm font-medium text-purple-800">Activity Name</label>
          <input 
            type="text" 
            id="activityName"
            v-model="newActivity.name" 
            class="mt-1 block w-full rounded-lg border-purple-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
            required
          >
        </div>

        <div>
          <label for="personId" class="block text-sm font-medium text-purple-800">Assign To</label>
          <select
            id="personId"
            v-model="newActivity.person_id"
            class="mt-1 block w-full rounded-lg border-purple-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
            required
          >
            <option value="">Select a person</option>
            <option v-for="person in people" :key="person.id" :value="person.id">
              {{ person.first_name }} {{ person.last_name }}
            </option>
          </select>
        </div>

        <div>
          <label for="dayOfWeek" class="block text-sm font-medium text-purple-800">Day of Week</label>
          <select
            id="dayOfWeek"
            v-model="newActivity.day_of_week"
            class="mt-1 block w-full rounded-lg border-purple-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
            required
          >
            <option value="">Select a day</option>
            <option v-for="day in daysOfWeek" :key="day" :value="day">{{ day }}</option>
          </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label for="startTime" class="block text-sm font-medium text-purple-800">Start Time</label>
            <input 
              type="time" 
              id="startTime"
              v-model="newActivity.start_time" 
              class="mt-1 block w-full rounded-lg border-purple-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
              required
            >
          </div>
          <div>
            <label for="endTime" class="block text-sm font-medium text-purple-800">End Time</label>
            <input 
              type="time" 
              id="endTime"
              v-model="newActivity.end_time" 
              class="mt-1 block w-full rounded-lg border-purple-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
              required
            >
          </div>
        </div>

        <button 
          type="submit" 
          class="w-full inline-flex justify-center rounded-lg border border-transparent bg-purple-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2"
        >
          Add Activity
        </button>
      </form>
    </div>

    <div v-if="activities.length === 0" class="text-center text-gray-500 my-8">
      No activities yet. Add one above!
    </div>

    <div v-else class="space-y-4">
      <div v-for="(activitiesByDay, day) in groupedActivities" :key="day" class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-purple-800 mb-4">{{ day }}</h3>
        <div class="space-y-4">
          <div v-for="activity in activitiesByDay" :key="activity.id" 
               class="flex justify-between items-start p-4 border border-gray-200 rounded-lg hover:bg-purple-50">
            <div>
              <div class="font-medium">{{ activity.name }}</div>
              <div class="text-sm text-gray-600">
                {{ activity.person.first_name }} {{ activity.person.last_name }}
              </div>
              <div class="text-sm text-gray-500">
                {{ formatTime(activity.start_time) }} - {{ formatTime(activity.end_time) }}
              </div>
            </div>
            <div class="flex space-x-2">
              <button 
                @click="startEdit(activity)"
                class="text-purple-600 hover:text-purple-800"
                title="Edit"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              </button>
              <button 
                @click="deleteActivity(activity)"
                class="text-red-500 hover:text-red-700"
                title="Delete"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const activities = ref([])
const people = ref([])
const error = ref(null)
const showAddForm = ref(false)
const editingActivity = ref(null)

const daysOfWeek = [
  'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'
]

const newActivity = ref({
  name: '',
  person_id: '',
  start_time: '',
  end_time: '',
  day_of_week: ''
})

const groupedActivities = computed(() => {
  const grouped = {}
  daysOfWeek.forEach(day => {
    const dayActivities = activities.value.filter(activity => activity.day_of_week === day)
    if (dayActivities.length > 0) {
      grouped[day] = dayActivities.sort((a, b) => a.start_time.localeCompare(b.start_time))
    }
  })
  return grouped
})

const formatTime = (time) => {
  return new Date(`2000-01-01T${time}`).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

const fetchActivities = async () => {
  error.value = null
  try {
    const response = await fetch('/api/activities')
    if (!response.ok) throw new Error('Failed to fetch activities')
    activities.value = await response.json()
  } catch (err) {
    error.value = `Error loading activities: ${err.message}`
    console.error('Error:', err)
  }
}

const fetchPeople = async () => {
  try {
    const response = await fetch('/api/people')
    if (!response.ok) throw new Error('Failed to fetch people')
    people.value = await response.json()
  } catch (err) {
    error.value = `Error loading people: ${err.message}`
    console.error('Error:', err)
  }
}

const addActivity = async () => {
  error.value = null
  try {
    const response = await fetch('/api/activities', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify(newActivity.value)
    })

    if (!response.ok) throw new Error('Failed to create activity')
    
    const activity = await response.json()
    activities.value.push(activity)
    
    // Reset form
    newActivity.value = {
      name: '',
      person_id: '',
      start_time: '',
      end_time: '',
      day_of_week: ''
    }
    showAddForm.value = false
  } catch (err) {
    error.value = `Error creating activity: ${err.message}`
    console.error('Error:', err)
  }
}

const deleteActivity = async (activity) => {
  if (!confirm('Are you sure you want to delete this activity?')) return

  error.value = null
  try {
    const response = await fetch(`/api/activities/${activity.id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    })

    if (!response.ok) throw new Error('Failed to delete activity')
    
    activities.value = activities.value.filter(a => a.id !== activity.id)
  } catch (err) {
    error.value = `Error deleting activity: ${err.message}`
    console.error('Error:', err)
  }
}

onMounted(() => {
  fetchActivities()
  fetchPeople()
})
</script>
