<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-title><img src="../../resources/logo.png" style="height:20px" class="me-2" />Appointments</ion-title>
      </ion-toolbar>
    </ion-header>
    <ion-content :fullscreen="true">

      <ion-refresher slot="fixed" @ionRefresh="doRefresh($event)">
        <ion-refresher-content></ion-refresher-content>
      </ion-refresher>

      <div class="p-2 mt-2">
        <div class="d-flex justify-content-around">
          <div class="animated animatedFadeInUp fadeInUp">
            <span class="text-muted">Good {{ welcomMessage }},</span>
            <h3 class="mt-0"><span v-if="sessionData.age < 3">Baby</span> {{ sessionData.user_lastname }},
              {{ sessionData.user_firstname }}
              {{ sessionData.user_middlename }}</h3>
          </div>
          <div id="userImage" class="animated2 animatedFadeInUp fadeInUp">
            <img src="../../resources/avatar.svg" style="height:50px; border-radius: 10px" />
          </div>
        </div>


        <ion-card class="animated2 animatedFadeInUp fadeInUp" v-for="app in allAppointments" :key="app.id">
          <ion-item @click="toggleAddDiagnosisModal(app)" :disabled="app.disabledPrescription">
            <ion-icon :icon="calendarOutline" slot="start"></ion-icon>
            <ion-label>{{ app.agendaTitle }}</ion-label>
            <ion-icon :icon="arrowForwardOutline" slot="end"></ion-icon>
          </ion-item>

          <ion-card-content>
            <div v-if="sessionData.user_level > 0">
              <ion-icon :icon="personCircleOutline" slot="start" class="me-2"></ion-icon>{{ app.fullName }}
            </div>

            <ion-icon :icon="calendarOutline" slot="start" class="me-2"></ion-icon>{{ app.convertedDate }}<br />
            <ion-icon :icon="timeOutline" slot="start" class="me-2"></ion-icon>{{ app.timeSlot }}<br />
            <span class="badge rounded-pill bg-secondary mt-4" style="font-size: 13px"
              v-if="app.app_status == 0">Created</span>
            <span class="badge rounded-pill bg-primary mt-4" style="font-size: 13px" v-if="app.app_status == 1">Doctor's
              Queue</span>
            <span class="badge rounded-pill bg-success mt-4" style="font-size: 13px"
              v-if="app.app_status == 2">Done</span>
            <span v-if="sessionData.user_level == 3">
              <button type="button" class="btn btn-danger float-end" @click="removeAppointment(app)">
                <ion-icon :icon="trashOutline"></ion-icon>
              </button>
              <button type="button" class="btn btn-info float-end me-2 text-white" :disabled="app.app_status == 0"
                @click="toggleShowMoreModal(app)">
                <ion-icon :icon="eyeOutline"></ion-icon>
              </button>
              <button type="button" class="btn btn-primary float-end me-2 text-white" :disabled="app.app_status != 0"
                @click="toggleEditAppointmentModal(app)">
                <ion-icon :icon="createOutline"></ion-icon>
              </button>
            </span>
          </ion-card-content>
        </ion-card>

        <div class="d-flex align-items-center mt-5"
          v-if="allAppointments.length == 0 && isLoadingItems == false && sessionData.user_level > 0">
          <div class="text-center">
            <img src="../../resources/slide-3.png" />
            <span style="font-size:15px" v-if="sessionData.user_level > 0">There are no appointments <span
                v-if="!isAppointmentListToday">to show!</span><span v-else>for today!</span></span>
          </div>
        </div>
        <div class="alert alert-info m-3 animated5 animatedFadeInUp fadeInUp" role="alert"
          v-if="allAppointments.length == 0 && isLoadingItems == false && sessionData.user_level == 0">
          <span style="font-size:15px">You have no upcoming appointments</span>
        </div>

        <ion-card class="animated4 animatedFadeInUp fadeInUp" v-if="sessionData.user_level == 0">
          <div style="width: 100%; overflow: hidden; height: 250px;">
            <div style="z-index:1000; position: absolute; border-radius: 10px; width:173px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class="bg-white p-2 m-3">
              Simborio Barangay Health Center <br>
              <a class="text-decoration-none" style="font-size:11px"
                href="https://www.google.com/maps/place/Simborio+Barangay+Health+Center/@9.475841,123.108306,15z/data=!4m5!3m4!1s0x0:0x4feef823daabd5a8!8m2!3d9.4758415!4d123.1083085?hl=en-US">
                View larger map
              </a>
            </div>
            <iframe width="100%" height="550" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"
              style="border:0; margin-top: -150px;"
              src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15741.510702807593!2d123.1083068!3d9.475841!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33ab7e6eb50f5a2f%3A0x4feef823daabd5a8!2sSimborio%20Barangay%20Health%20Center!5e0!3m2!1sen!2sph!4v1648729640632!5m2!1sen!2sph"></iframe>
          </div>
          <ion-card-header>
            <ion-card-subtitle>Location</ion-card-subtitle>
            <ion-card-title>Simborio Barangay Health Center</ion-card-title>
          </ion-card-header>
          <ion-card-content>
            Government office in Pamplona,
            <strong>Address</strong> F4G5+88M, Bayan ng Pamplona, Lalawigan ng Negros Oriental
            <div class="mt-3">
              <a href="tel:123-456-7890">
                <button type="button" class="btn btn-outline-success">
                  <ion-icon :icon="callOutline"></ion-icon>
                  123-456-7890
                </button>
              </a>
            </div>
          </ion-card-content>
        </ion-card>

        <ion-modal :is-open="isAppointmentModalOpen" @didDismiss="toggleAppointmentModal()" :tabindex="none">
          <ion-content>
            <ion-datetime presentation="date" @ionChange="checkForEnabledSlots()" v-model="selectedDate">
              <div slot="title">
                <div class="d-flex flex-row mb-3" @click="dismiss()">
                  <ion-icon :icon="chevronBackOutline" slot="start" size="large" />
                  <ion-title slot="start">Back</ion-title>
                </div>
              </div>
            </ion-datetime>

            <div class="alert alert-info" role="alert" style="border-radius:0px">
              <ion-icon :icon="informationCircleOutline" class="me-3 float-start" size="large"></ion-icon>
              Doctor's Schedule is
              <span v-for="(work, index) in workdays" :key="`work-${index}`">
                <span v-if="index == workdays.length - 1">and </span>
                <strong v-if="work == daysConstant.SUNDAY.VALUE">{{ daysConstant.SUNDAY.TITLE }}s<span
                    v-if="index != workdays.length - 1">, </span> </strong>
                <strong v-if="work == daysConstant.MONDAY.VALUE">{{ daysConstant.MONDAY.TITLE }}s<span
                    v-if="index != workdays.length - 1">, </span> </strong>
                <strong v-if="work == daysConstant.TUESDAY.VALUE">{{ daysConstant.TUESDAY.TITLE }}s<span
                    v-if="index != workdays.length - 1">, </span> </strong>
                <strong v-if="work == daysConstant.WEDNESDAY.VALUE">{{ daysConstant.WEDNESDAY.TITLE }}s<span
                    v-if="index != workdays.length - 1">, </span> </strong>
                <strong v-if="work == daysConstant.THURSDAY.VALUE">{{ daysConstant.THURSDAY.TITLE }}s<span
                    v-if="index != workdays.length - 1">, </span> </strong>
                <strong v-if="work == daysConstant.FRIDAY.VALUE">{{ daysConstant.FRIDAY.TITLE }}s<span
                    v-if="index != workdays.length - 1">, </span> </strong>
                <strong v-if="work == daysConstant.SATURDAY.VALUE">{{ daysConstant.SATURDAY.TITLE }}s<span
                    v-if="index != workdays.length - 1">, </span> </strong>
              </span>
              only.
            </div>
            <!-- Item Dividers in a List -->
            <ion-list>
              <ion-item-divider>
                <ion-label>
                  Morning Schedule
                </ion-label>
              </ion-item-divider>
              <div v-for="appointment in appointmentSlots" :key="appointment.slotid">
                <ion-item class="ps-2" :id="'slot-' + appointment.slotid" data-bs-toggle="modal"
                  data-bs-target="#createAppointmentModal" @click="openAppointmentModal(appointment)"
                  :disabled="appointment.disabled">
                  <ion-label>{{ appointment.sched }}</ion-label>
                  <ion-icon :icon="chevronForwardOutline" slot="end" />
                </ion-item>
                <ion-item-divider v-if="appointment.timeend == '12:00:00'">
                  <ion-label>
                    Afternoon Schedule
                  </ion-label>
                </ion-item-divider>
              </div>
            </ion-list>

            <br />
          </ion-content>
        </ion-modal>

        <ion-modal :is-open="isAddDiagnosisPrescription" @didDismiss="toggleAddDiagnosisModal()" v-if="apdModalData">
          <ion-header>
            <ion-toolbar @click="dismiss()" class="ms-2">
              <ion-icon :icon="chevronBackOutline" slot="start" size="large" />
              <ion-title slot="start">{{ apdModalData.agendaTitle }}</ion-title>
            </ion-toolbar>
          </ion-header>
          <ion-content>
            <div class="p-4">
              <form>
                <ion-list>
                  <ion-item>
                    <ion-icon :icon="personCircleOutline" slot="start" class="me-2"></ion-icon>
                    <ion-label>{{ apdModalData.fullName }} <span class="badge rounded-pill bg-success ms-2">Age:
                        {{apdModalData.patientAge}}</span></ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-icon :icon="timeOutline" slot="start" class="me-2"></ion-icon>
                    <ion-label>{{ apdModalData.timeSlot }}</ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-icon :icon="calendarOutline" slot="start" class="me-2"></ion-icon>
                    <ion-label>{{ apdModalData.convertedDate }}</ion-label>
                  </ion-item>
                  <ion-item v-if="apdModalData.app_patientconcerns">
                    <ion-icon :icon="informationCircleOutline" slot="start" class="me-2"></ion-icon>
                    <ion-label><strong>Symptoms / Concerns / More Info : </strong><br /> {{ apdModalData.app_patientconcerns }}
                    </ion-label>
                  </ion-item>


                  <!-- doctor PRENATAL VIEW -->
                  <span v-if="sessionData.user_level == 2 && apdModalData.app_apptype == appConstants.PRENATAL_CARE.VALUE">
                    <ion-item>
                      <ion-icon :icon="accessibilityOutline" slot="start" class="me-2"></ion-icon>
                      <ion-label><strong>Height / Length: </strong> {{ apdModalData.app_patient_height }} cm</ion-label>
                    </ion-item>
                    <ion-item>
                      <ion-icon :icon="barbellOutline" slot="start" class="me-2"></ion-icon>
                      <ion-label><strong>Weight: </strong> {{ apdModalData.app_patient_weight }} kg</ion-label>
                    </ion-item>
                    <ion-item>
                      <ion-icon :icon="speedometerOutline" slot="start" class="me-2"></ion-icon>
                      <ion-label><strong>Blood Pressure: </strong> {{ apdModalData.app_patient_bp }}</ion-label>
                    </ion-item>
                    <ion-item>
                      <ion-icon :icon="thermometerOutline" slot="start" class="me-2"></ion-icon>
                      <ion-label><strong>Temperature: </strong> {{ apdModalData.app_temp }}</ion-label>
                    </ion-item>
                    <ion-item>
                      <ion-icon :icon="calendarNumberOutline" slot="start" class="me-2"></ion-icon>
                      <ion-label><strong>LMP: </strong> {{ apdModalData.app_lmp }}</ion-label>
                    </ion-item>
                    <ion-item>
                      <ion-icon :icon="calendarNumberOutline" slot="start" class="me-2"></ion-icon>
                      <ion-label><strong>EDC: </strong> {{ apdModalData.app_edc }}</ion-label>
                    </ion-item>
                    <ion-item>
                      <ion-icon :icon="informationCircleOutline" slot="start" class="me-2"></ion-icon>
                      <ion-label><strong>MUAC: </strong> {{ apdModalData.app_muac }}</ion-label>
                    </ion-item>
                    <ion-item>
                      <ion-icon :icon="informationCircleOutline" slot="start" class="me-2"></ion-icon>
                      <ion-label><strong>Living children: </strong> {{ apdModalData.app_num_living_children }}</ion-label>
                    </ion-item>
                    <ion-item>
                      <ion-icon :icon="informationCircleOutline" slot="start" class="me-2"></ion-icon>
                      <ion-label><strong>Present illnesses: </strong><br/>{{ convertToIllnessString(apdModalData.app_medical_history_illness) }}</ion-label>
                    </ion-item>
                    <ion-item>
                      <ion-icon :icon="personCircleOutline" slot="start" class="me-2"></ion-icon>
                      <ion-label><strong>Spouse's Name: </strong><br/>{{ apdModalData.app_partners_name }}</ion-label>
                    </ion-item>
                    <ion-item>
                      <ion-icon :icon="informationCircleOutline" slot="start" class="me-2"></ion-icon>
                      <ion-label><strong>Spouse's Age: </strong>{{ apdModalData.app_partners_age }}</ion-label>
                    </ion-item>
                  </span>

                  <!-- doctor CHECKUP VIEW -->
                  <span v-if="sessionData.user_level == 2 && apdModalData.app_apptype == appConstants.CHECKUP.VALUE">
                    <ion-item>
                      <ion-icon :icon="accessibilityOutline" slot="start" class="me-2"></ion-icon>
                      <ion-label><strong>Height / Length: </strong> {{ apdModalData.app_patient_height }} cm</ion-label>
                    </ion-item>
                    <ion-item>
                      <ion-icon :icon="barbellOutline" slot="start" class="me-2"></ion-icon>
                      <ion-label><strong>Weight: </strong> {{ apdModalData.app_patient_weight }} kg</ion-label>
                    </ion-item>
                    <ion-item>
                      <ion-icon :icon="speedometerOutline" slot="start" class="me-2"></ion-icon>
                      <ion-label><strong>Blood Pressure: </strong> {{ apdModalData.app_patient_bp }}</ion-label>
                    </ion-item>
                    <ion-item>
                      <ion-icon :icon="thermometerOutline" slot="start" class="me-2"></ion-icon>
                      <ion-label><strong>Temperature: </strong> {{ apdModalData.app_temp }}</ion-label>
                    </ion-item>
                    <ion-item>
                      <ion-icon :icon="informationCircleOutline" slot="start" class="me-2"></ion-icon>
                      <ion-label><strong>MUAC: </strong> {{ apdModalData.app_muac }}</ion-label>
                    </ion-item>
                    <ion-item v-if="apdModalData.app_medical_history_illness">
                      <ion-icon :icon="informationCircleOutline" slot="start" class="me-2"></ion-icon>
                      <ion-label><strong>Present illnesses: </strong><br/>{{ convertToIllnessString(apdModalData.app_medical_history_illness) }}</ion-label>
                    </ion-item>
                  </span>

                  <!-- doctor FAMILY PLANNING VIEW -->
                  <span v-if="sessionData.user_level == 2 && apdModalData.app_apptype == appConstants.FAMILY_PLANNING.VALUE">
                    <ion-item>
                      <ion-icon :icon="personCircleOutline" slot="start" class="me-2"></ion-icon>
                      <ion-label><strong>Spouse's Name: </strong><br/>{{ apdModalData.app_partners_name }}</ion-label>
                    </ion-item>
                    <ion-item>
                      <ion-icon :icon="informationCircleOutline" slot="start" class="me-2"></ion-icon>
                      <ion-label><strong>Spouse's Age: </strong>{{ apdModalData.app_partners_age }}</ion-label>
                    </ion-item>
                    <ion-item>
                      <ion-icon :icon="informationCircleOutline" slot="start" class="me-2"></ion-icon>
                      <ion-label><strong>Living children: </strong> {{ apdModalData.app_num_living_children }}</ion-label>
                    </ion-item>
                    <ion-item>
                      <ion-icon :icon="informationCircleOutline" slot="start" class="me-2"></ion-icon>
                      <ion-label><strong>Planning more children? : </strong> {{ apdModalData.app_planning_more_children == 1 ? 'Yes' : 'No' }}</ion-label>
                    </ion-item>
                  </span>

                  <ion-item v-if="prescribedDrugList.length">
                      <ion-label>
                        <h4 class="mb-3"><strong>Drug for release</strong></h4>
                        <div class="w-100 pe-3">
                          <ul class="list-group list-group-flush">
                            <li class="list-group-item" v-for="drug in prescribedDrugList" :key="drug.drugid">
                              <img src="../../resources/drug-icon-mod.jpg" style="height:30px"
                                class="rounded-circle me-2" />
                              {{ drug.drugname }}
                              <span class="badge rounded-pill bg-info ms-2">{{ drug.quanity }}</span>
                              <button type="button" class="btn btn-danger float-end"
                                @click="removeRelease(drug.drugid)">
                                <ion-icon :icon="trashOutline"></ion-icon>
                              </button>
                            </li>
                          </ul>
                        </div>
                      </ion-label>
                  </ion-item>




                </ion-list>

                <!-- PRENATAL-->
                <ion-list v-if="sessionData.user_level == 1 && apdModalData.app_apptype == appConstants.PRENATAL_CARE.VALUE">
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>Weight (kg)</strong></h4>
                      <div class="w-100 pe-3">
                        <input type="number" class="form-control form-control-lg m-2 w-100" v-model="patientWeight" />
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>Height (cm)</strong></h4>
                      <div class="w-100 pe-3">
                        <input type="number" class="form-control form-control-lg m-2 w-100" v-model="patientHeight" />
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>Blood Pressure</strong></h4>
                      <div class="w-100 pe-3">
                        <input type="text" class="form-control form-control-lg m-2 w-100" v-model="patientBP" />
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>Last menstrual period (LMP)</strong></h4>
                      <div class="w-100 pe-3">
                        <input type="date" class="form-control form-control-lg m-2 w-100" v-model="patientLMP" />
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>Expected date of confinement (EDC)</strong></h4>
                      <div class="w-100 pe-3">
                        <input type="date" class="form-control form-control-lg m-2 w-100" v-model="patientEDC" />
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>MUAC</strong></h4>
                      <div class="w-100 pe-2">
                        <input type="text" class="form-control form-control-lg m-2 w-100" v-model="patientMuac" />
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>Temperature</strong></h4>
                      <div class="w-100 pe-2">
                        <input type="text" class="form-control form-control-lg m-2 w-100" v-model="patientTemp" />
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>Medical History</strong></h4>
                      <div class="w-100 pe-3 ms-2">
                        <div class="form-check">
                          <input class="form-check-input medical-hist" type="checkbox" value="1" id="tb" v-model="patientMedHist">
                          <label class="form-check-label" for="tb">
                            Tuberculosis (TB)
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input medical-hist" type="checkbox" value="2" id="heartdiseases" v-model="patientMedHist">
                          <label class="form-check-label" for="heartdiseases">
                            Heart Diseases
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input medical-hist" type="checkbox" value="3" id="diabetic" v-model="patientMedHist">
                          <label class="form-check-label" for="diabetic">
                            Diabetic
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input medical-hist" type="checkbox" value="4" id="goiter" v-model="patientMedHist">
                          <label class="form-check-label" for="goiter">
                            Goiter
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input medical-hist" type="checkbox" value="5" id="asthma" v-model="patientMedHist">
                          <label class="form-check-label" for="asthma">
                            Asthma
                          </label>
                        </div>
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>Num. of living children</strong></h4>
                      <div class="w-100 pe-2">
                        <input type="number" class="form-control form-control-lg m-2 w-100" v-model="patientChildCount" />
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>Husband's Name</strong></h4>
                      <div class="w-100 pe-2">
                        <input type="text" class="form-control form-control-lg m-2 w-100" v-model="patientHusbandName" />
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>Husband's Age</strong></h4>
                      <div class="w-100 pe-2">
                        <input type="number" class="form-control form-control-lg m-2 w-100" v-model="patientHusbandAge" />
                      </div>
                    </ion-label>
                  </ion-item>
                </ion-list>

                <!-- CHECKUP-->
                <ion-list v-if="sessionData.user_level == 1 && apdModalData.app_apptype == appConstants.CHECKUP.VALUE">
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>Weight (kg)</strong></h4>
                      <div class="w-100 pe-2">
                        <input type="number" class="form-control form-control-lg m-2 w-100" v-model="patientWeight" />
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>Height / Length (cm)</strong></h4>
                      <div class="w-100 pe-2">
                        <input type="number" class="form-control form-control-lg m-2 w-100" v-model="patientHeight" />
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>Blood Pressure</strong></h4>
                      <div class="w-100 pe-2">
                        <input type="text" class="form-control form-control-lg m-2 w-100" v-model="patientBP" />
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>MUAC</strong></h4>
                      <div class="w-100 pe-2">
                        <input type="text" class="form-control form-control-lg m-2 w-100" v-model="patientMuac" />
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>Temperature</strong></h4>
                      <div class="w-100 pe-2">
                        <input type="text" class="form-control form-control-lg m-2 w-100" v-model="patientTemp" />
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>Medical History</strong></h4>
                      <div class="w-100 pe-3 ms-2">
                        <div class="form-check">
                          <input class="form-check-input medical-hist" type="checkbox" value="1" id="tb" v-model="patientMedHist">
                          <label class="form-check-label" for="tb">
                            Tuberculosis (TB)
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input medical-hist" type="checkbox" value="2" id="heartdiseases" v-model="patientMedHist">
                          <label class="form-check-label" for="heartdiseases">
                            Heart Diseases
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input medical-hist" type="checkbox" value="3" id="diabetic" v-model="patientMedHist">
                          <label class="form-check-label" for="diabetic">
                            Diabetic
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input medical-hist" type="checkbox" value="4" id="goiter" v-model="patientMedHist">
                          <label class="form-check-label" for="goiter">
                            Goiter
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input medical-hist" type="checkbox" value="5" id="asthma" v-model="patientMedHist">
                          <label class="form-check-label" for="asthma">
                            Asthma
                          </label>
                        </div>
                      </div>
                    </ion-label>
                  </ion-item>
                </ion-list>

                <!-- API-->
                <ion-list v-if="sessionData.user_level == 1 && apdModalData.app_apptype == appConstants.API.VALUE"> 
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>Weight (kg)</strong></h4>
                      <div class="w-100 pe-2">
                        <input type="number" class="form-control form-control-lg m-2 w-100" v-model="patientWeight" />
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>Height / Length (cm)</strong></h4>
                      <div class="w-100 pe-2">
                        <input type="number" class="form-control form-control-lg m-2 w-100" v-model="patientHeight" />
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>Patient Status</strong></h4>
                      <div class="w-100 pe-2">
                        <div class="form-check mb-2 ms-2">
                          <input class="form-check-input" type="radio" value="S" name="flexRadioDefault" id="flexRadioDefault1" v-model="patientbabyStatus">
                          <label class="form-check-label w-100" for="flexRadioDefault1">
                            Stunted
                          </label>
                        </div>
                        <div class="form-check mb-2 ms-2">
                          <input class="form-check-input" type="radio" value="W-MAM" name="flexRadioDefault" id="flexRadioDefault2" v-model="patientbabyStatus">
                          <label class="form-check-label w-100" for="flexRadioDefault2">
                            Wasted
                          </label>
                        </div>
                        <div class="form-check mb-2 ms-2">
                          <input class="form-check-input" type="radio" value="W-SAM" name="flexRadioDefault" id="flexRadioDefault3" v-model="patientbabyStatus">
                          <label class="form-check-label w-100" for="flexRadioDefault3">
                            Wasted - SAM
                          </label>
                        </div>
                        <div class="form-check mb-2 ms-2">
                          <input class="form-check-input" type="radio" value="O" name="flexRadioDefault" id="flexRadioDefault4" v-model="patientbabyStatus">
                          <label class="form-check-label w-100" for="flexRadioDefault4">
                            Overweight / Obese
                          </label>
                        </div>
                        <div class="form-check mb-2 ms-2">
                          <input class="form-check-input" type="radio" value="N" name="flexRadioDefault" id="flexRadioDefault5" v-model="patientbabyStatus">
                          <label class="form-check-label w-100" for="flexRadioDefault5">
                            Normal
                          </label>
                        </div>
                      </div>
                    </ion-label>
                  </ion-item>
                  <span v-if="babySheetData">
                    <ion-item>
                      <ion-label>
                        <h4 class="mb-3"><strong>Low birth weight given Iron</strong></h4>
                        <div class="w-100 pe-2">
                          <ul class="list-group">
                            <li class="list-group-item">
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" @change="confirmCheckApi($event)" value="" id="baby_lbwgi_1" v-model="baby_lbwgi_1">
                                <label class="form-check-label w-100" for="baby_lbwgi_1">
                                  1 mo <span class="float-end" v-if="baby_lbwgi_1">{{babySheetData.baby_lbwgi_1_date ? babySheetData.baby_lbwgi_1_date : now}}</span>
                                </label>
                              </div>
                            </li>
                            <li class="list-group-item">
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" @change="confirmCheckApi($event)" value="" id="baby_lbwgi_2" v-model="baby_lbwgi_2">
                                <label class="form-check-label w-100" for="baby_lbwgi_2">
                                  2 mo <span class="float-end" v-if="baby_lbwgi_2">{{babySheetData.baby_lbwgi_2_date ? babySheetData.baby_lbwgi_2_date : now}}</span>
                                </label>
                              </div>
                            </li>
                            <li class="list-group-item">
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" @change="confirmCheckApi($event)" value="" id="baby_lbwgi_3" v-model="baby_lbwgi_3">
                                <label class="form-check-label w-100" for="baby_lbwgi_3">
                                  3 mo <span class="float-end" v-if="baby_lbwgi_3">{{babySheetData.baby_lbwgi_3_date ? babySheetData.baby_lbwgi_3_date : now}}</span>
                                </label>
                              </div>
                            </li>
                          </ul>
                        </div>
                      </ion-label>
                    </ion-item>
                    <ion-item>
                      <ion-label>
                        <h4 class="mb-3"><strong>DPT - HiB - HepB</strong></h4>
                        <div class="w-100 pe-2">
                          <ul class="list-group">
                            <li class="list-group-item">
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" @change="confirmCheckApi($event)" value="" id="baby_dpt_1" v-model="baby_dpt_1">
                                <label class="form-check-label w-100" for="baby_dpt_1">
                                  1st dose <span class="badge rounded-pill bg-primary" style="font-size: 9px">1 1/2 mos</span>
                                  <span class="float-end" v-if="baby_dpt_1">{{babySheetData.baby_dpt_1_date ? babySheetData.baby_dpt_1_date : now}}</span>
                                </label>
                              </div>
                            </li>
                            <li class="list-group-item">
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" @change="confirmCheckApi($event)" value="" id="baby_dpt_2" v-model="baby_dpt_2">
                                <label class="form-check-label w-100" for="baby_dpt_2">
                                  2nd dose <span class="badge rounded-pill bg-primary" style="font-size: 9px">2 1/2 mos</span>
                                  <span class="float-end" v-if="baby_dpt_2">{{babySheetData.baby_dpt_2_date ? babySheetData.baby_dpt_2_date : now}}</span>
                                </label>
                              </div>
                            </li>
                            <li class="list-group-item">
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" @change="confirmCheckApi($event)" value="" id="baby_dpt_3" v-model="baby_dpt_3">
                                <label class="form-check-label w-100" for="baby_dpt_3">
                                  3rd dose <span class="badge rounded-pill bg-primary" style="font-size: 9px">3 1/2 mos</span>
                                  <span class="float-end" v-if="baby_dpt_3">{{babySheetData.baby_dpt_3_date ? babySheetData.baby_dpt_3_date : now}}</span>
                                </label>
                              </div>
                            </li>
                          </ul>
                        </div>
                      </ion-label>
                    </ion-item>
                    <ion-item>
                      <ion-label>
                        <h4 class="mb-3"><strong>OPV</strong></h4>
                        <div class="w-100 pe-2">
                          <ul class="list-group">
                            <li class="list-group-item">
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" @change="confirmCheckApi($event)" value="" id="baby_opv_1" v-model="baby_opv_1">
                                <label class="form-check-label w-100" for="baby_opv_1">
                                  1st dose <span class="badge rounded-pill bg-primary" style="font-size: 9px">1 1/2 mos</span>
                                  <span class="float-end" v-if="baby_opv_1">{{babySheetData.baby_opv_1_date ? babySheetData.baby_opv_1_date : now}}</span>
                                </label>
                              </div>
                            </li>
                            <li class="list-group-item">
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" @change="confirmCheckApi($event)" value="" id="baby_opv_2" v-model="baby_opv_2">
                                <label class="form-check-label w-100" for="baby_opv_2">
                                  2nd dose <span class="badge rounded-pill bg-primary" style="font-size: 9px">2 1/2 mos</span>
                                  <span class="float-end" v-if="baby_opv_2">{{babySheetData.baby_opv_2_date ? babySheetData.baby_opv_2_date : now}}</span>
                                </label>
                              </div>
                            </li>
                            <li class="list-group-item">
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" @change="confirmCheckApi($event)" value="" id="baby_opv_3" v-model="baby_opv_3">
                                <label class="form-check-label w-100" for="baby_opv_3">
                                  3rd dose <span class="badge rounded-pill bg-primary" style="font-size: 9px">3 1/2 mos</span>
                                  <span class="float-end" v-if="baby_opv_3">{{babySheetData.baby_opv_3_date ? babySheetData.baby_opv_3_date : now}}</span>
                                </label>
                              </div>
                            </li>
                          </ul>
                        </div>
                      </ion-label>
                    </ion-item>
                    <ion-item>
                      <ion-label>
                        <h4 class="mb-3"><strong>PCV</strong></h4>
                        <div class="w-100 pe-2">
                          <ul class="list-group">
                            <li class="list-group-item">
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" @change="confirmCheckApi($event)" value="" id="baby_pcv_1" v-model="baby_pcv_1">
                                <label class="form-check-label w-100" for="baby_pcv_1">
                                  1st dose <span class="badge rounded-pill bg-primary" style="font-size: 9px">1 1/2 mos</span>
                                  <span class="float-end" v-if="baby_pcv_1">{{babySheetData.baby_pcv_1_date ? babySheetData.baby_pcv_1_date : now}}</span>
                                </label>
                              </div>
                            </li>
                            <li class="list-group-item">
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" @change="confirmCheckApi($event)" value="" id="baby_pcv_2" v-model="baby_pcv_2">
                                <label class="form-check-label w-100" for="baby_pcv_2">
                                  2nd dose <span class="badge rounded-pill bg-primary" style="font-size: 9px">2 1/2 mos</span>
                                  <span class="float-end" v-if="baby_pcv_2">{{babySheetData.baby_pcv_2_date ? babySheetData.baby_pcv_2_date : now}}</span>
                                </label>
                              </div>
                            </li>
                            <li class="list-group-item">
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" @change="confirmCheckApi($event)" value="" id="baby_pcv_3" v-model="baby_pcv_3">
                                <label class="form-check-label w-100" for="baby_pcv_3">
                                  3rd dose <span class="badge rounded-pill bg-primary" style="font-size: 9px">3 1/2 mos</span>
                                  <span class="float-end" v-if="baby_pcv_3">{{babySheetData.baby_pcv_3_date ? babySheetData.baby_pcv_3_date : now}}</span>
                                </label>
                              </div>
                            </li>
                          </ul>
                        </div>
                      </ion-label>
                    </ion-item>
                    <ion-item>
                      <ion-label>
                        <h4 class="mb-3"><strong>IPV</strong></h4>
                        <div class="w-100 pe-2">
                          <ul class="list-group">
                            <li class="list-group-item">
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" @change="confirmCheckApi($event)" value="" id="baby_ipv" v-model="baby_ipv">
                                <label class="form-check-label w-100" for="baby_ipv">
                                  1st dose <span class="badge rounded-pill bg-primary" style="font-size: 9px">3 1/2 mos</span>
                                  <span class="float-end" v-if="baby_ipv">{{babySheetData.baby_ipv_date ? babySheetData.baby_ipv_date : now}}</span>
                                </label>
                              </div>
                            </li>
                          </ul>
                        </div>
                      </ion-label>
                    </ion-item>
                  </span>
                </ion-list>

                <!-- FAMILY PLANNING-->
                <ion-list v-if="sessionData.user_level == 1 && apdModalData.app_apptype == appConstants.FAMILY_PLANNING.VALUE">
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>Spouse's Name</strong></h4>
                      <div class="w-100 pe-2">
                        <input type="text" class="form-control form-control-lg m-2 w-100" v-model="patientHusbandName" />
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>Spouse's Age</strong></h4>
                      <div class="w-100 pe-2">
                        <input type="number" class="form-control form-control-lg m-2 w-100" v-model="patientHusbandAge" />
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>Num. of living children</strong></h4>
                      <div class="w-100 pe-2">
                        <input type="number" class="form-control form-control-lg m-2 w-100" v-model="patientChildCount" />
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>Planning to have more children?</strong></h4>
                      <div class="w-100 pe-2">
                        <div class="form-check ms-2 mb-2">
                          <input class="form-check-input" type="radio" value="1" name="flexRadioDefault" id="morechildrenyes" checked v-model="patientPlanningMore">
                          <label class="form-check-label" for="flexRadioDefault1">
                            Yes
                          </label>
                        </div>
                        <div class="form-check ms-2">
                          <input class="form-check-input" type="radio" value="0" name="flexRadioDefault" id="morechildrenno" v-model="patientPlanningMore">
                          <label class="form-check-label" for="flexRadioDefault2">
                            No
                          </label>
                        </div>
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <h4 class="mb-3"><strong>Household Income (monthly)</strong></h4>
                      <div class="w-100 pe-2">
                        <input type="number" class="form-control form-control-lg m-2 w-100" v-model="patientHouseholdIncome" />
                      </div>
                    </ion-label>
                  </ion-item>
                </ion-list>


                <ion-list v-if="sessionData.user_level == 2">
                  <ion-item>
                    <ion-label>
                      <h5><strong>Diagnosis (if necessary)</strong></h5>
                      <div>
                        <textarea class="form-control" style="height:100px; border-radius: 15px" v-model="appDiagnosis"
                          required></textarea>
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <h5><strong>Prescription /  Doctor's Advice</strong></h5>
                      <div>
                        <textarea class="form-control" style="height:100px; border-radius: 15px"
                          v-model="appPrescription" required></textarea>
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <div>
                        <h5><strong>Add Release Drug</strong></h5><br />
                        <form @submit="addToDrugRelease()">
                          <div class="input-group mb-3">
                            <select class="form-select form-select-lg" v-model="selectedDrugId">
                              <option v-for="drug in drugList" :key="drug.id" :value="drug.id">{{ drug.drug_name }}
                                <span v-if="drug.drug_flavor">({{ drug.drug_flavor }})</span>
                              </option>
                            </select>
                            <input type="number" class="form-control form-control-md ms-2" placeholder="Quantity"
                              v-model="selectedQuantity" required>
                            <button type="submit" class="btn btn-primary ms-2">Add</button>
                          </div>
                        </form>
                      </div>
                    </ion-label>
                  </ion-item>
                </ion-list>



                <button type="button" class="btn btn-primary w-100 btn-lg" @click="concernAddressed(apdModalData)"
                  v-if="apdModalData.app_status == 1">
                  <ion-icon :icon="checkmarkOutline" slot="start" class="me-2"></ion-icon>
                  Mark as Done
                </button>
                <button type="button" class="btn btn-primary w-100 btn-lg" @click="moveToDoctorsQueue(apdModalData)"
                  v-if="apdModalData.app_status == 0 && apdModalData.app_apptype != appConstants.API.VALUE">
                  <ion-icon :icon="checkmarkOutline" slot="start" class="me-2"></ion-icon>
                  Initial Check-up Done
                </button>
                <button type="button" class="btn btn-primary w-100 btn-lg" @click="moveToDoctorsQueue(apdModalData)"
                  v-if="apdModalData.app_status == 0 && apdModalData.app_apptype == appConstants.API.VALUE">
                  <ion-icon :icon="checkmarkOutline" slot="start" class="me-2"></ion-icon>
                  Mark as Done
                </button>
              </form>
              <br /><br />
            </div>
          </ion-content>
        </ion-modal>

        <ion-modal :is-open="isShowMoreDetailsOpen" :breakpoints="[0.1, 0.7, 0.85]" :initialBreakpoint="0.7"
          @didDismiss="toggleShowMoreModal()">
          <ion-content>
            <div class="p-4">
              <ion-list>
                <ion-item>
                  <ion-icon :icon="personCircleOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label>{{ app2Fullname }}</ion-label>
                </ion-item>
                <ion-item>
                  <ion-icon :icon="timeOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label>{{ app2TimeSlot }}</ion-label>
                </ion-item>
                <ion-item>
                  <ion-icon :icon="calendarOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label>{{ app2ConvertedDate }}</ion-label>
                </ion-item>
                <ion-item>
                  <ion-icon :icon="informationCircleOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label>{{ app2Concern }}</ion-label>
                </ion-item>
                <ion-item v-if="app2Height != 0">
                  <ion-icon :icon="accessibilityOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-light text-dark">Height</span> {{ app2Height }} cm</ion-label>
                </ion-item>
                <ion-item v-if="app2Weight != 0">
                  <ion-icon :icon="barbellOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-light text-dark">Weight</span> {{ app2Weight }} kg</ion-label>
                </ion-item>
                <ion-item v-if="app2BP">
                  <ion-icon :icon="speedometerOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-light text-dark">Blood Pressure</span> {{ app2BP }}</ion-label>
                </ion-item>
                <ion-item v-if="app2Temp">
                  <ion-icon :icon="thermometerOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-light text-dark">Temperature</span> {{ app2Temp }}</ion-label>
                </ion-item>
                <ion-item v-if="app2Muac">
                  <ion-icon :icon="informationCircleOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-light text-dark">MUAC: </span> {{ app2Muac }}</ion-label>
                </ion-item>
                <ion-item v-if="app2MedHist">
                  <ion-icon :icon="informationCircleOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-light text-dark">Medical History: </span> {{ convertToIllnessString(app2MedHist) }}</ion-label>
                </ion-item>
                <ion-item v-if="app2LMP">
                  <ion-icon :icon="calendarNumberOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-light text-dark">LMP</span> {{ app2LMP }}</ion-label>
                </ion-item>
                <ion-item v-if="app2EDC">
                  <ion-icon :icon="calendarNumberOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-light text-dark">EDC</span> {{ app2EDC }}</ion-label>
                </ion-item>
                <ion-item v-if="app2HusbandName">
                  <ion-icon :icon="personCircleOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-light text-dark">Spouse's Name</span> {{ app2HusbandName }}</ion-label>
                </ion-item>
                <ion-item v-if="app2HusbandAge != 0">
                  <ion-icon :icon="informationCircleOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-light text-dark">Spouse's Age</span> {{ app2HusbandAge }}</ion-label>
                </ion-item>
                <ion-item v-if="app2ChildCount != 0">
                  <ion-icon :icon="informationCircleOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-light text-dark">Living children</span> {{ app2ChildCount }}</ion-label>
                </ion-item>
                <ion-item v-if="app2babyStatus">
                  <ion-icon :icon="informationCircleOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-light text-dark">Patient status: </span> {{ app2babyStatus }}</ion-label>
                </ion-item>
                <ion-item v-if="app2PlanningMore == 1">
                  <ion-icon :icon="informationCircleOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-light text-dark">Planning more children: </span> {{ app2PlanningMore == 1 ? 'Yes' : 'No' }}</ion-label>
                </ion-item>
                <ion-item v-if="app2HouseholdIncome != 0">
                  <ion-icon :icon="informationCircleOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-light text-dark">Household income: </span> {{ app2HouseholdIncome }}</ion-label>
                </ion-item>
                <ion-item v-if="app2Diagnosis">
                  <ion-icon :icon="bandageOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label><span class="badge bg-danger">Diagnosis</span> {{ app2Diagnosis }}</ion-label>
                </ion-item>
                <ion-item v-if="app2Prescription">
                  <ion-icon :icon="bandageOutline" slot="start" class="me-2"></ion-icon>
                  <ion-label>{{ app2Prescription }}</ion-label>
                </ion-item>
              </ion-list>
            </div>
          </ion-content>
        </ion-modal>

        <ion-modal :is-open="isEditAppointmentModalOpen" :breakpoints="[0.1, 0.7, 0.85]" :initialBreakpoint="0.7"
          @didDismiss="toggleEditAppointmentModal()">
          <ion-content>
            <div class="p-4">
              <form @submit="saveAppointmentChanges()">
                <ion-list>
                  <ion-item>
                    <ion-label>
                      <label style="font-size:15px" class="fw-bold mb-2">Agenda</label><br />
                      <select class="form-select form-select-lg" v-model="editAgenda">
                        <option value="1">Prenatal Care</option>
                        <option value="2">Checkup / Consultation</option>
                        <option value="3">API Immunization</option>
                        <option value="4">Family Planning</option>
                      </select>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <label style="font-size:13px" class="fw-bold mb-2">Change Date</label><br />
                      <div class="mb-3">
                        <input type="date" class="form-control form-control-lg" v-model="editDate" />
                      </div>
                    </ion-label>
                  </ion-item>
                  <ion-item>
                    <ion-label>
                      <label style="font-size:13px" class="fw-bold mb-2">Chage Time Slot</label><br />
                      <select class="form-select form-select-lg" v-model="editTimeSlot">
                        <option v-for="slot in appointmentSlots" :key="slot.id" :value="slot.slotid">{{ slot.sched }}
                        </option>
                      </select>
                    </ion-label>
                  </ion-item>
                </ion-list>
                <button type="submit" class="btn btn-primary btn-lg w-100 mb-2 mt-3">Update Appointment</button>
              </form>
            </div>
          </ion-content>
        </ion-modal>
      </div>

      <ion-fab vertical="bottom" horizontal="end" slot="fixed" v-if="sessionData.user_level == 0">
        <ion-fab-button @click="toggleAppointmentModal()">
          <ion-icon :icon="addOutline"></ion-icon>
        </ion-fab-button>
      </ion-fab>

    </ion-content>
  </ion-page>
</template>

<script lang="js">
import { defineComponent } from 'vue';
import { IonPage, IonHeader, IonToolbar, IonTitle, IonContent, IonRefresher, IonRefresherContent,
IonCard, IonCardContent, IonCardHeader, IonCardSubtitle, IonCardTitle, IonModal, IonDatetime, modalController,
  IonItem, IonItemDivider, IonLabel
} from '@ionic/vue';
import {
  chevronDownCircleOutline, pin, calendarOutline,
  timeOutline, arrowForwardOutline, callOutline, addOutline,
  chevronBackOutline, chevronForwardOutline, personCircleOutline, checkmarkOutline,
  informationCircleOutline, accessibilityOutline, barbellOutline, speedometerOutline,
  trashOutline, eyeOutline, bandageOutline, createOutline, thermometerOutline,
  calendarNumberOutline
} from 'ionicons/icons';
import store from '../store'
import moment from 'moment'
import { mapState } from 'vuex'
import AppConstants from '../constants/app.constants'
import axios from "axios"
import SettingsConstants from '../constants/settings.constants'
import Swal from 'sweetalert2'
import $ from 'jquery'

export default defineComponent({
  name: 'ScheduleAppointment',
  components: {
    IonHeader, IonToolbar, IonTitle, IonContent, IonPage, IonRefresher, IonRefresherContent,
    IonCard, IonCardContent, IonCardHeader, IonCardSubtitle, IonCardTitle, IonModal, IonDatetime,
    IonItem, IonItemDivider, IonLabel
  },
  watch: {
    apdModalData: function (newval) {
      if (newval) {
        this.resetFields();
        this.resetPatientData();
        this.babySheetData = null;
      }
    },
    // baby_lbwgi_1: function (newVal) {
    //   this.updateSheet('baby_lbwgi_1', newVal);
    // },
    // baby_lbwgi_2: function (newVal) {
    //   this.updateSheet('baby_lbwgi_2', newVal);
    // },
    // baby_lbwgi_3: function (newVal) {
    //   this.updateSheet('baby_lbwgi_3', newVal);
    // },
    // baby_dpt_1: function (newVal) {
    //   this.updateSheet('baby_dpt_1', newVal);
    // },
    // baby_dpt_2: function (newVal) {
    //   this.updateSheet('baby_dpt_2', newVal);
    // },
    // baby_dpt_3: function (newVal) {
    //   this.updateSheet('baby_dpt_3', newVal);
    // },
    // baby_opv_1: function (newVal) {
    //   this.updateSheet('baby_opv_1', newVal);
    // },
    // baby_opv_2: function (newVal) {
    //   this.updateSheet('baby_opv_2', newVal);
    // },
    // baby_opv_3: function (newVal) {
    //   this.updateSheet('baby_opv_3', newVal);
    // },
    // baby_pcv_1: function (newVal) {
    //   this.updateSheet('baby_pcv_1', newVal);
    // },
    // baby_pcv_2: function (newVal) {
    //   this.updateSheet('baby_pcv_2', newVal);
    // },
    // baby_pcv_3: function (newVal) {
    //   this.updateSheet('baby_pcv_3', newVal);
    // },
    // baby_ipv: function (newVal) {
    //   this.updateSheet('baby_ipv', newVal);
    // },
  },
  data() {
    return {
      baby_lbwgi_1: false,
      baby_lbwgi_2: false,
      baby_lbwgi_3: false,
      baby_dpt_1: false,
      baby_dpt_2: false,
      baby_dpt_3: false,
      baby_opv_1: false,
      baby_opv_2: false,
      baby_opv_3: false,
      baby_pcv_1: false,
      baby_pcv_2: false,
      baby_pcv_3: false,
      baby_ipv: false,
      now:  null,

      today: null,
      drugList: [],
      isAppointmentModalOpen: false,
      selectedDate: moment().format(),
      appointmentSlots: [],
      allAppointments: [],
      isAddDiagnosisPrescription: false,
      apdModalData: [],
      appConcern: null,
      appDiagnosis: null,
      appPrescription: null,
      appDateandTime: [],
      isLoadingItems: true,
      welcomMessage: null,
      patientHeight: null,
      patientWeight: null,
      patientBP: null,
      patientTemp: null,
      patientMuac: null,
      patientMedHist: [],
      patientLMP: null,
      patientEDC: null,
      patientHusbandName: null,
      patientHusbandAge: null,
      patientChildCount: null,
      patientbabyStatus: null,
      patientPlanningMore: 0,
      patientHouseholdIncome: null,

      isShowMoreDetailsOpen: false,
      app2Agenda: null,
      app2TimeSlot: null,
      app2ConvertedDate: null,
      app2Prescription: null,
      app2Diagnosis: null,
      app2Concern: null,
      app2Fullname: null,
      app2Height: null,
      app2Weight: null,
      app2BP: null,
      app2Temp: null,
      app2Muac: null,
      app2MedHist: [],
      app2LMP: null,
      app2EDC: null,
      app2HusbandName: null,
      app2HusbandAge: null,
      app2ChildCount: null,
      app2babyStatus: null,
      app2PlanningMore: 0,
      app2HouseholdIncome: null,

      isEditAppointmentModalOpen: false,
      editedAppointment: null,
      editDate: null,
      editTimeSlot: null,
      editAppointmentSlots: null,
      daysConstant: null,
      workdays: [],

      selectedDrugId: 1,
      selectedQuantity: null,
      prescribedDrugList: [],
      babySheetData: null,

      appConstants: null


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
      chevronDownCircleOutline, doRefresh, pin,
      calendarOutline, timeOutline, arrowForwardOutline,
      callOutline, addOutline, chevronBackOutline,
      chevronForwardOutline, personCircleOutline, checkmarkOutline,
      informationCircleOutline, accessibilityOutline, barbellOutline, speedometerOutline,
      trashOutline, eyeOutline, bandageOutline, createOutline, thermometerOutline,
      calendarNumberOutline
    }
  },
  computed: mapState([
    'sessionData',
    'isAppointmentListToday'
  ]),
  methods: {
    async dismiss() {
      modalController.dismiss({
        'dismissed': true
      });
    },
    convertToIllnessString: function (illness) {
      var ilnessIds = illness.split(',');
      var illnessString = '';
      ilnessIds.forEach( function (ill) {
        switch (ill) {
          case AppConstants.MEDICAL_HISTORY.TB.VALUE:
            illnessString = illnessString + AppConstants.MEDICAL_HISTORY.TB.TITLE + ', ';
            break;
          case AppConstants.MEDICAL_HISTORY.HEART_DISEASE.VALUE:
            illnessString = illnessString + AppConstants.MEDICAL_HISTORY.HEART_DISEASE.TITLE + ', ';
            break;
          case AppConstants.MEDICAL_HISTORY.DIABETIC.VALUE:
            illnessString = illnessString + AppConstants.MEDICAL_HISTORY.DIABETIC.TITLE + ', ';
            break;
          case AppConstants.MEDICAL_HISTORY.GOITER.VALUE:
            illnessString = illnessString + AppConstants.MEDICAL_HISTORY.GOITER.TITLE + ', ';
            break;
          case AppConstants.MEDICAL_HISTORY.ASTHMA.VALUE:
            illnessString = illnessString + AppConstants.MEDICAL_HISTORY.ASTHMA.TITLE + ', ';
            break;
        }
      });
      return illnessString.slice(0, -2);
    },
    updateSheet: function (field, isTrue) {
      var bodyFormData = new FormData();
      bodyFormData.append('field', field);
      bodyFormData.append('now', this.now);
      bodyFormData.append('isTrue', isTrue ? 1 : 0);
      axios({
        method: "post",
        url: SettingsConstants.BASE_URL + "appointment.rest.php?type=updatebabydatasheet&userid=" + this.babySheetData.baby_userid,
        data: bodyFormData,
        headers: { "Content-Type": "multipart/form-data" },
      }).then(function (response) {
        if (response.data > 0) {
          console.log('datasheet updated');
        }
      });
    },
    saveAppointmentChanges: function () {
      event.preventDefault();
      var bodyFormData = new FormData();
      bodyFormData.append('app_type', this.editAgenda);
      bodyFormData.append('app_timeslot', this.editTimeSlot);
      bodyFormData.append('app_date', this.editDate);
      axios({
        method: "post",
        url: SettingsConstants.BASE_URL + "appointment.rest.php?type=updateappointment&app_id=" + this.editedAppointment.id,
        data: bodyFormData,
        headers: { "Content-Type": "multipart/form-data" },
      })
        .then(function (response) {
          if (response.data == 1) {
            Swal.fire(
              'Success!',
              'Appointment has been updated!',
              'success'
            );
            setTimeout(function () {
              this.dismiss();
              this.resetFields();
              this.getAllAppointments();
            }.bind(this), 500);
          } else {
            Swal.fire(
              'Error!',
              'Something went wrong when updating appointment item!',
              'error'
            );
          }
        }.bind(this))
        .catch(function (response) {
          console.log(response);
        });
    },
    toggleShowMoreModal: function (app) {
      this.isShowMoreDetailsOpen = !this.isShowMoreDetailsOpen;
      if (app) {
        this.app2Agenda = app.agendaTitle;
        this.app2TimeSlot = app.timeSlot;
        this.app2ConvertedDate = app.convertedDate;
        this.app2Prescription = app.app_prescription;
        this.app2Diagnosis = app.app_diagnosis;
        this.app2Concern = app.app_patientconcerns;
        this.app2Fullname = app.fullName;
        this.app2Height = app.app_patient_height;
        this.app2Weight = app.app_patient_weight;
        this.app2BP = app.app_patient_bp;
        this.app2Temp = app.app_temp;
        this.app2Muac = app.app_muac;
        this.app2MedHist = app.app_medical_history_illness;
        this.app2LMP = app.app_lmp;
        this.app2EDC = app.app_edc;
        this.app2HusbandName = app.app_partners_name;
        this.app2HusbandAge = app.app_partners_age;
        this.app2ChildCount = app.app_num_living_children;
        this.app2babyStatus = app.app_baby_status;
        this.app2PlanningMore = app.app_planning_more_children;
        this.app2HouseholdIncome = app.app_household_income;
      } else {
        this.app2Agenda = null;
        this.app2TimeSlot = null;
        this.app2ConvertedDate = null;
        this.app2Prescription = null;
        this.app2Diagnosis = null;
        this.app2Concern = null;
        this.app2Fullname = null;
        this.app2Height = null;
        this.app2Weight = null;
        this.app2BP = null;
        this.app2Temp = null;
        this.app2Muac = null;
        this.app2MedHist = [];
        this.app2LMP = null;
        this.app2EDC = null;
        this.app2HusbandName = null;
        this.app2HusbandAge = 0;
        this.app2ChildCount = 0;
        this.app2babyStatus = null;
        this.app2PlanningMore = 0;
        this.app2HouseholdIncome = 0;
      }
    },
    toggleAddDiagnosisModal: function (appData) {
      console.log(appData);
      this.isAddDiagnosisPrescription = !this.isAddDiagnosisPrescription;
      if (this.isAddDiagnosisPrescription) {
        this.apdModalData = appData;
        if (appData.app_apptype == AppConstants.APPOINTMENT_SERVICES.API.VALUE) {
          this.getSheetData(appData.app_userId);
        }
        this.getAllDrugList();
      }
    },
    resetFields: function () {
      this.appDiagnosis = null;
      this.appPrescription = null;
    },
    resetPatientData: function () {
      this.patientHeight = null;
      this.patientWeight = null;
      this.patientBP = null;
      this.patientTemp = null;
      this.patientMuac = null;
      this.patientMedHist = [];
      this.patientLMP = null;
      this.patientEDC = null;
      this.patientHusbandName = null;
      this.patientHusbandAge = null;
      this.patientChildCount = null;
      this.patientPlanningMore = 0;
      this.patientbabyStatus = null;
      this.patientHouseholdIncome = null;
    },
    concernAddressed: function (details) {
      event.preventDefault();
      var bodyFormData = new FormData();
      this.appDiagnosis ? bodyFormData.append('app_diagnosis', this.appDiagnosis): bodyFormData.append('app_diagnosis', '');
      this.appPrescription ? bodyFormData.append('app_prescription', this.appPrescription) : bodyFormData.append('app_prescription', '');
      axios({
        method: "post",
        url: SettingsConstants.BASE_URL + "appointment.rest.php?type=adddiagnosis&appointmentId=" + details.id,
        data: bodyFormData,
        headers: { "Content-Type": "multipart/form-data" },
      })
        .then(function (response) {
          if (response.data == 1) {
            Swal.fire(
              'Great!',
              'Appointment has marked as done!',
              'success'
            ).then(function () {
              this.dismiss();
              this.resetFields();
              this.getAllAppointments();
            }.bind(this));
          } else {
            Swal.fire(
              'Error!',
              'Something went wrong when updating appointment!',
              'error'
            );
          }
        }.bind(this))
        .catch(function (response) {
          console.log(response);
        });

      this.prescribedDrugList.forEach(function (drug) {
        var newval = (parseInt(drug.currentquantity) - parseInt(drug.quanity)).toString();
        //drug.drug_quantity = newval;
        axios.get(SettingsConstants.BASE_URL + 'drug.rest.php?type=updatedrugquantity&drugid=' + drug.drugid + '&drug_quantity=' + newval, { crossdomain: true })
          .then(function (response) {
            if (response.data) {
              console.log(response.data);
            }
          }.bind(this));
        this.recordQuantityChange(drug.drugid, drug.quanity);
      }.bind(this));
    },
    moveToDoctorsQueue: function (details) {
      event.preventDefault();
      var bodyFormData = new FormData();
      this.patientHeight ? bodyFormData.append('app_patient_height', this.patientHeight) : bodyFormData.append('app_patient_height', 0);
      this.patientWeight ? bodyFormData.append('app_patient_weight', this.patientWeight) : bodyFormData.append('app_patient_weight', 0);
      this.patientBP ? bodyFormData.append('app_patient_bp', this.patientBP) : bodyFormData.append('app_patient_bp', '');
      this.patientMuac ? bodyFormData.append('app_muac', this.patientMuac) : bodyFormData.append('app_muac', '');
      this.patientTemp ? bodyFormData.append('app_temp', this.patientTemp) : bodyFormData.append('app_temp', '');
      this.patientMedHist ? bodyFormData.append('app_medical_history_illness', this.patientMedHist) : bodyFormData.append('app_medical_history_illness', '');
      this.patientLMP ? bodyFormData.append('app_lmp', this.patientLMP) : bodyFormData.append('app_lmp', '');
      this.patientEDC ? bodyFormData.append('app_edc', this.patientEDC) : bodyFormData.append('app_edc', '');
      this.patientHusbandName ? bodyFormData.append('app_partners_name', this.patientHusbandName) : bodyFormData.append('app_partners_name', '');
      this.patientHusbandAge ? bodyFormData.append('app_partners_age', this.patientHusbandAge) : bodyFormData.append('app_partners_age', 0);
      this.patientPlanningMore ? bodyFormData.append('app_planning_more_children', this.patientPlanningMore) : bodyFormData.append('app_planning_more_children', 0);
      this.patientbabyStatus ? bodyFormData.append('app_baby_status', this.patientbabyStatus) : bodyFormData.append('app_baby_status', '');
      this.patientHouseholdIncome ? bodyFormData.append('app_household_income', this.patientHouseholdIncome) : bodyFormData.append('app_household_income', 0);
      this.patientChildCount ? bodyFormData.append('app_num_living_children', this.patientChildCount) : bodyFormData.append('app_num_living_children', 0);
      
      axios({
        method: "post",
        url: SettingsConstants.BASE_URL + "appointment.rest.php?type=addinitialcheckup&appointmentId=" + details.id,
        data: bodyFormData,
        headers: { "Content-Type": "multipart/form-data" },
      })
        .then(function (response) {
          if (response.data == 1) {
            if (details.app_apptype == this.appConstants.API.VALUE) {
              this.concernAddressed(details);
            } else {
              Swal.fire(
                'Great!',
                'Appointment has been approved and moved to doctor\'s queue!',
                'success'
              ).then(function () {
                this.dismiss();
                this.resetFields();
                this.getAllAppointments();
              }.bind(this));
            }
            
          } else {
            Swal.fire(
              'Error!',
              'Something went wrong when updating appointment!',
              'error'
            );
          }
        }.bind(this))
        .catch(function (response) {
          console.log(response);
        });
    },
    getAllDrugList: function () {
      axios.get(SettingsConstants.BASE_URL + 'drug.rest.php?type=all', { crossdomain: true })
        .then(function (response) {
          if (response.data) {
            this.drugList = response.data;
          }
        }.bind(this));
    },
    addToDrugRelease: function () {
      event.preventDefault();
      var checkDrugs = obj => obj.id === this.selectedDrugId;
      if (!this.prescribedDrugList.some(checkDrugs)) {
        var drugname = null;
        var current_quantity = 0;
        this.drugList.forEach(function (drug) {
          if (drug.id == this.selectedDrugId) {
            drugname = drug.drug_name + (drug.drug_flavor ? '(' + drug.drug_flavor + ')' : '');
            current_quantity = drug.drug_quantity;
          }
        }.bind(this));
        if (this.selectedQuantity <= current_quantity) {
          this.prescribedDrugList.push(
            {
              'drugid': this.selectedDrugId,
              'quanity': this.selectedQuantity,
              'drugname': drugname,
              'currentquantity': current_quantity
            });
        } else {
          Swal.fire(
            'Error!',
            'Please input something lesser than the quantity we have (' + current_quantity + ')',
            'error'
          );
        }

      }
    },
    removeRelease: function (drugid) {
      this.prescribedDrugList = this.prescribedDrugList.filter(item => item.drugid != drugid);
    },
    recordQuantityChange: function (drugid, val) {
      axios.get(SettingsConstants.BASE_URL + 'drug.rest.php?type=recordstock&drugid=' + drugid + '&quantity=' + val, { crossdomain: true })
        .then(function (response) {
          if (response.data) {
            console.log(response.data);
          }
        }.bind(this));
    },
    getAllSchedule: function () {
      axios.get(SettingsConstants.BASE_URL + 'schedule.rest.php?type=all', { crossdomain: true })
        .then(function (response) {
          if (response.data) {
            this.appointmentSlots = response.data;
            this.getAllAppointments();
            if (this.sessionData.user_level == 0) {
              this.checkForEnabledSlots();
            }
          }
        }.bind(this));
    },
    getAllWorkDays: function () {
      axios.get(SettingsConstants.BASE_URL + 'schedule.rest.php?type=workdays', { crossdomain: true })
        .then(function (response) {
          if (response.data[0].daysarray) {
            this.workdays = response.data[0].daysarray.split(",");
          }
        }.bind(this));
    },
    getAllAppointments: function () {
      this.isLoadingItems = true;
      var today = moment().format().split('T')[0];
      this.allAppointments = [];
      var url = null;
      if (this.sessionData.user_level > 0) {
        switch (this.sessionData.user_level.toString()) {
          case '1':
            url = 'appointment.rest.php?type=nursesqueue';
            break;
          case '2':
            url = 'appointment.rest.php?type=doctorsqueue';
            break;
          case '3':
            url = 'appointment.rest.php?type=everything';
            break;
        }
      } else {
        url = 'appointment.rest.php?type=allbyId&userId=' + this.sessionData.id;
      }
      if (this.isAppointmentListToday) {
        url = url + '&isToday=' + today;
      }
      axios.get(SettingsConstants.BASE_URL + url, { crossdomain: true })
        .then(function (response) {
          if (response.data) {
            this.isLoadingItems = false;
            this.allAppointments = response.data;
            this.allAppointments.forEach(function (app) {
              app.agendaTitle = null;
              app.timeSlot = null;
              app.fullName = null;
              (this.sessionData.user_level == "0" || this.sessionData.user_level == "3") ? app.disabledPrescription = true : app.disabledPrescription = false;
              axios.get(SettingsConstants.BASE_URL + 'users.rest.php?type=nameonly&userId=' + app.app_userId, { crossdomain: true })
                .then(function (response) {
                  if (response.data) {
                    var resp = response.data[0];
                    app.patientAge = null;
                    app.fullName = resp.user_lastname + ', ' + resp.user_firstname + (resp.user_middlename ? ' ' + resp.user_middlename : '');
                    if (resp.user_dob) {
                      if ((moment(resp.user_dob, 'YYYYMMDD').fromNow()).includes('years')) {
                        app.patientAge = (moment(resp.user_dob, 'YYYYMMDD').fromNow()).split(" ")[0];
                      } else {
                        if ((moment(resp.user_dob, 'YYYYMMDD').fromNow()).includes('month')) {
                          app.patientAge = parseInt(moment(resp.user_dob, 'YYYYMMDD').fromNow().split(" ")[0]).toString() + ' months/old';
                        } else {
                          app.patientAge = '< 1 month old';
                        }
                      }
                    } else {
                      app.patientAge = "-";
                    }
                  }
                }.bind(app));
              switch (parseInt(app.app_apptype)) {
                case AppConstants.APPOINTMENT_SERVICES.PRENATAL_CARE.VALUE:
                  app.agendaTitle = AppConstants.APPOINTMENT_SERVICES.PRENATAL_CARE.TITLE
                  break;
                case AppConstants.APPOINTMENT_SERVICES.CHECKUP.VALUE:
                  app.agendaTitle = AppConstants.APPOINTMENT_SERVICES.CHECKUP.TITLE
                  break;
                case AppConstants.APPOINTMENT_SERVICES.API.VALUE:
                  app.agendaTitle = AppConstants.APPOINTMENT_SERVICES.API.TITLE
                  break;
                case AppConstants.APPOINTMENT_SERVICES.FAMILY_PLANNING.VALUE:
                  app.agendaTitle = AppConstants.APPOINTMENT_SERVICES.FAMILY_PLANNING.TITLE
                  break;
              }
              this.appointmentSlots.forEach(function (slot) {
                if (slot.slotid == app.app_timeslot) {
                  app.timeSlot = slot.sched;
                  app.timeStart = slot.timestart;
                  app.timeEnd = slot.timeend;
                }
              }.bind(app));
              app.convertedDate = moment(app.app_date).format('LL');
            }.bind(this));
          } else {
            this.isLoadingItems = false;
          }
        }.bind(this));
    },
    storeAppointmentDetails: function (details) {
      store.commit('SET_APPOINTMENTVIEW_DETAILS', details);
    },
    openAppointmentModal: function (appointment) {
      this.storeAppointmentDetails(appointment)
      console.log(appointment);
    },
    toggleAppointmentModal: function () {
      this.isAppointmentModalOpen = !this.isAppointmentModalOpen;
      $('ion-modal').removeAttr('tabindex');
    },
    checkForEnabledSlots: function () {
      var workdaysArray = [];
      this.workdays.forEach(function (day) {
        if (day.toString() == this.daysConstant.SUNDAY.VALUE.toString()) {
          workdaysArray.push(this.daysConstant.SUNDAY.TITLE)
        }
        if (day.toString() == this.daysConstant.MONDAY.VALUE.toString()) {
          workdaysArray.push(this.daysConstant.MONDAY.TITLE)
        }
        if (day.toString() == this.daysConstant.TUESDAY.VALUE.toString()) {
          workdaysArray.push(this.daysConstant.TUESDAY.TITLE)
        }
        if (day.toString() == this.daysConstant.WEDNESDAY.VALUE.toString()) {
          workdaysArray.push(this.daysConstant.WEDNESDAY.TITLE)
        }
        if (day.toString() == this.daysConstant.THURSDAY.VALUE.toString()) {
          workdaysArray.push(this.daysConstant.THURSDAY.TITLE)
        }
        if (day.toString() == this.daysConstant.FRIDAY.VALUE.toString()) {
          workdaysArray.push(this.daysConstant.FRIDAY.TITLE)
        }
        if (day.toString() == this.daysConstant.SATURDAY.VALUE.toString()) {
          workdaysArray.push(this.daysConstant.SATURDAY.TITLE)
        }
      }.bind(this));
      this.getAppDateandTimeSlot();
      var getNowTime = new Date();
      var allowance = 2;
      getNowTime = getNowTime.setHours(getNowTime.getHours() + allowance);
      this.appointmentSlots.forEach(function (slot) {
        var startTime = null;
        if (this.selectedDate) {
          slot.selectedDate = this.selectedDate;
          slot.title = moment(this.selectedDate).format('dddd') + ', ' + moment(this.selectedDate).format('LL');
          startTime = moment(this.selectedDate).format().split("T")[0] + 'T' + slot.timestart + '+' + moment(this.selectedDate).format().split("+")[1];
        } else {
          startTime = moment().format().split("T")[0] + 'T' + slot.timestart + '+' + moment().format().split("+")[1];
        }

        getNowTime > new Date(startTime).getTime() ||
          (!workdaysArray.includes(moment(startTime).format('dddd'))) ? slot.disabled = true : slot.disabled = false;
      }.bind(this));
    },
    getAppDateandTimeSlot: function () {
      this.appDateandTime = [];
      axios.get(SettingsConstants.BASE_URL + 'appointment.rest.php?type=gettimeslotanddate', { crossdomain: true })
        .then(function (response) {
          if (response.data) {
            this.appDateandTime = response.data;
            this.appDateandTime.forEach(function (timedate) {
              timedate.dateonly = timedate.app_date.split("T")[0];
            });
            this.appointmentSlots.forEach(function (slot) {
              var startTime = null;
              if (this.selectedDate) {
                startTime = moment(this.selectedDate).format().split("T")[0];
              } else {
                startTime = moment().format().split("T")[0];
              }
              this.appDateandTime.filter(function (datetime) {
                if (datetime.app_timeslot == slot.slotid.toString() && startTime == datetime.dateonly) {
                  if (!slot.disabled) {
                    slot.disabled = true;
                  }
                }
              }.bind(startTime));
            }.bind(this));
          }
        }.bind(this));
    },
    removeAppointment: function (app) {
      Swal.fire({
        title: 'Remove Appointment?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6C757D',
        confirmButtonText: 'Yes, remove it!'
      }).then(function (result) {
        if (result.isConfirmed) {
          axios.get(SettingsConstants.BASE_URL + 'appointment.rest.php?type=deleteappointment&appid=' + app.id, { crossdomain: true })
            .then(function (response) {
              if (response.data) {
                Swal.fire(
                  'Removed!',
                  'Appointment has been removed.',
                  'success'
                ).then(function () {
                  this.getAllAppointments();
                }.bind(this));
              }
            }.bind(this));
        }
      }.bind(this))
    },
    toggleEditAppointmentModal: function (app) {
      this.isEditAppointmentModalOpen = !this.isEditAppointmentModalOpen;
      if (app) {
        this.editedAppointment = app;
        this.editAgenda = this.editedAppointment.app_apptype;
        this.editDate = this.editedAppointment.app_date.split('T')[0];
        this.editTimeSlot = this.editedAppointment.app_timeslot;
      } else {
        this.editedAppointment = null;
        this.editAgenda = null;
        this.editDate = null;
        this.editTimeSlot = null;
      }
    },
    createBabyDataSheet: function () {
      axios.get(SettingsConstants.BASE_URL + 'appointment.rest.php?type=addbabydatasheet&userid=' + this.sessionData.id, { crossdomain: true })
        .then(function (response) {
          if (response.data == 1) {
            console.log('datasheet created');
          }
        });
    },
    getSheetData: function (userid) {
      axios.get(SettingsConstants.BASE_URL + 'appointment.rest.php?type=getbabydatasheetbyuserid&userid=' + userid, { crossdomain: true })
        .then(function (response) {
          if (response.data) {
            this.babySheetData = response.data[0];
            this.baby_lbwgi_1 = this.babySheetData.baby_lbwgi_1 != '0' ? true: false;
            this.baby_lbwgi_2 = this.babySheetData.baby_lbwgi_2 != '0' ? true: false;
            this.baby_lbwgi_3 = this.babySheetData.baby_lbwgi_3 != '0'  ? true: false;

            this.baby_dpt_1 = this.babySheetData.baby_dpt_1 != '0' ? true: false;
            this.baby_dpt_2 = this.babySheetData.baby_dpt_2 != '0' ? true: false;
            this.baby_dpt_3 = this.babySheetData.baby_dpt_3 != '0' ? true: false;

            this.baby_opv_1 = this.babySheetData.baby_opv_1 != '0' ? true: false;
            this.baby_opv_2 = this.babySheetData.baby_opv_2 != '0' ? true: false;
            this.baby_opv_3 = this.babySheetData.baby_opv_3 != '0' ? true: false;

            this.baby_pcv_1 = this.babySheetData.baby_pcv_1 != '0' ? true: false;
            this.baby_pcv_2 = this.babySheetData.baby_pcv_2 != '0' ? true: false;
            this.baby_pcv_3 = this.babySheetData.baby_pcv_3 != '0' ? true: false;

            this.baby_ipv = this.babySheetData.baby_ipv != '0' ? true: false;
          }
        }.bind(this));
    },
    confirmCheckApi: function (elem) {
      event.preventDefault();
      var isChecked = $('#'+elem.target.id)[0].checked;
      console.log(this);
      Swal.fire({
        title: 'Confirm',
        text: "Please confirm submission!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, done!'
      }).then( function (result) {
        if (result.isConfirmed) {
          $('#'+elem.target.id)[0].checked = isChecked;
          this[elem.target.id] = isChecked;
          this.updateSheet(elem.target.id, isChecked)
          Swal.fire(
            'Great!',
            'Record saved!',
            'success'
          )
        } else {
          $('#'+elem.target.id)[0].checked = !isChecked;
          this[elem.target.id] = !isChecked;
        }
      }.bind(this))
    }
  },
  mounted() {
    this.appConstants = AppConstants.APPOINTMENT_SERVICES;
    this.getAllSchedule();
    this.daysConstant = AppConstants.DAYS;
    if (this.sessionData.user_level == 0) {
      this.getAllWorkDays();
    }
    moment().format('LT').includes("AM") ? this.welcomMessage = 'morning' : this.welcomMessage = 'afternoon';
    this.emitter.on('isTodayChanged', function () {
      this.getAllAppointments();
    }.bind(this));

    if (this.sessionData.age <= 0.75 || this.sessionData.age == '< 1 month' && this.sessionData.user_level == 0) {
      axios.get(SettingsConstants.BASE_URL + 'appointment.rest.php?type=getbabydatasheetbyuserid&userid=' + this.sessionData.id, { crossdomain: true })
        .then(function (response) {
          if (!response.data) {
            this.createBabyDataSheet();
          }
        }.bind(this));
    }
    this.now = moment().format().toString().split("T")[0];
  },
});
</script>
