<template>
  <div class="relative">
    <input
      type="text"
      v-model="searchQuery"
      @input="onInput"
      @focus="showSuggestions = true"
      @blur="onBlur"
      @keydown="handleKeydown"
      :placeholder="placeholder"
      class="mt-1 block w-full rounded-lg border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
    >
    <div
      v-if="showSuggestions && filteredPeople.length > 0"
      class="absolute z-10 mt-1 w-full bg-white shadow-lg rounded-md border border-gray-200 max-h-60 overflow-auto"
    >
      <ul class="py-1">
        <li
          v-for="(person, index) in filteredPeople"
          :key="person.id"
          @mousedown="selectPerson(person)"
          @mouseover="highlightedIndex = index"
          :class="{
            'px-3 py-2 cursor-pointer': true,
            'bg-blue-50': highlightedIndex === index,
            'hover:bg-blue-50': highlightedIndex !== index
          }"
        >
          {{ person.name }}
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: [Number, null],
    default: null
  },
  people: {
    type: Array,
    required: true
  },
  placeholder: {
    type: String,
    default: 'Search for a person...'
  }
})

const emit = defineEmits(['update:modelValue'])

const searchQuery = ref('')
const showSuggestions = ref(false)
const highlightedIndex = ref(-1)

// Update search query when a person is selected externally
watch(() => props.modelValue, (newValue) => {
  if (newValue === null) {
    searchQuery.value = ''
  } else {
    const person = props.people.find(p => p.id === newValue)
    if (person) {
      searchQuery.value = person.name
    }
  }
}, { immediate: true })

const filteredPeople = computed(() => {
  if (!searchQuery.value) return props.people
  const query = searchQuery.value.toLowerCase()
  return props.people.filter(person => 
    person.name.toLowerCase().includes(query)
  )
})

const onInput = () => {
  showSuggestions.value = true
  emit('update:modelValue', null)
  highlightedIndex.value = -1
}

const selectPerson = (person) => {
  searchQuery.value = person.name
  emit('update:modelValue', person.id)
  showSuggestions.value = false
  highlightedIndex.value = -1
}

const handleKeydown = (event) => {
  if (!showSuggestions.value || filteredPeople.value.length === 0) {
    return
  }

  switch (event.key) {
    case 'ArrowDown':
      event.preventDefault()
      highlightedIndex.value = (highlightedIndex.value + 1) % filteredPeople.value.length
      break

    case 'ArrowUp':
      event.preventDefault()
      highlightedIndex.value = highlightedIndex.value <= 0 
        ? filteredPeople.value.length - 1 
        : highlightedIndex.value - 1
      break

    case 'Enter':
      event.preventDefault()
      if (highlightedIndex.value >= 0) {
        selectPerson(filteredPeople.value[highlightedIndex.value])
      }
      break

    case 'Escape':
      event.preventDefault()
      showSuggestions.value = false
      highlightedIndex.value = -1
      break

    case 'Tab':
      // If an item is highlighted, select it
      if (highlightedIndex.value >= 0) {
        event.preventDefault()
        selectPerson(filteredPeople.value[highlightedIndex.value])
      }
      // Otherwise, let the normal tab behavior occur
      showSuggestions.value = false
      break
  }
}

const onBlur = () => {
  // Delay hiding suggestions to allow for mousedown event
  setTimeout(() => {
    showSuggestions.value = false
    highlightedIndex.value = -1
    // Reset to selected person's name or empty if none selected
    if (props.modelValue) {
      const person = props.people.find(p => p.id === props.modelValue)
      if (person) {
        searchQuery.value = person.name
      }
    } else {
      searchQuery.value = ''
    }
  }, 200)
}
</script>
