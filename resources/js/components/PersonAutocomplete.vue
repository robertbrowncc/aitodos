<template>
  <v-select
    v-model="selected"
    :options="people"
    :reduce="person => person.id"
    label="name"
    :clearable="true"
    placeholder="Search for a person..."
    class="person-select"
  />
</template>

<script setup>
import { ref, watch } from 'vue'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'

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

const selected = ref(null)

// Watch for external changes
watch(() => props.modelValue, (newValue) => {
  selected.value = newValue
}, { immediate: true })

// Emit changes when selection changes
watch(() => selected.value, (newValue) => {
  emit('update:modelValue', newValue)
})
</script>

<style>
.person-select.v-select {
  @apply mt-1;
}
.person-select.v-select .vs__dropdown-toggle {
  @apply rounded-lg border-blue-300 shadow-sm;
}
.person-select.v-select.vs--open .vs__dropdown-toggle {
  @apply border-blue-500 ring-1 ring-blue-500;
}
.person-select.v-select .vs__selected {
  @apply text-sm;
}
.person-select.v-select .vs__dropdown-menu {
  @apply rounded-md border border-gray-200 shadow-lg;
}
.person-select.v-select .vs__dropdown-option {
  @apply px-3 py-2;
}
.person-select.v-select .vs__dropdown-option--highlight {
  @apply bg-blue-50 text-black;
}
.person-select.v-select .vs__clear {
  @apply fill-gray-400 hover:fill-gray-600;
}
</style>
