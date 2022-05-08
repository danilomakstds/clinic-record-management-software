<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand"><img src="../../resources/icon.png" style="height:50px"/></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item me-3" id="dashboard-item">
            <a class="nav-link" href="/dashboard"><font-awesome-icon :icon="['fa', 'chart-line']" class="me-2" /> Dashboard</a>
          </li>
          <li class="nav-item me-3" id="appointments-item">
            <a class="nav-link" href="/appointments"><font-awesome-icon :icon="['fa', 'calendar']" class="me-2" /> Appointments</a>
          </li>
          <li class="nav-item me-3" id="patients-item">
            <a class="nav-link" href="/patients"><font-awesome-icon :icon="['fa', 'user']" class="me-2" /> Patients & Users</a>
          </li>
          <li class="nav-item me-3" id="drug-item">
            <a class="nav-link" href="/drug"><font-awesome-icon :icon="['fa', 'capsules']" class="me-2" /> Drug Inventory</a>
          </li>
          <li class="nav-item me-3" @click="logOutUser()">
            <a class="nav-link" href="#"><font-awesome-icon :icon="['fa', 'arrow-right-from-bracket']" class="me-2" /> Logout</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
import store from '../store';
import { mapState } from 'vuex';
import $ from "jquery";
import Swal from 'sweetalert2'

export default {
  name: 'NavComponent',
  computed: mapState([
      'sessionData'
  ]),
  methods: {
    logOutUser: function () {
      Swal.fire({
        title: 'Logout?',
        text: "Please confirm logout action!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6C757D',
        confirmButtonText: 'Yes, logout!'
      }).then( function (result) {
        if (result.isConfirmed) {
          store.commit('RESET_SESSION_DATA');
          if (!this.sessionData) {
              location.href = "/";
          }
        }
      }.bind(this));
    },
    addActivetoLink: function () {
      var path = this.$route.path.split('/')[1];
      $('#'+path+'-item a').addClass('active').removeAttr('href');
    }
  },
  mounted() {
    this.addActivetoLink();
  },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
</style>
