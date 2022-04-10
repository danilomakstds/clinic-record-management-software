<template>
  <ion-page>
    <ion-header>
      <ion-toolbar>
        <ion-title>Empty Page</ion-title>
      </ion-toolbar>
    </ion-header>
    <ion-content :fullscreen="true">
    </ion-content>
  </ion-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { IonPage, IonHeader, IonToolbar, IonTitle, IonContent } from '@ionic/vue';

export default defineComponent({
  name: 'EmptyPage',
  components: { IonHeader, IonToolbar, IonTitle, IonContent, IonPage }
});
</script>
