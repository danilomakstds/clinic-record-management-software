<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-title><img src="../../resources/logo.png" style="height:20px" class="me-2" />Drug Inventory</ion-title>
      </ion-toolbar>
    </ion-header>
    <ion-content :fullscreen="true">

      <ion-refresher slot="fixed" @ionRefresh="doRefresh($event)">
        <ion-refresher-content></ion-refresher-content>
      </ion-refresher>

      <ion-toolbar class="pt-2" slot="fixed">
        <ion-searchbar placeholder="Filter Drug" v-model="searchValue"></ion-searchbar>
      </ion-toolbar>

      <div class=" overflow-hidden" style="margin-bottom:90px; margin-top: 65px">
        <ion-item-sliding v-for="drug in allDrugs" :key="drug.id">
          <ion-item-options side="start">
            <ion-item-option @click="storeDrugDetails(drug)" color="secondary" class="pe-2 ps-2" data-bs-toggle="modal"
              data-bs-target="#drugViewModal">
              <ion-icon :icon="eyeOutline" />
            </ion-item-option>
            <ion-item-option class="pe-2 ps-2" @click="toggleAddDrugModal(drug)">
              <ion-icon :icon="createOutline" />
            </ion-item-option>
            <!-- <ion-item-option @click="onclickDeleteDrug(drug)" color="danger" class="pe-2 ps-2" v-if="sessionData.user_level == 3">
                <ion-icon :icon="trashOutline"/>
              </ion-item-option> -->
          </ion-item-options>

          <ion-item>
            <ion-avatar slot="start">
              <img src="../../resources/drug-icon-mod.jpg">
            </ion-avatar>
            <ion-label>{{ drug.fullName }} <span class="badge rounded-pill bg-secondary"
                v-if="drug.drug_flavor !== 'null'">{{ drug.drug_flavor }}</span></ion-label>
            <ion-badge slot="end" :color="drug.drug_quantity < 10 ? 'danger' : 'success'">{{ drug.drug_quantity }}
            </ion-badge>
          </ion-item>

          <ion-item-options side="end">
            <ion-item-option class="pe-2 ps-2" @click="onAddClick(drug)">
              <ion-icon :icon="addOutline" />
            </ion-item-option>
            <ion-item-option class="pe-2 ps-2" @click="onSubtractClick(drug)">
              <ion-icon :icon="removeOutline" />
            </ion-item-option>
          </ion-item-options>
        </ion-item-sliding>
      </div>

      <!-- Sheet Modal -->
      <ion-modal :is-open="isAddDrugModalOpen" :breakpoints="[0.1, 0.7, 0.85]" :initialBreakpoint="0.7"
        @didDismiss="toggleAddDrugModal()">
        <ion-content>
          <div class="p-4">
            <form @submit="addNewDrug">
              <label class="form-label mb-0" style="font-size:12px">Add Drug</label>
              <hr class="mt-2 mb-2" />
              <table class="w-100">
                <tr>
                  <td colspan="2" class="p-2">
                    <div class="mb-2">
                      <label for="drugname" class="form-label">Drug Name</label>
                      <input type="drugname" class="form-control" id="drugname" v-model="newDrugName" required>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td class="p-2">
                    <div class="mb-2">
                      <label for="type" class="form-label">Weight / Volume Info</label>
                      <input type="text" class="form-control" id="type" v-model="newDrugInfo" required>
                      <div class="form-text">mg / ml / cc</div>
                    </div>
                  </td>
                  <td class="p-2">
                    <div class="mb-2">
                      <label for="flavor" class="form-label">Flavor</label>
                      <input type="text" class="form-control" id="flavor" v-model="newDrugFlavor">
                      <div class="form-text">Honey, Apple etc.</div>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td colspan="2" class="p-2">
                    <div class="mb-2">
                      <label class="form-label">Route / Administration</label>
                      <select class="form-select" v-model="newDrugRoute" required>
                        <option :value="appConstants.ORAL.VALUE">{{ appConstants.ORAL.TITLE }}</option>
                        <option :value="appConstants.TOPICAL.VALUE">{{ appConstants.TOPICAL.TITLE }}</option>
                        <option :value="appConstants.INJECTION.VALUE">{{ appConstants.INJECTION.TITLE }}</option>
                        <option :value="appConstants.SUBLINGUAL.VALUE">{{ appConstants.SUBLINGUAL.TITLE }}</option>
                        <option :value="appConstants.RECTAL.VALUE">{{ appConstants.RECTAL.TITLE }}</option>
                        <option :value="appConstants.VAGINAL.VALUE">{{ appConstants.VAGINAL.TITLE }}</option>
                        <option :value="appConstants.OCULAR.VALUE">{{ appConstants.OCULAR.TITLE }}</option>
                        <option :value="appConstants.OTIC.VALUE">{{ appConstants.OTIC.TITLE }}</option>
                        <option :value="appConstants.NEBULIZATION.VALUE">{{ appConstants.NEBULIZATION.TITLE }}</option>
                      </select>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td class="p-2">
                    <div class="mb-2">
                      <label for="expiration" class="form-label">Expiration Date</label>
                      <input type="date" class="form-control" id="expiration" v-model="newDrugExpirationDate" required>
                    </div>
                  </td>
                  <td class="p-2">
                    <div class="mb-2">
                      <label for="stock" class="form-label">In Stock</label>
                      <input type="number" class="form-control" id="stock" v-model="newDrugStock" required>
                    </div>
                  </td>
                </tr>
              </table>
              <button type="submit" class="btn btn-primary btn-md w-100 mb-2 btn-lg" v-if="!isEdit">
                <ion-icon :icon="add" size="small" class="me-1" />Add New Drug
              </button>
              <button type="button" class="btn btn-primary btn-md w-100 mb-2 btn-lg" @click="performDrugEdit()" v-else>
                <ion-icon :icon="createOutline" size="small" class="me-1" />Edit Drug
              </button>
            </form>
          </div>
        </ion-content>
      </ion-modal>

      <ion-fab vertical="bottom" horizontal="end" slot="fixed">
        <ion-fab-button @click="toggleAddDrugModal()">
          <ion-icon :icon="addOutline"></ion-icon>
        </ion-fab-button>
      </ion-fab>

    </ion-content>
  </ion-page>
</template>

<script lang="js">
import { defineComponent } from 'vue';
import { IonPage,
IonHeader, IonToolbar, IonTitle,
  IonContent, IonSearchbar, IonBadge,
  IonModal, IonItem, IonAvatar, IonItemOption,
  IonItemOptions, IonItemSliding, IonRefresher, IonRefresherContent,
  modalController, IonFab, IonFabButton,
} from '@ionic/vue';
import { addOutline, chevronDownCircleOutline, trashOutline, removeOutline, createOutline, eyeOutline, medkitOutline } from 'ionicons/icons';
import Swal from 'sweetalert2';
import { add, addCircle } from 'ionicons/icons';
import axios from "axios";
import store from '../store';
import SettingsConstants from '../constants/settings.constants'
import AppConstants from '../constants/app.constants'
import { mapState } from 'vuex'

export default defineComponent({
  name: 'DrugInventory',
  computed: mapState([
    'sessionData'
  ]),
  data() {
    return {
      searchValue: null,
      allDrugs: [],
      allDrugsTemp: [],
      isAddDrugModalOpen: false,

      // for add new drug
      newDrugStock: 0,
      newDrugName: null,
      newDrugInfo: null,
      newDrugFlavor: null,
      newDrugRoute: 1,
      newDrugExpirationDate: null,
      appConstants: null,
      isEdit: false,
      drugIdEdit: null,

      drugQuantityBefore: null,
      isQuantityLess: false
    }
  },
  watch: {
    searchValue: function (newVal) {
      this.allDrugs = this.allDrugsTemp;
      this.applySearch(newVal);
    },
    newDrugStock: function (newVal) {
      if (newVal < this.drugQuantityBefore && newVal) {
        this.isQuantityLess = true;
      } else {
        this.isQuantityLess = false;
      }
    }
  },
  setup() {
    const doRefresh = function (event) {
      location.reload(true);
      setTimeout(function () {
        event.target.complete();
      }, 2000);
    }
    return {
      eyeOutline, createOutline, removeOutline, trashOutline, add, addCircle, chevronDownCircleOutline, addOutline, medkitOutline, doRefresh
    }
  },
  components: {
    IonHeader, IonToolbar, IonTitle,
    IonContent, IonPage, IonSearchbar,
    IonBadge, IonModal, IonItem, IonAvatar,
    IonItemOption, IonItemOptions, IonItemSliding,
    IonRefresher, IonRefresherContent,
    IonFab, IonFabButton,
  },
  methods: {
    async dismiss() {
      modalController.dismiss({
        'dismissed': true
      });
    },
    getAllDrugs: function () {
      this.allDrugs = [];
      axios.get(SettingsConstants.BASE_URL + 'drug.rest.php?type=all', { crossdomain: true })
        .then(function (response) {
          if (response.data) {
            this.allDrugs = response.data;
            this.allDrugs.forEach(function (drug) {
              drug.fullName = drug.drug_name + ' (' + drug.drug_dose + ')';
            });
            this.allDrugsTemp = this.allDrugs;
          }
        }.bind(this));
    },
    applySearch: function (newVal) {
      if (newVal) {
        this.allDrugs = this.allDrugs.filter(drug => drug.drug_name.toLowerCase().startsWith(newVal.toLowerCase()));
      } else {
        this.allDrugs = this.allDrugsTemp;
      }
    },
    toggleAddDrugModal: function (isEdit) {
      this.isAddDrugModalOpen = !this.isAddDrugModalOpen;
      this.resetFields();
      if (isEdit) {
        this.isEdit = true;
        this.editDrugItem(isEdit);
        this.drugIdEdit = isEdit.id;
      }
    },
    editDrugItem: function (drugItem) {
      this.newDrugStock = drugItem.drug_quantity;
      this.newDrugName = drugItem.drug_name;
      this.newDrugInfo = drugItem.drug_dose;
      this.newDrugFlavor = drugItem.drug_flavor;
      this.newDrugRoute = drugItem.drug_route;
      this.newDrugExpirationDate = drugItem.drug_expirationdate;

      this.drugQuantityBefore = drugItem.drug_quantity;
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
        url: SettingsConstants.BASE_URL + "drug.rest.php?type=editdrug&drugid=" + this.drugIdEdit,
        data: bodyFormData,
        headers: { "Content-Type": "multipart/form-data" },
      })
        .then(function (response) {
          if (response.data == 1) {
            if (this.isQuantityLess) {
              this.recordQuantityChange(this.drugIdEdit, (parseInt(this.drugQuantityBefore) - this.newDrugStock));
            }
            Swal.fire(
              'Success!',
              this.newDrugName + ' has been edited!',
              'success'
            );
            setTimeout(function () {
              this.isEdit = false;
              this.dismiss();
              this.resetFields();
              this.drugIdEdit = null;
              this.getAllDrugs();
            }.bind(this), 500);
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
            );
            setTimeout(function () {
              this.dismiss();
              this.resetFields();
              this.getAllDrugs();
            }.bind(this), 500);
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
    storeDrugDetails: function (details) {
      store.commit('SET_DRUGVIEW_DETAILS', details);
    },
    onAddClick: function (drug) {
      var newval = (parseInt(drug.drug_quantity) + 1).toString();
      drug.drug_quantity = newval;
      axios.get(SettingsConstants.BASE_URL + 'drug.rest.php?type=updatedrugquantity&drugid=' + drug.id + '&drug_quantity=' + newval, { crossdomain: true })
        .then(function (response) {
          if (response.data) {
            console.log(response.data);
          }
        }.bind(this));
    },
    onSubtractClick: function (drug) {
      if (drug.drug_quantity != 0) {
        var newval = (parseInt(drug.drug_quantity) - 1).toString();
        drug.drug_quantity = newval;
        axios.get(SettingsConstants.BASE_URL + 'drug.rest.php?type=updatedrugquantity&drugid=' + drug.id + '&drug_quantity=' + newval, { crossdomain: true })
          .then(function (response) {
            if (response.data) {
              console.log(response.data);
            }
          }.bind(this));
        this.recordQuantityChange(drug.id, 1);
      } else {
        Swal.fire(
          'Not allowed!',
          'Stock quantity already 0!',
          'warning'
        )
      }
    },
    recordQuantityChange: function (drugid, val) {
      axios.get(SettingsConstants.BASE_URL + 'drug.rest.php?type=recordstock&drugid=' + drugid + '&quantity=' + val, { crossdomain: true })
        .then(function (response) {
            if (response.data) {
              console.log(response.data);
            }
        }.bind(this));
    },
    onclickDeleteDrug: function (drug) {
        Swal.fire({
        title: 'Remove' + drug.fullName + ' ?',
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
    }
  },
  mounted() {
    this.getAllDrugs();
    this.appConstants = AppConstants.ROUTE_ADMINISTRATION;
  },
});
</script>
