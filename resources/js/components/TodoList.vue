<template>
  <div class="max-w-2xl mx-auto">
    <div class="mb-6">
      <form @submit.prevent="addTodo" class="space-y-4 bg-white p-6 rounded-lg shadow-md mb-8">
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
            type="url" 
            id="todoUrl"
            v-model="newTodo.url" 
            class="mt-1 block w-full rounded-lg border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            placeholder="https://example.com"
          >
        </div>
        <button 
          type="submit" 
          class="w-full inline-flex justify-center rounded-lg border border-transparent bg-blue-500 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors transform hover:scale-105"
        >
          Add Todo
        </button>
      </form>
    </div>

    <div class="space-y-3">
      <div v-for="todo in todos" :key="todo.id" 
        class="flex items-center justify-between p-4 bg-white/80 backdrop-blur-sm rounded-lg shadow-lg hover:shadow-xl transition-all"
      >
        <div class="flex items-center space-x-3">
          <input 
            type="checkbox" 
            :checked="todo.done" 
            @change="toggleTodo(todo)"
            class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-blue-300 rounded"
          >
          <div>
            <span :class="{ 'line-through text-gray-400': todo.done, 'text-blue-800': !todo.done }" class="text-lg">
              {{ todo.name }}
            </span>
            <a 
              v-if="todo.url" 
              :href="todo.url" 
              target="_blank" 
              class="block text-sm text-blue-600 hover:text-blue-800 hover:underline"
            >
              {{ todo.url }}
            </a>
          </div>
        </div>
        <button 
          @click="deleteTodo(todo)" 
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
</template>

<script setup>
import { ref, onMounted } from 'vue'

const todos = ref([])
const newTodo = ref({
  name: '',
  url: '',
  done: false
})

// Get CSRF token from meta tag
const getCsrfToken = () => {
  return document.querySelector('meta[name="csrf-token"]').getAttribute('content')
}

const fetchTodos = async () => {
  try {
    const response = await fetch('/api/todos')
    if (!response.ok) {
      const error = await response.text()
      throw new Error(error)
    }
    todos.value = await response.json()
  } catch (error) {
    console.error('Error fetching todos:', error)
  }
}

const addTodo = async () => {
  if (!newTodo.value.name.trim()) return

  try {
    const response = await fetch('/api/todos', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
      },
      body: JSON.stringify(newTodo.value)
    })

    if (!response.ok) {
      const error = await response.text()
      throw new Error(error)
    }
    
    const todo = await response.json()
    todos.value.unshift(todo)
    newTodo.value.name = ''
    newTodo.value.url = ''
  } catch (error) {
    console.error('Error adding todo:', error)
  }
}

const toggleTodo = async (todo) => {
  try {
    const response = await fetch(`/api/todos/${todo.id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
      },
      body: JSON.stringify({
        ...todo,
        done: !todo.done
      })
    })

    if (!response.ok) {
      const error = await response.text()
      throw new Error(error)
    }

    const updatedTodo = await response.json()
    const index = todos.value.findIndex(t => t.id === todo.id)
    todos.value[index] = updatedTodo
  } catch (error) {
    console.error('Error updating todo:', error)
  }
}

const deleteTodo = async (todo) => {
  try {
    const response = await fetch(`/api/todos/${todo.id}`, {
      method: 'DELETE',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
      },
    })

    if (!response.ok) {
      const error = await response.text()
      throw new Error(error)
    }

    todos.value = todos.value.filter(t => t.id !== todo.id)
  } catch (error) {
    console.error('Error deleting todo:', error)
  }
}

onMounted(() => {
  fetchTodos()
})
</script>
