<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-title><img src="../../resources/logo.png" style="height:20px" class="me-2"/>Appointments</ion-title>
      </ion-toolbar>
    </ion-header>
    <ion-content :fullscreen="true">
      
      <ion-refresher slot="fixed" @ionRefresh="doRefresh($event)">
        <ion-refresher-content></ion-refresher-content>
      </ion-refresher>

      <div class="p-2 mt-2">
        <div class="d-flex justify-content-around">
          <div class="animated animatedFadeInUp fadeInUp">
            <span class="text-muted">Good {{welcomMessage}},</span>
            <h3 class="mt-0">{{sessionData.user_lastname}}, {{sessionData.user_firstname}} {{sessionData.user_middlename}}</h3>
          </div>
          <div id="userImage" class="animated2 animatedFadeInUp fadeInUp">
            <img src="../../resources/avatar.svg" style="height:50px; border-radius: 10px"/>
          </div>
        </div>

        
        <ion-card class="animated2 animatedFadeInUp fadeInUp" v-for="app in allAppointments" :key="app.id" >
          <ion-item @click="toggleAddDiagnosisModal(app)" :disabled="app.disabledPrescription">
            <ion-icon :icon="calendarOutline" slot="start"></ion-icon>
            <ion-label>{{app.agendaTitle}}</ion-label>
            <ion-icon :icon="arrowForwardOutline" slot="end"></ion-icon>
          </ion-item>

          <ion-card-content>
            <div v-if="sessionData.user_level > 0">
              <ion-icon :icon="personCircleOutline" slot="start" class="me-2"></ion-icon>{{app.fullName}}
            </div>
            
            <ion-icon :icon="calendarOutline" slot="start" class="me-2"></ion-icon>{{app.convertedDate}}<br/>
            <ion-icon :icon="timeOutline" slot="start" class="me-2"></ion-icon>{{app.timeSlot}}<br/>
          </ion-card-content>
        </ion-card>

        <div class="d-flex align-items-center mt-5" v-if="allAppointments.length == 0 && isLoadingItems == false && sessionData.user_level > 0">
          <div class="text-center">
            <img src="../../resources/slide-3.png" />
            <span style="font-size:15px" v-if="sessionData.user_level > 0">There are no appointments <span v-if="!isAppointmentListToday">to show!</span><span v-else>for today!</span></span>
          </div>
        </div>
        <div class="alert alert-info m-3 animated5 animatedFadeInUp fadeInUp" role="alert" v-if="allAppointments.length == 0 && isLoadingItems == false && sessionData.user_level == 0">
          <span style="font-size:15px">You have no upcoming appointments</span>
        </div>

        <ion-card class="animated4 animatedFadeInUp fadeInUp" v-if="sessionData.user_level == 0">
          <div style="width: 100%; overflow: hidden; height: 250px;">
            <div style="z-index:1000; position: absolute; border-radius: 10px; width:173px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class="bg-white p-2 m-3">
              Simborio Barangay Health Center <br>
              <a
              class="text-decoration-none" style="font-size:11px"
              href="https://www.google.com/maps/place/Simborio+Barangay+Health+Center/@9.475841,123.108306,15z/data=!4m5!3m4!1s0x0:0x4feef823daabd5a8!8m2!3d9.4758415!4d123.1083085?hl=en-US">
              View larger map
              </a>
            </div>
          <iframe width="100%" height="550" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"  style="border:0; margin-top: -150px;"  src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15741.510702807593!2d123.1083068!3d9.475841!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33ab7e6eb50f5a2f%3A0x4feef823daabd5a8!2sSimborio%20Barangay%20Health%20Center!5e0!3m2!1sen!2sph!4v1648729640632!5m2!1sen!2sph"></iframe>
          </div>
          <ion-card-header>
            <ion-card-subtitle>Location</ion-card-subtitle>
            <ion-card-title>Simborio Barangay Health Center</ion-card-title>
          </ion-card-header>
          <ion-card-content>
            Government office in Pamplona, 
            <strong>Address</strong> F4G5+88M, Bayan ng Pamplona, Lalawigan ng Negros Oriental
            <div class="mt-3">
              <a href="tel:123-456-7890">
                <button type="button" class="btn btn-outline-success">
                  <ion-icon :icon="callOutline"></ion-icon>
                  123-456-7890
                </button>
              </a>
            </div>
          </ion-card-content>
        </ion-card>

        <ion-modal
        :is-open="isAppointmentModalOpen"
        @didDismiss="toggleAppointmentModal()"
      >
        <ion-content>
          <ion-datetime presentation="date" @ionChange="checkForEnabledSlots()" v-model="selectedDate">
            <div slot="title">
              <div class="d-flex flex-row mb-3" @click="dismiss()">
                <ion-icon :icon="chevronBackOutline" slot="start" size="large"/>
                <ion-title slot="start">Back</ion-title>
              </div>
            </div>
          </ion-datetime>

           <!-- Item Dividers in a List -->
          <ion-list>
            <ion-item-divider>
              <ion-label>
                Morning Schedule
              </ion-label>
            </ion-item-divider>
            <div v-for="appointment in appointmentSlots" :key="appointment.SLOTID">
              <ion-item class="ps-2" :id="'slot-'+appointment.SLOTID"
              data-bs-toggle="modal" data-bs-target="#createAppointmentModal" 
              @click="openAppointmentModal(appointment)" :disabled="appointment.disabled">
                <ion-label>{{appointment.SCHED}}</ion-label>
                <ion-icon :icon="chevronForwardOutline" slot="end"/>
              </ion-item>
              <ion-item-divider v-if="appointment.SLOTID == 8">
                <ion-label>
                  Afternoon Schedule
                </ion-label>
              </ion-item-divider>
            </div>
          </ion-list>

          <br/>
        </ion-content>
        </ion-modal>
        
        <ion-modal
          :is-open="isAddDiagnosisPrescription"
          @didDismiss="toggleAddDiagnosisModal()"
          v-if="apdModalData"
        >
          <ion-header>
            <ion-toolbar @click="dismiss()" class="ms-2">
              <ion-icon :icon="chevronBackOutline" slot="start" size="large"/>
              <ion-title slot="start">{{apdModalData.agendaTitle}}</ion-title>
            </ion-toolbar>
          </ion-header>
          <ion-content>
            <div class="p-4">
              <form @submit="savePrescription()">
                <ion-list>
                  <ion-item>
                    <ion-icon :icon="personCircleOutline" slot="start" class="me-2"></ion-icon>
                    <ion-label>{{apdModalData.fullName}}</ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-icon :icon="timeOutline" slot="start" class="me-2"></ion-icon>
                    <ion-label>{{apdModalData.timeSlot}}</ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-icon :icon="calendarOutline" slot="start" class="me-2"></ion-icon>
                    <ion-label>{{apdModalData.convertedDate}}</ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <h5><strong>Patient's Concern</strong></h5>
                      <div>
                        <textarea class="form-control" style="height:100px; border-radius: 15px" v-model="appConcern" required></textarea>
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <h5><strong>Diagnosis</strong></h5>
                      <div>
                        <textarea class="form-control" style="height:100px; border-radius: 15px" v-model="appDiagnosis" required></textarea>
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <h5><strong>Prescription</strong></h5>
                      <div>
                        <textarea class="form-control" style="height:100px; border-radius: 15px" v-model="appPrescription" required></textarea>
                      </div>
                    </ion-label>
                  </ion-item>
                </ion-list>
                <button type="button" class="btn btn-primary w-100 btn-lg" @click="concernAddressed(apdModalData)">
                  <ion-icon :icon="checkmarkOutline" slot="start" class="me-2"></ion-icon>
                  Marked Done
                </button>
              </form>
              <br/><br/>
            </div>
          </ion-content>
        </ion-modal>
      </div>

      <ion-fab vertical="bottom" horizontal="end" slot="fixed" v-if="sessionData.user_level == 0">
        <ion-fab-button @click="toggleAppointmentModal()">
          <ion-icon :icon="addOutline"></ion-icon>
        </ion-fab-button>
      </ion-fab>

    </ion-content>
  </ion-page>
</template>

<script lang="js">
import { defineComponent } from 'vue';
import { IonPage, IonHeader, IonToolbar, IonTitle, IonContent, IonRefresher, IonRefresherContent,
IonCard, IonCardContent, IonCardHeader, IonCardSubtitle, IonCardTitle, IonModal, IonDatetime, modalController,
IonItem, IonItemDivider, IonLabel } from '@ionic/vue';
import { chevronDownCircleOutline, pin, calendarOutline,
timeOutline, arrowForwardOutline, callOutline, addOutline,
chevronBackOutline, chevronForwardOutline, personCircleOutline, checkmarkOutline} from 'ionicons/icons';
import store from '../store'
import moment from 'moment'
import { mapState } from 'vuex'
import AppConstants from '../constants/app.constants'
import axios from "axios"
import SettingsConstants from '../constants/settings.constants'
import Swal from 'sweetalert2';

export default  defineComponent({
  name: 'ScheduleAppointment',
  components: { IonHeader, IonToolbar, IonTitle, IonContent, IonPage, IonRefresher, IonRefresherContent,
  IonCard, IonCardContent, IonCardHeader, IonCardSubtitle, IonCardTitle, IonModal, IonDatetime,
  IonItem, IonItemDivider, IonLabel},
  data() {
    return {
      today: null,
      isAppointmentModalOpen: false,
      selectedDate: null,
      appointmentSlots: [],
      allAppointments: [],
      isAddDiagnosisPrescription: false,
      apdModalData: [],
      appConcern: null,
      appDiagnosis: null,
      appPrescription: null,
      appDateandTime: [],
      isLoadingItems: true,
      welcomMessage: null,
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
      chevronForwardOutline, personCircleOutline, checkmarkOutline
    }
  },
  computed: mapState([
      'sessionData',
      'isAppointmentListToday'
  ]),
  methods: {
    async dismiss() {
      modalController .dismiss({
        'dismissed': true
      });
    },
    toggleAddDiagnosisModal: function (appData) {
      console.log(appData);
      this.isAddDiagnosisPrescription = !this.isAddDiagnosisPrescription;
      if (this.isAddDiagnosisPrescription) {
        this.apdModalData = appData;
      }
    },
    resetFields: function () {
      this.appConcern = null;
      this.appDiagnosis = null;
      this.appPrescription = null;
    },
    concernAddressed: function (details) {
      event.preventDefault();
      var bodyFormData = new FormData();
      bodyFormData.append('app_concern', this.appConcern);
      bodyFormData.append('app_diagnosis', this.appDiagnosis);
      bodyFormData.append('app_prescription', this.appPrescription);
      axios({
          method: "post",
          url: SettingsConstants.BASE_URL + "appointment.rest.php?type=adddiagnosis&appointmentId="+ details.id,
          data: bodyFormData,
          headers: { "Content-Type": "multipart/form-data" },
      })
          .then(function (response) {
            if (response.data == 1) {
                Swal.fire(
                  'Great!',
                  'Appointment has marked as done!',
                  'success'
                ).then(function (){
                  this.dismiss();
                  this.resetFields();
                  this.getAllAppointments();
                }.bind(this));
            } else {
                Swal.fire(
                  'Error!',
                  'Something went wrong when updating appointment!',
                  'error'
                );
            }
          }.bind(this))
          .catch(function (response) {
              console.log(response);
          });
    },
    getAllAppointments: function() {
      this.isLoadingItems = true;
      var today = moment().format().split('T')[0];
      this.allAppointments = [];
      var url = null;
      (this.sessionData.user_level > 0) ? url = 'appointment.rest.php?type=all' : url = 'appointment.rest.php?type=allbyId&userId=' + this.sessionData.id;
      if (this.isAppointmentListToday) {
        url = url + '&isToday=' + today;
      }
      axios.get(SettingsConstants.BASE_URL + url, { crossdomain: true })
          .then(function (response) {
              if (response.data) {
                this.isLoadingItems = false;
                this.allAppointments = response.data;
                this.allAppointments.forEach(function(app) {
                  app.agendaTitle = null;
                  app.timeSlot = null;
                  app.fullName = null;
                  (this.sessionData.user_level == 2) ? app.disabledPrescription = false: app.disabledPrescription = true;
                  axios.get(SettingsConstants.BASE_URL + 'users.rest.php?type=nameonly&userId=' + app.app_userId, { crossdomain: true })
                  .then(function (response) {
                    if (response.data) {
                      var resp = response.data[0];
                      app.fullName = resp.user_lastname + ', ' + resp.user_firstname + (resp.user_middlename ? ' '+resp.user_middlename : '');
                    }
                  }.bind(app));
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
                  AppConstants.APPOINTMENT_SLOTS.forEach( function (slot){
                    if (slot.SLOTID == app.app_timeslot) {
                      app.timeSlot = slot.SCHED;
                      app.timeStart = slot.TIMESTART;
                      app.timeEnd = slot.TIMEEND;
                    }
                  }.bind(app));
                  app.convertedDate = moment(app.app_date).format('LL');
                }.bind(this));
              } else {
                this.isLoadingItems = false;
              }
          }.bind(this));
    },
    storeAppointmentDetails: function (details) {
      store.commit('SET_APPOINTMENTVIEW_DETAILS', details);
    },
    openAppointmentModal: function (appointment) {
      this.storeAppointmentDetails(appointment)
      console.log(appointment);
    },
    toggleAppointmentModal: function () {
      this.isAppointmentModalOpen = !this.isAppointmentModalOpen;
    },
    checkForEnabledSlots: function () {
      this.getAppDateandTimeSlot();
      this.appointmentSlots = AppConstants.APPOINTMENT_SLOTS;
      var getNowTime = new Date();
      var allowance = 2;
      getNowTime = getNowTime.setHours(getNowTime.getHours() + allowance);
      this.appointmentSlots.forEach(function (slot) {
        var startTime = null;
        if (this.selectedDate) {
          slot.selectedDate = this.selectedDate;
          slot.title = moment(this.selectedDate).format('dddd') +', '+ moment(this.selectedDate).format('LL');
          startTime = moment(this.selectedDate).format().split("T")[0]+'T'+slot.TIMESTART+'+'+moment(this.selectedDate).format().split("+")[1];
        } else {
          startTime = moment().format().split("T")[0]+'T'+slot.TIMESTART+'+'+moment().format().split("+")[1];
        }
        getNowTime > new Date(startTime).getTime() || (moment(startTime).format('dddd') == 'Saturday' || moment(startTime).format('dddd') == 'Sunday') ? slot.disabled = true : slot.disabled = false;
      }.bind(this));
    },
    getAppDateandTimeSlot: function () {
      this.appDateandTime = [];
      axios.get(SettingsConstants.BASE_URL + 'appointment.rest.php?type=gettimeslotanddate', { crossdomain: true })
        .then(function (response) {
            if (response.data) {
                this.appDateandTime = response.data;
                this.appDateandTime.forEach(function (timedate){
                  timedate.dateonly = timedate.app_date.split("T")[0];
                });
                this.appointmentSlots.forEach(function (slot) {
                  var startTime = null;
                  if (this.selectedDate) {
                    startTime = moment(this.selectedDate).format().split("T")[0];
                  } else {
                    startTime = moment().format().split("T")[0];
                  }
                  this.appDateandTime.filter( function (datetime){
                    if (datetime.app_timeslot == slot.SLOTID.toString() && startTime == datetime.dateonly){
                      if(!slot.disabled) {
                        slot.disabled = true;
                      }
                    }
                  }.bind(startTime));
                }.bind(this));
            }
        }.bind(this));
    }
  },
  mounted() {
    this.appointmentSlots = AppConstants.APPOINTMENT_SLOTS;
    moment().format('LT').includes("AM") ? this.welcomMessage = 'morning' : this.welcomMessage = 'afternoon';
    if (this.sessionData.user_level == 0) {
      this.checkForEnabledSlots();
    }
    this.getAllAppointments();
    this.emitter.on('isTodayChanged', function () {
      this.getAllAppointments();
    }.bind(this));
  },
});
</script>
