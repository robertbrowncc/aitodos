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
        console.log('Fetching items for list:', this.list.id);
        const response = await axios.get(`/api/lists/${this.list.id}/items`)
        console.log('Fetched items:', response.data);
        this.items = response.data
      } catch (error) {
        console.error('Error fetching items:', error)
      }
    },
    async addItem() {
      if (!this.newItemContent.trim()) return;
      
      try {
        console.log('Adding item to list:', this.list.id, 'Content:', this.newItemContent);
        const response = await axios.post(`/api/lists/${this.list.id}/items`, {
          content: this.newItemContent.trim(),
          completed: false
        });
        console.log('Response:', response.data);
        this.items.push(response.data);
        this.newItemContent = '';
      } catch (error) {
        console.error('Error adding item:', error.response?.data || error.message);
      }
    },
    async toggleComplete(item) {
      const newCompletedState = !item.completed;
      
      try {
        console.log('Toggling item completion:', item.id, newCompletedState);
        await axios.patch(`/api/lists/${this.list.id}/items/${item.id}`, {
          completed: newCompletedState
        });
        item.completed = newCompletedState;
      } catch (error) {
        console.error('Error toggling item completion:', error);
        // Revert the UI state if the API call fails
        item.completed = !newCompletedState;
      }
    },
    async deleteItem(item) {
      try {
        console.log('Deleting item:', item.id);
        await axios.delete(`/api/lists/${this.list.id}/items/${item.id}`);
        const index = this.items.findIndex(i => i.id === item.id);
        if (index !== -1) {
          this.items.splice(index, 1);
        }
      } catch (error) {
        console.error('Error deleting item:', error);
        await this.fetchItems();
      }
    },
    startDrag(event, index) {
      console.log('Started dragging item at index:', index);
      this.draggedItem = index;
      event.dataTransfer.effectAllowed = 'move';
      // Add some visual feedback
      event.target.classList.add('dragging');
    },
    async onDrop(event, index) {
      event.preventDefault();
      event.target.classList.remove('dragging');
      
      // Don't do anything if dropping onto the same item
      if (this.draggedItem === index) {
        console.log('Dropped on same position, ignoring');
        return;
      }

      try {
        console.log('Moving item from index', this.draggedItem, 'to', index);
        
        // Get the dragged item
        const itemToMove = this.items[this.draggedItem];
        
        // Remove the item from its original position
        this.items.splice(this.draggedItem, 1);
        
        // Add it at the new position
        this.items.splice(index, 0, itemToMove);

        // Update the order on the server
        console.log('Updating order on server');
        await axios.patch(`/api/lists/${this.list.id}/items/${itemToMove.id}`, {
          order: index
        });
        
        // Refresh the list to ensure order is correct
        await this.fetchItems();
      } catch (error) {
        console.error('Error updating item order:', error);
        await this.fetchItems(); // Refresh the list if there was an error
      }
      
      this.draggedItem = null;
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
