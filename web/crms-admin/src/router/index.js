import { createRouter, createWebHistory } from 'vue-router'
import LoginPage from '../views/LoginPage.vue'
import MainPage from '../views/DashboardPage.vue'
import AppointmentsPage from '../views/AppointmentsPage.vue'
import PatientsPage from '../views/PatientsPage.vue'
import DrugInventoryPage from '../views/DrugInventoryPage.vue'
import store from '../store';

const routes = [
  {
    path: '/',
    name: 'LoginPage',
    component: LoginPage
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'LoginPage',
    component: LoginPage
  },
  {
    path: '/dashboard',
    name: 'DashboardPage',
    component: MainPage
  },
  {
    path: '/appointments',
    name: 'AppointmentsPage',
    component: AppointmentsPage
  },
  {
    path: '/patients',
    name: 'PatientsPage',
    component: PatientsPage
  },
  {
    path: '/drug',
    name: 'DrugInventoryPage',
    component: DrugInventoryPage
  },
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

router.beforeEach((to) => {
  const sessionData = store.state.sessionData;
  if (to.name == 'Login' || to.path == "/") {
    if (sessionData) {
      return { name: 'DashboardPage' };
    }
  } else {
    if (!sessionData) {
      return { name: 'Login' };
    }
  }
})

export default router
