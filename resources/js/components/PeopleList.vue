<template>
  <div class="max-w-3xl mx-auto">
    <div v-if="error" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
      {{ error }}
    </div>
    <div class="mb-6">
      <form @submit.prevent="addPerson" class="space-y-4 bg-white p-6 rounded-lg shadow-md mb-8">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label for="firstName" class="block text-sm font-medium text-blue-800">First Name</label>
            <input 
              type="text" 
              id="firstName"
              v-model="newPerson.first_name" 
              class="mt-1 block w-full rounded-lg border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              placeholder="Enter first name"
              required
            >
          </div>
          <div>
            <label for="lastName" class="block text-sm font-medium text-blue-800">Last Name</label>
            <input 
              type="text" 
              id="lastName"
              v-model="newPerson.last_name" 
              class="mt-1 block w-full rounded-lg border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              placeholder="Enter last name"
              required
            >
          </div>
        </div>
        <div>
          <label for="email" class="block text-sm font-medium text-blue-800">Email</label>
          <input 
            type="email" 
            id="email"
            v-model="newPerson.email" 
            class="mt-1 block w-full rounded-lg border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            placeholder="email@example.com"
          >
        </div>
        <div>
          <label for="phone" class="block text-sm font-medium text-blue-800">Phone</label>
          <input 
            type="tel" 
            id="phone"
            v-model="newPerson.phone" 
            class="mt-1 block w-full rounded-lg border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            placeholder="(123) 456-7890"
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
            class="mt-1 block w-full rounded-lg border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            placeholder="Enter address"
            rows="2"
          ></textarea>
        </div>
        <button 
          type="submit" 
          :disabled="isLoading"
          class="w-full inline-flex justify-center rounded-lg border border-transparent bg-blue-500 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors transform hover:scale-105 disabled:opacity-50"
        >
          {{ isLoading ? 'Adding...' : 'Add Person' }}
        </button>
      </form>
    </div>

    <div v-if="isLoading" class="text-center py-4">
      Loading...
    </div>
    <div v-else class="space-y-4">
      <div v-for="person in people" :key="person.id" 
        class="bg-white/80 backdrop-blur-sm rounded-lg shadow-lg hover:shadow-xl transition-all p-4"
      >
        <div class="flex justify-between items-start">
          <div class="space-y-1">
            <h3 class="text-lg font-medium text-blue-800">
              {{ person.first_name }} {{ person.last_name }}
            </h3>
            <div v-if="person.email" class="text-sm text-gray-600">
              <a :href="'mailto:' + person.email" class="hover:text-blue-600">
                {{ person.email }}
              </a>
            </div>
            <div v-if="person.phone" class="text-sm text-gray-600">
              {{ person.phone }}
            </div>
            <div v-if="person.date_of_birth" class="text-sm text-gray-600">
              Born: {{ new Date(person.date_of_birth).toLocaleDateString() }}
            </div>
            <div v-if="person.address" class="text-sm text-gray-600">
              {{ person.address }}
            </div>
          </div>
          <button 
            @click="deletePerson(person)" 
            class="text-red-500 hover:text-red-700 transition-colors"
          >
            <span class="sr-only">Delete</span>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const people = ref([])
const error = ref(null)
const isLoading = ref(false)
const newPerson = ref({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  date_of_birth: '',
  address: ''
})

const getCsrfToken = () => {
  const token = document.querySelector('meta[name="csrf-token"]')
  return token ? token.getAttribute('content') : null
}

const fetchPeople = async () => {
  error.value = null
  isLoading.value = true
  try {
    const response = await fetch('/api/people')
    if (!response.ok) {
      const errorData = await response.text()
      throw new Error(errorData || 'Failed to fetch people')
    }
    people.value = await response.json()
  } catch (err) {
    error.value = `Error loading people: ${err.message}`
    console.error('Error fetching people:', err)
  } finally {
    isLoading.value = false
  }
}

const addPerson = async () => {
  if (!newPerson.value.first_name.trim() || !newPerson.value.last_name.trim()) return

  error.value = null
  isLoading.value = true
  try {
    const response = await fetch('/api/people', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
      },
      body: JSON.stringify(newPerson.value)
    })

    if (!response.ok) {
      const errorData = await response.json()
      throw new Error(errorData.message || 'Failed to add person')
    }
    
    const person = await response.json()
    people.value.unshift(person)
    // Reset form
    newPerson.value = {
      first_name: '',
      last_name: '',
      email: '',
      phone: '',
      date_of_birth: '',
      address: ''
    }
  } catch (err) {
    error.value = `Error adding person: ${err.message}`
    console.error('Error adding person:', err)
  } finally {
    isLoading.value = false
  }
}

const deletePerson = async (person) => {
  if (!confirm('Are you sure you want to delete this person?')) return

  error.value = null
  try {
    const response = await fetch(`/api/people/${person.id}`, {
      method: 'DELETE',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
      }
    })

    if (!response.ok) {
      const errorData = await response.text()
      throw new Error(errorData || 'Failed to delete person')
    }

    const index = people.value.findIndex(p => p.id === person.id)
    people.value.splice(index, 1)
  } catch (err) {
    error.value = `Error deleting person: ${err.message}`
    console.error('Error deleting person:', err)
  }
}

onMounted(() => {
  fetchPeople()
})
</script>
