<template>
  <div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex justify-between items-center">
      <h1 class="text-3xl font-bold text-gray-900">Activities</h1>
      <div class="space-x-4">
        <button @click="expandAll" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">
          Expand All (E)
        </button>
        <button @click="collapseAll" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">
          Collapse All (C)
        </button>
        <button
          @click="showAddForm = true"
          class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
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
            <select
              v-model="newActivity.person_id"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
              <option value="">Select a person</option>
              <option v-for="person in people" :key="person.id" :value="person.id">
                {{ person.name }}
              </option>
            </select>
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
            <select
              v-model="editingActivity.person_id"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
              <option value="">Select a person</option>
              <option v-for="person in people" :key="person.id" :value="person.id">
                {{ person.name }}
              </option>
            </select>
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
    <div class="space-y-6">
      <div v-for="day in daysOfWeek" :key="day" class="border rounded-lg overflow-hidden">
        <div
          @click="toggleDay(day)"
          class="bg-gray-100 px-4 py-3 flex justify-between items-center cursor-pointer hover:bg-gray-200"
        >
          <h2 class="text-xl font-semibold">{{ day }}</h2>
          <span class="text-gray-500">
            {{ groupedActivities[day]?.length || 0 }} activities
          </span>
        </div>
        
        <div v-if="expandedDays[day] && groupedActivities[day]" class="divide-y">
          <div
            v-for="activity in groupedActivities[day]"
            :key="activity.id"
            class="px-4 py-3 hover:bg-gray-50"
          >
            <div class="flex justify-between items-center">
              <div>
                <h3 class="font-medium">{{ activity.name }}</h3>
                <p class="text-sm text-gray-600">
                  {{ activity.person?.name || 'No person assigned' }} |
                  {{ formatTime(activity.start_time) }} - {{ formatTime(activity.end_time) }}
                </p>
              </div>
              <div class="space-x-2">
                <button
                  @click="startEdit(activity)"
                  class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded hover:bg-blue-200"
                >
                  Edit
                </button>
                <button
                  @click="deleteActivity(activity)"
                  class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200"
                >
                  Delete
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <div
          v-else-if="expandedDays[day] && (!groupedActivities[day] || groupedActivities[day].length === 0)"
          class="px-4 py-3 text-gray-500"
        >
          No activities scheduled for this day
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import axios from 'axios'

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
    activities.value = response.data.data
    error.value = ''
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load activities'
    console.error('Error fetching activities:', err)
  }
}

async function fetchPeople() {
  try {
    const response = await axios.get('/api/people')
    people.value = response.data.data
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load people'
    console.error('Error fetching people:', err)
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
    
    const index = activities.value.findIndex(a => a.id === editingActivity.value.id)
    if (index > -1) {
      activities.value[index] = response.data.data
    }
    
    editingActivity.value = null
    error.value = ''
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
