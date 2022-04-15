<template>
  <div class="maindiv">
    <div class="d-flex justify-content-center">
      <img src="../../resources/icon.png" style="height:150px" class="d-flex justify-content-center"/>
      
    </div>
    <div class="mb-3">
    <label for="exampleFormControlInput1" class="form-label">Email address</label>
    <input type="email" class="form-control" id="exampleFormControlInput1" placeholder="name@example.com">
    </div>
</div>
</template>

<script>
// @ is an alias to /src
export default {
  name: 'LoginPage',
  
}
</script>

<style scoped>
.maindiv {
  max-width: 500px;
  margin: 0 auto;
}
</style>