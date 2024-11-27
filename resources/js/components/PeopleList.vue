<template>
  <div class="max-w-3xl mx-auto">
    <div v-if="error" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
      {{ error }}
    </div>
    
    <div class="mb-6">
      <button 
        @click="toggleAddForm"
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
            ref="nameInput"
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

    <div v-else>
      <!-- Alphabet Navigation -->
      <div class="sticky top-0 bg-white z-10 py-2 px-4 shadow-md">
        <div class="flex flex-wrap gap-2 justify-center items-center">
          <button
            v-for="letter in availableLetters"
            :key="letter"
            @click="scrollToLetter(letter)"
            :class="[
              'px-3 py-1 rounded-md text-sm font-medium transition-colors duration-200',
              currentLetter === letter
                ? 'bg-blue-600 text-white'
                : people.some(p => p.name.charAt(0).toUpperCase() === letter)
                  ? 'bg-gray-200 text-gray-700 hover:bg-blue-500 hover:text-white'
                  : 'bg-gray-100 text-gray-400'
            ]"
          >
            {{ letter }}
          </button>
        </div>
      </div>

      <!-- People List Grouped by Letter -->
      <div class="mt-6">
        <div
          v-for="letter in availableLetters"
          :key="letter"
          :id="'letter-' + letter"
          class="mb-8"
        >
          <h2 class="text-2xl font-bold text-blue-800 mb-4 sticky top-16 bg-gray-100 py-2 px-4 rounded-lg shadow-sm">
            {{ letter }}
            <span v-if="groupedPeople[letter].length === 0" class="text-gray-400 text-lg ml-2">
              (No entries)
            </span>
          </h2>
          <div v-if="groupedPeople[letter].length > 0" class="space-y-4">
            <div 
              v-for="person in groupedPeople[letter]" 
              :key="person.id" 
              class="bg-white p-6 rounded-lg shadow"
            >
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
                  <div class="space-y-2">
                    <h3 class="text-lg font-semibold">{{ person.name }}</h3>
                    
                    <div v-if="person.email" class="flex items-center text-gray-600">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                      </svg>
                      <a :href="'mailto:' + person.email" class="hover:text-blue-600 hover:underline">
                        {{ person.email }}
                      </a>
                    </div>
                    
                    <div v-if="person.phone" class="flex items-center text-gray-600">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                      </svg>
                      <a :href="'tel:' + person.phone" class="hover:text-blue-600 hover:underline">
                        {{ person.phone }}
                      </a>
                    </div>
                    
                    <div v-if="person.address" class="flex items-center text-gray-600">
                      <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                      <a 
                        :href="'https://www.google.com/maps/dir/?api=1&destination=' + encodeURIComponent(person.address)"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="hover:text-blue-600 hover:underline"
                      >
                        {{ person.address }}
                      </a>
                    </div>
                    
                    <div v-if="person.date_of_birth" class="flex items-center text-gray-600">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                      <span>Born: {{ new Date(person.date_of_birth).toLocaleDateString() }}</span>
                    </div>
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
      </div>
    </div>
  </div>
  
  <!-- Back to Top Button -->
  <button
    v-show="showBackToTop"
    @click="scrollToTop"
    class="fixed bottom-6 right-6 p-3 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 transition-all transform hover:scale-110 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
    aria-label="Back to top"
  >
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
    </svg>
  </button>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue'
import axios from 'axios'

const people = ref([])
const error = ref(null)
const showAddForm = ref(false)
const editingPerson = ref(null)
const originalName = ref(null)
const currentLetter = ref(null)
const showBackToTop = ref(false)
const nameInput = ref(null)

// Create array of all letters A-Z
const allLetters = Array.from('ABCDEFGHIJKLMNOPQRSTUVWXYZ')

// Use all letters instead of just available ones
const availableLetters = computed(() => allLetters)

// Computed property for grouped people, including empty groups
const groupedPeople = computed(() => {
  const groups = {}
  allLetters.forEach(letter => {
    groups[letter] = people.value.filter(person => 
      person.name.charAt(0).toUpperCase() === letter
    )
  })
  return groups
})

const scrollToLetter = (letter) => {
  const element = document.getElementById('letter-' + letter)
  if (element) {
    const offset = 100 // Offset to account for sticky header and navigation
    const elementPosition = element.getBoundingClientRect().top + window.pageYOffset
    window.scrollTo({
      top: elementPosition - offset,
      behavior: 'smooth'
    })
    currentLetter.value = letter
  }
}

// Update current letter based on scroll position
const updateCurrentLetter = () => {
  const letters = availableLetters.value
  const scrollPosition = window.scrollY
  
  // Show/hide back to top button
  showBackToTop.value = scrollPosition > 500

  for (let i = letters.length - 1; i >= 0; i--) {
    const element = document.getElementById('letter-' + letters[i])
    if (element && element.getBoundingClientRect().top <= 100) {
      currentLetter.value = letters[i]
      break
    }
  }
}

// Fetch people from the API
const fetchPeople = async () => {
  try {
    const response = await axios.get('/api/people')
    if (response.data?.data) {
      people.value = response.data.data.sort((a, b) => 
        a.name.localeCompare(b.name)
      )
    }
  } catch (e) {
    error.value = 'Failed to fetch people'
    console.error('Error fetching people:', e)
  }
}

// Scroll to top function
const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  })
}

// Add scroll event listener
onMounted(() => {
  fetchPeople()
  window.addEventListener('scroll', updateCurrentLetter)
})

onBeforeUnmount(() => {
  window.removeEventListener('scroll', updateCurrentLetter)
})

const newPerson = ref({
  name: '',
  email: '',
  phone: '',
  date_of_birth: '',
  address: ''
})

async function addPerson() {
  try {
    const response = await axios.post('/api/people', newPerson.value)
    if (response.data?.data) {
      people.value.push(response.data.data)
      // Sort people array after adding new person
      people.value.sort((a, b) => a.name.localeCompare(b.name))
      showAddForm.value = false
      newPerson.value = {
        name: '',
        email: '',
        phone: '',
        date_of_birth: '',
        address: ''
      }
      error.value = null
      // Force recomputation of groupedPeople and availableLetters
      nextTick(() => {
        updateCurrentLetter()
      })
    } else {
      error.value = 'Failed to create person: unexpected response format'
      console.warn('Unexpected API response format:', response.data)
    }
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to create person'
    console.error('Error adding person:', e)
  }
}

async function startEdit(person) {
  // Create a deep copy of the person object with all fields
  editingPerson.value = {
    id: person.id,
    name: person.name || '',
    email: person.email || '',
    phone: person.phone || '',
    date_of_birth: person.date_of_birth || '',
    address: person.address || ''
  }
  originalName.value = person.name
}

async function cancelEdit() {
  editingPerson.value = null
}

async function updatePerson() {
  if (!editingPerson.value) return

  try {
    // Create update payload with all fields
    const updateData = {
      name: editingPerson.value.name,
      email: editingPerson.value.email,
      phone: editingPerson.value.phone,
      date_of_birth: editingPerson.value.date_of_birth,
      address: editingPerson.value.address
    }

    const response = await axios.patch(`/api/people/${editingPerson.value.id}`, updateData)
    if (response.data?.data) {
      const index = people.value.findIndex(p => p.id === editingPerson.value.id)
      if (index !== -1) {
        // Replace the entire person object with the response data
        people.value[index] = response.data.data
      }
      editingPerson.value = null
      error.value = null
    } else {
      console.warn('Unexpected API response format:', response.data)
      error.value = 'Failed to update person: unexpected response format'
    }
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

const toggleAddForm = () => {
  showAddForm.value = !showAddForm.value
  if (showAddForm.value) {
    nextTick(() => {
      nameInput.value?.focus()
    })
  }
}
</script>
