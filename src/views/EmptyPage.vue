<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-title>Empty Page</ion-title>
      </ion-toolbar>
    </ion-header>
    <ion-content :fullscreen="true">
        <ion-list>
          <ion-item v-for="variable in namesData" :key="variable.id">
            <ion-label>{{variable.names}}</ion-label>
          </ion-item>
        </ion-list>
    </ion-content>
  </ion-page>
</template>

<script lang="js">
import { defineComponent } from 'vue';
import { IonPage, IonHeader, IonToolbar, IonTitle, IonContent, IonList, IonLabel, IonItem} from '@ionic/vue';
import axios from "axios";

export default defineComponent({
  name: 'EmptyPage',
  data() {
    return {
      namesData: [],
      variableName: 'TEsting'
    }
  },
  components: { IonHeader, IonToolbar, IonTitle, IonContent, IonPage, IonList, IonLabel, IonItem },
  methods: {
    getallNames: function () {
      axios.get('https://norsucrms.000webhostapp.com/REST-API/test.rest.php', { crossdomain: true })
          .then(function (response) {
              this.namesData = response.data;
          }.bind(this));
    }
  },
  mounted() {
    this.getallNames();
  },
});
</script>
