import { createStore } from 'vuex'
import createPersistedState from 'vuex-persistedstate'

export default createStore({
  state: {
    sessionData: null,
    drugViewDetails: [],
    patientViewDetails: [],
    appointmentViewDetails: [],
    isAppointmentListToday: true,
    showPatientsOnly: null,
    showAdminsOnly: null
  },
  mutations: {
    SET_SESSION_DATA: function (state, sessionData) {
      state.sessionData = sessionData;
    },
    RESET_SESSION_DATA: function (state) {
      state.sessionData = null;
    },
    SET_DRUGVIEW_DETAILS: function (state, drugViewDetails) {
      state.drugViewDetails = drugViewDetails;
    },
    RESET_DRUGVIEW_DETAILS: function (state) {
      state.drugViewDetails = [];
    },
    SET_PATIENTVIEW_DETAILS: function (state, patientViewDetails) {
      state.patientViewDetails = patientViewDetails;
    },
    RESET_PATIENTVIEW_DETAILS: function (state) {
      state.patientViewDetails = [];
    },
    SET_APPOINTMENTVIEW_DETAILS: function (state, appointmentViewDetails) {
      state.appointmentViewDetails = appointmentViewDetails;
    },
    RESET_APPOINTMENTVIEW_DETAILS: function (state) {
      state.appointmentViewDetails = [];
    },
    SET_TODAY_APPOINTMENT_LIST: function (state, isAppointmentListToday) {
      state.isAppointmentListToday = isAppointmentListToday;
    },
    SET_SHOW_ADMINS_ONLY: function (state, showAdminsOnly) {
      state.showAdminsOnly = showAdminsOnly;
    },
    SET_SHOW_PATIENTS_ONLY: function (state, showPatientsOnly) {
      state.showPatientsOnly = showPatientsOnly;
    }
  },
  getters: {

  },
  actions: {

  },
  modules: {

  },
  plugins: [createPersistedState()],
})
