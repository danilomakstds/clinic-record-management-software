<template>
  <nav-component/>
  <div class="dashboard p-4">
    <div class="row">
      <div class="col-md-6 mb-4">
        <div class="card">
          <div class="card-header">
            Age Distribution of Patients
          </div>
          <div class="card-body" style="min-height:318px">
            <vc-donut
              background="white" foreground="grey"
              :size="200" unit="px" :thickness="30"
              has-legend legend-placement="top"
              :sections="sectionsAge" :total="100"
              :start-angle="0" :auto-adjust-text-size="true"
              @section-click="handleSectionClick">
            </vc-donut>
          </div>
        </div>
      </div>
      <div class="col-md-6 mb-4">
        <div class="card">
          <div class="card-header">
            Appointment Agenda Distribution
          </div>
          <div class="card-body">
            <vc-donut
              background="white" foreground="grey"
              :size="200" unit="px" :thickness="30"
              has-legend legend-placement="top"
              :sections="sectionAppointment" :total="100"
              :start-angle="0" :auto-adjust-text-size="true"
              @section-click="handleSectionClick">
            </vc-donut>
          </div>
        </div>
      </div>
      <div class="col-md-12 mb-4">
        <div class="card">
          <div class="card-header">
            Drug releases (Past 30 days)
          </div>
          <div class="card-body" style="min-height:418px">
            <Bar
              :chart-options="chartOptions"
              :chart-data="chartData"
              :chart-id="chartId"
              :dataset-id-key="datasetIdKey"
              :plugins="plugins"
              :css-classes="cssClasses"
              :styles="styles"
              :width="width"
              :height="height"
            />
          </div>
        </div>
      </div>
    </div>
  
    
  </div>
</template>


<script>
// @ is an alias to /src
import SettingsConstants from '../constants/settings.constants'
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'
import axios from "axios";
import moment from 'moment';
//import store from '../store';
import { mapState } from 'vuex';
import NavComponent from '../components/NavComponent.vue';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

export default {
  components: { NavComponent, Bar },
  name: 'MainPage',
  props: {
    chartId: {
      type: String,
      default: 'bar-chart'
    },
    datasetIdKey: {
      type: String,
      default: 'label'
    },
    width: {
      type: Number,
      default: 100
    },
    height: {
      type: Number,
      default: 20
    },
    cssClasses: {
      default: '',
      type: String
    },
    styles: {
      type: Object,
      default: () => {}
    },
    plugins: {
      type: Object,
      default: () => {}
    }
  },
  computed: mapState([
      'sessionData'
  ]),
  data() {
    return {
      lessThan1: 0,
      zero20: 0,
      twenty40: 0,
      forty60: 0,
      sixty80: 0,
      eighty100: 0,
      sectionsAge: [
          { label: '10 and below', value: 0, color: '#6f42c1', minval: 0, maxval: 10, usercount: 0 },
          { label: '11 to 20', value: 0, color: '#0d6efd', minval: 11, maxval: 20, usercount: 0 },
          { label: '21 to 40', value: 0, color: '#d63384', minval: 21, maxval: 40, usercount: 0 },
          { label: '41 to 60', value: 0, color: '#fd7e14', minval: 41, maxval: 60, usercount: 0 },
          { label: '61 to 100', value: 0, color: '#198754', minval: 61, maxval: 100, usercount: 0 }
        ],
      totalAgeCount: 0,
      sectionAppointment: [
        { label: 'Prenatal Care', value: 0, color: '#198754', apptype: 1, appcount: 0 },
        { label: 'Checkup / Consultation', value: 0, color: '#fd7e14', apptype: 2, appcount: 0 },
        { label: 'API Immunization', value: 0, color: '#d63384', apptype: 3, appcount: 0 },
        { label: 'Family Planning', value: 0, color: '#0d6efd', apptype: 4, appcount: 0 }
        ],
      totalAppointmentCount: 0,
      drugDataRecords: [],
      chartData: {
        labels: [],
        datasets: [
          {
            label: '',
            data: [],
            backgroundColor: ['#198754','#fd7e14','#d63384', '#0d6efd', '#6f42c1', '#0DCAF0', '#DC3545', '#FFF3CD', '#A3CFBB', '#A6E9D5', '#ccc', '#E9ECEF', '#CFF4FC', '#842029', '#801F4F']
          }
        ]
      },
      chartOptions: {
        responsive: true
      }
    }
  },
  methods: {
    getAllUsers: function () {
      var url = 'users.rest.php?type=all';
      //getallpatients
      axios.get(SettingsConstants.BASE_URL + url , { crossdomain: true })
          .then(function (response) {
              if (response.data) {
                  this.totalAgeCount = 0;
                  response.data.forEach( function (patient, idx, array){
                    if (patient.user_dob) {
                      this.totalAgeCount = this.totalAgeCount + 1;
                      if ((moment(patient.user_dob, 'YYYYMMDD').fromNow()).includes('years')) {
                        patient.age = (moment(patient.user_dob, 'YYYYMMDD').fromNow()).split(" ")[0];
                      } else {
                        patient.age = 0;
                      }
                      this.sectionsAge.forEach( function (secage) {
                        if (parseInt(patient.age) <= secage.maxval && parseInt(patient.age) >= secage.minval) {
                          secage.usercount = secage.usercount + 1;
                        }
                      }.bind(patient));
                      if (idx === array.length - 1){ 
                        this.sectionsAge.forEach( function (secage) {
                          secage.value = (secage.usercount / this.totalAgeCount) * 100;
                        }.bind(this));
                      }
                    } else {
                      patient.age = "-";
                    }
                  }.bind(this));
              }
              
          }.bind(this));
    },
    getAllAppointments: function () {
      var url = 'appointment.rest.php?type=everything';
      axios.get(SettingsConstants.BASE_URL + url , { crossdomain: true })
          .then(function (response) {
              if (response.data) {
                  this.totalAppointmentCount = response.data.length;
                  response.data.forEach( function (app, idx, array){
                    if (app.app_apptype) {
                      this.sectionAppointment.forEach( function (secapp){
                        if (secapp.apptype.toString() == app.app_apptype.toString()){
                          secapp.appcount = secapp.appcount + 1;
                        }
                      }.bind(app));
                    }
                    if (idx === array.length - 1){
                      this.sectionAppointment.forEach( function (secapp){
                        secapp.value = (secapp.appcount / this.totalAppointmentCount) * 100;
                      }.bind(this));
                      console.log(this.sectionAppointment);
                    }
                  }.bind(this));
              }
              
          }.bind(this));
    },
    getallDrugInventoryRecords: function () {
      axios.get(SettingsConstants.BASE_URL + 'drug.rest.php?type=getallrecords', { crossdomain: true })
        .then(function (response) {
            if (response.data) {
              var dataset = [];
              var nameset = [];
              var counter = 0;
              this.drugDataRecords = response.data;
              this.drugDataRecords.forEach(function (record) {
                 axios.get(SettingsConstants.BASE_URL + 'drug.rest.php?type=getdrugbyid&drugid=' + record.drugid, { crossdomain: true })
                 .then(function (response) {
                   counter = counter + 1;
                   if (response.data.length == 1) {
                    record.drugname = response.data[0].drug_name + ' '+ response.data[0].drug_flavor;
                    //console.log(record.drugname + ' - ' + record.sum_quantity);
                    nameset.push(record.drugname);
                    dataset.push(record.sum_quantity);
                   }
                   if (counter == this.drugDataRecords.length) {
                     this.chartData.labels = nameset;
                     this.chartData.datasets[0].data = dataset;
                   }
                 }.bind(this).bind(record).bind(dataset).bind(nameset));
              }.bind(this));
            }
        }.bind(this));
    }
  },
  mounted() {
    this.getAllUsers();
    this.getAllAppointments();
    this.getallDrugInventoryRecords();
  },
}
</script>