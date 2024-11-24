<template>
  <div class="list-items">
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
      v-else
      name="list"
      tag="div"
      class="space-y-2"
    >
      <div 
        v-for="(item, index) in items" 
        :key="item.id"
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
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'ListItems',
  props: {
    list: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      newItemContent: '',
      items: [],
      draggedItem: null
    }
  },
  watch: {
    'list.id': {
      immediate: true,
      handler() {
        this.fetchItems()
      }
    }
  },
  methods: {
    async fetchItems() {
      try {
        const response = await axios.get(`/api/lists/${this.list.id}/items`)
        this.items = response.data
      } catch (error) {
        this.$emit('error', 'Failed to load list items')
      }
    },
    async addItem() {
      if (!this.newItemContent.trim()) return
      
      try {
        const response = await axios.post(`/api/lists/${this.list.id}/items`, {
          content: this.newItemContent
        })
        this.items.push(response.data)
        this.newItemContent = ''
      } catch (error) {
        this.$emit('error', 'Failed to add item')
      }
    },
    async toggleComplete(item) {
      const newCompletedState = !item.completed
      try {
        await axios.patch(`/api/lists/${this.list.id}/items/${item.id}`, {
          completed: newCompletedState
        })
        item.completed = newCompletedState
      } catch (error) {
        this.$emit('error', 'Failed to update item')
      }
    },
    async deleteItem(item) {
      try {
        await axios.delete(`/api/lists/${this.list.id}/items/${item.id}`)
        const index = this.items.indexOf(item)
        if (index > -1) {
          this.items.splice(index, 1)
        }
      } catch (error) {
        this.$emit('error', 'Failed to delete item')
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
        // Update all items with their new order
        const updatedOrder = this.items.map((item, index) => ({
          id: item.id,
          order: index
        }))
        
        await axios.post(`/api/lists/${this.list.id}/reorder`, {
          order: updatedOrder.map(item => item.id)
        })
        
        // Refresh items to ensure we have the correct order
        await this.fetchItems()
      } catch (error) {
        console.error('Failed to update item order:', error)
        this.$emit('error', 'Failed to update item order')
        await this.fetchItems() // Refresh items to restore correct order
      }
      
      this.draggedItem = null
    }
  }
}
</script>

<style scoped>
.list-items {
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
.list-move {
  transition: transform 0.3s ease;
}

.list-enter-active,
.list-leave-active {
  transition: all 0.3s ease;
}

.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

.dragging {
  opacity: 0.5;
  background-color: #e5e7eb;
}
</style>
