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
          <select
            id="personId"
            v-model="newTodo.person_id"
            class="mt-1 block w-full rounded-lg border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
          >
            <option :value="null">Select a person</option>
            <option v-for="person in people" :key="person.id" :value="person.id">
              {{ person.first_name }} {{ person.last_name }}
            </option>
          </select>
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
                Assigned to: {{ todo.person.first_name }} {{ todo.person.last_name }}
                <select
                  v-model="todo.person_id"
                  @change="updateAssignment(todo)"
                  class="ml-2 text-sm border-gray-300 rounded-md"
                >
                  <option :value="null">Unassign</option>
                  <option v-for="person in people" :key="person.id" :value="person.id">
                    {{ person.first_name }} {{ person.last_name }}
                  </option>
                </select>
              </div>
              <div v-else class="text-sm text-gray-600">
                <select
                  v-model="todo.person_id"
                  @change="updateAssignment(todo)"
                  class="text-sm border-gray-300 rounded-md"
                >
                  <option :value="null">Assign to someone</option>
                  <option v-for="person in people" :key="person.id" :value="person.id">
                    {{ person.first_name }} {{ person.last_name }}
                  </option>
                </select>
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

const todos = ref([])
const people = ref([])
const error = ref(null)
const showAddForm = ref(false)
const newTodo = ref({
  name: '',
  url: '',
  person_id: null
})

const fetchTodos = async () => {
  error.value = null
  try {
    const response = await fetch('/api/todos')
    if (!response.ok) throw new Error('Failed to fetch todos')
    todos.value = await response.json()
  } catch (err) {
    error.value = `Error loading todos: ${err.message}`
    console.error('Error:', err)
  }
}

const fetchPeople = async () => {
  error.value = null
  try {
    const response = await fetch('/api/people', {
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    })
    
    if (!response.ok) {
      const errorData = await response.json().catch(() => ({}))
      throw new Error(errorData.message || `Failed to fetch people: ${response.status} ${response.statusText}`)
    }
    
    const data = await response.json()
    if (!Array.isArray(data)) {
      throw new Error('Invalid response format: expected an array of people')
    }
    
    people.value = data
  } catch (err) {
    error.value = `Error loading people: ${err.message}`
    console.error('Error:', err)
  }
}

const addTodo = async () => {
  error.value = null
  try {
    const response = await fetch('/api/todos', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify(newTodo.value)
    })

    if (!response.ok) throw new Error('Failed to create todo')
    
    const todo = await response.json()
    todos.value.push(todo)
    
    // Reset form
    newTodo.value = {
      name: '',
      url: '',
      person_id: null
    }
    showAddForm.value = false
  } catch (err) {
    error.value = `Error creating todo: ${err.message}`
    console.error('Error:', err)
  }
}

const toggleTodo = async (todo) => {
  error.value = null
  try {
    const requestBody = {
      completed: !todo.completed,
      person_id: todo.person_id || null
    };
    console.log('Sending update request:', requestBody);
    
    const response = await fetch(`/api/todos/${todo.id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify(requestBody)
    })

    if (!response.ok) {
      const errorData = await response.text();
      console.error('Server response:', errorData);
      throw new Error('Failed to update todo');
    }
    
    const updatedTodo = await response.json()
    const index = todos.value.findIndex(t => t.id === todo.id)
    todos.value[index] = updatedTodo
  } catch (err) {
    error.value = `Error updating todo: ${err.message}`
    console.error('Error:', err)
  }
}

const updateAssignment = async (todo) => {
  error.value = null
  try {
    const requestBody = {
      person_id: todo.person_id || null,
      completed: todo.completed
    };
    console.log('Sending update request:', requestBody);
    
    const response = await fetch(`/api/todos/${todo.id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify(requestBody)
    })

    if (!response.ok) {
      const errorData = await response.text();
      console.error('Server response:', errorData);
      throw new Error('Failed to update assignment');
    }
    
    const updatedTodo = await response.json()
    const index = todos.value.findIndex(t => t.id === todo.id)
    todos.value[index] = updatedTodo
  } catch (err) {
    error.value = `Error updating assignment: ${err.message}`
    console.error('Error:', err)
  }
}

const deleteTodo = async (todo) => {
  if (!confirm('Are you sure you want to delete this todo?')) return

  error.value = null
  try {
    const response = await fetch(`/api/todos/${todo.id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    })

    if (!response.ok) throw new Error('Failed to delete todo')
    
    todos.value = todos.value.filter(t => t.id !== todo.id)
  } catch (err) {
    error.value = `Error deleting todo: ${err.message}`
    console.error('Error:', err)
  }
}

onMounted(() => {
  fetchTodos()
  fetchPeople()
})
</script>
