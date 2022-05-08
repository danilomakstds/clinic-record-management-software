<template>
  <ion-page>
    <ion-content :fullscreen="true">

      <ion-refresher slot="fixed" @ionRefresh="doRefresh($event)">
        <ion-refresher-content></ion-refresher-content>
      </ion-refresher>

      <ion-header collapse="condense">
      </ion-header>
      <div class="p-4">
        <div class="text-center mb-5">
        <img src="../../resources/logo.png" style="height: 200px" class="mb-3"/>
        <h5 style="margin:0 auto">{{sofwareName}}</h5>
        </div>
        <!-- show alert message here -->
        <div class="alert fade show mb-4 text-start" role="alert" :class="messageType" v-if="message" v-html="message"></div>
        <form class="text-start" @submit="checkIfUserExist">
          <div class="mb-3">
            <ion-label class="text-dark">Email address / Phone Number</ion-label>
            <input type="text" class="form-control form-control-lg mt-2" id="exampleFormControlInput1" v-model="userEmail" required>
          </div>
          <div class="mb-3">
            <ion-label class="text-dark">Password</ion-label>
            <input type="password" class="form-control form-control-lg mt-2" id="exampleFormControlInput1" v-model="userPassword" required>
          </div>
          <button type="submit" class="btn btn-primary btn-lg w-100">Login</button>
        </form>
        <div class="text-center mt-2">
          Or<br/>
          <a href="/register" class="text-decoration-none">Create account</a>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script lang="js">
import { defineComponent } from 'vue';
import { IonPage, IonHeader, IonContent, IonLabel, IonRefresher, IonRefresherContent } from '@ionic/vue';
import { chevronDownCircleOutline } from 'ionicons/icons';
import SettingsConstants from '../constants/settings.constants';
import axios from "axios";
import store from '../store';
import { mapState } from 'vuex';

export default  defineComponent({
  name: 'LoginPage',
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
    return { chevronDownCircleOutline, doRefresh }
  },
  data() {
    return {
      userEmail: null,
      userPassword: null,
      showMessage: false,
      message: null,
      messageType: null,
      sofwareName: null
    }
  },
  components: { IonHeader, IonContent, IonPage, IonLabel, IonRefresher, IonRefresherContent },
  methods: {
    checkIfUserExist: function () {
      event.preventDefault();
      var bodyFormData = new FormData();
      bodyFormData.append('user_email', this.userEmail);
      bodyFormData.append('user_password', this.userPassword);
      axios({
          method: "post",
          url: SettingsConstants.BASE_URL + "users.rest.php?type=login",
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
                    window.location.href = '/tabs';
                }
            } else {
                this.showMessage = true;
                this.message = "<strong>Error! </strong> No user found matching email/contact number and password!";
                this.messageType = "alert-danger";
            }
          }.bind(this))
          .catch(function (response) {
              console.log(response);
          });
    }
  },
  mounted() {
    this.sofwareName = SettingsConstants.SOFTWARE_NAME;
  },
});
</script>
