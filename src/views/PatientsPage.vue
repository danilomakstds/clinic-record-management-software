<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-title><img src="../../resources/logo.png" style="height:20px" class="me-2"/>Patients</ion-title>
      </ion-toolbar>
    </ion-header>
    <ion-content :fullscreen="true">

      <ion-refresher slot="fixed" @ionRefresh="doRefresh($event)">
        <ion-refresher-content></ion-refresher-content>
      </ion-refresher>

      <ion-toolbar class="pt-2" slot="fixed">
        <ion-searchbar placeholder="Filter Patients" v-model="searchValue"></ion-searchbar>
      </ion-toolbar>
      <br/><br/>
      <ion-list class="mt-2">
        <!-- Sliding item with text options on both sides -->
        <ion-item-sliding v-for="user in allPatients" :key="user.id">
          <ion-item-options side="start">
            <ion-item-option @click="storePatientsDetails(user)" color="secondary" class="pe-2 ps-2" data-bs-toggle="modal" data-bs-target="#patientViewModal">
              <ion-icon :icon="eyeOutline"/>
            </ion-item-option>
            <ion-item-option class="pe-2 ps-2" @click="toggleAddPatientModal(user)">
              <ion-icon :icon="createOutline"/>
            </ion-item-option>
            <ion-item-option class="pe-2 ps-2" @click="toggleShowHistoryModal(user)" color="tertiary" v-if="user.user_level == 0">
              <ion-icon :icon="folderOutline"/>
            </ion-item-option>
            <!-- <ion-item-option @click="onclickDeleteUser(user)" color="danger" class="pe-2 ps-2" v-if="sessionData.user_level == 3">
              <ion-icon :icon="trashOutline"/>
            </ion-item-option> -->
          </ion-item-options>
          

          <ion-item>
            <ion-avatar slot="start">
              <img src="../../resources/avatar.svg">
            </ion-avatar>
            <ion-label>{{ user.fullName }}
              <span class="badge rounded-pill bg-primary ms-2" v-if="user.user_level == 3">Super Admin</span>
              <span class="badge rounded-pill bg-info ms-2" v-if="user.user_level == 2">Doctor</span>
              <span class="badge rounded-pill bg-warning ms-2" v-if="user.user_level == 1">Nurse</span>
            </ion-label>
          </ion-item>
        </ion-item-sliding>
      </ion-list>


            <!-- Sheet Modal -->
      <ion-modal
        :is-open="isAddNewPatientOpen"
        @didDismiss="toggleAddPatientModal()"
      >
        <ion-header>
          <ion-toolbar @click="dismiss()" class="ms-2">
            <ion-icon :icon="chevronBackOutline" slot="start" size="large"/>
            <ion-title slot="start">Back</ion-title>
          </ion-toolbar>
        </ion-header>
        <ion-content>
          <div class="p-4" >
              <label class="form-label mb-0" style="font-size:12px"><span v-if="!isEdit">Add</span><span v-else>Edit</span>
              <span v-if="sessionData.user_level != 3">Patient</span> <span v-else> User</span> Modal</label>
              <hr class="mt-2 mb-2"/>
              <form @submit="registerPatient">
                <table class="w-100">
                  <tr class="w-100">
                    <td colspan="2">
                      <div class="mb-3 w-100">
                        <label class="form-label">User Type</label>
                        <select class="form-select w-100" required v-model="userLevel" :disabled="sessionData.user_level != 3">
                          <option :value="0">Patient</option>
                          <option :value="1">Nurse</option>
                          <option :value="2">Doctor</option>
                          <option :value="3">Admin</option>
                        </select>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding: 5px">
                      <div class="mb-3">
                        <label for="fname" class="form-label">First name</label>
                        <input type="text" class="form-control form-control-lg" id="fname" required v-model="userFirstname">
                      </div>
                    </td>
                    <td style="padding: 5px">
                      <div class="mb-3">
                        <label for="lname" class="form-label">Last name</label>
                        <input type="text" class="form-control form-control-lg" id="lname" required v-model="userLastname">
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding: 5px">
                      <div class="mb-3">
                        <label for="mname" class="form-label">Middle name</label>
                        <input type="text" class="form-control form-control-lg" id="mname" v-model="userMiddlename">
                      </div>
                    </td>
                    <td style="padding: 5px">
                      <div class="mb-3">
                        <label for="suffix" class="form-label">Suffix name</label>
                        <input type="text" class="form-control form-control-lg" id="suffix" v-model="userSuffix">
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td :colspan="(userLevel > 0) ? 1 : 2" style="padding: 5px">
                      <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control form-control-lg" id="email" required v-model="userEmail">
                      </div>
                    </td>
                    <td v-if="userLevel > 0">
                      <div class="mb-3">
                        <label class="form-label">User Password</label>
                        <input type="password" class="form-control form-control-lg" v-model="userPassword" required>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="!isEmailValid && !isEdit">
                    <td colspan="2">
                      <div class="alert alert-danger" role="alert">
                        This email account was already registered. Please use another one.
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding: 5px">
                      <label class="form-label">Sex</label>
                    </td>
                    <td style="padding: 5px">
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="sex" value="1" id="sex1" checked v-model="userSex">
                        <label class="form-check-label" for="sex1">
                          Male
                        </label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="sex" value="2" id="sex2" v-model="userSex">
                        <label class="form-check-label" for="sex2">
                          Female
                        </label>
                      </div>
                    </td>
                  </tr>
                  <tr>
                  <td colspan="2" style="padding: 5px">
                    <div class="mb-3">
                      <label for="address" class="form-label">Select Sitio/Purok</label>
                      <select class="form-control" id="exampleFormControlSelect1" v-model="selectedSitio">
                        <option value="">None</option>
                        <option value="Abog">Abog</option>
                        <option value="Arapiles">Arapiles</option>
                        <option value="Bantolinao">Bantolinao</option>
                        <option value="Baryong Daan">Baryong Daan</option>
                        <option value="Buntod">Buntod</option>
                        <option value="Common">Common</option>
                        <option value="Kalantukan">Kalantukan</option>
                        <option value="Kalubinhan">Kalubinhan</option>
                        <option value="Kambaghak">Kambaghak</option>
                        <option value="Tubaon">Tubaon</option>
                      </select>
                    </div>
                  </td>
                </tr>
                  <tr>
                    <td colspan="2" style="padding: 5px">
                      <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control form-control-lg" id="address" rows="3" required v-model="userAddress"></textarea>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding: 5px">
                      <div class="mb-3">
                        <label for="city" class="form-label">City / Municipality</label>
                        <input type="text" class="form-control form-control-lg" id="city" required v-model="userCity">
                      </div>
                    </td>
                    <td style="padding: 5px">
                      <div class="mb-3">
                        <label for="province" class="form-label">Province</label>
                        <input type="text" class="form-control form-control-lg" id="province" required v-model="userProvince">
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2" style="padding: 5px">
                      <div class="mb-3">
                        <label for="contactnum" class="form-label">Contact Number</label>
                        <input type="text" class="form-control form-control-lg" id="contactnum" maxlength="11" required v-model="userContact">
                      </div>
                    </td>
                  </tr>
                  <tr v-if="!isContactNumValid && !isEdit">
                    <td colspan="2">
                      <div class="alert alert-danger" role="alert">
                        This contact number was already registered or invalid. Please use another one.
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2" style="padding: 5px">
                      <div class="mb-3">
                        <label for="dob" class="form-label">Date of Birth</label>
                        <input type="date" class="form-control form-control-lg" id="dob" required v-model="userDateofbirth">
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding: 5px">
                      <label class="form-label">Marital Status</label>
                    </td>
                    <td style="padding: 5px">
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="maritalstatus" value="1" id="maritalstatus1" checked v-model="userMaritalstatus">
                        <label class="form-check-label" for="maritalstatus1">
                          Single
                        </label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="maritalstatus" value="2" id="maritalstatus2" v-model="userMaritalstatus">
                        <label class="form-check-label" for="maritalstatus2">
                          Married
                        </label>
                      </div>
                    </td>
                  </tr>
                </table>
                <button type="submit" class="btn btn-primary btn-md w-100 mb-2 btn-lg" v-if="!isEdit" :disabled="!isContactNumValid || !isEmailValid">
                  <ion-icon :icon="add" size="small" class="me-1"/>Add Patient
                </button>
                <button type="button" class="btn btn-primary btn-md w-100 mb-2 btn-lg" @click="updatePatient()" v-else>
                  <ion-icon :icon="createOutline" size="small" class="me-1"/>Edit Patient
                </button>
                <br/><br/><br/>
            </form>
          </div>
        </ion-content>
      </ion-modal>

      <ion-modal
        :is-open="isShowHistoryOpen"
        @didDismiss="toggleShowHistoryModal()"
      >
        <ion-header>
          <ion-toolbar @click="dismiss()" class="ms-2">
            <ion-icon :icon="chevronBackOutline" slot="start" size="large"/>
            <ion-title slot="start">Back</ion-title>
          </ion-toolbar>
        </ion-header>
        <ion-content>
          <div class="p-2">
            <div class="d-flex align-items-center h-75" v-if="!allAppointments.length">
              <div class="text-center">
                <img src="../../resources/slide-3.png" />
                <span style="font-size:15px" class="mt-2">No Items to show here!<br/>This patient has no appointment history</span>
              </div>
            </div>
            <div v-if="allAppointments.length">
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
            </div>
          </div>
        </ion-content>
      </ion-modal>

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

      <ion-fab vertical="bottom" horizontal="end" slot="fixed">
        <ion-fab-button @click="toggleAddPatientModal()">
          <ion-icon :icon="addOutline"></ion-icon>
        </ion-fab-button>
      </ion-fab>


    </ion-content>
  </ion-page>
</template>

<script lang="js">
import { defineComponent } from 'vue';
import { IonPage, IonHeader, IonToolbar,
IonTitle, IonContent, IonItem, IonAvatar,
IonItemOption, IonItemOptions, IonItemSliding,
IonSearchbar, IonRefresher, IonRefresherContent,
IonModal, modalController
} from '@ionic/vue';
import Swal from 'sweetalert2';
import store from '../store';
import moment from 'moment'
import { mapState } from 'vuex'
import { chevronDownCircleOutline, addOutline, eyeOutline, createOutline, chevronBackOutline, trashOutline, folderOutline, calendarOutline,
timeOutline, arrowForwardOutline, accessibilityOutline, bandageOutline, informationCircleOutline, barbellOutline, speedometerOutline,
personCircleOutline } from 'ionicons/icons';
import axios from "axios";
import SettingsConstants from '../constants/settings.constants'
import AppConstants from '../constants/app.constants'

export default defineComponent({
  name: 'PatientsPage',
  computed: mapState([
      'sessionData',
      'showPatientsOnly',
      'showAdminsOnly'
  ]),
  setup() {
    const doRefresh = function (event) {
      location.reload(true);
      setTimeout(function () {
        event.target.complete();
      }, 2000);
    }
    return {
      chevronDownCircleOutline, addOutline, doRefresh, eyeOutline, createOutline, chevronBackOutline, trashOutline, folderOutline,
      calendarOutline, timeOutline, arrowForwardOutline, accessibilityOutline, bandageOutline, informationCircleOutline, barbellOutline,
      speedometerOutline, personCircleOutline
    }
  },
  data() {
    return {
      searchValue: null,
      allPatients: [],
      allPatientsTemp: [],
      isAddNewPatientOpen: false,
      isEdit: false,
      patientIdEdit: null,
      allNumbers: [],
      allNumbersTemp: [],
      isEmailValid: true,
      isContactNumValid: true,

      userEmail: null,
      userFirstname: null,
      userLastname: null,
      userMiddlename: null,
      userSuffix: null,
      userSex: 1,
      userAddress: null,
      userCity: null,
      userProvince: null,
      userContact: null,
      userDateofbirth: null,
      userMaritalstatus: 1,
      userLevel: 0,
      userPassword: null,

      appointmentSlots: [],
      isShowHistoryOpen: false,
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

      selectedSitio: null
    }
  },
  watch: {
    searchValue: function (newVal) {
      this.allPatients = this.allPatientsTemp;
      this.applySearch(newVal);
    },
    userEmail: function (newVal) {
      if (newVal) {
        this.allEmails = this.allEmailsTemp;
        var counter = [];
        counter = this.allEmails.filter(email => email.user_email.toLowerCase() == newVal.toLowerCase());
        !counter.length ? this.isEmailValid = true : this.isEmailValid = false ;
      }
    },
    userContact: function (newVal) {
      if (newVal) {
        this.allNumbers = this.allNumbersTemp;
        var counter = [];
        counter = this.allNumbers.filter(num => num.user_contactnum == newVal);
        !counter.length ? this.isContactNumValid = true : this.isContactNumValid = false ;
      }
    },
    selectedSitio: function (newVal) {
      if (newVal) {
        this.userAddress = newVal + ', Brgy. Simborio';
        this.userCity = 'Pamplona';
        this.userProvince = 'Negros Oriental';
      } else {
        this.userAddress = null;
        this.userCity = null;
        this.userProvince = null;
      }
    }
  },
  components: {IonHeader, IonToolbar, IonTitle,
  IonContent, IonPage, IonItem,
  IonAvatar, IonItemOption, IonItemOptions, 
  IonItemSliding, IonSearchbar, IonRefresher, IonRefresherContent,
  IonModal
  },
  methods: {
    async dismiss() {
      modalController .dismiss({
        'dismissed': true
      });
    },
    toggleAddPatientModal: function (isEdit) {
      this.resetFields();
      this.isAddNewPatientOpen = !this.isAddNewPatientOpen;
      if (isEdit) {
        this.patientIdEdit = isEdit.id;
        this.isEdit = true;
        this.editPatientItem(isEdit);
      } else {
        this.isEdit = false;
      }
    },
    toggleShowHistoryModal: function (patient) {
      if (patient) {
        this.getAllAppointments(patient);
      } else {
        this.isShowHistoryOpen = false;
      }
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
    editPatientItem: function (patientItem) {
      this.userEmail = patientItem.user_email;
      this.userFirstname = patientItem.user_firstname;
      this.userLastname = patientItem.user_lastname;
      this.userMiddlename = patientItem.user_middlename;
      this.userSuffix = patientItem.user_suffix;
      this.userSex = patientItem.user_sex;
      this.userAddress = patientItem.user_address;
      this.userCity = patientItem.user_city;
      this.userProvince = patientItem.user_province;
      this.userContact = patientItem.user_contactnum;
      this.userDateofbirth = patientItem.user_dob;
      this.userMaritalstatus = patientItem.user_maritalstatus;
      this.userLevel = patientItem.user_level;
      this.userPassword = patientItem.user_password;
    },
    updatePatient: function () {
      event.preventDefault();
      var bodyFormData = new FormData();
      bodyFormData.append('user_email', this.userEmail);
      this.userPassword ? bodyFormData.append('user_password', this.userPassword) : bodyFormData.append('user_password', '');
      bodyFormData.append('user_firstname', this.userFirstname);
      bodyFormData.append('user_lastname', this.userLastname);
      this.userMiddlename ? bodyFormData.append('user_middlename', this.userMiddlename) : bodyFormData.append('user_middlename', '');
      this.userSuffix ? bodyFormData.append('user_suffix', this.userSuffix) : bodyFormData.append('user_suffix', '');
      bodyFormData.append('user_sex', this.userSex);
      this.userAddress ? bodyFormData.append('user_address', this.userAddress) : bodyFormData.append('user_address', '');
      this.userCity ? bodyFormData.append('user_city', this.userCity) : bodyFormData.append('user_city', '');
      this.userProvince ? bodyFormData.append('user_province', this.userProvince) : bodyFormData.append('user_province', '');
      this.userContact ? bodyFormData.append('user_contactnum', this.userContact) : bodyFormData.append('user_contactnum', '');
      this.userDateofbirth ? bodyFormData.append('user_dob', this.userDateofbirth) : bodyFormData.append('user_dob', '');
      bodyFormData.append('user_maritalstatus', this.userMaritalstatus);
      bodyFormData.append('user_level', this.userLevel);

      axios({
          method: "post",
          url: SettingsConstants.BASE_URL + "users.rest.php?type=updatepatient&userid=" + this.patientIdEdit,
          data: bodyFormData,
          headers: { "Content-Type": "multipart/form-data" },
      })
          .then(function (response) {
            if (response.data == 1) {
                Swal.fire(
                  'Great!',
                  'Patient has been updated!',
                  'success'
                );
                this.dismiss();
                this.resetFields();
                this.isEdit = false;
                this.patientIdEdit = null;
                this.getAllPatients();
            } else {
                Swal.fire(
                  'Error!',
                  'Something went wrong when updating Patient/User Information!',
                  'error'
                );
            }
          }.bind(this))
          .catch(function (response) {
              console.log(response);
          });
    },
    registerPatient: function () {
      event.preventDefault();
      var bodyFormData = new FormData();
      bodyFormData.append('user_email', this.userEmail);
      this.userPassword ? bodyFormData.append('user_password', this.userPassword) : bodyFormData.append('user_password', 'patient1234');
      bodyFormData.append('user_firstname', this.userFirstname);
      bodyFormData.append('user_lastname', this.userLastname);
      this.userMiddlename ? bodyFormData.append('user_middlename', this.userMiddlename) : bodyFormData.append('user_middlename', '');
      this.userSuffix ? bodyFormData.append('user_suffix', this.userSuffix) : bodyFormData.append('user_suffix', '');
      bodyFormData.append('user_sex', this.userSex);
      this.userAddress ? bodyFormData.append('user_address', this.userAddress) : bodyFormData.append('user_address', '');
      this.userCity ? bodyFormData.append('user_city', this.userCity) : bodyFormData.append('user_city', '');
      this.userProvince ? bodyFormData.append('user_province', this.userProvince) : bodyFormData.append('user_province', '');
      this.userContact ? bodyFormData.append('user_contactnum', this.userContact) : bodyFormData.append('user_contactnum', '');
      this.userDateofbirth ? bodyFormData.append('user_dob', this.userDateofbirth) : bodyFormData.append('user_dob', '');
      bodyFormData.append('user_maritalstatus', this.userMaritalstatus);
      bodyFormData.append('user_level', this.userLevel);

      axios({
          method: "post",
          url: SettingsConstants.BASE_URL + "users.rest.php?type=addpatient",
          data: bodyFormData,
          headers: { "Content-Type": "multipart/form-data" },
      })
          .then(function (response) {
            if (response.data == 1) {
                Swal.fire(
                  'Great!',
                  'Account has been created!',
                  'success'
                );
                this.dismiss();
                this.resetFields();
                this.getAllPatients();
            } else {
                Swal.fire(
                  'Error!',
                  'Something went wrong when creating your account!',
                  'error'
                );
            }
          }.bind(this))
          .catch(function (response) {
              console.log(response);
          });
    },
    resetFields: function () {
      this.userEmail = null;
      this.userFirstname = null;
      this.userLastname = null;
      this.userMiddlename = null;
      this.userSuffix = null;
      this.userSex = 1;
      this.userAddress = null;
      this.userCity = null;
      this.userProvince = null;
      this.userContact = null;
      this.userDateofbirth = null;
      this.userMaritalstatus = 1;
      this.userPassword = null;
      this.userLevel = 0;
    },
    storePatientsDetails: function (details) {
      store.commit('SET_PATIENTVIEW_DETAILS', details);
    },
    getAllPatients: function () {
      this.allPatients = [];
      var url = null;
      if (this.sessionData.user_level == '3') {
        if (this.showPatientsOnly) {
          url = 'users.rest.php?type=getallpatients';
        } else if (this.showAdminsOnly) {
          url = 'users.rest.php?type=getalladmins';
        } else {
          url = 'users.rest.php?type=all';
        }
      } else {
        url = 'users.rest.php?type=getallpatients';
      }

      axios.get(SettingsConstants.BASE_URL + url , { crossdomain: true })
          .then(function (response) {
              if (response.data) {
                  this.allPatients = response.data;
                  this.allPatients.forEach( function (patient){
                    if (patient.user_dob) {
                      if ((moment(patient.user_dob, 'YYYYMMDD').fromNow()).includes('years')) {
                        patient.age = (moment(patient.user_dob, 'YYYYMMDD').fromNow()).split(" ")[0];
                      } else {
                        patient.age = '< 1 year';
                      }
                    } else {
                      patient.age = "-";
                    }
                    patient.fullName = patient.user_firstname + ' ' + patient.user_middlename + ' ' + patient.user_lastname + ' ' + patient.user_suffix; 
                  });
                  this.allPatientsTemp = this.allPatients;
              }
          }.bind(this));
    },
    applySearch: function (newVal) {
      if (newVal) {
        this.allPatients = this.allPatients.filter(patient => patient.fullName.toLowerCase().startsWith(newVal.toLowerCase()));
      } else {
        this.allPatients = this.allPatientsTemp;
      }
    },
    getAllExistingEmails: function () {
      this.allEmails = [];
      axios.get(SettingsConstants.BASE_URL + 'users.rest.php?type=allemails', { crossdomain: true })
          .then(function (response) {
              if (response.data) {
                  this.allEmails = response.data;
                  this.allEmailsTemp = this.allEmails;
              }
          }.bind(this));
    },
    getAllExistingContactNumbers: function () {
      this.allNumbers = [];
      axios.get(SettingsConstants.BASE_URL + 'users.rest.php?type=allnumbers', { crossdomain: true })
          .then(function (response) {
              if (response.data) {
                  this.allNumbers = response.data;
                  this.allNumbersTemp = this.allNumbers;
              }
          }.bind(this));
    },
    onclickDeleteUser: function (user) {
        Swal.fire({
        title: 'Remove' + user.fullName + ' ?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6C757D',
        confirmButtonText: 'Yes, remove it!'
      }).then((result) => {
        if (result.isConfirmed) {
          axios.get(SettingsConstants.BASE_URL + 'users.rest.php?type=deletepatient&userId=' + user.id, { crossdomain: true })
            .then(function (response) {
              if (response.data) {
                Swal.fire(
                  'Removed!',
                  'Patient has been removed.',
                  'success'
                ).then(function (){
                  this.getAllPatients();
                }.bind(this));
              }
            }.bind(this));
        }
      })
    },
    getAllAppointments: function(patient) {
      this.allAppointments = [];
      var url = 'appointment.rest.php?type=alladdreesedbyId&userId=' + patient.id;
      axios.get(SettingsConstants.BASE_URL + url, { crossdomain: true })
          .then(function (response) {
              this.isShowHistoryOpen = true;
              if (response.data) {
                  this.allAppointments = response.data;
                  this.allAppointments.forEach(function(app) {
                    app.agendaTitle = null;
                    app.timeSlot = null;
                    app.fullName = null;
                    app.fullName = patient.user_lastname + ', ' + patient.user_firstname + (patient.user_middlename ? ' '+patient.user_middlename : '');
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
    getAllSchedule: function () {
       axios.get(SettingsConstants.BASE_URL + 'schedule.rest.php?type=all', { crossdomain: true })
          .then(function (response) {
            if (response.data) {
              this.appointmentSlots = response.data;
              this.getAllAppointments();
            }
          }.bind(this));
    },
  },
  mounted() {
    this.getAllSchedule();
    this.getAllPatients();
    this.getAllExistingEmails();
    this.getAllExistingContactNumbers();
    this.emitter.on('isShowAdminChange', function () {
      this.getAllPatients();
    }.bind(this));
    this.emitter.on('isShowPatientChange', function () {
      this.getAllPatients();
    }.bind(this));
  },
});
</script>
