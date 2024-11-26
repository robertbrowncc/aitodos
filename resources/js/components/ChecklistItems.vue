<template>
  <div class="checklist-items">
    <!-- Add Item Form -->
    <form @submit.prevent="addItem" class="mb-4">
      <div class="flex space-x-2">
        <input
          v-model="newItemContent"
          type="text"
          placeholder="Add new item..."
          class="flex-grow px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
          required
        >
        <button type="submit" class="btn btn-primary">
          Add
        </button>
      </div>
    </form>

    <!-- Items List -->
    <div v-if="items.length === 0" class="text-gray-500 text-center py-4">
      No items yet. Add your first item above.
    </div>
    
    <TransitionGroup
      name="checklist"
      tag="div"
      class="space-y-2"
    >
      <div 
        v-for="(item, index) in items" 
        :key="`item-${item.id}`"
        class="flex items-center space-x-2 p-2 bg-gray-50 rounded-md"
        draggable="true"
        @dragstart="startDrag($event, index)"
        @dragover.prevent
        @dragenter.prevent
        @drop="onDrop($event, index)"
      >
        <span class="cursor-move text-gray-400 px-2">↕</span>
        <input
          type="checkbox"
          :checked="item.completed"
          @change="toggleComplete(item)"
          class="rounded text-indigo-600 focus:ring-indigo-500"
        >
        <span
          :class="{ 'line-through text-gray-400': item.completed }"
          class="flex-grow"
        >
          {{ item.content }}
        </span>
        <button
          @click.prevent="deleteItem(item)"
          class="px-2 py-1 bg-red-100 text-red-600 hover:bg-red-200 rounded-md"
        >
          Delete
        </button>
      </div>
    </TransitionGroup>
    <div v-if="error" class="text-red-600 text-center py-4">
      {{ error }}
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'ChecklistItems',
  props: {
    checklist: {
      type: Object,
      required: true,
      validator: function(value) {
        return value && typeof value.id !== 'undefined'
      }
    }
  },
  data() {
    return {
      newItemContent: '',
      items: [],
      draggedItem: null,
      error: null
    }
  },
  errorCaptured(err, vm, info) {
    console.error('Error captured in ChecklistItems:', err, info)
    this.error = err.message || 'An unexpected error occurred'
    return false // prevent error from propagating
  },
  watch: {
    'checklist.id': {
      immediate: true,
      async handler(newId) {
        if (newId) {
          await this.fetchItems()
        } else {
          this.items = []
        }
      }
    }
  },
  methods: {
    async fetchItems() {
      if (!this.checklist?.id) {
        this.items = []
        return
      }

      try {
        const response = await axios.get(`/api/checklists/${this.checklist.id}/items`)
        if (!response.data || !Array.isArray(response.data.data)) {
          throw new Error('Invalid response format')
        }
        this.items = response.data.data
        this.error = null
      } catch (error) {
        console.error('Error fetching checklist items:', error)
        this.error = error.response?.data?.message || error.message || 'Failed to load checklist items'
        this.items = []
      }
    },
    async addItem() {
      if (!this.newItemContent.trim()) return
      
      try {
        const response = await axios.post(`/api/checklists/${this.checklist.id}/items`, {
          content: this.newItemContent.trim(),
          completed: false
        })
        this.items.push(response.data.data)
        this.newItemContent = ''
        this.error = null
      } catch (error) {
        console.error('Error adding item:', error)
        this.error = error.response?.data?.message || error.message || 'Failed to add item'
      }
    },
    async toggleComplete(item) {
      const newCompletedState = !item.completed
      try {
        const response = await axios.patch(`/api/checklists/${this.checklist.id}/items/${item.id}`, {
          completed: newCompletedState
        })
        Object.assign(item, response.data.data)
      } catch (error) {
        this.$emit('error', error.response?.data?.message || 'Failed to update item')
        item.completed = !newCompletedState // Revert the change
      }
    },
    async deleteItem(item) {
      try {
        await axios.delete(`/api/checklists/${this.checklist.id}/items/${item.id}`)
        const index = this.items.indexOf(item)
        if (index > -1) {
          this.items.splice(index, 1)
        }
      } catch (error) {
        this.$emit('error', error.response?.data?.message || 'Failed to delete item')
      }
    },
    startDrag(event, index) {
      this.draggedItem = index
      event.dataTransfer.effectAllowed = 'move'
    },
    async onDrop(event, index) {
      event.preventDefault()
      if (this.draggedItem === null) return
      if (this.draggedItem === index) return

      // Update local state
      const itemToMove = this.items.splice(this.draggedItem, 1)[0]
      this.items.splice(index, 0, itemToMove)
      
      // Update order on server
      try {
        const response = await axios.post(`/api/checklists/${this.checklist.id}/reorder`, {
          order: this.items.map(item => item.id)
        })
        // Update items with server response to ensure consistency
        this.items = response.data.data
      } catch (error) {
        this.$emit('error', error.response?.data?.message || 'Failed to update item order')
        await this.fetchItems() // Refresh items to restore correct order
      }
      
      this.draggedItem = null
    }
  }
}
</script>

<style scoped>
.checklist-items {
  min-height: 50px;
}

.btn {
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  font-weight: 500;
  transition: all 0.2s;
}

.btn-primary {
  background-color: #4f46e5;
  color: white;
}

.btn-primary:hover {
  background-color: #4338ca;
}

/* Transition styles */
.checklist-move {
  transition: transform 0.3s ease;
}

.checklist-enter-active,
.checklist-leave-active {
  transition: all 0.3s ease;
}

.checklist-enter-from,
.checklist-leave-to {
  opacity: 0;
  transform: translateY(30px);
}
</style>
