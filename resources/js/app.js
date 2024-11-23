import './bootstrap';
import { createApp } from 'vue';
import TodoList from './components/TodoList.vue';
import PeopleList from './components/PeopleList.vue';

// Mount TodoList component if element exists
const todoElement = document.querySelector('#todo-list');
if (todoElement) {
    createApp(TodoList).mount('#todo-list');
}

// Mount PeopleList component if element exists
const peopleElement = document.querySelector('#people-list');
if (peopleElement) {
    createApp(PeopleList).mount('#people-list');
}
