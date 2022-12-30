<template>
  <div class="modal-wrapper">
    <div class="modal">
      <form action="">
        <table>
          <tr v-for="(property, key) in editObject" :key="key">
            <td><label :for="key">{{property.name}}:</label></td>
            <td><input type="text" :id="key" v-model="property.value" :disabled="property.readOnly"></td>
          </tr>
        </table>
        <div class="actions">
          <button @click.prevent="save">Сохранить</button>
          <button @click.prevent="cancel">Отмена</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: "CustomModalEditor",
  props: {
    origObject: Object,
    dictionary: Object,
  },
  data() {
    return {
      editObject: {}
    }
  },
  methods: {
    prepareEdit() {
      Object.keys(this.editObject).forEach((key) => {
        this.editObject[key].name = this.dictionary[key] !== undefined ? this.dictionary[key] : key
      })
    },
    prepareSave() {
      Object.keys(this.editObject).forEach((key) => {
        delete this.editObject[key].name
      })
    },
    save() {
      this.prepareSave();
      this.$emit('save', this.editObject)
      this.$emit('close', this.editObject)
    },
    cancel() {
      this.$emit('close', this.editObject)
    }
  },
  mounted() {
    this.editObject = structuredClone(this.origObject);
    this.prepareEdit();
  }
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
.actions {
  margin-top: 10px;
}
</style>