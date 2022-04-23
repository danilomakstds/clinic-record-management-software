<template>
  <nav-component/>
  <div class="appointments p-4">
    <div class="card">
        <div class="card-body">
            <div class="overflow-hidden mb-2">
              <input type="email" class="form-control w-25 float-start" placeholder="Search Appointment" v-model="searchValue">
              <div class="form-check form-switch float-end">
                <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" v-model="isAppointmentListToday" value="true">
                <label class="form-check-label" for="flexSwitchCheckDefault">Show today's appointments</label>
              </div>
            </div>
            
            <table class="table table-striped" style="font-size:14px">
                <thead>
                  <tr>
                    <th scope="col">Patient</th>
                    <th scope="col">Appointment #</th>
                    <th scope="col">Agenda</th>
                    <th scope="col">Date</th>
                    <th scope="col">Time Slot</th>
                    <th scope="col">Status</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="app in allAppointments" :key="app.id">
                    <td><img src="../../resources/avatar.svg" style="height:35px" class="rounded-circle me-2">{{app.fullName}}</td>
                    <td>{{app.id}}</td>
                    <td>{{app.agendaTitle}}</td>
                    <td>{{app.convertedDate}}</td>
                    <td>{{app.timeSlot}}</td>
                    <td>
                      <span class="badge rounded-pill bg-success" v-if="app.app_status == 2">Done</span>
                      <span class="badge rounded-pill bg-primary" v-if="app.app_status == 1">Doctor's Queue</span>
                      <span class="badge rounded-pill bg-secondary" v-if="app.app_status == 0">Created</span>
                    </td>
                    <td>
                      <button @click="editAppointment(app)" :disabled="app.app_status > 0" data-bs-toggle="modal" data-bs-target="#editAppointment" type="button" class="btn btn-primary btn-sm me-2" title="Edit appointment"><font-awesome-icon :icon="['fa', 'pen']" /></button>
                      <button @click="viewAppointment(app)" :disabled="app.app_status == 0" data-bs-toggle="modal" data-bs-target="#viewMore" type="button" class="btn btn-info btn-sm me-2 text-white" title="View more details"><font-awesome-icon :icon="['fa', 'eye']" /></button>
                      <button @click="removeAppointment(app)" type="button" class="btn btn-danger btn-sm me-2" title="Remove appointment"><font-awesome-icon :icon="['fa', 'trash-can']" /></button>
                    </td>
                  </tr>
                </tbody>
            </table>
        </div>
    </div>
  </div>

  <div class="modal fade" id="viewMore">
    <div class="modal-dialog">
      <div class="modal-content" v-if="viewedAppointment">
        <div class="modal-header">
          <h5 class="modal-title">Appointment #{{viewedAppointment.id}} <span class="badge rounded-pill bg-success" v-if="viewedAppointment.app_status == 1">Done</span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <ul class="list-group list-group-flush">
            <li class="list-group-item">
              <label style="font-size:13px" class="fw-bold">Patient Concerns</label><br/>
              {{viewedAppointment.app_patientconcerns}}
            </li>
            <li class="list-group-item">
              <label style="font-size:13px" class="fw-bold">Doctor's Diagnosis</label><br/>
              {{viewedAppointment.app_diagnosis}}
            </li>
            <li class="list-group-item">
              <label style="font-size:13px" class="fw-bold">Doctor's Prescription</label><br/>
              {{viewedAppointment.app_prescription}}
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="editAppointment">
    <div class="modal-dialog">
      <div class="modal-content" v-if="editedAppointment">
        <div class="modal-header">
          <h5 class="modal-title">Edit Appointment #{{editedAppointment.id}}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form @submit="saveAppointmentChanges()">
        <div class="modal-body">
          <ul class="list-group list-group-flush">
            <li class="list-group-item">
              <label style="font-size:13px" class="fw-bold">Agenda</label><br/>
              <select class="form-select" v-model="editAgenda">
                <option value="1">Labor</option>
                <option value="2">Prenatal Care</option>
                <option value="3">Checkup / Consultation</option>
                <option value="4">COVID-19 Vaccination</option>
                <option value="5">Anti Rabies Vaccination Care</option>
                <option value="6">Anti Tetanus Vaccination</option>
                <option value="7">Depo-Provera Injections</option>
              </select>
            </li>
            <li class="list-group-item">
              <label style="font-size:13px" class="fw-bold">Change Date</label><br/>
              <div class="mb-3">
                <input type="date" class="form-control" v-model="editDate"/>
              </div>
            </li>
            <li class="list-group-item">
              <label style="font-size:13px" class="fw-bold">Chage Time Slot</label><br/>
              <select class="form-select" v-model="editTimeSlot">
                <option v-for="slot in appointmentSlots" :key="slot.id" :value="slot.SLOTID">{{slot.SCHED}}</option>
              </select>
            </li>
          </ul>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Save changes</button>
        </div>
        </form>
      </div>
    </div>
  </div>

</template>


<script>
// @ is an alias to /src
import SettingsConstants from '../constants/settings.constants'
import AppConstants from '../constants/app.constants'
import axios from "axios"
import moment from 'moment'
//import store from '../store';
import { mapState } from 'vuex'
import NavComponent from '../components/NavComponent.vue'
import Swal from 'sweetalert2'
//import { Modal } from 'bootstrap'

export default {
  components: { NavComponent },
  name: 'AppointmentsPage',
  computed: mapState([
      'sessionData'
  ]),
  watch: {
    isAppointmentListToday: function (newVal) {
      this.isAppointmentListToday = newVal;
      this.getAllAppointments();
    },
    searchValue: function (newVal) {
      this.allAppointments = this.allAppointmentsTemp;
      this.applySearch(newVal);
    }
  },
  data() {
    return {
      isAdmin: true,
      allAppointments: [],
      allAppointmentsTemp: [],
      isAppointmentListToday: false,
      searchValue: null,
      viewedAppointment: null,
      editedAppointment: null,
      editAgenda: null,
      editDate: null,
      editTimeSlot: null,
      appointmentSlots: []
    }
  },
  methods: {
    getAllAppointments: function() {
      var today = moment().format().split('T')[0];
      this.allAppointments = [];
      var url = 'appointment.rest.php?type=everything';
      if (this.isAppointmentListToday) {
        url = url + '&isToday=' + today;
      }
      axios.get(SettingsConstants.BASE_URL + url, { crossdomain: true })
          .then(function (response) {
              if (response.data) {
                this.allAppointments = response.data;
                this.allAppointmentsTemp = this.allAppointments;
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
              }
          }.bind(this));
    },
    applySearch: function (newVal) {
      if (newVal) {
        this.allAppointments = this.allAppointments.filter(app => app.fullName.toLowerCase().startsWith(newVal.toLowerCase()));
      } else {
        this.allAppointments = this.allAppointmentsTemp;
      }
    },
    removeAppointment: function (app) {
      Swal.fire({
        title: 'Remove Appointment?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0D6EFD',
        cancelButtonColor: '#6C757D',
        confirmButtonText: 'Yes, remove it!'
      }).then( function (result) {
        if (result.isConfirmed) {
          axios.get(SettingsConstants.BASE_URL + 'appointment.rest.php?type=deleteappointment&appid=' + app.id, { crossdomain: true })
            .then(function (response) {
              if (response.data) {
                Swal.fire(
                  'Removed!',
                  'Appointment has been removed.',
                  'success'
                ).then(function (){
                  this.getAllAppointments();
                }.bind(this));
              }
            }.bind(this));
        }
      }.bind(this))
    },
    viewAppointment: function (app) {
      this.viewedAppointment = app;
    },
    editAppointment: function (app) {
      this.editedAppointment = app;
      this.editAgenda = this.editedAppointment.app_apptype;
      this.editDate = this.editedAppointment.app_date.split('T')[0];
      this.editTimeSlot = this.editedAppointment.app_timeslot;
      this.appointmentSlots = AppConstants.APPOINTMENT_SLOTS;
    },
    saveAppointmentChanges: function () {
      event.preventDefault();
      var bodyFormData = new FormData();
      bodyFormData.append('app_type', this.editAgenda);
      bodyFormData.append('app_timeslot', this.editTimeSlot);
      bodyFormData.append('app_date', moment(this.editDate).format());
      axios({
          method: "post",
          url: SettingsConstants.BASE_URL + "appointment.rest.php?type=updateappointment&app_id=" + this.editedAppointment.id,
          data: bodyFormData,
          headers: { "Content-Type": "multipart/form-data" },
      })
          .then(function (response) {
            if (response.data == 1) {
                Swal.fire(
                  'Success!',
                  'Appointment #'+this.editedAppointment.id + ' has been edited!',
                  'success'
                );
                setTimeout(function(){
                  this.resetFields();
                  this.getAllAppointments();
                }.bind(this), 500); 
            } else {
                Swal.fire(
                  'Error!',
                  'Something went wrong when editing drug item!',
                  'error'
                );
            }
          }.bind(this))
          .catch(function (response) {
              console.log(response);
          });
    },
    resetFields: function () {
      this.editedAppointment = null;
      this.editAgenda = null;
      this.editDate = null;
      this.editTimeSlot = null;
    }
  },
  mounted() {
    this.getAllAppointments();
  },
}
</script>
