<template>
  <div class="max-w-3xl mx-auto">
    <div v-if="error" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
      {{ error }}
    </div>
    
    <div class="mb-6">
      <button 
        @click="showAddForm = !showAddForm"
        class="w-full flex justify-between items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors mb-2"
      >
        <span class="text-lg font-medium text-blue-800">Add New Person</span>
        <svg 
          class="w-6 h-6 text-blue-800 transform transition-transform"
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
        @submit.prevent="addPerson" 
        class="space-y-4 bg-white p-6 rounded-lg shadow-md mb-8"
      >
        <div>
          <label for="name" class="block text-sm font-medium text-blue-800">Name</label>
          <input 
            type="text" 
            id="name"
            v-model="newPerson.name" 
            class="mt-1 block w-full rounded-lg border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            required
          >
        </div>
        <div>
          <label for="email" class="block text-sm font-medium text-blue-800">Email</label>
          <input 
            type="email" 
            id="email"
            v-model="newPerson.email" 
            class="mt-1 block w-full rounded-lg border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
          >
        </div>
        <div>
          <label for="phone" class="block text-sm font-medium text-blue-800">Phone</label>
          <input 
            type="tel" 
            id="phone"
            v-model="newPerson.phone" 
            class="mt-1 block w-full rounded-lg border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
          >
        </div>
        <div>
          <label for="dateOfBirth" class="block text-sm font-medium text-blue-800">Date of Birth</label>
          <input 
            type="date" 
            id="dateOfBirth"
            v-model="newPerson.date_of_birth" 
            class="mt-1 block w-full rounded-lg border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
          >
        </div>
        <div>
          <label for="address" class="block text-sm font-medium text-blue-800">Address</label>
          <textarea 
            id="address"
            v-model="newPerson.address" 
            rows="2"
            class="mt-1 block w-full rounded-lg border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
          ></textarea>
        </div>
        <button 
          type="submit" 
          class="w-full inline-flex justify-center rounded-lg border border-transparent bg-blue-500 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
        >
          Add Person
        </button>
      </form>
    </div>

    <div v-if="people.length === 0" class="text-center text-gray-500 my-8">
      No people yet. Add someone above!
    </div>

    <div v-else class="space-y-4">
      <div v-for="person in sortedPeople" :key="person.id" class="bg-white p-6 rounded-lg shadow">
        <div v-if="editingPerson && editingPerson.id === person.id">
          <!-- Edit Form -->
          <form @submit.prevent="updatePerson" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-blue-800">Name</label>
              <input 
                type="text" 
                v-model="editingPerson.name" 
                class="mt-1 block w-full rounded-lg border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                required
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-blue-800">Email</label>
              <input 
                type="email" 
                v-model="editingPerson.email" 
                class="mt-1 block w-full rounded-lg border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-blue-800">Phone</label>
              <input 
                type="tel" 
                v-model="editingPerson.phone" 
                class="mt-1 block w-full rounded-lg border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-blue-800">Date of Birth</label>
              <input 
                type="date" 
                v-model="editingPerson.date_of_birth" 
                class="mt-1 block w-full rounded-lg border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-blue-800">Address</label>
              <textarea 
                v-model="editingPerson.address" 
                rows="2"
                class="mt-1 block w-full rounded-lg border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              ></textarea>
            </div>
            <div class="flex justify-end space-x-3">
              <button 
                type="button"
                @click="cancelEdit"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors"
              >
                Cancel
              </button>
              <button 
                type="submit"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
              >
                Save Changes
              </button>
            </div>
          </form>
        </div>
        <div v-else>
          <!-- View Mode -->
          <div class="flex justify-between items-start">
            <div>
              <h3 class="text-lg font-semibold">{{ person.name }}</h3>
              <div v-if="person.email" class="text-gray-600">{{ person.email }}</div>
              <div v-if="person.phone" class="text-gray-600">{{ person.phone }}</div>
              <div v-if="person.date_of_birth" class="text-gray-600">
                Born: {{ new Date(person.date_of_birth).toLocaleDateString() }}
              </div>
              <div v-if="person.address" class="text-gray-600 mt-2">{{ person.address }}</div>
            </div>
            <div class="flex space-x-2">
              <button 
                @click="startEdit(person)"
                class="text-blue-500 hover:text-blue-700"
                title="Edit"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              </button>
              <button 
                @click="deletePerson(person)"
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
import axios from 'axios'

const people = ref([])
const error = ref(null)
const showAddForm = ref(false)
const editingPerson = ref(null)
const originalName = ref(null)

const newPerson = ref({
  name: '',
  email: '',
  phone: '',
  date_of_birth: '',
  address: ''
})

const sortedPeople = computed(() => {
  return [...people.value].sort((a, b) => {
    return a.name.localeCompare(b.name);
  });
})

async function fetchPeople() {
  try {
    const response = await axios.get('/api/people')
    people.value = response.data.data
    error.value = null
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load people'
    console.error('Error fetching people:', err)
  }
}

async function addPerson() {
  try {
    const response = await axios.post('/api/people', newPerson.value)
    people.value.push(response.data.data)
    newPerson.value = {
      name: '',
      email: '',
      phone: '',
      date_of_birth: '',
      address: ''
    }
    showAddForm.value = false
    error.value = null
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to create person'
    console.error('Error adding person:', err)
  }
}

async function startEdit(person) {
  editingPerson.value = { ...person }
  originalName.value = person.name
}

async function cancelEdit() {
  editingPerson.value = null
}

async function updatePerson() {
  if (!editingPerson.value) return

  try {
    const response = await axios.patch(`/api/people/${editingPerson.value.id}`, editingPerson.value)
    const index = people.value.findIndex(p => p.id === editingPerson.value.id)
    if (index !== -1) {
      people.value[index] = response.data.data
    }
    editingPerson.value = null
    error.value = null
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to update person'
    console.error('Error updating person:', err)
  }
}

async function deletePerson(person) {
  if (!confirm(`Are you sure you want to delete ${person.name}?`)) return

  try {
    await axios.delete(`/api/people/${person.id}`)
    const index = people.value.indexOf(person)
    if (index !== -1) {
      people.value.splice(index, 1)
    }
    error.value = null
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to delete person'
    console.error('Error deleting person:', err)
  }
}

onMounted(() => {
  fetchPeople()
})
</script>
