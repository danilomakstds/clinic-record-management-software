<template>
  <nav-component/>
  <div class="appointments p-4">
    <div class="card">
        <div class="card-body">
            <div class="overflow-hidden mb-2">
              <input type="email" class="form-control w-25 float-start" placeholder="Search Drug" v-model="searchValue">
              <button @click="editOrAddDrugItem" data-bs-toggle="modal" data-bs-target="#editAddDrug" type="button" class="btn btn-primary float-end me-2 btn-sm"><font-awesome-icon :icon="['fa', 'plus']" /> Add Drug</button>
            </div>
            <table class="table table-striped" style="font-size:14px">
                <thead>
                  <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Drug ID</th>
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
                    <td>{{drug.id}}</td>
                    <td>{{drug.drug_dose}}</td>
                    <td>{{drug.drug_flavor}}</td>
                    <td>
                      <span :class="'badge rounded-pill '+drug.route_color">{{drug.route}}</span>
                    </td>
                    <td>{{drug.drug_quantity}}</td>
                    <td>{{drug.drug_expirationdate}}</td>
                    <td>
                      <button @click="editOrAddDrugItem(drug)" data-bs-toggle="modal" data-bs-target="#editAddDrug" type="button" class="btn btn-primary btn-sm me-2" title="Edit drug"><font-awesome-icon :icon="['fa', 'pen']" /></button>
                      <!-- <button @click="onclickDeleteDrug(drug)" type="button" class="btn btn-danger btn-sm me-2" title="Remove drug"><font-awesome-icon :icon="['fa', 'trash-can']" /></button> -->
                    </td>
                  </tr>
                </tbody>
            </table>
        </div>
    </div>
  </div>


  <div class="modal fade" id="editAddDrug">
    <div class="modal-dialog">
      <div class="modal-content" v-if="selectedDrugItem">
        <div class="modal-header">
          <h5 class="modal-title">
            <span v-if="selectedDrugItem.id">
              Edit Drug #{{selectedDrugItem.id}}
            </span>
            <span v-else>
              Add Drug
            </span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form @submit="addNewDrug()">
        <div class="modal-body">
          <ul class="list-group list-group-flush">
            <li class="list-group-item">
              <div class="mb-3">
                <label style="font-size: 14px" class="form-label fw-bold">Drug Name</label>
                <input type="text" class="form-control" v-model="newDrugName" required>
              </div>
              <div class="mb-3">
                <label style="font-size: 14px" class="form-label fw-bold">Info</label>
                <input type="text" class="form-control" v-model="newDrugInfo" required>
              </div>
              <div class="mb-3">
                <label style="font-size: 14px" class="form-label fw-bold">Flavor</label>
                <input type="text" class="form-control" v-model="newDrugFlavor">
              </div>
              <div class="mb-3">
                <label style="font-size: 14px" class="form-label fw-bold">Route / Administration</label>
                <select class="form-select" v-model="newDrugRoute" required>
                  <option :value="appConstants.ORAL.VALUE">{{appConstants.ORAL.TITLE}}</option>
                  <option :value="appConstants.TOPICAL.VALUE">{{appConstants.TOPICAL.TITLE}}</option>
                  <option :value="appConstants.INJECTION.VALUE">{{appConstants.INJECTION.TITLE}}</option>
                  <option :value="appConstants.SUBLINGUAL.VALUE">{{appConstants.SUBLINGUAL.TITLE}}</option>
                  <option :value="appConstants.RECTAL.VALUE">{{appConstants.RECTAL.TITLE}}</option>
                  <option :value="appConstants.VAGINAL.VALUE">{{appConstants.VAGINAL.TITLE}}</option>
                  <option :value="appConstants.OCULAR.VALUE">{{appConstants.OCULAR.TITLE}}</option>
                  <option :value="appConstants.OTIC.VALUE">{{appConstants.OTIC.TITLE}}</option>
                  <option :value="appConstants.NEBULIZATION.VALUE">{{appConstants.NEBULIZATION.TITLE}}</option>
                </select>
              </div>
              <div class="mb-3">
                <label style="font-size: 14px" class="form-label fw-bold">Quantity / Remaining Stock</label>
                <input type="number" class="form-control" v-model="newDrugStock">
              </div>
              <div class="mb-3">
                <label style="font-size: 14px" class="form-label fw-bold">Expiration Date</label>
                <input type="date" class="form-control" v-model="newDrugExpirationDate" required>
              </div>
            </li>
          </ul>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <span v-if="selectedDrugItem.id">
              <button type="button"  @click="performDrugEdit" class="btn btn-primary" data-bs-dismiss="modal">Save changes</button>
          </span>
          <span v-else>
              <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Save changes</button>
          </span>
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
import axios from "axios";
// import store from '../store';
import { mapState } from 'vuex';
import NavComponent from '../components/NavComponent.vue';
import Swal from 'sweetalert2'

export default {
  components: { NavComponent },
  name: 'DrugInventoryPage',
  computed: mapState([
      'sessionData'
  ]),
  watch: {
    searchValue: function (newVal) {
      this.allDrugs = this.allDrugsTemp;
      this.applySearch(newVal);
    }
  },
  data() {
    return {
      allDrugs: [],
      allDrugsTemp: [],
      searchValue: null,

            // for add new drug
      newDrugStock: 0,
      newDrugName: null,
      newDrugInfo: null,
      newDrugFlavor: null,
      newDrugRoute: 1,
      newDrugExpirationDate: null,
      selectedDrugItem: null,
      appConstants: null
    }
  },
  methods: {
    applySearch: function (newVal) {
      if (newVal) {
        this.allDrugs = this.allDrugs.filter(drug => drug.drug_name.toLowerCase().startsWith(newVal.toLowerCase()));
      } else {
        this.allDrugs = this.allDrugsTemp;
      }
    },
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
    onclickDeleteDrug: function (drug) {
        Swal.fire({
        title: 'Remove ' + drug.drug_name + ' ?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6C757D',
        confirmButtonText: 'Yes, remove it!'
      }).then((result) => {
        if (result.isConfirmed) {
          axios.get(SettingsConstants.BASE_URL + 'drug.rest.php?type=deletedrug&drugid=' + drug.id, { crossdomain: true })
            .then(function (response) {
              if (response.data) {
                Swal.fire(
                  'Removed!',
                  'Drug has been removed.',
                  'success'
                ).then(function (){
                  this.getAllDrugs();
                }.bind(this));
              }
            }.bind(this));
        }
      })
    },
    editOrAddDrugItem: function (drugItem) {
      if (drugItem) {
        this.selectedDrugItem = drugItem;
        this.newDrugStock = drugItem.drug_quantity;
        this.newDrugName = drugItem.drug_name;
        this.newDrugInfo = drugItem.drug_dose;
        this.newDrugFlavor = drugItem.drug_flavor;
        this.newDrugRoute = drugItem.drug_route;
        this.newDrugExpirationDate = drugItem.drug_expirationdate;
      } else {
        this.selectedDrugItem = null;
        this.newDrugStock = 0;
        this.newDrugName = null;
        this.newDrugInfo = null;
        this.newDrugFlavor = null;
        this.newDrugRoute = 1;
        this.newDrugExpirationDate = null;
      }
    },
    performDrugEdit: function () {
      var bodyFormData = new FormData();
      bodyFormData.append('drug_name', this.newDrugName);
      bodyFormData.append('drug_dose', this.newDrugInfo);
      bodyFormData.append('drug_flavor', this.newDrugFlavor);
      bodyFormData.append('drug_route', this.newDrugRoute);
      bodyFormData.append('drug_quantity', this.newDrugStock);
      bodyFormData.append('drug_expirationdate', this.newDrugExpirationDate);
      axios({
          method: "post",
          url: SettingsConstants.BASE_URL + "drug.rest.php?type=editdrug&drugid=" + this.selectedDrugItem.id,
          data: bodyFormData,
          headers: { "Content-Type": "multipart/form-data" },
      })
          .then(function (response) {
            if (response.data == 1) {
                Swal.fire(
                  'Success!',
                  this.newDrugName + ' has been edited!',
                  'success'
                ).then(function (){
                  this.resetFields();
                  this.selectedDrugItem = null;
                  this.getAllDrugs();
                }.bind(this));
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
    addNewDrug: function () {
      event.preventDefault();
      var bodyFormData = new FormData();
      bodyFormData.append('drug_name', this.newDrugName);
      bodyFormData.append('drug_dose', this.newDrugInfo);
      bodyFormData.append('drug_flavor', this.newDrugFlavor);
      bodyFormData.append('drug_route', this.newDrugRoute);
      bodyFormData.append('drug_quantity', this.newDrugStock);
      bodyFormData.append('drug_expirationdate', this.newDrugExpirationDate);

      axios({
          method: "post",
          url: SettingsConstants.BASE_URL + "drug.rest.php?type=adddrug",
          data: bodyFormData,
          headers: { "Content-Type": "multipart/form-data" },
      })
          .then(function (response) {
            if (response.data == 1) {
                Swal.fire(
                  'Success!',
                  this.newDrugName + ' has been added!',
                  'success'
                ).then(function (){
                  this.resetFields();
                  this.selectedDrugItem = null;
                  this.getAllDrugs();
                }.bind(this));
            } else {
                Swal.fire(
                  'Error!',
                  'Something went wrong when creating drug item!',
                  'error'
                );
            }
          }.bind(this))
          .catch(function (response) {
              console.log(response);
          });
    },
    resetFields: function () {
      this.newDrugStock = 0;
      this.newDrugName = null;
      this.newDrugInfo = null;
      this.newDrugFlavor = null;
      this.newDrugRoute = 1;
      this.newDrugExpirationDate = null;
    },
  },
  mounted() {
    this.getAllDrugs();
    this.appConstants = AppConstants.ROUTE_ADMINISTRATION;
  },
  
}
</script>
