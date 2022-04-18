<template>
  <nav-component/>
  <div class="appointments p-4">
    <div class="card">
        <div class="card-body">
            <div class="overflow-hidden mb-2">
              <input type="email" class="form-control w-25 float-start" placeholder="Search User">
              <button type="button" class="btn btn-primary float-end me-2 btn-sm"><font-awesome-icon :icon="['fa', 'plus']" /> Add User</button>
              <button type="button" class="btn btn-primary float-end me-2 btn-sm"><font-awesome-icon :icon="['fa', 'eye']" /> Show Patients</button>
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
                    <th scope="col">DOB</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="user in allUsers" :key="user.id">
                    <td><img src="../../resources/avatar.svg" class="rounded-circle me-2" style="height:30px"> {{user.fullName}}</td>
                    <td>{{user.user_email}}</td>
                    <td>
                      <span class="badge bg-primary rounded-pill" style="font-size:12px" v-if="user.user_level == '3'">{{user.level}}</span>
                      <span class="badge bg-info rounded-pill" style="font-size:12px" v-if="user.user_level == '2'">{{user.level}}</span>
                      <span class="badge bg-warning rounded-pill" style="font-size:12px" v-if="user.user_level == '1'">{{user.level}}</span>
                      <span class="badge bg-secondary rounded-pill" style="font-size:12px" v-if="user.user_level == '0'">{{user.level}}</span>
                    </td>
                    <td>{{user.user_contactnum}}</td>
                    <td><span v-if="user.user_address">{{user.user_address}}, {{user.user_city}} {{user.user_province}}</span><span v-else>-</span></td>
                    <td>
                      <span class="badge rounded-pill bg-primary" v-if="user.user_sex == '1'">{{user.sex}}</span>
                      <span class="badge rounded-pill bg-danger" v-else>{{user.sex}}</span>
                    </td>
                    <td>{{user.user_dob}}</td>
                    <td>
                      <button type="button" class="btn btn-primary btn-sm me-2" title="Edit user"><font-awesome-icon :icon="['fa', 'pen']" /></button>
                      <button type="button" class="btn btn-info btn-sm me-2 text-white" title="View more details"><font-awesome-icon :icon="['fa', 'eye']" /></button>
                      <button type="button" class="btn btn-danger btn-sm me-2" title="Remove user"><font-awesome-icon :icon="['fa', 'trash-can']" /></button>
                    </td>
                  </tr>
                </tbody>
            </table>
        </div>
    </div>
  </div>
</template>


<script>
// @ is an alias to /src
import SettingsConstants from '../constants/settings.constants'
import AppConstants from '../constants/app.constants'
import axios from "axios";
//import store from '../store';
import { mapState } from 'vuex';
import NavComponent from '../components/NavComponent.vue';

export default {
  components: { NavComponent },
  name: 'PatientsPage',
  computed: mapState([
      'sessionData'
  ]),
  data() {
    return {
      allUsers: [],
      allUsersTemp: []
    }
  },
  methods: {
    getAllUsers: function () {
      this.allUsers = [];
      axios.get(SettingsConstants.BASE_URL + 'users.rest.php?type=all', { crossdomain: true })
          .then(function (response) {
              if (response.data) {
                  this.allUsers = response.data;
                  this.allUsers.forEach( function (patient){
                    patient.fullName = patient.user_firstname + ' ' + patient.user_middlename + ' ' + patient.user_lastname + ' ' + patient.user_suffix; 
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
                        patient.sex = AppConstants.USER_SEX.MALE.TITLE;
                        break;
                      case AppConstants.USER_SEX.FEMALE.VALUE.toString():
                        patient.sex = AppConstants.USER_SEX.FEMALE.TITLE;
                        break;
                    }
                  });
                  this.allUsersTemp = this.allUsers;
              }
          }.bind(this));
    },
  },
  mounted() {
    this.getAllUsers();
  },
}
</script>
