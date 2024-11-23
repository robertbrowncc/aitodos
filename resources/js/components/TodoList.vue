<template>
  <div class="max-w-2xl mx-auto">
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
        class="space-y-4 bg-white p-6 rounded-lg shadow-md mb-8"
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
            type="url" 
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
            <option value="">Select a person</option>
            <option v-for="person in people" :key="person.id" :value="person.id">
              {{ person.first_name }} {{ person.last_name }}
            </option>
          </select>
        </div>
        <button 
          type="submit" 
          class="w-full inline-flex justify-center rounded-lg border border-transparent bg-blue-500 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors transform hover:scale-105"
        >
          Add Todo
        </button>
      </form>
    </div>

    <div v-if="todos.length === 0" class="text-center text-gray-500 my-8">
      No todos yet. Add one above!
    </div>

    <div v-else class="space-y-4">
      <div v-for="todo in todos" :key="todo.id" class="bg-white p-4 rounded-lg shadow flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <input 
            type="checkbox"
            :checked="todo.completed"
            @change="toggleTodo(todo)"
            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
          >
          <div>
            <span :class="{ 'line-through text-gray-500': todo.completed }" class="text-lg">
              {{ todo.name }}
            </span>
            <div v-if="todo.person" class="text-sm text-gray-600">
              Assigned to: {{ todo.person.first_name }} {{ todo.person.last_name }}
            </div>
            <a 
              v-if="todo.url" 
              :href="todo.url" 
              target="_blank"
              class="text-sm text-blue-500 hover:text-blue-700"
            >
              View Link
            </a>
          </div>
        </div>
        <div class="flex items-center space-x-4">
          <select
            v-model="todo.person_id"
            @change="updateAssignment(todo)"
            class="rounded-lg border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
          >
            <option value="">Unassigned</option>
            <option v-for="person in people" :key="person.id" :value="person.id">
              {{ person.first_name }} {{ person.last_name }}
            </option>
          </select>
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

<script>
export default {
  data() {
    return {
      todos: [],
      people: [],
      showAddForm: false,
      newTodo: {
        name: '',
        url: '',
        person_id: ''
      }
    }
  },
  methods: {
    async fetchTodos() {
      try {
        const response = await fetch('/api/todos')
        this.todos = await response.json()
      } catch (error) {
        console.error('Error fetching todos:', error)
      }
    },
    async fetchPeople() {
      try {
        const response = await fetch('/api/todos/people')
        this.people = await response.json()
      } catch (error) {
        console.error('Error fetching people:', error)
      }
    },
    async addTodo() {
      try {
        const response = await fetch('/api/todos', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': this.getCsrfToken()
          },
          body: JSON.stringify(this.newTodo)
        })
        
        const todo = await response.json()
        this.todos.push(todo)
        this.newTodo = {
          name: '',
          url: '',
          person_id: ''
        }
      } catch (error) {
        console.error('Error adding todo:', error)
      }
    },
    async toggleTodo(todo) {
      try {
        const response = await fetch(`/api/todos/${todo.id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': this.getCsrfToken()
          },
          body: JSON.stringify({
            completed: !todo.completed
          })
        })
        
        const updatedTodo = await response.json()
        const index = this.todos.findIndex(t => t.id === todo.id)
        this.todos[index] = updatedTodo
      } catch (error) {
        console.error('Error toggling todo:', error)
      }
    },
    async updateAssignment(todo) {
      try {
        const response = await fetch(`/api/todos/${todo.id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': this.getCsrfToken()
          },
          body: JSON.stringify({
            person_id: todo.person_id || null
          })
        })
        
        const updatedTodo = await response.json()
        const index = this.todos.findIndex(t => t.id === todo.id)
        this.todos[index] = updatedTodo
      } catch (error) {
        console.error('Error updating assignment:', error)
      }
    },
    async deleteTodo(todo) {
      if (!confirm('Are you sure you want to delete this todo?')) return
      
      try {
        await fetch(`/api/todos/${todo.id}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': this.getCsrfToken()
          }
        })
        
        const index = this.todos.findIndex(t => t.id === todo.id)
        this.todos.splice(index, 1)
      } catch (error) {
        console.error('Error deleting todo:', error)
      }
    },
    getCsrfToken() {
      return document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
  },
  mounted() {
    this.fetchTodos()
    this.fetchPeople()
  }
}
</script>
