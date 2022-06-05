<template>
  <ion-app>
    <ion-router-outlet />
  </ion-app>

  <!-- Bootstrap Modal Vie item-->
  <!-- Modal -->
  <div class="modal fade" id="drugViewModal" aria-labelledby="drugViewModalLabel" aria-hidden="true"
    v-if="drugViewDetails">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="drugViewModalLabel">
            <img src="../resources/drug-icon-mod.jpg" style="height:25px; border-radius: 100px" class="me-2" />
            {{drugViewDetails.drug_name}}
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
            @click="clearDrugView()"></button>
        </div>
        <div class="modal-body">
          <ion-list>
            <ion-item>
              <ion-label>
                <h5><strong>Weight / Volume Info</strong></h5>
                {{ drugViewDetails.drug_dose }}
              </ion-label>
            </ion-item>
            <ion-item v-if="drugViewDetails.drug_flavor">
              <ion-label>
                <h5><strong>Flavor</strong></h5>
                {{ drugViewDetails.drug_flavor }}
              </ion-label>
            </ion-item>
            <ion-item>
              <ion-label>
                <h5><strong>Route / Administration</strong></h5>
                {{ returnRouteValue(drugViewDetails.drug_route) }}
              </ion-label>
            </ion-item>
            <ion-item>
              <ion-label>
                <h5><strong>Expiration Date</strong></h5>
                {{ drugViewDetails.drug_expirationdate }}
              </ion-label>
            </ion-item>
            <ion-item>
              <ion-label>
                <h5><strong>In Stock</strong></h5>
                {{ drugViewDetails.drug_quantity }}
              </ion-label>
            </ion-item>
          </ion-list>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="patientViewModal" aria-labelledby="patientViewModalLabel" aria-hidden="true"
    v-if="patientViewDetails">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="patientViewModalLabel">
            <img src="../resources/avatar.svg" style="height:25px" class="me-2" />
            {{ patientViewDetails.fullName }}
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
            @click="clearDrugView()"></button>
        </div>
        <div class="modal-body">
          <ion-list>
            <ion-item>
              <ion-label>
                <h5><strong>Sex</strong></h5>
                {{ returnSexValue(patientViewDetails.user_sex) }}
              </ion-label>
            </ion-item>
            <ion-item>
              <ion-label>
                <h5><strong>Address</strong></h5>
                {{ patientViewDetails.user_address }},
                {{ patientViewDetails.user_city }}, <br />
                {{ patientViewDetails.user_province }}
              </ion-label>
            </ion-item>
            <ion-item>
              <ion-label>
                <h5><strong>Contact Number</strong></h5>
                {{ patientViewDetails.user_contactnum }}
              </ion-label>
            </ion-item>
            <ion-item>
              <ion-label>
                <h5><strong>Date of Birth</strong></h5>
                {{ patientViewDetails.user_dob }}
              </ion-label>
            </ion-item>
            <ion-item>
              <ion-label>
                <h5><strong>Age</strong></h5>
                {{ patientViewDetails.age }}
              </ion-label>
            </ion-item>
            <ion-item>
              <ion-label>
                <h5><strong>Marital Status</strong></h5>
                {{ returnMaritalStatusValue(patientViewDetails.user_maritalstatus) }}
              </ion-label>
            </ion-item>
          </ion-list>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="createAppointmentModal" aria-labelledby="createAppointmentModalLabel" aria-hidden="true"
    v-if="appointmentViewDetails">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createAppointmentModalLabel">
            <img src="https://png.pngtree.com/element_our/png/20181114/business-deadline-icon-png_239815.jpg"
              style="height:25px" class="me-2" />
            {{ appointmentViewDetails.title }}
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
            @click="clearAppointmentView()"></button>
        </div>
        <div class="modal-body">
          <form @submit="createAppointment(appointmentViewDetails)">
            <ion-list>
              <ion-item>
                <ion-label>
                  <h5><strong>Selected Timeslot</strong></h5>
                  <img src="https://www.svgrepo.com/show/13698/time.svg" style="height:15px" class="me-2" />
                  {{ appointmentViewDetails.sched }}
                </ion-label>
              </ion-item>
              <ion-item>
                <ion-label>
                  <h5><strong>Select Service</strong></h5>
                  <select class="form-select w-100" aria-label="Default select example" v-model="selectedAgenda"
                    required>
                    <option value="1">Labor</option>
                    <option value="2">Prenatal Care</option>
                    <option value="3">Checkup / Consultation</option>
                    <option value="4">COVID-19 Vaccination</option>
                    <option value="5">Anti Rabies Vaccination Care</option>
                    <option value="6">Anti Tetanus Vaccination</option>
                    <option value="7">Depo-Provera Injections</option>
                  </select>
                </ion-label>
              </ion-item>
              <ion-item>
                <ion-label>
                  <h5 class="mb-2"><strong>Symptopms / Concerns</strong></h5>
                  <textarea class="form-control" v-model="patientSymptoms" rows="3"></textarea>
                </ion-label>
              </ion-item>
            </ion-list>
            <br />

            <button type="submit" class="btn btn-primary btn-lg w-100">
              Create Appointment
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Change password modal-->
  <div class="modal fade" id="showChangePassModal" aria-labelledby="changePass" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="changePass">Change Password</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="oldPassword" class="form-label">Current Password</label>
            <input type="email" class="form-control form-control-lg" id="oldPassword">
          </div>
          <div class="mb-3">
            <label for="newPassword" class="form-label">New Password</label>
            <input type="email" class="form-control form-control-lg" id="newPassword">
          </div>
          <div class="mb-3">
            <label for="confirm" class="form-label">Confirm Password</label>
            <input type="email" class="form-control form-control-lg" id="confirm">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary">Save changes</button>
        </div>
      </div>
    </div>
  </div>

</template>

<script lang="js">
import { IonApp, IonRouterOutlet, IonItem, IonList, IonLabel } from '@ionic/vue'
import { defineComponent } from 'vue'
import store from './store'
import { mapState } from 'vuex'
import AppConstants from './constants/app.constants'
import axios from "axios"
import SettingsConstants from './constants/settings.constants'
import Swal from 'sweetalert2'

export default defineComponent({
  name: 'App',
  computed: mapState([
    'drugViewDetails',
    'patientViewDetails',
    'appointmentViewDetails',
    'sessionData',
    'showChangePasswordModal'
  ]),
  data() {
    return {
      selectedAgenda: null,
      patientSymptoms: null,
    }
  },
  components: {
    IonApp,
    IonRouterOutlet,
    IonItem, IonList, IonLabel
  },
  methods: {
    clearDrugView: function () {
      store.commit('RESET_DRUGVIEW_DETAILS');
    },
    clearPatientView: function () {
      store.commit('RESET_PATIENTVIEW_DETAILS');
    },
    clearAppointmentView: function () {
      store.commit('RESET_APPOINTMENTVIEW_DETAILS');
    },
    createAppointment: function (details) {
      event.preventDefault();
      var bodyFormData = new FormData();
      bodyFormData.append('app_userId', this.sessionData.id);
      bodyFormData.append('app_apptype', this.selectedAgenda);
      bodyFormData.append('app_timeslot', details.slotid);
      bodyFormData.append('app_date', details.selectedDate);
      bodyFormData.append('app_patientconcerns', this.patientSymptoms);
      axios({
          method: "post",
          url: SettingsConstants.BASE_URL + "appointment.rest.php?type=addappointment",
          data: bodyFormData,
          headers: { "Content-Type": "multipart/form-data" },
      })
          .then(function (response) {
            if (response.data == 1) {
                Swal.fire(
                  'Great!',
                  'Appointment has been created!',
                  'success'
                ).then(function (){
                  document.location.reload()
                });
            } else {
                Swal.fire(
                  'Error!',
                  'Something went wrong when creating your appointment!',
                  'error'
                );
            }
          }.bind(this))
          .catch(function (response) {
              console.log(response);
          });
    },
    returnRouteValue: function (route) {
      var routeValue;
      switch (parseInt(route)) {
        case AppConstants.ROUTE_ADMINISTRATION.ORAL.VALUE:
          routeValue = AppConstants.ROUTE_ADMINISTRATION.ORAL.TITLE;
        break;
        case AppConstants.ROUTE_ADMINISTRATION.TOPICAL.VALUE:
          routeValue = AppConstants.ROUTE_ADMINISTRATION.TOPICAL.TITLE;
        break;
        case AppConstants.ROUTE_ADMINISTRATION.INJECTION.VALUE:
          routeValue = AppConstants.ROUTE_ADMINISTRATION.INJECTION.TITLE;
        break;
        case AppConstants.ROUTE_ADMINISTRATION.SUBLINGUAL.VALUE:
          routeValue = AppConstants.ROUTE_ADMINISTRATION.SUBLINGUAL.TITLE;
        break;
        case AppConstants.ROUTE_ADMINISTRATION.RECTAL.VALUE:
          routeValue = AppConstants.ROUTE_ADMINISTRATION.RECTAL.TITLE;
        break;
        case AppConstants.ROUTE_ADMINISTRATION.VAGINAL.VALUE:
          routeValue = AppConstants.ROUTE_ADMINISTRATION.VAGINAL.TITLE;
        break;
        case AppConstants.ROUTE_ADMINISTRATION.OCULAR.VALUE:
          routeValue = AppConstants.ROUTE_ADMINISTRATION.OCULAR.TITLE;
        break;
        case AppConstants.ROUTE_ADMINISTRATION.OTIC.VALUE:
          routeValue = AppConstants.ROUTE_ADMINISTRATION.OTIC.TITLE;
        break;
        case AppConstants.ROUTE_ADMINISTRATION.NEBULIZATION.VALUE:
          routeValue = AppConstants.ROUTE_ADMINISTRATION.NEBULIZATION.TITLE;
        break;
      }
      return routeValue;
    },
    returnSexValue: function (value) {
      var sexValue;
      AppConstants.USER_SEX.MALE.VALUE == parseInt(value)? sexValue = AppConstants.USER_SEX.MALE.TITLE : sexValue = AppConstants.USER_SEX.FEMALE.TITLE;
      return sexValue;
    },
    returnMaritalStatusValue: function (value) {
      var maritalValue;
      AppConstants.MARITAL_STATUS.SINGLE.VALUE == parseInt(value)? maritalValue = AppConstants.MARITAL_STATUS.SINGLE.TITLE : maritalValue = AppConstants.MARITAL_STATUS.MARRIED.TITLE;
      return maritalValue;
    },
  },
  mounted() {
    this.healthCareService = AppConstants.APPOINTMENT_SERVICES;
  },
});
</script>