<template>
<ion-page>
    <ion-tabs>
        <ion-router-outlet id="main"></ion-router-outlet>
        <ion-tab-bar slot="bottom">
            <ion-tab-button tab="tab1" href="/tabs/appointment">
                <ion-icon :icon="calendarOutline" />
                <ion-label>Appointment</ion-label>
            </ion-tab-button>

            <ion-tab-button tab="tab2" href="/tabs/patients" v-if="sessionData.user_level > 0">
                <ion-icon :icon="personAddOutline" />
                <ion-label v-if="sessionData.user_level < 3">Patients</ion-label>
                <ion-label v-else>Users List</ion-label>
            </ion-tab-button>

            <ion-tab-button tab="tab3" href="/tabs/inventory" v-if="sessionData.user_level > 0">
                <ion-icon :icon="listOutline" />
                <ion-label>Drug Inventory</ion-label>
            </ion-tab-button>

            <ion-tab-button tab="tab4" href="/tabs/prescription" v-if="sessionData.user_level == 0">
                <ion-icon :icon="documentOutline" />
                <ion-label>Prescriptions</ion-label>
            </ion-tab-button>

            <ion-tab-button tab="tab5" @click="openFirst()">
                <ion-icon :icon="optionsOutline" />
                <ion-label>Options</ion-label>
            </ion-tab-button>
        </ion-tab-bar>
    </ion-tabs>
</ion-page>
<ion-menu side="start" menu-id="first" content-id="main">
    <ion-header>
        <ion-toolbar color="primary">
            <ion-title>Menu Options</ion-title>
        </ion-toolbar>
    </ion-header>
    <ion-content>
        <ion-list>
            <ion-item @click="logOutUserModal()">
                <ion-icon :icon="logOutOutline" slot="start" />
                <ion-label>Logout</ion-label>
            </ion-item>
        </ion-list>
        <ion-list v-if="sessionData.user_level > 0">
            <ion-item>
                <ion-toggle color="primary" slot="start" @ionChange="onChangeToggle()" v-model="isToday"></ion-toggle>
                <ion-label>Today's Appointments</ion-label>
            </ion-item>
        </ion-list>
        <ion-list v-if="sessionData.user_level == 3">
            <ion-item>
                <ion-toggle color="primary" slot="start" @ionChange="onChangeShowAdmins()" v-model="isShowAdmin"></ion-toggle>
                <ion-label>Show admins only</ion-label>
            </ion-item>
        </ion-list>
        <ion-list v-if="sessionData.user_level == 3">
            <ion-item>
                <ion-toggle color="primary" slot="start" @ionChange="onChangeShowshowPatients()" v-model="isShowPatients"></ion-toggle>
                <ion-label>Show patients only</ion-label>
            </ion-item>
        </ion-list>
    </ion-content>
</ion-menu>
</template>

<script lang="js">
import { defineComponent } from 'vue';

import {
    IonTabBar,
    IonTabButton,
    IonTabs,
    IonLabel,
    IonIcon,
    IonPage,
    IonRouterOutlet,
    IonContent,
    IonHeader,
    IonItem,
    IonList,
    IonMenu,
    IonTitle,
    IonToolbar,
    menuController,
    alertController,
    IonToggle,
    //IonItemDivider
} from '@ionic/vue';

import {
    optionsOutline,
    calendar,
    personAdd,
    list,
    document,
    calendarOutline,
    personAddOutline,
    listOutline,
    documentOutline,
    logOutOutline
} from 'ionicons/icons';
import { mapState } from 'vuex'
import store from '../store'

export default defineComponent({
    name: 'TabsPage',
    watch: {
        isShowPatients: function (newVal) {
            if (newVal) {
                this.isShowAdmin = false;  
            }
        },
        isShowAdmin: function (newVal) {
            if (newVal) {
                this.isShowPatients = false;
            }
        }
    },
    data() {
        return {
            isToday: false,
            isShowAdmin: false,
            isShowPatients: false
        }
    },
    computed: mapState([
        'sessionData',
        'isAppointmentListToday',
        'showPatientsOnly',
        'showAdminsOnly'
    ]),
    components: {
        IonLabel,
        IonTabs,
        IonTabBar,
        IonTabButton,
        IonIcon,
        IonPage,
        IonRouterOutlet,
        IonContent,
        IonHeader,
        IonItem,
        IonList,
        IonMenu,
        IonTitle,
        IonToolbar,
        IonToggle,
        //IonItemDivider
    },
    setup() {
        return {
            calendar,
            personAdd,
            list,
            document,
            calendarOutline,
            personAddOutline,
            listOutline,
            documentOutline,
            optionsOutline,
            logOutOutline,
        }
    },
    methods: {
        openFirst() {
            menuController.open('first');
        },
        onChangeShowAdmins: function () {
            store.commit('SET_SHOW_ADMINS_ONLY', this.isShowAdmin);
            this.emitter.emit('isShowAdminChange');
        },
        onChangeShowshowPatients: function () {
            store.commit('SET_SHOW_PATIENTS_ONLY', this.isShowPatients);
            this.emitter.emit('isShowPatientChange');
        },
        onChangeToggle: function () {
            store.commit('SET_TODAY_APPOINTMENT_LIST', this.isToday);
            this.emitter.emit('isTodayChanged');
        },
        logOutUser: function () {
            store.commit('RESET_SESSION_DATA');
            if (!this.sessionData) {
                location.href = "/login";
            }
        },
        async logOutUserModal() {
            const alert = await alertController
                .create({
                    cssClass: 'my-custom-class',
                    header: 'Log Out!',
                    message: 'Are you sure you would like to <strong>log out</strong>!',
                    buttons: [{
                            text: 'Cancel',
                            role: 'cancel',
                            cssClass: 'secondary',
                            id: 'cancel-button',
                            handler: blah => {
                                console.log('Confirm Cancel:', blah)
                            },
                        },
                        {
                            text: 'Logout',
                            id: 'confirm-button',
                            handler: () => {
                                console.log('Confirm Okay');
                                this.logOutUser();
                            },
                        },
                    ],
                });
            return alert.present();
        },
    },
    mounted() {
      this.isToday = this.isAppointmentListToday;
      this.isShowAdmin = this.showAdminsOnly;
      this.isShowPatients = this.showPatientsOnly;
    },
});
</script>
