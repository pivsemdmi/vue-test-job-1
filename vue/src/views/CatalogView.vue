<template>
  <div class="div">
    <custom-table :titles="catalog.titles" :items="catalog.list"
                  :sort="catalog.sort"
                  @edit="edit"
                  @delete="remove"
                  @sort="setSort"
                  @add="add"></custom-table>
    <custom-pagination :current_page="pagination.current_page"
                       :pages="pagination.pages" @set-page="setPage"></custom-pagination>
  </div>
  <custom-modal-editor v-if="modal_edit.active" :orig-object="modal_edit.item" :dictionary="modal_edit.dictionary" @save="save" @close="edit_close"></custom-modal-editor>
  <custom-modal-message :show="modal_message.show" :text="modal_message.text"></custom-modal-message>
</template>

<script>
import CustomModalEditor from '../components/CustomModalEditor'
import CustomModalMessage from '../components/CustomModalMessage'
import CustomTable from '../components/CustomTable'
import CustomPagination from '../components/CustomPagination'

export default {
  name: "CatalogView",
  components: {
    CustomModalEditor,
    CustomModalMessage,
    CustomTable,
    CustomPagination,
  },
  data() {
    return {
      init: false,
      catalog: {
        titles: {
          'id': "Идентификатор",
              'name': "Наименование",
              'quantity': "Количество",
              'price': "Цена",
        },
        sort: {},
        list: [],
        sample: {
          'id': {value: "-1", readOnly: true},
          'name': {value: ""},
          'price': {value: ""},
          'quantity': {value: ""},
        }
      },
      pagination: {
        pages: [],
        current_page: 0,
      },
      modal_edit: {
        active: false,
        dictionary: {
          'id': "Идентификатор",
          'name': "Наименование",
          'quantity': "Количество",
          'price': "Цена",
        },
        index: -1,
        item: {},
      },
      modal_message: {
        show: false,
        text: '',
      },
      default: {
        catalog: {
          sort: {
            item: 'id',
            by: 'asc',
          },
        },
        pagination: {
          current_page: 0,
        },
      },
    };
  },
  methods: {
    edit(index) {
      this.modal_edit.index = index;
      this.modal_edit.item = this.catalog.list[index];
      this.modal_edit.active = true;
    },
    remove(index) {
      const productId = this.catalog.list[index].id.value;

      const callback = (text) => {
        const {error, message} = JSON.parse(text);

        if (Number(error) === 0) {
          console.log('Продукт с идентификатором ' + productId + ' удален');
          this.modal_message.show = false;
          this.updateData();
        } else {
          console.error('Продукт с идентификатором ' + productId + ' не удален', message);
        }
      };

      const productData = {
        id: productId
      };

      console.log('Удаление продукта с идентификатором', productData);
      this.ajax('delete', productData, callback);
    },
    edit_close() {
      this.modal_edit.active = false;
    },
    save(product) {
      this.modal_message.text = 'Сохранение данных';
      this.modal_message.show = true;

      const callback = (text) => {
        const {error, message} = JSON.parse(text);

        if (Number(error) === 0) {
          console.log('Данные о продукте сохранены', product);
          this.modal_message.show = false;

          if (this.modal_edit.index >= 0) {
            this.catalog.list[this.modal_edit.index] = product;
          }
          this.updateData();
        } else {
          console.error('Данные о продукте не сохранены', message);
        }
      };

      const prepareProduct = {
        id: product.id.value,
        name: product.name.value,
        quantity: product.quantity.value,
        price: product.price.value,
      };

      console.log('Сохранение данных о продукте', prepareProduct);
      this.ajax('update', prepareProduct, callback);
    },
    setSort(item, by) {
      let query = {
        ...this.$route.query,
        ...{
          si: item,
          sb: by,
        }
      }

      if (query.si === this.default.catalog.sort.item)
        delete query.si
      if (query.sb === this.default.catalog.sort.by)
        delete query.sb

      this.$router.push({path: this.$route.path, query: query})

      this.catalog.sort.item = item;
      this.catalog.sort.by = by;

      this.updateData();
    },
    setPage(page) {
      let query = {
        ...this.$route.query,
        ...{
          p: page,
        }
      }

      if (query.p === this.default.pagination.current_page)
        delete query.p

      this.$router.push({path: this.$route.path, query: query})

      this.pagination.current_page = page;

      this.updateData();
    },
    add() {
      const product = structuredClone(this.catalog.sample);

      this.modal_edit.index = -1;
      this.modal_edit.item = product;
      this.modal_edit.active = true;
    },
    updateData() {
      if (! this.init) return;

      const query = {
        sortItem: this.catalog.sort.item,
        sortBy: this.catalog.sort.by,
        page: this.pagination.current_page,
      }

      this.modal_message.text = 'Загрузка данных';
      this.modal_message.show = true;

      const callback = (text) => {
        const {error, data, message} = JSON.parse(text);

        console.log('result', error, data, message)
        if (Number(error) === 0) {
          data.items.forEach((item) => {
            item.id = {value: item.id, readOnly: true};
            item.name = {value: item.name};
            item.quantity = {value: item.quantity};
            item.price = {value: item.price};
          });
          console.log('result', error, data, message)

          this.catalog.list = data.items;

          this.pagination.pages = data.pages;

          if (this.pagination.current_page >= this.pagination.pages) {
            this.setPage(this.pagination.pages - 1);
          }
        } else {
          alert(message);
        }

        this.modal_message.show = false;
      };

      console.log('Запрос данных с сервера', query);
      //TODO: Ajax запрос на сервер. Запрос данных с сервера
      this.ajax('get', query, callback);
    },
    ajax(action, data, callback) {

      const url = this.$router.resolve({
        path: '/api/',
        query: {
          action: action,
          data: JSON.stringify(data)
        }
      }).href;
      console.log(url);

      this.makeRequest(url, callback);
    },
    makeRequest(url, callback) {
      let httpRequest = false;

      if (window.XMLHttpRequest) { // Mozilla, Safari, ...
        httpRequest = new XMLHttpRequest();
        if (httpRequest.overrideMimeType) {
          httpRequest.overrideMimeType('text/xml');
        }
      }

      if (!httpRequest) {
        alert('Не вышло :( Невозможно создать экземпляр класса XMLHTTP ');
        return false;
      }
      httpRequest.onreadystatechange = function() {
        if (httpRequest.readyState == 4) {
          callback(httpRequest.responseText, httpRequest.status, httpRequest);
        }
      };
      httpRequest.open('GET', url, true);
      httpRequest.send(null);
    }
  },
  mounted() {
    this.setSort(
      this.$route.query.si ?? this.default.catalog.sort.item,
      this.$route.query.sb ?? this.default.catalog.sort.by,
    )

    this.pagination.current_page = this.$route.query.p ?? this.default.pagination.current_page;

    this.init = true;

    this.updateData();
  }
}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin: 30px auto 0 auto;

  max-width: 1200px;
  padding: 0 15px;
}
</style>