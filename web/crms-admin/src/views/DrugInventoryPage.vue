<template>
  <nav-component/>
  <div class="appointments p-4">
    <div class="card">
        <div class="card-body">
            <div class="overflow-hidden mb-2">
              <input type="email" class="form-control w-25 float-start" placeholder="Search Drug">
              <button type="button" class="btn btn-primary float-end me-2 btn-sm"><font-awesome-icon :icon="['fa', 'plus']" /> Add Drug</button>
            </div>
            <table class="table table-striped" style="font-size:14px">
                <thead>
                  <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Info</th>
                    <th scope="col">Flavor</th>
                    <th scope="col">Route / Administration</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Expiration Date</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="drug in allDrugs" :key="drug.id">
                    <td scope="row">
                      <img src="../../resources/drug-icon-mod.jpg" style="height:35px" class="rounded-circle me-2">
                      {{drug.drug_name}}
                    </td>
                    <td>{{drug.drug_dose}}</td>
                    <td>{{drug.drug_flavor}}</td>
                    <td>
                      <span :class="'badge rounded-pill '+drug.route_color">{{drug.route}}</span>
                    </td>
                    <td>{{drug.drug_quantity}}</td>
                    <td>{{drug.drug_expirationdate}}</td>
                    <td>
                      <button type="button" class="btn btn-primary btn-sm me-2" title="Edit drug"><font-awesome-icon :icon="['fa', 'pen']" /></button>
                      <button type="button" class="btn btn-danger btn-sm me-2" title="Remove drug"><font-awesome-icon :icon="['fa', 'trash-can']" /></button>
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
// import store from '../store';
import { mapState } from 'vuex';
import NavComponent from '../components/NavComponent.vue';

export default {
  components: { NavComponent },
  name: 'DrugInventoryPage',
  computed: mapState([
      'sessionData'
  ]),
  data() {
    return {
      allDrugs: [],
      allDrugsTemp: []
    }
  },
  methods: {
    getAllDrugs: function () {
      this.allDrugs = [];
      axios.get(SettingsConstants.BASE_URL + 'drug.rest.php?type=all', { crossdomain: true })
          .then(function (response) {
              if (response.data) {
                  this.allDrugs = response.data;
                  this.allDrugs.forEach( function (drug){
                    drug.route = null;
                    switch (drug.drug_route) {
                      case AppConstants.ROUTE_ADMINISTRATION.ORAL.VALUE.toString():
                        drug.route = AppConstants.ROUTE_ADMINISTRATION.ORAL.TITLE;
                        drug.route_color = 'bg-primary';
                        break;
                      case AppConstants.ROUTE_ADMINISTRATION.TOPICAL.VALUE.toString():
                        drug.route = AppConstants.ROUTE_ADMINISTRATION.TOPICAL.TITLE;
                        drug.route_color = 'bg-success';
                        break;
                      case AppConstants.ROUTE_ADMINISTRATION.INJECTION.VALUE.toString():
                        drug.route = AppConstants.ROUTE_ADMINISTRATION.INJECTION.TITLE;
                        drug.route_color = 'bg-info';
                        break;
                      case AppConstants.ROUTE_ADMINISTRATION.SUBLINGUAL.VALUE.toString():
                        drug.route = AppConstants.ROUTE_ADMINISTRATION.SUBLINGUAL.TITLE;
                        drug.route_color = 'bg-warning';
                        break;
                      case AppConstants.ROUTE_ADMINISTRATION.RECTAL.VALUE.toString():
                        drug.route = AppConstants.ROUTE_ADMINISTRATION.RECTAL.TITLE;
                        drug.route_color = 'bg-danger';
                        break;
                      case AppConstants.ROUTE_ADMINISTRATION.VAGINAL.VALUE.toString():
                        drug.route = AppConstants.ROUTE_ADMINISTRATION.VAGINAL.TITLE;
                        drug.route_color = 'bg-purple';
                        break;
                      case AppConstants.ROUTE_ADMINISTRATION.OCULAR.VALUE.toString():
                        drug.route = AppConstants.ROUTE_ADMINISTRATION.OCULAR.TITLE;
                        drug.route_color = 'bg-pink';
                        break;
                      case AppConstants.ROUTE_ADMINISTRATION.OTIC.VALUE.toString():
                        drug.route = AppConstants.ROUTE_ADMINISTRATION.OTIC.TITLE;
                        drug.route_color = 'bg-indigo';
                        break;
                      case AppConstants.ROUTE_ADMINISTRATION.NEBULIZATION.VALUE.toString():
                        drug.route = AppConstants.ROUTE_ADMINISTRATION.NEBULIZATION.TITLE;
                        drug.route_color = 'bg-secondary';
                        break;
                    }
                  });
                  this.allDrugsTemp = this.allDrugs;
              }
          }.bind(this));
    },
  },
  mounted() {
    this.getAllDrugs();
  },
}
</script>
