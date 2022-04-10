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
            <ion-item-option @click="onclickDeleteUser(user)" color="danger" class="pe-2 ps-2" v-if="sessionData.user_level == 3">
              <ion-icon :icon="trashOutline"/>
            </ion-item-option>
          </ion-item-options>
          

          <ion-item>
            <ion-avatar slot="start">
              <img src="../../resources/avatar.svg">
            </ion-avatar>
            <ion-label>{{ user.fullName }}</ion-label>
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
              <label class="form-label mb-0" style="font-size:12px"><span v-if="!isEdit">Add</span><span v-else>Edit</span> Patient Modal</label>
              <hr class="mt-2 mb-2"/>
              <form @submit="registerPatient">
                <table class="w-100">
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
                    <td colspan="2" style="padding: 5px">
                      <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control form-control-lg" id="email" required v-model="userEmail">
                      </div>
                    </td>
                  </tr>
                  <tr v-if="!isEmailValid && !isEdit && userEmail">
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
                <button type="submit" class="btn btn-primary btn-md w-100 mb-2 btn-lg" v-if="!isEdit">
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
import { mapState } from 'vuex'
import { chevronDownCircleOutline, addOutline, eyeOutline, createOutline, chevronBackOutline, trashOutline } from 'ionicons/icons';
import axios from "axios";
import SettingsConstants from '../constants/settings.constants'

export default defineComponent({
  name: 'PatientsPage',
  computed: mapState([
      'sessionData'
  ]),
  setup() {
    const doRefresh = function (event) {
      location.reload(true);
      setTimeout(function () {
        event.target.complete();
      }, 2000);
    }
    return {
      chevronDownCircleOutline, addOutline, doRefresh, eyeOutline, createOutline, chevronBackOutline, trashOutline
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
      isEmailValid: true,

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
    },
    updatePatient: function () {
      event.preventDefault();
      var bodyFormData = new FormData();
      bodyFormData.append('user_email', this.userEmail);
      bodyFormData.append('user_firstname', this.userFirstname);
      bodyFormData.append('user_lastname', this.userLastname);
      this.userMiddlename ? bodyFormData.append('user_middlename', this.userMiddlename) : bodyFormData.append('user_middlename', '');
      this.userSuffix ? bodyFormData.append('user_suffix', this.userSuffix) : bodyFormData.append('user_suffix', '');
      bodyFormData.append('user_sex', this.userSex);
      bodyFormData.append('user_address', this.userAddress);
      bodyFormData.append('user_city', this.userCity);
      bodyFormData.append('user_province', this.userProvince);
      bodyFormData.append('user_contactnum', this.userContact);
      bodyFormData.append('user_dob', this.userDateofbirth);
      bodyFormData.append('user_maritalstatus', this.userMaritalstatus);

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
                  'Something went wrong when creating your account!',
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
      bodyFormData.append('user_password', '');
      bodyFormData.append('user_firstname', this.userFirstname);
      bodyFormData.append('user_lastname', this.userLastname);
      this.userMiddlename ? bodyFormData.append('user_middlename', this.userMiddlename) : bodyFormData.append('user_middlename', '');
      this.userSuffix ? bodyFormData.append('user_suffix', this.userSuffix) : bodyFormData.append('user_suffix', '');
      bodyFormData.append('user_sex', this.userSex);
      bodyFormData.append('user_address', this.userAddress);
      bodyFormData.append('user_city', this.userCity);
      bodyFormData.append('user_province', this.userProvince);
      bodyFormData.append('user_contactnum', this.userContact);
      bodyFormData.append('user_dob', this.userDateofbirth);
      bodyFormData.append('user_maritalstatus', this.userMaritalstatus);

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
    },
    storePatientsDetails: function (details) {
      store.commit('SET_PATIENTVIEW_DETAILS', details);
    },
    getAllPatients: function () {
      this.allPatients = [];
      axios.get(SettingsConstants.BASE_URL + 'users.rest.php?type=getallpatients', { crossdomain: true })
          .then(function (response) {
              if (response.data) {
                  this.allPatients = response.data;
                  this.allPatients.forEach( function (patient){
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
    onclickDeleteUser: function (user) {
        Swal.fire({
        title: 'Remove' + user.fullName + ' ?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
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
    }
  },
  mounted() {
    this.getAllPatients();
    this.getAllExistingEmails();
  },
});
</script>
