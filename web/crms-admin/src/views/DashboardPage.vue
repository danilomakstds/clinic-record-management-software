<template>
  <nav-component/>
  <div class="dashboard p-4">
    
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
  name: 'MainPage',
  computed: mapState([
      'sessionData'
  ]),
  data() {
    return {
      isAdmin: true
    }
  }
}
</script>

