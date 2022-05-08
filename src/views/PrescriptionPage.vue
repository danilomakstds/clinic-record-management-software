<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-title>
            <img src="../../resources/logo.png" style="height:20px" class="me-2"/>
            Prescriptions
        </ion-title>
      </ion-toolbar>
    </ion-header>
    <ion-content :fullscreen="true">
      
      <ion-refresher slot="fixed" @ionRefresh="doRefresh($event)">
        <ion-refresher-content></ion-refresher-content>
      </ion-refresher>

      <ion-card class="animated4 animatedFadeInUp fadeInUp" v-for="app in allAppointments" :key="app.id">
        <ion-item @click="toggleShowMoreModal(app)">
          <ion-icon :icon="calendarOutline" slot="start"></ion-icon>
          <ion-label>{{app.agendaTitle}}</ion-label>
          <ion-icon :icon="arrowForwardOutline" slot="end"></ion-icon>
        </ion-item>

        <ion-card-content>
          <ion-icon :icon="timeOutline" slot="start" class="me-2"></ion-icon>{{app.timeSlot}}<br/>
          <ion-icon :icon="calendarOutline" slot="start" class="me-2"></ion-icon>{{app.convertedDate}}<br/>
          <span class="badge rounded-pill bg-success mt-4" style="font-size: 13px">Done</span>
        </ion-card-content>
      </ion-card>

      <div class="d-flex align-items-center h-75" v-if="!allAppointments.length">
        <div class="text-center">
          <img src="../../resources/slide-3.png" />
          <span style="font-size:15px">No Items to show here! Please create <br/> an <a href="./tabs/appointment" class="text-decoration-none">appointment</a> first</span>
        </div>
      </div>

      <ion-modal
        :is-open="isShowMoreDetailsOpen"
        :breakpoints="[0.1, 0.7, 0.85]"
        :initialBreakpoint="0.7"
        @didDismiss="toggleShowMoreModal()"
      >
        <ion-content>
          <div class="p-4">
             <ion-list>
                <ion-item>
                  <ion-icon :icon="personCircleOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label>{{appFullname}}</ion-label>
                </ion-item>
                <ion-item>
                  <ion-icon :icon="timeOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label>{{appTimeSlot}}</ion-label>
                </ion-item>
                <ion-item>
                  <ion-icon :icon="calendarOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label>{{appConvertedDate}}</ion-label>
                </ion-item>
                <ion-item>
                  <ion-icon :icon="informationCircleOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label>{{appConcern}}</ion-label>
                </ion-item>
                <ion-item>
                    <ion-icon :icon="accessibilityOutline" slot="start" class="me-2"></ion-icon>
                    <ion-label><span class="badge bg-light text-dark">Height</span> {{appHeight}} cm</ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-icon :icon="barbellOutline" slot="start" class="me-2"></ion-icon>
                    <ion-label><span class="badge bg-light text-dark">Weight</span> {{appWeight}} kg</ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-icon :icon="speedometerOutline" slot="start" class="me-2"></ion-icon>
                    <ion-label><span class="badge bg-light text-dark">Blood Pressure</span> {{appBP}}</ion-label>
                  </ion-item>
                <ion-item>
                  <ion-icon :icon="bandageOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-danger">Diagnosis</span> {{appDiagnosis}}</ion-label>
                </ion-item>
                <ion-item @click="printPrescription(app)">
                  <ion-icon :icon="bandageOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label>{{appPrescription}}</ion-label>
                  <ion-icon :icon="printOutline" slot="end"></ion-icon>
                </ion-item>
             </ion-list>
          </div>
        </ion-content>
      </ion-modal>

    </ion-content>
  </ion-page>
</template>

<script lang="js">
import { defineComponent } from 'vue';

import { IonPage, IonHeader, IonToolbar, IonTitle,
IonContent, IonRefresher, IonRefresherContent,
IonCard, IonCardContent,
IonItem, IonLabel, modalController, IonModal} from '@ionic/vue';

import { chevronDownCircleOutline, pin, calendarOutline,
timeOutline, arrowForwardOutline, callOutline, addOutline,
chevronBackOutline, chevronForwardOutline, personCircleOutline,
informationCircleOutline, bandageOutline, printOutline,
accessibilityOutline, barbellOutline, speedometerOutline} from 'ionicons/icons';
//import store from '../store'
import moment from 'moment'
import { mapState } from 'vuex'
import AppConstants from '../constants/app.constants'
import axios from "axios"
import SettingsConstants from '../constants/settings.constants'
import Swal from 'sweetalert2';

export default  defineComponent({
  name: 'PrescriptionPage',
  components: { IonHeader, IonToolbar, IonTitle,
  IonContent, IonPage, IonRefresher, IonRefresherContent,
  IonCard, IonCardContent, IonItem, IonLabel,
  IonModal},
  data() {
    return {
      allAppointments: [],
      isShowMoreDetailsOpen: false,
      appAgenda: null,
      appTimeSlot: null,
      appConvertedDate: null,
      appPrescription: null,
      appDiagnosis: null,
      appConcern: null,
      appFullname: null,
      appHeight: null,
      appWeight: null,
      appBP: null,
      appointmentSlots: []
    }
  },
  setup() {
    const doRefresh = function (event) {
      location.reload(true);
      setTimeout(function () {
        event.target.complete();
      }, 2000);
    }
    return {
      chevronDownCircleOutline, doRefresh, pin,
      calendarOutline, timeOutline, arrowForwardOutline,
      callOutline, addOutline, chevronBackOutline,
      chevronForwardOutline, personCircleOutline, informationCircleOutline,
      bandageOutline, printOutline, accessibilityOutline, barbellOutline, speedometerOutline
    }
  },
  computed: mapState([
      'sessionData'
  ]),
  methods: {
    async dismiss() {
      modalController .dismiss({
        'dismissed': true
      });
    },
    printPrescription: function (app) {
      Swal.fire(
        'Sorry!',
        'Feature comming soon!',
        'warning'
      );
      console.log(app);
    },
    toggleShowMoreModal: function (app) {
      this.isShowMoreDetailsOpen = !this.isShowMoreDetailsOpen;
      if (app) {
        this.appAgenda = app.agendaTitle;
        this.appTimeSlot = app.timeSlot;
        this.appConvertedDate = app.convertedDate;
        this.appPrescription = app.app_prescription;
        this.appDiagnosis = app.app_diagnosis;
        this.appConcern = app.app_patientconcerns;
        this.appFullname = app.fullName;
        this.appHeight = app.app_patient_height;
        this.appWeight = app.app_patient_weight;
        this.appBP = app.app_patient_bp;
      } else {
        this.appAgenda = null;
        this.appTimeSlot = null;
        this.appConvertedDate = null;
        this.appPrescription = null;
        this.appDiagnosis = null;
        this.appConcern = null;
        this.appFullname = null;
        this.appHeight = null;
        this.appWeight = null;
        this.appBP = null;
      }
    },
    getAllSchedule: function () {
       axios.get(SettingsConstants.BASE_URL + 'schedule.rest.php?type=all', { crossdomain: true })
          .then(function (response) {
            if (response.data) {
              this.appointmentSlots = response.data;
              this.getAllAppointments();
            }
          }.bind(this));
    },
    getAllAppointments: function() {
      this.allAppointments = [];
      var url = 'appointment.rest.php?type=alladdreesedbyId&userId=' + this.sessionData.id;
      axios.get(SettingsConstants.BASE_URL + url, { crossdomain: true })
          .then(function (response) {
              if (response.data) {
                  this.allAppointments = response.data;
                  this.allAppointments.forEach(function(app) {
                    app.agendaTitle = null;
                    app.timeSlot = null;
                    app.fullName = null;
                    app.fullName = this.sessionData.user_lastname + ', ' + this.sessionData.user_firstname + (this.sessionData.user_middlename ? ' '+this.sessionData.user_middlename : '');
                    switch(parseInt(app.app_apptype)){
                      case AppConstants.APPOINTMENT_SERVICES.LABOR.VALUE:
                        app.agendaTitle = AppConstants.APPOINTMENT_SERVICES.LABOR.TITLE
                        break;
                      case AppConstants.APPOINTMENT_SERVICES.PRENATAL_CARE.VALUE:
                        app.agendaTitle = AppConstants.APPOINTMENT_SERVICES.PRENATAL_CARE.TITLE
                        break;
                      case AppConstants.APPOINTMENT_SERVICES.CHECKUP.VALUE:
                        app.agendaTitle = AppConstants.APPOINTMENT_SERVICES.CHECKUP.TITLE
                        break;
                      case AppConstants.APPOINTMENT_SERVICES.COVID_VACCINE.VALUE:
                        app.agendaTitle = AppConstants.APPOINTMENT_SERVICES.COVID_VACCINE.TITLE
                        break;
                      case AppConstants.APPOINTMENT_SERVICES.ANTI_RABIES.VALUE:
                        app.agendaTitle = AppConstants.APPOINTMENT_SERVICES.ANTI_RABIES.TITLE
                        break;
                      case AppConstants.APPOINTMENT_SERVICES.ANTI_TETANUS.VALUE:
                        app.agendaTitle = AppConstants.APPOINTMENT_SERVICES.ANTI_TETANUS.TITLE
                        break;
                      case AppConstants.APPOINTMENT_SERVICES.DEPO_PROVERA.VALUE:
                        app.agendaTitle = AppConstants.APPOINTMENT_SERVICES.DEPO_PROVERA.TITLE
                        break;
                    }
                    this.appointmentSlots.forEach( function (slot){
                      if (slot.slotid == app.app_timeslot) {
                        app.timeSlot = slot.sched;
                        app.timeStart = slot.timestart;
                        app.timeEnd = slot.timeend;
                      }
                    }.bind(app));
                    app.convertedDate = moment(app.app_date).format('LL');
                  }.bind(this));
              }
          }.bind(this));
    },
  },
  mounted() {
    this.getAllSchedule();
  },
});
</script>
