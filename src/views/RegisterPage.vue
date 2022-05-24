<template>
  <ion-page>
    <ion-content :fullscreen="true">

      <ion-refresher slot="fixed" @ionRefresh="doRefresh($event)">
        <ion-refresher-content></ion-refresher-content>
      </ion-refresher>

      <div class="p-4">
        <a href="/login" class="text-decoration-none text-dark">
          <div class="d-flex justify-content-center mb-4">
            <img src="../../resources/logo.png" style="height: 50px"/>
            <h5 class="ms-2">B A C M S</h5>
          </div>
        </a>
        <form @submit="registerPatient">
        <table class="w-100">
          <tr>
            <td colspan="2">
              <label class="form-label" style="font-size:12px">Login Details</label>
              <hr class="mt-2 mb-2"/>
            </td>
          </tr>
          <tr>
            <td colspan="2" style="padding: 5px">
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control form-control-lg" id="email" v-model="userEmail" required>
              </div>
            </td>
          </tr>
          <tr v-if="!isEmailValid">
            <td colspan="2">
              <div class="alert alert-danger" role="alert">
                This email account was already registered. Please use another one.
              </div>
            </td>
          </tr>
          <tr>
            <td style="padding: 5px">
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control form-control-lg" id="password" required v-model="userPassword">
              </div>
            </td>
            <td style="padding: 5px">
              <div class="mb-3">
                <label for="repassword" class="form-label">Re-type Password</label>
                <input type="password" class="form-control form-control-lg" id="repassword" required v-model="userConfirmPassword">
              </div>
            </td>
          </tr>
          <tr v-if="(userPassword !== userConfirmPassword) && userPassword != ''">
            <td colspan="2">
              <div class="alert alert-danger" role="alert">
                Passwords did not match!
              </div>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <label class="form-label" style="font-size:12px">Personal Details</label>
              <hr class="mt-2 mb-2"/>
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
          <tr v-if="!isContactNumValid">
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

        <button type="submit" class="btn btn-primary btn-lg w-100 mt-2" :disabled="!isEmailValid || !isContactNumValid || (!(userPassword == userConfirmPassword) && userPassword != '')">Register</button>
        </form>
        
      </div>
    </ion-content>
  </ion-page>
</template>

<script lang="js">
import { defineComponent } from 'vue';
import { IonPage, IonContent, IonRefresher, IonRefresherContent } from '@ionic/vue';
import { chevronDownCircleOutline } from 'ionicons/icons';
import Swal from 'sweetalert2';
import axios from "axios";
import SettingsConstants from '../constants/settings.constants'

export default  defineComponent({
  name: 'RegisterPage',
  watch: {
    userEmail: function (newVal) {
      if (newVal) {
        this.allEmails = this.allEmailsTemp;
        var counter = [];
        counter = this.allEmails.filter(email => email.user_email.toLowerCase() == newVal.toLowerCase());
        !counter.length ? this.isEmailValid = true : this.isEmailValid = false ;
      } else {
        this.isEmailValid = false;
      }
    },
    userContact: function (newVal) {
      if (newVal) {
        this.allNumbers = this.allNumbersTemp;
        var counter = [];
        counter = this.allNumbers.filter(num => num.user_contactnum == newVal);
        !counter.length ? this.isContactNumValid = true : this.isContactNumValid = false ;
      } else {
        this.isContactNumValid = false;
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
  setup() {
    const doRefresh = function (event) {
      location.reload(true);
      setTimeout(function () {
        event.target.complete();
      }, 2000);
    }
    return { chevronDownCircleOutline, doRefresh }
  },
  data() {
    return {
      userEmail: null,
      userPassword: null,
      userConfirmPassword: null,
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
      allEmails: [],
      allEmailsTemp: [],
      allNumbers: [],
      allNumbersTemp: [],
      isEmailValid: true,
      isContactNumValid: true,
      selectedSitio: null
    }
  },
  components: { IonContent, IonPage, IonRefresher, IonRefresherContent },
  methods: {
    registerPatient: function () {
      event.preventDefault();
      var bodyFormData = new FormData();
      this.userEmail ? bodyFormData.append('user_email', this.userEmail) : bodyFormData.append('user_email', '');
      bodyFormData.append('user_password', this.userPassword);
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
                ).then(function (){
                    this.userEmail = null,
                    this.userPassword = null;
                    this.userConfirmPassword = null;
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
                }.bind(this));

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
    }
  },
  mounted() {
    this.getAllExistingEmails();
    this.getAllExistingContactNumbers();
  },
});
</script>
