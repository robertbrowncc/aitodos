<template>
  <div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
      <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Activities</h1>
      <div class="flex flex-wrap justify-center sm:justify-end gap-2 sm:space-x-4 w-full sm:w-auto">
        <button @click="expandAll" class="px-3 sm:px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-sm sm:text-base whitespace-nowrap">
          <span class="hidden sm:inline">Expand All</span>
          <span class="sm:hidden">Expand</span> (E)
        </button>
        <button @click="collapseAll" class="px-3 sm:px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-sm sm:text-base whitespace-nowrap">
          <span class="hidden sm:inline">Collapse All</span>
          <span class="sm:hidden">Collapse</span> (C)
        </button>
        <button
          @click="showAddForm = true"
          class="px-3 sm:px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm sm:text-base whitespace-nowrap"
        >
          Add Activity
        </button>
      </div>
    </div>

    <div v-if="error" class="mb-4 p-4 bg-red-100 text-red-700 rounded">
      {{ error }}
    </div>

    <!-- Add Activity Modal -->
    <div v-if="showAddForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">Add New Activity</h2>
        <form @submit.prevent="addActivity" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input
              v-model="newActivity.name"
              type="text"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700">Person</label>
            <PersonAutocomplete
              v-model="newActivity.person_id"
              :people="people"
              class="mt-1"
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700">Day of Week</label>
            <select
              v-model="newActivity.day_of_week"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
              <option value="">Select a day</option>
              <option v-for="day in daysOfWeek" :key="day" :value="day">
                {{ day }}
              </option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700">Start Time</label>
            <input
              v-model="newActivity.start_time"
              type="time"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700">End Time</label>
            <input
              v-model="newActivity.end_time"
              type="time"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
          </div>
          
          <div class="flex justify-end space-x-4">
            <button
              type="button"
              @click="showAddForm = false"
              class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
            >
              Add
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Edit Activity Modal -->
    <div v-if="editingActivity" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">Edit Activity</h2>
        <form @submit.prevent="updateActivity" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input
              v-model="editingActivity.name"
              type="text"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700">Person</label>
            <PersonAutocomplete
              v-model="editingActivity.person_id"
              :people="people"
              class="mt-1"
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700">Day of Week</label>
            <select
              v-model="editingActivity.day_of_week"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
              <option value="">Select a day</option>
              <option v-for="day in daysOfWeek" :key="day" :value="day">
                {{ day }}
              </option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700">Start Time</label>
            <input
              v-model="editingActivity.start_time"
              type="time"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700">End Time</label>
            <input
              v-model="editingActivity.end_time"
              type="time"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
          </div>
          
          <div class="flex justify-end space-x-4">
            <button
              type="button"
              @click="cancelEdit"
              class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
            >
              Save
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Activities List -->
    <div class="space-y-4">
      <div v-for="day in daysOfWeek" :key="day" class="bg-white rounded-lg shadow">
        <div
          @click="toggleDay(day)"
          class="p-4 flex justify-between items-center cursor-pointer hover:bg-gray-50"
        >
          <h2 class="text-lg sm:text-xl font-semibold">{{ day }}</h2>
          <div class="flex items-center space-x-2">
            <span class="text-gray-500 text-sm">{{ groupedActivities[day]?.length || 0 }} activities</span>
            <svg
              class="w-5 h-5 transform transition-transform"
              :class="{ 'rotate-180': expandedDays[day] }"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 9l-7 7-7-7" />
            </svg>
          </div>
        </div>
        
        <div v-if="expandedDays[day]" class="p-4 border-t border-gray-100">
          <div v-if="groupedActivities[day]?.length" class="space-y-3">
            <div
              v-for="activity in groupedActivities[day]"
              :key="activity.id"
              class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-3 bg-gray-50 rounded"
            >
              <div class="flex flex-col sm:flex-row sm:items-center mb-2 sm:mb-0">
                <span class="font-medium mr-2">{{ activity.name }}</span>
                <span class="text-sm text-gray-600">
                  with {{ activity.person?.name }}
                </span>
              </div>
              <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 w-full sm:w-auto">
                <span class="text-sm text-gray-600 whitespace-nowrap">
                  {{ formatTime(activity.start_time) }} - {{ formatTime(activity.end_time) }}
                </span>
                <div class="flex space-x-2 w-full sm:w-auto justify-start sm:justify-end">
                  <button
                    @click="startEdit(activity)"
                    class="px-3 py-1.5 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 flex-1 sm:flex-none"
                  >
                    Edit
                  </button>
                  <button
                    @click="deleteActivity(activity)"
                    class="px-3 py-1.5 bg-red-500 text-white text-sm rounded hover:bg-red-600 flex-1 sm:flex-none"
                  >
                    Delete
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="text-gray-500 text-center py-4">
            No activities scheduled for {{ day }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import axios from 'axios'
import PersonAutocomplete from './PersonAutocomplete.vue'

const activities = ref([])
const people = ref([])
const error = ref(null)
const showAddForm = ref(false)
const editingActivity = ref(null)
const expandedDays = ref({})

const daysOfWeek = [
  'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'
]

const newActivity = ref({
  name: '',
  person_id: '',
  day_of_week: '',
  start_time: '',
  end_time: ''
})

// Function to calculate end time based on start time
const updateEndTime = (startTime) => {
  if (!startTime) return
  
  const [hours, minutes] = startTime.split(':')
  const date = new Date()
  date.setHours(parseInt(hours))
  date.setMinutes(parseInt(minutes))
  date.setHours(date.getHours() + 1)
  
  const newHours = String(date.getHours()).padStart(2, '0')
  const newMinutes = String(date.getMinutes()).padStart(2, '0')
  return `${newHours}:${newMinutes}`
}

// Watch for changes to start time
watch(() => newActivity.value.start_time, (newTime) => {
  newActivity.value.end_time = updateEndTime(newTime)
})

// Also watch editing activity start time
watch(() => editingActivity.value?.start_time, (newTime) => {
  if (editingActivity.value && newTime) {
    editingActivity.value.end_time = updateEndTime(newTime)
  }
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

async function fetchActivities() {
  try {
    const response = await axios.get('/api/activities')
    if (response.data?.status === 'success' && response.data?.data) {
      activities.value = response.data.data
      error.value = ''
    } else {
      console.warn('Unexpected API response format:', response.data)
      activities.value = []
      error.value = 'Unexpected response format from server'
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load activities'
    console.error('Error fetching activities:', err)
    activities.value = []
  }
}

async function fetchPeople() {
  try {
    const response = await axios.get('/api/people')
    if (response.data?.status === 'success' && response.data?.data) {
      people.value = response.data.data
    } else {
      console.warn('Unexpected API response format:', response.data)
      people.value = []
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load people'
    console.error('Error fetching people:', err)
    people.value = []
  }
}

async function addActivity() {
  try {
    const response = await axios.post('/api/activities', {
      name: newActivity.value.name,
      person_id: newActivity.value.person_id,
      day_of_week: newActivity.value.day_of_week,
      start_time: newActivity.value.start_time,
      end_time: newActivity.value.end_time
    })
    
    if (response.data?.status === 'success' && response.data?.data) {
      activities.value.push(response.data.data)
      showAddForm.value = false
      newActivity.value = {
        name: '',
        person_id: '',
        day_of_week: '',
        start_time: '',
        end_time: ''
      }
      error.value = ''
    } else {
      console.warn('Unexpected API response format:', response.data)
      error.value = 'Failed to add activity: unexpected response format'
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to add activity'
    console.error('Error adding activity:', err)
  }
}

async function deleteActivity(activity) {
  if (!confirm(`Are you sure you want to delete "${activity.name}"?`)) return
  
  try {
    await axios.delete(`/api/activities/${activity.id}`)
    const index = activities.value.indexOf(activity)
    if (index > -1) {
      activities.value.splice(index, 1)
    }
    error.value = ''
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to delete activity'
    console.error('Error deleting activity:', err)
  }
}

async function updateActivity() {
  if (!editingActivity.value) return
  
  try {
    const response = await axios.patch(`/api/activities/${editingActivity.value.id}`, {
      name: editingActivity.value.name,
      person_id: editingActivity.value.person_id,
      day_of_week: editingActivity.value.day_of_week,
      start_time: editingActivity.value.start_time,
      end_time: editingActivity.value.end_time
    })
    
    if (response.data?.status === 'success' && response.data?.data) {
      const index = activities.value.findIndex(a => a.id === editingActivity.value.id)
      if (index > -1) {
        activities.value[index] = response.data.data
      }
      editingActivity.value = null
      error.value = ''
    } else {
      console.warn('Unexpected API response format:', response.data)
      error.value = 'Failed to update activity: unexpected response format'
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to update activity'
    console.error('Error updating activity:', err)
  }
}

const toggleDay = (day) => {
  expandedDays.value[day] = !expandedDays.value[day]
}

const expandAll = () => {
  daysOfWeek.forEach(day => {
    expandedDays.value[day] = true
  })
}

const collapseAll = () => {
  daysOfWeek.forEach(day => {
    expandedDays.value[day] = false
  })
}

const handleKeyPress = (event) => {
  // Only respond to keypresses if not in an input field
  if (event.target.tagName === 'INPUT' || event.target.tagName === 'TEXTAREA') {
    return
  }
  
  if (event.key.toLowerCase() === 'e') {
    expandAll()
  } else if (event.key.toLowerCase() === 'c') {
    collapseAll()
  }
}

const startEdit = (activity) => {
  // Create a deep copy of the activity and ensure time values are in HH:mm format
  editingActivity.value = {
    ...activity,
    person_id: activity.person?.id || null,  // Explicitly set person_id from the person relationship
    start_time: activity.start_time.substring(0, 5), // Get only HH:mm part
    end_time: activity.end_time.substring(0, 5), // Get only HH:mm part
  }
}

const cancelEdit = () => {
  editingActivity.value = null
  error.value = null
}

onMounted(() => {
  fetchActivities()
  fetchPeople()
  // Add keyboard event listener
  window.addEventListener('keydown', handleKeyPress)
})

onUnmounted(() => {
  // Remove keyboard event listener
  window.removeEventListener('keydown', handleKeyPress)
})
</script>
