<template>
  <div v-if="show" class="modal-wrapper">
    <div class="modal">
      <span class="text">{{text}}</span>
    </div>
  </div>
</template>

<script>
export default {
  name: "CustomModalMessage",
  props: {
    text: {
      type: String,
      require: true
    },
    show: {
      type: Boolean,
      require: true
    },
  },
}
</script>

<style scoped>
.modal-wrapper {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}
.modal-wrapper:before {
  content: '';
  display: block;
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: #000;
  opacity: .5;
}
.modal {
  position: fixed;
  bottom: 50%;
  right: 50%;
  transform: translate(50%, 50%);
  background: #fff;
  padding: 10px;
}
</style>