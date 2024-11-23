import { createRouter, createWebHistory } from 'vue-router'
import Welcome from './components/Welcome.vue'
import TodoList from './components/TodoList.vue'
import PeopleList from './components/PeopleList.vue'
import ActivityList from './components/ActivityList.vue'

const routes = [
  {
    path: '/',
    name: 'welcome',
    component: Welcome
  },
  {
    path: '/todos',
    name: 'todos',
    component: TodoList
  },
  {
    path: '/people',
    name: 'people',
    component: PeopleList
  },
  {
    path: '/activities',
    name: 'activities',
    component: ActivityList
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
