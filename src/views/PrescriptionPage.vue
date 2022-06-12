<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-title>
          <img src="../../resources/logo.png" style="height:20px" class="me-2" />
          Appointment History
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
          <ion-label>{{ app.agendaTitle }}</ion-label>
          <ion-icon :icon="arrowForwardOutline" slot="end"></ion-icon>
        </ion-item>

        <ion-card-content>
          <ion-icon :icon="timeOutline" slot="start" class="me-2"></ion-icon>{{ app.timeSlot }}<br />
          <ion-icon :icon="calendarOutline" slot="start" class="me-2"></ion-icon>{{ app.convertedDate }}<br />
          <span class="badge rounded-pill bg-success mt-4" style="font-size: 13px">Done</span>
        </ion-card-content>
      </ion-card>

      <div class="d-flex align-items-center h-75" v-if="!allAppointments.length">
        <div class="text-center">
          <img src="../../resources/slide-3.png" />
          <span style="font-size:15px">No Items to show here! Please create <br /> an <a href="./tabs/appointment"
              class="text-decoration-none">appointment</a> first</span>
        </div>
      </div>

      <ion-modal :is-open="isShowMoreDetailsOpen"
        @didDismiss="toggleShowMoreModal()">
        <ion-header>
          <ion-toolbar @click="dismiss()" class="ms-2">
            <ion-icon :icon="chevronBackOutline" slot="start" size="large" />
            <ion-title slot="start">Back</ion-title>
          </ion-toolbar>
        </ion-header>
        <ion-content>
          <div class="pt-4 ps-4 pe-2" style="padding: 100px">
            <ion-list>
              <span id="forPrinting">
                <ion-item>
                  <ion-icon :icon="personCircleOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label>{{ appFullname }}</ion-label>
                </ion-item>
                <ion-item>
                  <ion-icon :icon="timeOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label>{{ appTimeSlot }}</ion-label>
                </ion-item>
                <ion-item>
                  <ion-icon :icon="calendarOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label>{{ appConvertedDate }}</ion-label>
                </ion-item>
                <ion-item>
                  <ion-icon :icon="informationCircleOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label>{{ appConcern }}</ion-label>
                </ion-item>
                <ion-item v-if="appHeight != 0">
                  <ion-icon :icon="accessibilityOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-light text-dark">Height</span> {{ appHeight }} cm</ion-label>
                </ion-item>
                <ion-item v-if="appWeight != 0">
                  <ion-icon :icon="barbellOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-light text-dark">Weight</span> {{ appWeight }} kg</ion-label>
                </ion-item>
                <ion-item v-if="appBP">
                  <ion-icon :icon="speedometerOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-light text-dark">Blood Pressure</span> {{ appBP }}</ion-label>
                </ion-item>
                <ion-item v-if="appTemp">
                  <ion-icon :icon="thermometerOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-light text-dark">Temperature</span> {{ appTemp }}</ion-label>
                </ion-item>
                <ion-item v-if="appMuac">
                  <ion-icon :icon="informationCircleOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-light text-dark">MUAC: </span> {{ appMuac }}</ion-label>
                </ion-item>
                <ion-item v-if="appMedHist">
                  <ion-icon :icon="informationCircleOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-light text-dark">Medical History: </span> {{ convertToIllnessString(appMedHist) }}</ion-label>
                </ion-item>
                <ion-item v-if="appLMP">
                  <ion-icon :icon="calendarNumberOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-light text-dark">LMP</span> {{ appLMP }}</ion-label>
                </ion-item>
                <ion-item v-if="appEDC">
                  <ion-icon :icon="calendarNumberOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-light text-dark">EDC</span> {{ appEDC }}</ion-label>
                </ion-item>
                <ion-item v-if="appHusbandName">
                  <ion-icon :icon="personCircleOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-light text-dark">Spouse's Name</span> {{ appHusbandName }}</ion-label>
                </ion-item>
                <ion-item v-if="appHusbandAge != 0">
                  <ion-icon :icon="informationCircleOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-light text-dark">Spouse's Age</span> {{ appHusbandAge }}</ion-label>
                </ion-item>
                <ion-item v-if="appChildCount != 0">
                  <ion-icon :icon="informationCircleOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-light text-dark">Living children</span> {{ appChildCount }}</ion-label>
                </ion-item>
                <ion-item v-if="appbabyStatus">
                  <ion-icon :icon="informationCircleOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-light text-dark">Patient status: </span> {{ appbabyStatus }}</ion-label>
                </ion-item>
                <ion-item v-if="appPlanningMore == 1">
                  <ion-icon :icon="informationCircleOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-light text-dark">Planning more children: </span> {{ appPlanningMore == 1 ? 'Yes' : 'No' }}</ion-label>
                </ion-item>
                <ion-item v-if="appHouseholdIncome != 0">
                  <ion-icon :icon="informationCircleOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-light text-dark">Household income: </span> {{ appHouseholdIncome }}</ion-label>
                </ion-item>
                <ion-item v-if="appDiagnosis">
                  <ion-icon :icon="bandageOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-danger">Diagnosis</span> {{ appDiagnosis }}</ion-label>
                </ion-item>
                <ion-item v-if="appPrescription">
                  <ion-icon :icon="bandageOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label>{{ appPrescription }}</ion-label>
                </ion-item>
              </span>
              

                <span v-if="babySheetData">
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>Low birth weight given Iron</strong></h4>
                      <div class="w-100 pe-2">
                        <ul class="list-group">
                          <li class="list-group-item">
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" disabled="true" value="" id="baby_lbwgi_1" v-model="baby_lbwgi_1">
                              <label class="form-check-label w-100" for="baby_lbwgi_1">
                                1 mo <span class="float-end" v-if="baby_lbwgi_1">{{babySheetData.baby_lbwgi_1_date ? babySheetData.baby_lbwgi_1_date : now}}</span>
                              </label>
                            </div>
                          </li>
                          <li class="list-group-item">
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" disabled="true" value="" id="baby_lbwgi_2" v-model="baby_lbwgi_2">
                              <label class="form-check-label w-100" for="baby_lbwgi_2">
                                2 mo <span class="float-end" v-if="baby_lbwgi_2">{{babySheetData.baby_lbwgi_2_date ? babySheetData.baby_lbwgi_2_date : now}}</span>
                              </label>
                            </div>
                          </li>
                          <li class="list-group-item">
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" disabled="true" value="" id="baby_lbwgi_3" v-model="baby_lbwgi_3">
                              <label class="form-check-label w-100" for="baby_lbwgi_3">
                                3 mo <span class="float-end" v-if="baby_lbwgi_3">{{babySheetData.baby_lbwgi_3_date ? babySheetData.baby_lbwgi_3_date : now}}</span>
                              </label>
                            </div>
                          </li>
                        </ul>
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>DPT - HiB - HepB</strong></h4>
                      <div class="w-100 pe-2">
                        <ul class="list-group">
                          <li class="list-group-item">
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" disabled="true" value="" id="baby_dpt_1" v-model="baby_dpt_1">
                              <label class="form-check-label w-100" for="baby_dpt_1">
                                1st dose <span class="badge rounded-pill bg-primary" style="font-size: 9px">1 1/2 mos</span>
                                <span class="float-end" v-if="baby_dpt_1">{{babySheetData.baby_dpt_1_date ? babySheetData.baby_dpt_1_date : now}}</span>
                              </label>
                            </div>
                          </li>
                          <li class="list-group-item">
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" disabled="true" value="" id="baby_dpt_2" v-model="baby_dpt_2">
                              <label class="form-check-label w-100" for="baby_dpt_2">
                                2nd dose <span class="badge rounded-pill bg-primary" style="font-size: 9px">2 1/2 mos</span>
                                <span class="float-end" v-if="baby_dpt_2">{{babySheetData.baby_dpt_2_date ? babySheetData.baby_dpt_2_date : now}}</span>
                              </label>
                            </div>
                          </li>
                          <li class="list-group-item">
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" disabled="true" value="" id="baby_dpt_3" v-model="baby_dpt_3">
                              <label class="form-check-label w-100" for="baby_dpt_3">
                                3rd dose <span class="badge rounded-pill bg-primary" style="font-size: 9px">3 1/2 mos</span>
                                <span class="float-end" v-if="baby_dpt_3">{{babySheetData.baby_dpt_3_date ? babySheetData.baby_dpt_3_date : now}}</span>
                              </label>
                            </div>
                          </li>
                        </ul>
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>OPV</strong></h4>
                      <div class="w-100 pe-2">
                        <ul class="list-group">
                          <li class="list-group-item">
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" disabled="true" value="" id="baby_opv_1" v-model="baby_opv_1">
                              <label class="form-check-label w-100" for="baby_opv_1">
                                1st dose <span class="badge rounded-pill bg-primary" style="font-size: 9px">1 1/2 mos</span>
                                <span class="float-end" v-if="baby_opv_1">{{babySheetData.baby_opv_1_date ? babySheetData.baby_opv_1_date : now}}</span>
                              </label>
                            </div>
                          </li>
                          <li class="list-group-item">
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" disabled="true" value="" id="baby_opv_2" v-model="baby_opv_2">
                              <label class="form-check-label w-100" for="baby_opv_2">
                                2nd dose <span class="badge rounded-pill bg-primary" style="font-size: 9px">2 1/2 mos</span>
                                <span class="float-end" v-if="baby_opv_2">{{babySheetData.baby_opv_2_date ? babySheetData.baby_opv_2_date : now}}</span>
                              </label>
                            </div>
                          </li>
                          <li class="list-group-item">
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" disabled="true" value="" id="baby_opv_3" v-model="baby_opv_3">
                              <label class="form-check-label w-100" for="baby_opv_3">
                                3rd dose <span class="badge rounded-pill bg-primary" style="font-size: 9px">3 1/2 mos</span>
                                <span class="float-end" v-if="baby_opv_3">{{babySheetData.baby_opv_3_date ? babySheetData.baby_opv_3_date : now}}</span>
                              </label>
                            </div>
                          </li>
                        </ul>
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>PCV</strong></h4>
                      <div class="w-100 pe-2">
                        <ul class="list-group">
                          <li class="list-group-item">
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" disabled="true" value="" id="baby_pcv_1" v-model="baby_pcv_1">
                              <label class="form-check-label w-100" for="baby_pcv_1">
                                1st dose <span class="badge rounded-pill bg-primary" style="font-size: 9px">1 1/2 mos</span>
                                <span class="float-end" v-if="baby_pcv_1">{{babySheetData.baby_pcv_1_date ? babySheetData.baby_pcv_1_date : now}}</span>
                              </label>
                            </div>
                          </li>
                          <li class="list-group-item">
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" disabled="true" value="" id="baby_pcv_2" v-model="baby_pcv_2">
                              <label class="form-check-label w-100" for="baby_pcv_2">
                                2nd dose <span class="badge rounded-pill bg-primary" style="font-size: 9px">2 1/2 mos</span>
                                <span class="float-end" v-if="baby_pcv_2">{{babySheetData.baby_pcv_2_date ? babySheetData.baby_pcv_2_date : now}}</span>
                              </label>
                            </div>
                          </li>
                          <li class="list-group-item">
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" disabled="true" value="" id="baby_pcv_3" v-model="baby_pcv_3">
                              <label class="form-check-label w-100" for="baby_pcv_3">
                                3rd dose <span class="badge rounded-pill bg-primary" style="font-size: 9px">3 1/2 mos</span>
                                <span class="float-end" v-if="baby_pcv_3">{{babySheetData.baby_pcv_3_date ? babySheetData.baby_pcv_3_date : now}}</span>
                              </label>
                            </div>
                          </li>
                        </ul>
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>IPV</strong></h4>
                      <div class="w-100 pe-2">
                        <ul class="list-group">
                          <li class="list-group-item">
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" disabled="true" value="" id="baby_ipv" v-model="baby_ipv">
                              <label class="form-check-label w-100" for="baby_ipv">
                                1st dose <span class="badge rounded-pill bg-primary" style="font-size: 9px">3 1/2 mos</span>
                                <span class="float-end" v-if="baby_ipv">{{babySheetData.baby_ipv_date ? babySheetData.baby_ipv_date : now}}</span>
                              </label>
                            </div>
                          </li>
                        </ul>
                      </div>
                    </ion-label>
                  </ion-item>
                </span>


                
                <ion-item @click="printPrescription(app)">
                  <ion-label class="text-end">Print Section</ion-label>
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
  IonItem, IonLabel, modalController, IonModal
} from '@ionic/vue';

import {
  chevronDownCircleOutline, pin, calendarOutline,
  timeOutline, arrowForwardOutline, callOutline, addOutline,
  chevronBackOutline, chevronForwardOutline, personCircleOutline,
  informationCircleOutline, bandageOutline, printOutline,
  accessibilityOutline, barbellOutline, speedometerOutline, thermometerOutline,
  calendarNumberOutline
} from 'ionicons/icons';
//import store from '../store'
import moment from 'moment'
import { mapState } from 'vuex'
import AppConstants from '../constants/app.constants'
import axios from "axios"
import SettingsConstants from '../constants/settings.constants'
import Swal from 'sweetalert2';

export default defineComponent({
  name: 'PrescriptionPage',
  components: {
    IonHeader, IonToolbar, IonTitle,
    IonContent, IonPage, IonRefresher, IonRefresherContent,
    IonCard, IonCardContent, IonItem, IonLabel,
    IonModal
  },
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
      appTemp: null,
      appMuac: null,
      appMedHist: [],
      appLMP: null,
      appEDC: null,
      appHusbandName: null,
      appHusbandAge: null,
      appChildCount: null,
      appbabyStatus: null,
      appPlanningMore: 0,
      appHouseholdIncome: null,

      babySheetData: null,
      baby_lbwgi_1: false,
      baby_lbwgi_2: false,
      baby_lbwgi_3: false,
      baby_dpt_1: false,
      baby_dpt_2: false,
      baby_dpt_3: false,
      baby_opv_1: false,
      baby_opv_2: false,
      baby_opv_3: false,
      baby_pcv_1: false,
      baby_pcv_2: false,
      baby_pcv_3: false,
      baby_ipv: false,

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
      bandageOutline, printOutline, accessibilityOutline, barbellOutline, speedometerOutline,
      thermometerOutline, calendarNumberOutline
    }
  },
  computed: mapState([
    'sessionData'
  ]),
  methods: {
    async dismiss() {
      modalController.dismiss({
        'dismissed': true
      });
    },
    convertToIllnessString: function (illness) {
      var ilnessIds = illness.split(',');
      var illnessString = '';
      ilnessIds.forEach( function (ill) {
        switch (ill) {
          case AppConstants.MEDICAL_HISTORY.TB.VALUE:
            illnessString = illnessString + AppConstants.MEDICAL_HISTORY.TB.TITLE + ', ';
            break;
          case AppConstants.MEDICAL_HISTORY.HEART_DISEASE.VALUE:
            illnessString = illnessString + AppConstants.MEDICAL_HISTORY.HEART_DISEASE.TITLE + ', ';
            break;
          case AppConstants.MEDICAL_HISTORY.DIABETIC.VALUE:
            illnessString = illnessString + AppConstants.MEDICAL_HISTORY.DIABETIC.TITLE + ', ';
            break;
          case AppConstants.MEDICAL_HISTORY.GOITER.VALUE:
            illnessString = illnessString + AppConstants.MEDICAL_HISTORY.GOITER.TITLE + ', ';
            break;
          case AppConstants.MEDICAL_HISTORY.ASTHMA.VALUE:
            illnessString = illnessString + AppConstants.MEDICAL_HISTORY.ASTHMA.TITLE + ', ';
            break;
        }
      });
      return illnessString.slice(0, -2);
    },
    printPrescription: function (app) {
      // Swal.fire(
      //   'Sorry!',
      //   'Feature comming soon!',
      //   'warning'
      // );
      // console.log(app);
      var divToPrint = document.getElementById("forPrinting");
      var newWin = window.open('', '', 'height=600,width=1000');
      newWin.document.write('<head><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"' +
        'integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous"><link rel="stylesheet" href="https://unpkg.com/@ionic/core@4.0.0/css/ionic.bundle.css">');
      setTimeout(function () {
        newWin.document.write(divToPrint.outerHTML.replace(/<ion-item/gm, '<br/><br/><ion-item'));
        newWin.document.close();
        newWin.focus();
        newWin.print();
        setTimeout(function () {
          newWin.close();
        }, 500);
      }, 500);
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
        this.appTemp = app.app_temp;
        this.appMuac = app.app_muac;
        this.appMedHist = app.app_medical_history_illness;
        this.appLMP = app.app_lmp;
        this.appEDC = app.app_edc;
        this.appHusbandName = app.app_partners_name;
        this.appHusbandAge = app.app_partners_age;
        this.appChildCount = app.app_num_living_children;
        this.appbabyStatus = app.app_baby_status;
        this.appPlanningMore = app.app_planning_more_children;
        this.appHouseholdIncome = app.app_household_income;
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
        this.appTemp = null;
        this.appMuac = null;
        this.appMedHist = [];
        this.appLMP = null;
        this.appEDC = null;
        this.appHusbandName = null;
        this.appHusbandAge = 0;
        this.appChildCount = 0;
        this.appbabyStatus = null;
        this.appPlanningMore = 0;
        this.appHouseholdIncome = 0;
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
                      case AppConstants.APPOINTMENT_SERVICES.PRENATAL_CARE.VALUE:
                        app.agendaTitle = AppConstants.APPOINTMENT_SERVICES.PRENATAL_CARE.TITLE
                        break;
                      case AppConstants.APPOINTMENT_SERVICES.CHECKUP.VALUE:
                        app.agendaTitle = AppConstants.APPOINTMENT_SERVICES.CHECKUP.TITLE
                        break;
                      case AppConstants.APPOINTMENT_SERVICES.API.VALUE:
                        app.agendaTitle = AppConstants.APPOINTMENT_SERVICES.API.TITLE
                        break;
                      case AppConstants.APPOINTMENT_SERVICES.FAMILY_PLANNING.VALUE:
                        app.agendaTitle = AppConstants.APPOINTMENT_SERVICES.FAMILY_PLANNING.TITLE
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
    getSheetData: function (userid) {
      axios.get(SettingsConstants.BASE_URL + 'appointment.rest.php?type=getbabydatasheetbyuserid&userid=' + userid, { crossdomain: true })
        .then(function (response) {
          if (response.data) {
            this.babySheetData = response.data[0];
            this.baby_lbwgi_1 = this.babySheetData.baby_lbwgi_1 != '0' ? true: false;
            this.baby_lbwgi_2 = this.babySheetData.baby_lbwgi_2 != '0' ? true: false;
            this.baby_lbwgi_3 = this.babySheetData.baby_lbwgi_3 != '0'  ? true: false;

            this.baby_dpt_1 = this.babySheetData.baby_dpt_1 != '0' ? true: false;
            this.baby_dpt_2 = this.babySheetData.baby_dpt_2 != '0' ? true: false;
            this.baby_dpt_3 = this.babySheetData.baby_dpt_3 != '0' ? true: false;

            this.baby_opv_1 = this.babySheetData.baby_opv_1 != '0' ? true: false;
            this.baby_opv_2 = this.babySheetData.baby_opv_2 != '0' ? true: false;
            this.baby_opv_3 = this.babySheetData.baby_opv_3 != '0' ? true: false;

            this.baby_pcv_1 = this.babySheetData.baby_pcv_1 != '0' ? true: false;
            this.baby_pcv_2 = this.babySheetData.baby_pcv_2 != '0' ? true: false;
            this.baby_pcv_3 = this.babySheetData.baby_pcv_3 != '0' ? true: false;

            this.baby_ipv = this.babySheetData.baby_ipv != '0' ? true: false;
          }
        }.bind(this));
    }
  },
  mounted() {
    this.getAllSchedule();
    this.getSheetData(this.sessionData.id);
  },
});
</script>
