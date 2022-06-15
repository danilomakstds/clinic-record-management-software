<template>
  <nav-component />
  <div class="appointments p-4">

    <div class="card">
      <div class="card-body">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button"
              role="tab" aria-controls="home" aria-selected="true">Appointment List</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule" type="button"
              role="tab" aria-controls="schedule" aria-selected="false">Schedule</button>
          </li>
        </ul>
        <div class="tab-content" id="myTabContent">
          <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <br />
            <div class="overflow-hidden mb-2">
              <input type="email" class="form-control w-25 float-start" placeholder="Search Appointment"
                v-model="searchValue">
              <div class="form-check form-switch float-end">
                <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault"
                  v-model="isAppointmentListToday" value="true">
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
                  <td><img src="../../resources/avatar.svg" style="height:35px"
                      class="rounded-circle me-2">{{app.fullName}}</td>
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
                    <button @click="editAppointment(app)" :disabled="app.app_status > 0" data-bs-toggle="modal"
                      data-bs-target="#editAppointment" type="button" class="btn btn-primary btn-sm me-2"
                      title="Edit appointment">
                      <font-awesome-icon :icon="['fa', 'pen']" />
                    </button>
                    <button @click="viewAppointment(app)" :disabled="app.app_status == 0" data-bs-toggle="modal"
                      data-bs-target="#viewMore" type="button" class="btn btn-info btn-sm me-2 text-white"
                      title="View more details">
                      <font-awesome-icon :icon="['fa', 'eye']" />
                    </button>
                    <button @click="removeAppointment(app)" type="button" class="btn btn-danger btn-sm me-2"
                      title="Remove appointment">
                      <font-awesome-icon :icon="['fa', 'trash-can']" />
                    </button>
                    <button @click="viewPrescription(app)" :disabled="app.app_status != 2 || app.app_apptype > 2" data-bs-toggle="modal"
                      data-bs-target="#showPrescriptionModal" type="button" class="btn btn-success btn-sm me-2"
                      title="View Prescription">
                      <font-awesome-icon :icon="['fa', 'file-contract']" />
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="tab-pane fade" id="schedule" role="tabpanel" aria-labelledby="schedule-tab">
            <br />
            <div class="overflow-hidden mb-2">
              <div class="form-check form-switch float-end">
                <button type="button" class="btn btn-primary btn-sm" @click="showAddEditScheduleModal()">
                  <font-awesome-icon :icon="['fa', 'plus']" /> Add Schedule
                </button>
              </div>
            </div>
            <div class="row">
              <div class="col-md-3">

                <div class="alert alert-info" role="alert" v-if="workdays.length">
                  <ion-icon :icon="informationCircleOutline" class="me-3 float-start" size="large"></ion-icon>
                  Doctor's schedule is
                  <span v-for="(work, index) in workdays" :key="`work-${index}`">
                    <span v-if="index == workdays.length - 1">and </span>
                    <strong v-if="work == daysConstant.SUNDAY.VALUE">{{daysConstant.SUNDAY.TITLE}}s<span
                        v-if="index != workdays.length - 1">, </span> </strong>
                    <strong v-if="work == daysConstant.MONDAY.VALUE">{{daysConstant.MONDAY.TITLE}}s<span
                        v-if="index != workdays.length - 1">, </span> </strong>
                    <strong v-if="work == daysConstant.TUESDAY.VALUE">{{daysConstant.TUESDAY.TITLE}}s<span
                        v-if="index != workdays.length - 1">, </span> </strong>
                    <strong v-if="work == daysConstant.WEDNESDAY.VALUE">{{daysConstant.WEDNESDAY.TITLE}}s<span
                        v-if="index != workdays.length - 1">, </span> </strong>
                    <strong v-if="work == daysConstant.THURSDAY.VALUE">{{daysConstant.THURSDAY.TITLE}}s<span
                        v-if="index != workdays.length - 1">, </span> </strong>
                    <strong v-if="work == daysConstant.FRIDAY.VALUE">{{daysConstant.FRIDAY.TITLE}}s<span
                        v-if="index != workdays.length - 1">, </span> </strong>
                    <strong v-if="work == daysConstant.SATURDAY.VALUE">{{daysConstant.SATURDAY.TITLE}}s<span
                        v-if="index != workdays.length - 1">, </span> </strong>
                  </span>
                  only.
                </div>
                <label class="mt-2">Select clinic schedule</label>
                <select class="js-example-basic-multiple w-100" multiple="multiple" @change="workdaysChanged()">
                  <option value="0">Sunday</option>
                  <option value="1">Monday</option>
                  <option value="2">Tuesday</option>
                  <option value="3">Wednesday</option>
                  <option value="4">Thursday</option>
                  <option value="5">Friday</option>
                  <option value="6">Saturday</option>
                </select>
              </div>
              <div class="col-md-9">
                <table class="table table-striped" style="font-size:14px">
                  <thead>
                    <tr>
                      <th scope="col">Time Start</th>
                      <th scope="col">Time End</th>
                      <th scope="col">Display as</th>
                      <th scope="col">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="slot in appointmentSlots" :key="slot.id">
                      <td>{{slot.timestart}}</td>
                      <td>{{slot.timeend}}</td>
                      <td>{{slot.sched}}</td>
                      <td>
                        <button @click="showAddEditScheduleModal(slot)" type="button"
                          class="btn btn-primary btn-sm me-2" title="Edit appointment">
                          <font-awesome-icon :icon="['fa', 'pen']" />
                        </button>
                        <button @click="removeSchedule(slot)" type="button" class="btn btn-danger btn-sm me-2"
                          title="Remove appointment">
                          <font-awesome-icon :icon="['fa', 'trash-can']" />
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>

  <div class="modal fade" id="addeditTimeSlot">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" v-if="!isEditSchedule">Add schedule/timeslot</h5>
          <h5 class="modal-title" v-if="isEditSchedule">Edit schedule/timeslot</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form @submit="addNewtimeSlot()">
          <div class="modal-body">
            <ul class="list-group list-group-flush">
              <li class="list-group-item">
                <label style="font-size:13px" class="fw-bold">Time Start</label><br />
                <div class="mb-3">
                  <input type="text" class="form-control" placeholder="00:00:00" v-model="timeslotStart" required>
                </div>
              </li>
              <li class="list-group-item">
                <label style="font-size:13px" class="fw-bold">Time End</label><br />
                <div class="mb-3">
                  <input type="text" class="form-control" placeholder="00:00:00" v-model="timeslotEnd" required>
                </div>
              </li>
              <li class="list-group-item">
                <label style="font-size:13px" class="fw-bold">Name</label><br />
                <div class="mb-3">
                  <input type="text" class="form-control" placeholder="08:00 - 08:30 AM" v-model="timeslotName"
                    required>
                </div>
              </li>
            </ul>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary" v-if="!isEditSchedule">Add Schedule</button>
            <button type="button" class="btn btn-primary" v-if="isEditSchedule" @click="updateTimeSlot()">Update
              Schedule</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="viewMore">
    <div class="modal-dialog">
      <div class="modal-content" v-if="viewedAppointment">
        <div class="modal-header">
          <h5 class="modal-title">Appointment #{{viewedAppointment.id}} <span class="badge rounded-pill bg-success"
              v-if="viewedAppointment.app_status == 1">Done</span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <ul class="list-group list-group-flush">
            <li class="list-group-item">
              <label style="font-size:13px" class="fw-bold">Patient Concerns</label><br />
              {{viewedAppointment.app_patientconcerns}}
            </li>
            <li class="list-group-item">
              <label style="font-size:13px" class="fw-bold">Doctor's Diagnosis</label><br />
              {{viewedAppointment.app_diagnosis}}
            </li>
            <li class="list-group-item">
              <label style="font-size:13px" class="fw-bold">Doctor's Prescription</label><br />
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
                <label style="font-size:13px" class="fw-bold">Agenda</label><br />
                <select class="form-select" v-model="editAgenda">
                  <option value="1">Prenatal Care</option>
                  <option value="2">Checkup / Consultation</option>
                  <option value="3">API Immunization</option>
                  <option value="4">Family Planning</option>
                </select>
              </li>
              <li class="list-group-item">
                <label style="font-size:13px" class="fw-bold">Change Date</label><br />
                <div class="mb-3">
                  <input type="date" class="form-control" v-model="editDate" />
                </div>
              </li>
              <li class="list-group-item">
                <label style="font-size:13px" class="fw-bold">Chage Time Slot</label><br />
                <select class="form-select" v-model="editTimeSlot">
                  <option v-for="slot in appointmentSlots" :key="slot.id" :value="slot.slotid">{{slot.sched}}</option>
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

  <div class="modal fade" id="showPrescriptionModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" v-if="viewedAppointment">
        <div class="modal-header">
          <h5 class="modal-title">Appointment # {{viewedAppointment.id}}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="prescriptionPrinting">
          <div style="border: 1px solid #555; width: 600px; margin: 0 auto" class="p-5">
            <h4 style="color: #00ACC1" class="text-center"><strong>{{this.clinicName}}</strong></h4>
            <div class="row" style="border-bottom: 5px solid #00ACC1">
              <div class="col-md-6">Dra. Beth G. Catacutan<p class="text-muted" style="font-size: 13px">{{this.clinicAddress}}</p></div>
              <div class="col-md-6 text-end">
                <span class="text-muted" style="font-size: 13px">PH: (123) 808 2014 2015</span><br/>
                <span class="text-muted" style="font-size: 13px">FAX: (123) 808 2015 2202</span>
              </div>
            </div>
            <div class="mt-3">
                <strong>Patient Name</strong>: <span class="me-3">{{viewedAppointment.fullName}}</span>
                <strong>Age</strong>: <span class="me-3">{{viewedAppointment.patientAge}}</span>
                <strong>Gender</strong>: <span class="text-decoration-underline">{{viewedAppointment.patientGender}}</span><br/>
                <strong>Address</strong>: <span class="me-1">{{viewedAppointment.userAddress}}</span><br/>
                <strong>Date</strong>: <span class="text-decoration-underline">{{viewedAppointment.app_date}}</span>
            </div>
            <div class="mt-3" style="height: 350px; border-bottom: 5px solid #00ACC1">
              <h1><strong style="font-size: 50px">R</strong><strong style="font-size: 14px">x</strong></h1>
              <p>{{viewedAppointment.app_prescription}}</p>
            </div>
            <div class="mt-3">
              Doctor's Signature: <span class="me-2"><img src="https://norsucrms.000webhostapp.com/REST-API/signature.png" style="height: 80px"/></span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-success" data-bs-dismiss="modal" @click="printPrescription()">
          <font-awesome-icon :icon="['fa', 'print']" class="me-2" /> Print</button>
        </div>
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
import appConstants from '../constants/app.constants'
import { Modal } from 'bootstrap'
import settingsConstants from '@/constants/settings.constants'

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
      appointmentSlots: [],
      workdays: [],
      defaultSelected: null,
      daysConstant: null,

      timeslotStart: null,
      timeslotEnd: null,
      timeslotName: null,
      editAddModal: null,
      isEditSchedule: false,
      selectedforEdit: null,
      clinicName: null,
      clinicAddress: null
    }
  },
  methods: {
    showAddEditScheduleModal: function (slot) {
      this.editAddModal = new Modal(document.getElementById('addeditTimeSlot'));
      this.editAddModal.toggle();
      if (slot) {
        this.selectedforEdit = slot;
        this.isEditSchedule = true;
        this.timeslotStart = slot.timestart;
        this.timeslotEnd = slot.timeend;
        this.timeslotName = slot.sched;
      } else {
        this.selectedforEdit = null;
        this.isEditSchedule = false;
        this.timeslotStart = null;
        this.timeslotEnd = null;
        this.timeslotName = null;
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
                      app.userAddress = resp.user_address + ', ' + resp.user_city + ', ' + resp.user_province;
                      app.patientAge = null;
                      if (resp.user_dob) {
                        if ((moment(resp.user_dob, 'YYYYMMDD').fromNow()).includes('years')) {
                          app.patientAge = (moment(resp.user_dob, 'YYYYMMDD').fromNow()).split(" ")[0];
                        } else {
                          if ((moment(resp.user_dob, 'YYYYMMDD').fromNow()).includes('month')) {
                            app.patientAge = parseInt(moment(resp.user_dob, 'YYYYMMDD').fromNow().split(" ")[0]).toString() + ' months/old';
                          } else {
                            app.patientAge = '< 1 month old';
                          }
                        }
                      }
                      app.patientGender = (resp.user_sex == '1') ? 'Male':'Female';
                    }
                  }.bind(app));
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
        confirmButtonColor: '#d33',
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
    removeSchedule: function (slot) {
      Swal.fire({
        title: 'Remove '+slot.sched+' schedule?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6C757D',
        confirmButtonText: 'Yes, remove it!'
      }).then( function (result) {
        if (result.isConfirmed) {
          axios.get(SettingsConstants.BASE_URL + 'schedule.rest.php?type=deleteschedule&schedid=' + slot.slotid, { crossdomain: true })
            .then(function (response) {
              if (response.data) {
                Swal.fire(
                  'Removed!',
                  'Schedule has been removed.',
                  'success'
                ).then(function (){
                  this.getAllSchedule();
                }.bind(this));
              }
            }.bind(this));
        }
      }.bind(this))
    },
    viewAppointment: function (app) {
      this.viewedAppointment = app;
    },
    viewPrescription: function (app) {
      this.viewedAppointment = app;
    },
    printPrescription: function () {
      var divToPrint = document.getElementById("prescriptionPrinting");
      var newWin = window.open('', '', 'height=600,width=1000');
      newWin.document.write('<head><title>Prescription'+this.viewedAppointment.id+'-'+this.viewedAppointment.fullName+'</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"' +
          'integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">');
      //newWin.document.write('<style> #payslip-table {border: 1px solid #ccc;} #payslip-table tr td {border: 1px solid #ccc; padding: 10px;}</style></head>');
      setTimeout(function () {
          newWin.document.write(divToPrint.outerHTML);
          newWin.document.close();
          newWin.focus();
          newWin.print();
          setTimeout(function () {
              newWin.close();
          }, 500);
      }, 500);
    },
    editAppointment: function (app) {
      this.editedAppointment = app;
      this.editAgenda = this.editedAppointment.app_apptype;
      this.editDate = this.editedAppointment.app_date.split('T')[0];
      this.editTimeSlot = this.editedAppointment.app_timeslot;
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
    getAllWorkDays: function () {
       axios.get(SettingsConstants.BASE_URL + 'schedule.rest.php?type=workdays', { crossdomain: true })
          .then(function (response) {
            if (response.data[0].daysarray) {
              this.workdays = response.data[0].daysarray.split(',');
            }
            window.$(document).ready(function() {
                  window.$('.js-example-basic-multiple').val(this.workdays).select2();
                  window.$('.select2').addClass('w-100');
                  window.$('.js-example-basic-multiple').on('select2:select', function (e) {
                      this.workdaysChanged(e.params.data, 'add');
                  }.bind(this));
                  window.$('.js-example-basic-multiple').on('select2:unselect', function (e) {
                      this.workdaysChanged(e.params.data, 'remove');
                  }.bind(this));
              }.bind(this));
          }.bind(this));
    },
    workdaysChanged: function (data, type) {
      if (type == 'add') {
        this.workdays.push(data.id);
        console.log(this.workdays);
      } else {
        this.workdays = this.workdays.filter(function(item) {
            return item !== data.id
        }.bind(data));
        console.log(this.workdays);
      }
      var workdays = this.workdays;
      workdays = workdays.toString();
      this.updateWorkDays(workdays);
    },
    updateWorkDays: function (newWorkdays) {
      var bodyFormData = new FormData();
      bodyFormData.append('daysarray', newWorkdays);
      axios({
          method: "post",
          url: SettingsConstants.BASE_URL + "schedule.rest.php?type=updateworkdays",
          data: bodyFormData,
          headers: { "Content-Type": "multipart/form-data" },
      })
          .then(function (response) {
            if (response.data) {
              Swal.fire(
                'Great!',
                'Schedule successfuly updated!',
                'success'
              )
            }
          });
    },
    resetFields: function () {
      this.editedAppointment = null;
      this.editAgenda = null;
      this.editDate = null;
      this.editTimeSlot = null;
    },
    addNewtimeSlot: function () {
      event.preventDefault();
      var bodyFormData = new FormData();
      bodyFormData.append('timestart', this.timeslotStart);
      bodyFormData.append('timeend', this.timeslotEnd);
      bodyFormData.append('sched', this.timeslotName);
       axios({
          method: "post",
          url: SettingsConstants.BASE_URL + "schedule.rest.php?type=addnewschedule",
          data: bodyFormData,
          headers: { "Content-Type": "multipart/form-data" },
      })
          .then(function (response) {
            if (response.data == 1) {
                Swal.fire(
                  'Great!',
                  'A new time-slot/schedule has been created!',
                  'success'
                ).then( function () {
                  this.getAllSchedule();
                  this.editAddModal.hide();
                }.bind(this));
            } else {
                Swal.fire(
                  'Error!',
                  'Something went wrong when creating new time-slot!',
                  'error'
                );
            }
          }.bind(this));
    },
    updateTimeSlot: function () {
      var bodyFormData = new FormData();
      bodyFormData.append('timestart', this.timeslotStart);
      bodyFormData.append('timeend', this.timeslotEnd);
      bodyFormData.append('sched', this.timeslotName);
       axios({
          method: "post",
          url: SettingsConstants.BASE_URL + "schedule.rest.php?type=updateschedule&slotid=" + this.selectedforEdit.slotid,
          data: bodyFormData,
          headers: { "Content-Type": "multipart/form-data" },
      })
          .then(function (response) {
            if (response.data == 1) {
                Swal.fire(
                  'Great!',
                  'Time-slot/schedule has been updated!',
                  'success'
                ).then( function () {
                  this.getAllSchedule();
                  this.editAddModal.hide();
                }.bind(this));
            } else {
                Swal.fire(
                  'Error!',
                  'Something went wrong when updating time-slot!',
                  'error'
                );
            }
          }.bind(this));
    }
  },
  mounted() {
    this.getAllSchedule();
    this.getAllWorkDays();
    this.daysConstant = appConstants.DAYS;
    this.clinicName = settingsConstants.CLINIC_NAME;
    this.clinicAddress = settingsConstants.CLINIC_ADDRESS;
  },
}
</script>
