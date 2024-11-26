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
        <span class="text-lg font-medium text-blue-800">Add New Todo</span>
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
        @submit.prevent="addTodo" 
        class="space-y-4 bg-white p-6 rounded-lg shadow-md"
      >
        <div>
          <label for="todoName" class="block text-sm font-medium text-blue-800">Task Name</label>
          <input 
            type="text" 
            id="todoName"
            v-model="newTodo.name" 
            class="mt-1 block w-full rounded-lg border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            placeholder="Enter a new task"
            required
          >
        </div>
        <div>
          <label for="todoUrl" class="block text-sm font-medium text-blue-800">URL (Optional)</label>
          <input 
            type="text" 
            id="todoUrl"
            v-model="newTodo.url" 
            class="mt-1 block w-full rounded-lg border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            placeholder="https://example.com"
          >
        </div>
        <div>
          <label for="personId" class="block text-sm font-medium text-blue-800">Assign To (Optional)</label>
          <PersonAutocomplete
            v-model="newTodo.person_id"
            :people="people"
            placeholder="Search for a person..."
          />
        </div>
        <button 
          type="submit" 
          class="w-full inline-flex justify-center rounded-lg border border-transparent bg-blue-500 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
        >
          Add Todo
        </button>
      </form>
    </div>

    <div v-if="todos.length === 0" class="text-center text-gray-500 my-8">
      No todos yet. Add one above!
    </div>

    <div v-else class="space-y-4">
      <div v-for="todo in todos" :key="todo.id" class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-start justify-between">
          <div class="flex items-start space-x-3">
            <input 
              type="checkbox" 
              :checked="todo.completed" 
              @change="toggleTodo(todo)"
              class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            >
            <div>
              <h3 class="text-lg font-medium" :class="{ 'line-through text-gray-500': todo.completed }">
                {{ todo.name }}
              </h3>
              <a 
                v-if="todo.url" 
                :href="todo.url" 
                target="_blank" 
                class="text-sm text-blue-600 hover:text-blue-800"
              >
                {{ todo.url }}
              </a>
              <div v-if="todo.person" class="text-sm text-gray-600">
                Assigned to:
                <PersonAutocomplete
                  v-model="todo.person_id"
                  :people="people"
                  @update:modelValue="updateAssignment(todo)"
                  class="inline-block min-w-[200px]"
                />
              </div>
              <div v-else class="text-sm text-gray-600">
                <PersonAutocomplete
                  v-model="todo.person_id"
                  :people="people"
                  @update:modelValue="updateAssignment(todo)"
                  placeholder="Assign to someone..."
                  class="inline-block min-w-[200px]"
                />
              </div>
            </div>
          </div>
          <button 
            @click="deleteTodo(todo)"
            class="text-red-500 hover:text-red-700"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import PersonAutocomplete from './PersonAutocomplete.vue'

const todos = ref([])
const people = ref([])
const error = ref('')
const showAddForm = ref(false)
const newTodo = ref({
  name: '',
  url: '',
  person_id: null,
  completed: false
})

async function fetchTodos() {
  try {
    const response = await axios.get('/api/todos');
    todos.value = response.data.data;
    error.value = '';
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load todos';
    console.error('Error fetching todos:', err);
    todos.value = []; // Reset todos on error
  }
}

async function addTodo() {
  try {
    const response = await axios.post('/api/todos', newTodo.value)
    todos.value.unshift(response.data.data)
    newTodo.value = {
      name: '',
      url: '',
      person_id: null,
      completed: false
    }
    showAddForm.value = false
    error.value = ''
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to add todo'
    console.error('Error adding todo:', err)
  }
}

async function toggleTodo(todo) {
  try {
    const response = await axios.patch(`/api/todos/${todo.id}`, {
      completed: !todo.completed
    })
    Object.assign(todo, response.data.data)
    error.value = ''
  } catch (err) {
    todo.completed = !todo.completed // Revert the change
    error.value = err.response?.data?.message || 'Failed to update todo'
    console.error('Error updating todo:', err)
  }
}

async function updateAssignment(todo) {
  try {
    const response = await axios.patch(`/api/todos/${todo.id}`, {
      person_id: todo.person_id
    })
    Object.assign(todo, response.data.data)
    error.value = ''
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to update assignment'
    console.error('Error updating assignment:', err)
  }
}

async function deleteTodo(todo) {
  if (!confirm('Are you sure you want to delete this todo?')) return
  
  try {
    await axios.delete(`/api/todos/${todo.id}`)
    const index = todos.value.indexOf(todo)
    if (index > -1) {
      todos.value.splice(index, 1)
    }
    error.value = ''
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to delete todo'
    console.error('Error deleting todo:', err)
  }
}

onMounted(() => {
  fetchTodos()
  fetchPeople()
})

async function fetchPeople() {
  try {
    const response = await axios.get('/api/people')
    people.value = response.data.data
  } catch (err) {
    console.error('Error fetching people:', err)
    error.value = err.response?.data?.message || 'Failed to load people'
  }
}
</script>
