<template>
  <nav-component />
  <div class="appointments p-4">
    <div class="card">
      <div class="card-body">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button"
              role="tab" aria-controls="home" aria-selected="true">Drug Inventory List</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="release-tab" data-bs-toggle="tab" data-bs-target="#release" type="button"
              role="tab" aria-controls="release" aria-selected="false">Drug Release Data</button>
          </li>
        </ul>
        <div class="tab-content" id="myTabContent">
          <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div class="overflow-hidden mb-2 mt-3">
              <input type="email" class="form-control w-25 float-start" placeholder="Search Drug" v-model="searchValue">
              <button @click="editOrAddDrugItem" data-bs-toggle="modal" data-bs-target="#editAddDrug" type="button"
                class="btn btn-primary float-end me-2 btn-sm">
                <font-awesome-icon :icon="['fa', 'plus']" /> Add Drug
              </button>
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
                    <button @click="editOrAddDrugItem(drug)" data-bs-toggle="modal" data-bs-target="#editAddDrug"
                      type="button" class="btn btn-primary btn-sm me-2" title="Edit drug">
                      <font-awesome-icon :icon="['fa', 'pen']" />
                    </button>
                    <button @click="onclickDeleteDrug(drug)" type="button" class="btn btn-danger btn-sm me-2"
                      title="Remove drug">
                      <font-awesome-icon :icon="['fa', 'trash-can']" />
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="tab-pane fade" id="release" role="tabpanel" aria-labelledby="release-tab">
            <div class="input-group mb-3 mt-3 w-25 float-end">
              <select class="form-select" aria-label="Default select example" v-model="selectedMonth">
                <option value="0">January</option>
                <option value="1">February</option>
                <option value="2">March</option>
                <option value="3">April</option>
                <option value="4">May</option>
                <option value="5">June</option>
                <option value="6">July</option>
                <option value="7">August</option>
                <option value="8">September</option>
                <option value="9">October</option>
                <option value="10">November</option>
                <option value="11">December</option>
              </select>
              <button type="button" class="btn btn-success" data-bs-dismiss="modal" @click="printRecords()">
              <font-awesome-icon :icon="['fa', 'print']" class="me-2" /> Print</button>
            </div>
            <table class="table table-striped" style="font-size:14px" id="drugReleasePrinting">
              <thead>
                <tr>
                  <th scope="col">Name</th>
                  <th scope="col">Quantity Released</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="rel in drugDataRecords" :key="rel.drugid">
                  <td>{{rel.drugFullname}}</td>
                  <td>{{rel.sum_quantity}}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
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
              <button type="button" @click="performDrugEdit" class="btn btn-primary" data-bs-dismiss="modal">Save
                changes</button>
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
    },
    selectedMonth: function (newVal) {
      this.drugDataRecords = [];
      this.getReleasesForMonth(newVal);
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
      appConstants: null,
      monthList: ["January","February","March","April","May","June","July","August","September","October","November","December"],
      selectedMonth: null,
      drugDataRecords: []
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
    printRecords: function () {
      var m = (parseInt(this.selectedMonth)+1).toString().length == 1 ? '0'+(parseInt(this.selectedMonth)+1).toString(): (parseInt(this.selectedMonth)+1).toString();
      var searchQuery = (new Date()).getFullYear()+'-'+m;
      var divToPrint = document.getElementById("drugReleasePrinting");
      var newWin = window.open('', '', 'height=600,width=1000');
      newWin.document.write('<head><title>Prescription'+searchQuery+'</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"' +
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
    getReleasesForMonth: function (month) {
      var m = (parseInt(month)+1).toString().length == 1 ? '0'+(parseInt(month)+1).toString(): (parseInt(month)+1).toString();
      var searchQuery = (new Date()).getFullYear()+'-'+m;
      axios.get(SettingsConstants.BASE_URL + 'drug.rest.php?type=getallrecordsbymonth&month='+searchQuery, { crossdomain: true })
        .then(function (response) {
            if (response.data) {
              this.drugDataRecords = response.data;
              this.drugDataRecords.forEach(function (record) {
                 axios.get(SettingsConstants.BASE_URL + 'drug.rest.php?type=getdrugbyid&drugid=' + record.drugid, { crossdomain: true })
                 .then(function (response) {
                   var drg = response.data[0];
                   if (drg) {
                    var flavor = (drg.drug_flavor) ? ' ('+drg.drug_flavor+')':'';
                    record.drugFullname = drg.drug_name+flavor;
                   } else {
                    record.drugFullname = 'Item was deleted'
                   }
                 }.bind(this).bind(record));
              }.bind(this));
            }
        }.bind(this));
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
    this.selectedMonth = (new Date()).getMonth();
  },
  
}
</script>
