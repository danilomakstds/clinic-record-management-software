<template>
  <nav-component />
  <div class="appointments p-4">
    <div class="card">
      <div class="card-body">
        <div class="overflow-hidden mb-2">
          <input type="email" class="form-control w-25 float-start" placeholder="Search User" v-model="searchValue">
          <button @click="editAddUser" type="button" class="btn btn-primary float-end me-2 btn-sm">
            <font-awesome-icon :icon="['fa', 'plus']" /> Add User
          </button>
          <div class="form-check form-switch float-end me-4">
            <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault1" v-model="showOnlyPatients">
            <label class="form-check-label" for="flexSwitchCheckDefault1"> Show Patients</label>
          </div>
          <div class="form-check form-switch float-end me-4">
            <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault2" v-model="showOnlyAdmins">
            <label class="form-check-label" for="flexSwitchCheckDefault2"> Show Admins</label>
          </div>
        </div>
        <table class="table table-striped" style="font-size:14px">
          <thead>
            <tr>
              <th scope="col">Name</th>
              <th scope="col">Email</th>
              <th scope="col">User Level</th>
              <th scope="col">Contact Number</th>
              <th scope="col">Address</th>
              <th scope="col">Gender</th>
              <th scope="col">Marital Status</th>
              <th scope="col">DOB</th>
              <th scope="col">Age</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in allUsers" :key="user.id">
              <td><img src="../../resources/avatar.svg" class="rounded-circle me-2" style="height:30px">
                {{user.fullName}}
                <font-awesome-icon
                @click="viewAppointmentHistory(user)"
                :icon="['fa', 'external-link-alt']" class="ms-2" v-if="user.user_level == 0"/></td>
              <td>{{user.user_email}}</td>
              <td>
                <span class="badge bg-primary rounded-pill" style="font-size:12px"
                  v-if="user.user_level == '3'">{{user.level}}</span>
                <span class="badge bg-info rounded-pill" style="font-size:12px"
                  v-if="user.user_level == '2'">{{user.level}}</span>
                <span class="badge bg-warning rounded-pill" style="font-size:12px"
                  v-if="user.user_level == '1'">{{user.level}}</span>
                <span class="badge bg-secondary rounded-pill" style="font-size:12px"
                  v-if="user.user_level == '0'">{{user.level}}</span>
              </td>
              <td>{{user.user_contactnum}}</td>
              <td><span v-if="user.user_address">{{user.user_address}}, {{user.user_city}}
                  {{user.user_province}}</span><span v-else>-</span></td>
              <td>
                <span class="badge rounded-pill bg-primary" v-if="user.user_sex == '1'">{{user.sex}}</span>
                <span class="badge rounded-pill bg-pink" v-else>{{user.sex}}</span>
              </td>
              <td>
                <span class="badge rounded-pill bg-secondary"
                  v-if="user.user_maritalstatus == '1'">{{user.martial_status}}</span>
                <span class="badge rounded-pill bg-warning"
                  v-else-if="user.user_maritalstatus == '2'">{{user.martial_status}}</span>
                <span v-else>{{user.martial_status}}</span>
              </td>
              <td>{{user.user_dob}}</td>
              <td>{{user.age}}</td>
              <td>
                <button type="button" @click="editAddUser(user)" class="btn btn-primary btn-sm me-2" title="Edit user">
                  <font-awesome-icon :icon="['fa', 'pen']" />
                </button>
                <button type="button" @click="onclickDeleteUser(user)" class="btn btn-danger btn-sm me-2"
                  title="Remove user">
                  <font-awesome-icon :icon="['fa', 'trash-can']" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="modal fade" id="editAddUser">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <span v-if="selectedUserItem">Edit</span><span v-else>Add</span> User
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form @submit="addNewUser()">
          <div class="modal-body">
            <ul class="list-group list-group-flush">
              <li class="list-group-item">
                <table class="w-100">
                  <tr>
                    <td>
                      <div class="mb-3">
                        <label style="font-size: 14px" class="form-label fw-bold">First Name</label>
                        <input type="text" class="form-control" required v-model="eaFirstName">
                      </div>
                    </td>
                    <td>
                      <div class="mb-3">
                        <label style="font-size: 14px" class="form-label fw-bold">Middle Name</label>
                        <input type="text" class="form-control" v-model="eaMiddleName">
                      </div>
                    </td>
                    <td>
                      <div class="mb-3">
                        <label style="font-size: 14px" class="form-label fw-bold">Last Name</label>
                        <input type="text" class="form-control" required v-model="eaLastName">
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div class="mb-3">
                        <label style="font-size: 14px" class="form-label fw-bold">Suffix</label>
                        <input type="text" class="form-control" v-model="eaSuffix">
                      </div>
                    </td>
                    <td>
                      <div class="mb-3">
                        <label style="font-size: 14px" class="form-label fw-bold">Marital Status</label>
                        <select class="form-select" required v-model="eaMaritalStatus">
                          <option :value="0">Select Options</option>
                          <option :value="1">Single</option>
                          <option :value="2">Married</option>
                        </select>
                      </div>
                    </td>
                  </tr>
                </table>
              </li>
              <li class="list-group-item">
                <table class="w-100">
                  <tr class="w-100">
                    <td>
                      <div class="mb-3">
                        <label style="font-size: 14px" class="form-label fw-bold">User Type</label>
                        <select class="form-select" required v-model="eaUserLevel">
                          <option :value="0">Patient</option>
                          <option :value="1">Nurse</option>
                          <option :value="2">Doctor</option>
                          <option :value="3">Admin</option>
                        </select>
                      </div>
                    </td>
                    <td>
                      <div class="mb-3">
                        <label style="font-size: 14px" class="form-label fw-bold">Date of birth</label>
                        <input type="date" class="form-control" v-model="eaUserDOB">
                      </div>
                    </td>
                    <td>
                      <div class="mb-3">
                        <label style="font-size: 14px" class="form-label fw-bold">Sex</label>
                        <select class="form-select" v-model="eaUserSex">
                          <option :value="0">Select Options</option>
                          <option :value="1">Male</option>
                          <option :value="2">Female</option>
                        </select>
                      </div>
                    </td>
                  </tr>
                </table>
              </li>
              <li class="list-group-item">
                <table class="w-100">
                  <tr class="w-100">
                    <td>
                      <div class="mb-3">
                        <label style="font-size: 14px" class="form-label fw-bold">User Email</label>
                        <input type="text" class="form-control" v-model="eaUserEmail" required>
                      </div>
                    </td>
                    <td v-if="eaUserLevel > 0">
                      <div class="mb-3">
                        <label style="font-size: 14px" class="form-label fw-bold">User Password</label>
                        <input type="password" class="form-control" v-model="eaUserPassword" required>
                      </div>
                    </td>
                  </tr>
                </table>
              </li>
              <li class="list-group-item">
                <div class="mb-3">
                  <label style="font-size: 14px" class="form-label fw-bold">Address</label>
                  <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"
                    v-model="eaUserAddress"></textarea>
                </div>
              </li>
              <li class="list-group-item">
                <table class="w-100">
                  <tr>
                    <td>
                      <label style="font-size: 14px" class="form-label fw-bold">City</label>
                      <input type="text" class="form-control" v-model="eaUserCity">
                    </td>
                    <td>
                      <label style="font-size: 14px" class="form-label fw-bold">Province</label>
                      <input type="text" class="form-control" v-model="eaUserProvince">
                    </td>
                    <td>
                      <label style="font-size: 14px" class="form-label fw-bold">Contact Number</label>
                      <input type="text" class="form-control" v-model="eaUserContactNum" maxlength="11">
                    </td>
                  </tr>
                </table>
              </li>
            </ul>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <span v-if="selectedUserItem">
              <button type="button" @click="performUserEdit" class="btn btn-primary">Save changes</button>
            </span>
            <span v-else>
              <button type="submit" class="btn btn-primary">Save changes</button>
            </span>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="appointmentHistory">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            Appointment History - <span class="badge rounded-pill bg-primary ms-2">{{historyModalName}}</span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <table class="table table-striped" style="font-size:14px" v-if="allAppointments.length">
              <thead>
                <tr>
                  <th scope="col">Agenda</th>
                  <th scope="col">Date</th>
                  <th scope="col">Time Slot</th>
                  <th scope="col">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="app in allAppointments" :key="app.id">
                  <td>{{app.agendaTitle}}</td>
                  <td>{{app.convertedDate}}</td>
                  <td>{{app.timeSlot}}</td>
                  <td>
                    <span class="badge rounded-pill bg-success" v-if="app.app_status == 2">Done</span>
                    <span class="badge rounded-pill bg-primary" v-if="app.app_status == 1">Doctor's Queue</span>
                    <span class="badge rounded-pill bg-secondary" v-if="app.app_status == 0">Created</span>
                  </td>
                </tr>
              </tbody>
          </table>
          <span v-if="!allAppointments.length">
            No records found.
          </span>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
// @ is an alias to /src
import Swal from 'sweetalert2'
import SettingsConstants from '../constants/settings.constants'
import AppConstants from '../constants/app.constants'
import axios from "axios"
import moment from 'moment'
//import store from '../store';
import { mapState } from 'vuex'
import NavComponent from '../components/NavComponent.vue'
import { Modal } from 'bootstrap'

export default {
  components: { NavComponent },
  name: 'PatientsPage',
  computed: mapState([
      'sessionData'
  ]),
  watch: {
    searchValue: function (newVal) {
      this.allUsers = this.allUsersTemp;
      this.applySearch(newVal);
    },
    showOnlyPatients: function (newVal) {
      if (newVal) {
        this.showOnlyAdmins = false;  
      }
      this.getAllUsers();
    },
    showOnlyAdmins: function (newVal) {
      if (newVal) {
        this.showOnlyPatients = false;
      }
      this.getAllUsers();
    }
  },
  data() {
    return {
      allUsers: [],
      allUsersTemp: [],
      searchValue: null,
      showOnlyPatients: false,
      showOnlyAdmins: false,
      selectedUserItem: null,
      isEditing: false,
      editAddModal: null,

      eaFirstName: null,
      eaSuffix: null,
      eaMiddleName: null,
      eaLastName: null,
      eaUserLevel: 0,
      eaUserDOB: null,
      eaUserSex: 0,
      eaUserAddress: null,
      eaUserCity: null,
      eaUserProvince: null,
      eaUserContactNum: null,
      eaUserEmail: null,
      eaUserPassword: null,
      eaMaritalStatus: 0,

      allAppointments: [],
      historyModalName: null
    }
  },
  methods: {
    applySearch: function (newVal) {
      if (newVal) {
        this.allUsers = this.allUsers.filter(user => user.fullName.toLowerCase().startsWith(newVal.toLowerCase()));
      } else {
        this.allUsers = this.allUsersTemp;
      }
    },
    getAllAppointments: function(user) {
      this.allAppointments = [];
      var url = 'appointment.rest.php?type=alladdreesedbyId&userId=' + user.id;
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
    viewAppointmentHistory: function (user) {
      this.historyModalName = user.fullName;
      this.historyModal = new Modal(document.getElementById('appointmentHistory'));
      this.historyModal.toggle();
      axios.get(SettingsConstants.BASE_URL + 'schedule.rest.php?type=all', { crossdomain: true })
          .then(function (response) {
            if (response.data) {
              this.appointmentSlots = response.data;
              this.getAllAppointments(user);
            }
          }.bind(this));
    },
    editAddUser: function (user) {
      this.editAddModal = new Modal(document.getElementById('editAddUser'));
      this.editAddModal.toggle();
      if (user.id) {
        this.selectedUserItem = user;
        this.eaFirstName = this.selectedUserItem.user_firstname;
        this.eaMiddleName = this.selectedUserItem.user_middlename;
        this.eaLastName = this.selectedUserItem.user_lastname;
        this.eaSuffix = this.selectedUserItem.user_suffix;
        this.eaUserLevel = this.selectedUserItem.user_level;
        this.eaUserDOB = this.selectedUserItem.user_dob;
        this.eaUserSex = this.selectedUserItem.user_sex;
        this.eaUserAddress = this.selectedUserItem.user_address;
        this.eaUserCity = this.selectedUserItem.user_city;
        this.eaUserProvince = this.selectedUserItem.user_province;
        this.eaUserContactNum = this.selectedUserItem.user_contactnum;
        this.eaUserEmail = this.selectedUserItem.user_email;
        this.eaUserPassword = this.selectedUserItem.user_password;
        this.eaMaritalStatus = this.selectedUserItem.user_maritalstatus;
      } else {
        this.selectedUserItem = null;
        this.eaFirstName = null;
        this.eaMiddleName = null;
        this.eaLastName = null;
        this.eaSuffix = null;
        this.eaUserLevel = 0;
        this.eaUserDOB = null;
        this.eaUserSex = 0;
        this.eaUserAddress = null;
        this.eaUserCity = null;
        this.eaUserProvince = null;
        this.eaUserContactNum = null;
        this.eaUserEmail = null;
        this.eaUserPassword = null;
        this.eaMaritalStatus = 0;
      }
    },
    performUserEdit: function () {
      var bodyFormData = new FormData();
      bodyFormData.append('user_email', this.eaUserEmail);
      this.eaUserPassword ? bodyFormData.append('user_password', this.eaUserPassword) : bodyFormData.append('user_password', '');
      bodyFormData.append('user_firstname', this.eaFirstName);
      bodyFormData.append('user_lastname', this.eaLastName);
      this.eaMiddleName ? bodyFormData.append('user_middlename', this.eaMiddleName) : bodyFormData.append('user_middlename', '');
      this.eaSuffix ? bodyFormData.append('user_suffix', this.eaSuffix) : bodyFormData.append('user_suffix', '');
      bodyFormData.append('user_sex', this.eaUserSex);
      this.eaUserAddress ? bodyFormData.append('user_address', this.eaUserAddress) : bodyFormData.append('user_address', '');
      this.eaUserCity ? bodyFormData.append('user_city', this.eaUserCity) : bodyFormData.append('user_city', '');
      this.eaUserProvince ? bodyFormData.append('user_province', this.eaUserProvince) : bodyFormData.append('user_province', '');
      this.eaUserContactNum ? bodyFormData.append('user_contactnum', this.eaUserContactNum) : bodyFormData.append('user_contactnum', '');
      this.eaUserDOB ? bodyFormData.append('user_dob', this.eaUserDOB) : bodyFormData.append('user_dob', '');
      bodyFormData.append('user_maritalstatus', this.eaMaritalStatus);
      bodyFormData.append('user_level', this.eaUserLevel);

      axios({
          method: "post",
          url: SettingsConstants.BASE_URL + "users.rest.php?type=updatepatient&userid=" + this.selectedUserItem.id,
          data: bodyFormData,
          headers: { "Content-Type": "multipart/form-data" },
      })
          .then(function (response) {
            if (response.data == 1) {
                Swal.fire(
                  'Great!',
                  'User has been updated!',
                  'success'
                ).then(function () {
                  this.getAllUsers();
                  this.editAddModal.hide();
                }.bind(this));
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
    addNewUser: function () {
      event.preventDefault();
      var bodyFormData = new FormData();
      bodyFormData.append('user_email', this.eaUserEmail);
      this.eaUserPassword ? bodyFormData.append('user_password', this.eaUserPassword) : bodyFormData.append('user_password', 'patient1234');
      bodyFormData.append('user_firstname', this.eaFirstName);
      bodyFormData.append('user_lastname', this.eaLastName);
      this.eaMiddleName ? bodyFormData.append('user_middlename', this.eaMiddleName) : bodyFormData.append('user_middlename', '');
      this.eaSuffix ? bodyFormData.append('user_suffix', this.eaSuffix) : bodyFormData.append('user_suffix', '');
      bodyFormData.append('user_sex', this.eaUserSex);
      this.eaUserAddress ? bodyFormData.append('user_address', this.eaUserAddress) : bodyFormData.append('user_address', '');
      this.eaUserCity ? bodyFormData.append('user_city', this.eaUserCity) : bodyFormData.append('user_city', '');
      this.eaUserProvince ? bodyFormData.append('user_province', this.eaUserProvince) : bodyFormData.append('user_province', '');
      this.eaUserContactNum ? bodyFormData.append('user_contactnum', this.eaUserContactNum) : bodyFormData.append('user_contactnum', '');
      this.eaUserDOB ? bodyFormData.append('user_dob', this.eaUserDOB) : bodyFormData.append('user_dob', '');
      bodyFormData.append('user_maritalstatus', this.eaMaritalStatus);
      bodyFormData.append('user_level', this.eaUserLevel);

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
                ).then( function () {
                  this.getAllUsers();
                  this.editAddModal.hide();
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
    getAllUsers: function () {
      this.allUsers = [];
      var url = null;
      if (this.showOnlyPatients) {
        url = 'users.rest.php?type=getallpatients';
      } else if (this.showOnlyAdmins) {
        url = 'users.rest.php?type=getalladmins';
      } else {
        url = 'users.rest.php?type=all'
      }
      //getallpatients
      axios.get(SettingsConstants.BASE_URL + url , { crossdomain: true })
          .then(function (response) {
              if (response.data) {
                  this.allUsers = response.data;
                  this.allUsers.forEach( function (patient){
                    patient.fullName = patient.user_firstname + ' ' + patient.user_middlename + ' ' + patient.user_lastname + ' ' + patient.user_suffix;
                    if (patient.user_dob) {
                      if ((moment(patient.user_dob, 'YYYYMMDD').fromNow()).includes('years')) {
                        patient.age = (moment(patient.user_dob, 'YYYYMMDD').fromNow()).split(" ")[0];
                      } else {
                        patient.age = '< 1 year';
                      }
                    } else {
                      patient.age = "-";
                    }
                    switch (patient.user_level) {
                      case AppConstants.USER_LEVEL.PATIENT.VALUE.toString():
                        patient.level = AppConstants.USER_LEVEL.PATIENT.TITLE;
                        break;
                      case AppConstants.USER_LEVEL.NURSE.VALUE.toString():
                        patient.level = AppConstants.USER_LEVEL.NURSE.TITLE;
                        break;
                      case AppConstants.USER_LEVEL.DOCTOR.VALUE.toString():
                        patient.level = AppConstants.USER_LEVEL.DOCTOR.TITLE;
                        break;
                      case AppConstants.USER_LEVEL.SUPERADMIN.VALUE.toString():
                        patient.level = AppConstants.USER_LEVEL.SUPERADMIN.TITLE;
                        break;
                    }
                    switch (patient.user_sex) {
                      case AppConstants.USER_SEX.MALE.VALUE.toString():
                        patient.sex = 'M';
                        break;
                      case AppConstants.USER_SEX.FEMALE.VALUE.toString():
                        patient.sex = 'F';
                        break;
                    }
                    switch (patient.user_maritalstatus.toString()) {
                      case '1':
                        patient.martial_status = 'Single';
                        break;
                      case '2':
                        patient.martial_status = 'Married';
                        break;
                      case '0':
                        patient.martial_status = '-';
                        break;
                    }
                  });
                  this.allUsersTemp = this.allUsers;
              }
          }.bind(this));
    },
    onclickDeleteUser: function (user) {
        Swal.fire({
        title: 'Remove ' + user.fullName + ' ?',
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
                  'User has been removed.',
                  'success'
                ).then(function (){
                  this.getAllUsers();
                }.bind(this));
              }
            }.bind(this));
        }
      })
    }
  },
  mounted() {
    this.getAllUsers();
  },
  
}
</script>
