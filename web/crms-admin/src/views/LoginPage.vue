<template>
  <div class="maindiv mt-5">
    <div class="d-flex justify-content-center">
      <img src="../../resources/icon.png" style="height:170px" class="d-flex justify-content-center"/>
    </div>
    <h5 class="text-center m-4">{{appName}}</h5>
    <br/>
    <form class="text-start" @submit="checkIfUserExist">
    <div class="m-4 bg-white p-4 overflow-hidden">
      <div class="alert fade show mb-4 text-start" role="alert" :class="messageType" v-if="message" v-html="message"></div>
      <div class="mb-3">
        <label for="exampleFormControlInput1" class="form-label">Email address</label>
        <input type="email" class="form-control form-control-lg" id="exampleFormControlInput1" v-model="userEmail">
      </div>
      <div class="mb-3">
        <label for="exampleFormControlInput1" class="form-label">Password</label>
        <input type="password" class="form-control form-control-lg" id="exampleFormControlInput1" v-model="userPassword">
      </div>
      <button type="submit" class="btn btn-primary">Login</button>
    </div>
    </form>
  </div>
</template>

<script>
// @ is an alias to /src
import SettingsConstants from '../constants/settings.constants'
import axios from "axios";
import store from '../store';
import { mapState } from 'vuex';

export default {
  name: 'LoginPage',
  computed: mapState([
      'sessionData'
  ]),
  data() {
    return {
      userEmail: null,
      userPassword: null,
      showMessage: false,
      message: null,
      messageType: null,
      appName: null
    }
  },
  methods: {
    checkIfUserExist: function (event) {
      event.preventDefault();
      var bodyFormData = new FormData();
      bodyFormData.append('user_email', this.userEmail);
      bodyFormData.append('user_password', this.userPassword);
      axios({
          method: "post",
          url: SettingsConstants.BASE_URL + "users.rest.php?type=loginadmin",
          data: bodyFormData,
          headers: { "Content-Type": "multipart/form-data" },
      })
          .then(function (response) {
            if (response.data.length == 1) {
                this.showMessage = true;
                this.message = "<strong>Success! </strong>";
                this.messageType = "alert-success";
                //start session
                store.commit('SET_SESSION_DATA', response.data[0]);
                if (this.sessionData) {
                    window.location.href = '/dashboard';
                }
            } else {
                this.showMessage = true;
                this.message = "<strong>Error! </strong> No ADMIN found matching email and password!";
                this.messageType = "alert-danger";
            }
          }.bind(this))
          .catch(function (response) {
              console.log(response);
          });
    }
  },
  mounted() {
    this.appName = SettingsConstants.SOFTWARE_NAME;
  },
}
</script>

<style scoped>
.maindiv {
  max-width: 500px;
  margin: 0 auto;
}
</style>