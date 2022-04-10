import { createRouter, createWebHistory } from '@ionic/vue-router';
import { RouteRecordRaw } from 'vue-router';
import TabsPage from '../views/TabsPage.vue'
import store from '../store'

const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    component: () => import('@/views/LoginPage.vue')
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/LoginPage.vue')
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('@/views/RegisterPage.vue')
  },
  {
    path: '/empty',
    name: 'Empty',
    component: () => import('@/views/EmptyPage.vue')
  },
  {
    path: '/tabs/',
    name: 'TabsPage',
    component: TabsPage,
    children: [
      {
        path: '',
        redirect: '/tabs/appointment'
      },
      {
        path: 'appointment',
        name: 'ScheduleAppointment',
        component: () => import('@/views/ScheduleAppointment.vue')
      },
      {
        path: 'patients',
        name: 'PatientsPage',
        component: () => import('@/views/PatientsPage.vue')
      },
      {
        path: 'inventory',
        name: 'DrugInventory',
        component: () => import('@/views/DrugInventory.vue')
      },
      {
        path: 'prescription',
        name: 'PrescriptionPage',
        component: () => import('@/views/PrescriptionPage.vue')
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

router.beforeEach((to) => {
  const sessionData = store.state.sessionData;
  if (to.name == 'Login' || to.path == "/") {
    if (sessionData) {
      return { name: 'ScheduleAppointment' };
    }
  } else {
    if (to.name == 'Register') {
      if (sessionData) {
        return { name: 'ScheduleAppointment' };
      }
    } else {
      if (!sessionData) {
        return { name: 'Login' };
      }
    }
  }
})

export default router
