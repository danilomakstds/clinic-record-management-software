<template>
  <nav-component/>
  <div class="appointments p-4">
    <div class="card">
        <div class="card-body">
            <table class="table table-striped" style="font-size:14px">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">First</th>
                    <th scope="col">Last</th>
                    <th scope="col">Handle</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th scope="row">1</th>
                    <td>Mark</td>
                    <td>Otto</td>
                    <td>@mdo</td>
                  </tr>
                  <tr>
                    <th scope="row">2</th>
                    <td>Jacob</td>
                    <td>Thornton</td>
                    <td>@fat</td>
                  </tr>
                  <tr>
                    <th scope="row">3</th>
                    <td colspan="2">Larry the Bird</td>
                    <td>@twitter</td>
                  </tr>
                </tbody>
            </table>
        </div>
    </div>
  </div>
</template>


<script>
// @ is an alias to /src
// import SettingsConstants from '../constants/settings.constants'
// import axios from "axios";
//import store from '../store';
import { mapState } from 'vuex';
import NavComponent from '../components/NavComponent.vue';

export default {
  components: { NavComponent },
  name: 'AppointmentsPage',
  computed: mapState([
      'sessionData'
  ]),
  data() {
    return {
      isAdmin: true
    }
  },
}
</script>
