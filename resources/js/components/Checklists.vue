<template>
  <div class="checklists-container">
    <div class="checklists-header flex justify-between items-center mb-4">
      <h2 class="text-xl font-bold">Checklists</h2>
      <div class="flex space-x-2">
        <button 
          @click="expandAllChecklists" 
          class="btn btn-secondary"
          :disabled="checklists.length === 0"
        >
          Expand All
        </button>
        <button 
          @click="collapseAllChecklists" 
          class="btn btn-secondary"
          :disabled="checklists.length === 0"
        >
          Collapse All
        </button>
        <button @click="showNewChecklistForm = true" class="btn btn-primary">
          New Checklist
        </button>
      </div>
    </div>

    <!-- New Checklist Form -->
    <div v-if="showNewChecklistForm" class="mb-4 p-4 bg-white rounded shadow">
      <form @submit.prevent="createChecklist" class="space-y-4">
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700">Checklist Name</label>
          <input
            v-model="newChecklistName"
            type="text"
            id="name"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            required
          >
        </div>
        <div>
          <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
          <textarea
            v-model="newChecklistDescription"
            id="description"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            rows="3"
          ></textarea>
        </div>
        <div class="flex justify-end space-x-2">
          <button type="button" @click="showNewChecklistForm = false" class="btn btn-secondary">
            Cancel
          </button>
          <button type="submit" class="btn btn-primary">
            Create Checklist
          </button>
        </div>
      </form>
    </div>

    <!-- Checklists Container -->
    <div class="space-y-4">
      <div v-for="checklist in checklists" :key="checklist.id" class="bg-white p-4 rounded-lg shadow">
        <div class="flex justify-between items-center mb-4">
          <div class="flex items-center space-x-3">
            <button 
              @click="toggleChecklist(checklist.id)"
              class="text-gray-500 hover:text-gray-700 w-6 h-6 flex items-center justify-center"
              :title="isChecklistExpanded(checklist.id) ? 'Collapse Checklist' : 'Expand Checklist'"
            >
              <span class="text-xl" v-if="isChecklistExpanded(checklist.id)">▼</span>
              <span class="text-xl" v-else>▶</span>
            </button>
            <div>
              <h3 class="text-lg font-semibold">{{ checklist.name }}</h3>
              <p class="text-gray-600">{{ checklist.description }}</p>
            </div>
          </div>
          <button 
            @click="deleteChecklist(checklist)"
            class="px-3 py-1 bg-red-100 text-red-600 hover:bg-red-200 rounded-md"
          >
            Delete Checklist
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
          <div v-show="isChecklistExpanded(checklist.id)">
            <ChecklistItems :checklist="checklist" @update-checklist="fetchChecklists" />
          </div>
        </Transition>
      </div>
    </div>
  </div>
</template>

<script>
import ChecklistItems from './ChecklistItems.vue'
import axios from 'axios'

export default {
  name: 'Checklists',
  components: {
    ChecklistItems
  },
  data() {
    return {
      checklists: [],
      newChecklistName: '',
      newChecklistDescription: '',
      showNewChecklistForm: false,
      expandedChecklists: new Set() // Track which checklists are expanded
    }
  },
  mounted() {
    this.fetchChecklists()
  },
  methods: {
    async fetchChecklists() {
      try {
        const response = await axios.get('/api/checklists')
        this.checklists = response.data.data
      } catch (error) {
        this.$emit('error', error.response?.data?.message || 'Failed to load checklists')
      }
    },
    async createChecklist() {
      if (!this.newChecklistName.trim()) return
      
      try {
        const response = await axios.post('/api/checklists', {
          name: this.newChecklistName.trim(),
          description: this.newChecklistDescription
        })
        this.checklists.push(response.data.data)
        this.newChecklistName = ''
        this.newChecklistDescription = ''
        this.showNewChecklistForm = false
      } catch (error) {
        this.$emit('error', error.response?.data?.message || 'Failed to create checklist')
      }
    },
    async deleteChecklist(checklist) {
      if (!confirm(`Are you sure you want to delete "${checklist.name}"?`)) {
        return
      }
      try {
        await axios.delete(`/api/checklists/${checklist.id}`)
        const index = this.checklists.indexOf(checklist)
        if (index > -1) {
          this.checklists.splice(index, 1)
        }
        this.expandedChecklists.delete(checklist.id)
      } catch (error) {
        this.$emit('error', error.response?.data?.message || 'Failed to delete checklist')
      }
    },
    toggleChecklist(checklistId) {
      if (this.expandedChecklists.has(checklistId)) {
        this.expandedChecklists.delete(checklistId)
      } else {
        this.expandedChecklists.add(checklistId)
      }
    },
    isChecklistExpanded(checklistId) {
      return this.expandedChecklists.has(checklistId)
    },
    expandAllChecklists() {
      this.checklists.forEach(checklist => {
        this.expandedChecklists.add(checklist.id)
      })
    },
    collapseAllChecklists() {
      this.expandedChecklists.clear()
    }
  }
}
</script>

<style scoped>
.checklists-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 1rem;
}

.checklists-header {
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
