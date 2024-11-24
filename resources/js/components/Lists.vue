<template>
  <div class="lists-container">
    <div class="lists-header flex justify-between items-center mb-4">
      <h2 class="text-xl font-bold">Lists</h2>
      <div class="flex space-x-2">
        <button 
          @click="expandAllLists" 
          class="btn btn-secondary"
          :disabled="lists.length === 0"
        >
          Expand All
        </button>
        <button 
          @click="collapseAllLists" 
          class="btn btn-secondary"
          :disabled="lists.length === 0"
        >
          Collapse All
        </button>
        <button @click="showNewListForm = true" class="btn btn-primary">
          New List
        </button>
      </div>
    </div>

    <!-- New List Form -->
    <div v-if="showNewListForm" class="mb-4 p-4 bg-white rounded shadow">
      <form @submit.prevent="createList" class="space-y-4">
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700">List Name</label>
          <input
            v-model="newListName"
            type="text"
            id="name"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            required
          >
        </div>
        <div>
          <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
          <textarea
            v-model="newListDescription"
            id="description"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            rows="3"
          ></textarea>
        </div>
        <div class="flex justify-end space-x-2">
          <button type="button" @click="showNewListForm = false" class="btn btn-secondary">
            Cancel
          </button>
          <button type="submit" class="btn btn-primary">
            Create List
          </button>
        </div>
      </form>
    </div>

    <!-- Lists Container -->
    <div class="space-y-4">
      <div v-for="list in lists" :key="list.id" class="bg-white p-4 rounded-lg shadow">
        <div class="flex justify-between items-center mb-4">
          <div class="flex items-center space-x-3">
            <button 
              @click="toggleList(list.id)"
              class="text-gray-500 hover:text-gray-700 w-6 h-6 flex items-center justify-center"
              :title="isListExpanded(list.id) ? 'Collapse List' : 'Expand List'"
            >
              <span class="text-xl" v-if="isListExpanded(list.id)">▼</span>
              <span class="text-xl" v-else>▶</span>
            </button>
            <div>
              <h3 class="text-lg font-semibold">{{ list.name }}</h3>
              <p class="text-gray-600">{{ list.description }}</p>
            </div>
          </div>
          <button 
            @click="deleteList(list)"
            class="px-3 py-1 bg-red-100 text-red-600 hover:bg-red-200 rounded-md"
          >
            Delete List
          </button>
        </div>
        <Transition
          enter-active-class="transition-all duration-300 ease-out"
          enter-from-class="opacity-0 max-h-0 overflow-hidden"
          enter-to-class="opacity-100 max-h-[1000px] overflow-hidden"
          leave-active-class="transition-all duration-300 ease-in"
          leave-from-class="opacity-100 max-h-[1000px] overflow-hidden"
          leave-to-class="opacity-0 max-h-0 overflow-hidden"
        >
          <div v-show="isListExpanded(list.id)">
            <ListItems :list="list" @update-list="fetchLists" />
          </div>
        </Transition>
      </div>
    </div>
  </div>
</template>

<script>
import ListItems from './ListItems.vue'
import axios from 'axios'

export default {
  name: 'Lists',
  components: {
    ListItems
  },
  data() {
    return {
      lists: [],
      newListName: '',
      newListDescription: '',
      showNewListForm: false,
      expandedLists: new Set() // Track which lists are expanded
    }
  },
  mounted() {
    this.fetchLists()
  },
  methods: {
    async fetchLists() {
      try {
        const response = await axios.get('/api/lists')
        this.lists = response.data
      } catch (error) {
        console.error('Error loading lists:', error)
      }
    },
    async createList() {
      try {
        await axios.post('/api/lists', {
          name: this.newListName,
          description: this.newListDescription
        })
        this.newListName = ''
        this.newListDescription = ''
        this.showNewListForm = false
        await this.fetchLists()
      } catch (error) {
        console.error('Error creating list:', error)
      }
    },
    async deleteList(list) {
      if (!confirm(`Are you sure you want to delete "${list.name}"?`)) {
        return
      }
      try {
        await axios.delete(`/api/lists/${list.id}`)
        this.expandedLists.delete(list.id)
        await this.fetchLists()
      } catch (error) {
        console.error('Error deleting list:', error)
      }
    },
    toggleList(listId) {
      if (this.expandedLists.has(listId)) {
        this.expandedLists.delete(listId)
      } else {
        this.expandedLists.add(listId)
      }
    },
    isListExpanded(listId) {
      return this.expandedLists.has(listId)
    },
    expandAllLists() {
      this.lists.forEach(list => {
        this.expandedLists.add(list.id)
      })
    },
    collapseAllLists() {
      this.expandedLists.clear()
    }
  }
}
</script>

<style scoped>
.lists-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 1rem;
}

.lists-header {
  margin-bottom: 1rem;
}

.btn {
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  font-weight: 500;
  transition: all 0.2s;
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-primary {
  background-color: #4f46e5;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background-color: #4338ca;
}

.btn-secondary {
  background-color: #e5e7eb;
  color: #374151;
}

.btn-secondary:hover:not(:disabled) {
  background-color: #d1d5db;
}
</style>
