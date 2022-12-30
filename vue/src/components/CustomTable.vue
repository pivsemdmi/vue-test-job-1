<template>
  <div class="catalog-wrapper">
    <table class="catalog">
      <thead v-if="titles">
      <tr>
        <td class="head-id"
            v-for="(title, key) in titles" :key="key"
            :class="{sorted: sort.item === key}">
          <template v-if="sort.item !== key">
            <router-link :to="{path: $route.path, query: {...$route.query, si: key, sb: 'asc'}}"
                         @click.prevent="this.$emit('sort', key, 'asc')">
              {{title}}
            </router-link>
            <span class="sort-symbol">⇳</span>
          </template>
          <template v-else>
            <template v-if="sort.by !== 'asc'">
              <router-link :to="{path: $route.path, query: {...$route.query, si: key, sb: 'asc'}}"
                           @click.prevent="this.$emit('sort', key, 'asc')">
                {{title}}
              </router-link>
              <span class="sort-symbol">⇩</span>
            </template>
            <template v-else>
              <router-link :to="{path: $route.path, query: {...$route.query, si: key, sb: 'desc'}}"
                           @click.prevent="this.$emit('sort', key, 'desc')">
                {{title}}
              </router-link>
              <span class="sort-symbol">⇧</span>
            </template>
          </template>
        </td>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(item, key) in items" :key="item.id.value">
        <td class="body-id">{{item.id.value}}</td>
        <td class="body-name">{{item.name.value}}</td>
        <td class="body-price">{{item.price.value}}</td>
        <td class="body-quantity">{{item.quantity.value}}</td>
        <td class="body-actions">
        <span class="actions">
          <button v-for="action in rowActions" :key="action.key"
                  :class="action.key"
                  @click="this.$emit(action.key,key)">{{action.text}}</button>
        </span>
        </td>
      </tr>
      <tr v-if="bottomActions.length > 0">
        <td class="body-actions"
            :colspan="countColumn + 1">
        <span class="actions">
          <button v-for="action in bottomActions" :key="action.key"
                  :class="action.key"
                  @click="this.$emit(action.key)">{{action.text}}</button>
        </span>
        </td>
      </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name: "CustomTable",
  props: {
    titles: Object,
    items: Array,
    actions: {
      default: [
        'edit',
        'delete',
        'add',
      ]
    },
    sort: {
      default: {
        item: 'id',
        by: 'asc',
      }
    }
  },
  data() {
    return {
      allActions: [
        {
          key: 'edit',
          text: 'E',
          location: 0
        },
        {
          key: 'delete',
          text: 'D',
          location: 0
        },
        {
          key: 'add',
          text: 'A',
          location: 1
        },
      ],
      locations: {
        row: 0,
        bottom: 1,
      }
    };
  },
  computed: {
    rowActions() {
      return this.allActions.filter((item) => {
        return item.location === this.locations.row && this.actions.includes(item.key);
      });
    },
    bottomActions() {
      return this.allActions.filter((item) => {
        return item.location === this.locations.bottom && this.actions.includes(item.key);
      });
    },
    countColumn() {
      if (this.titles !== undefined) {
        return Object.keys(this.titles).length
      } else if (this.items && Object.keys(this.items).length > 0) {
        return Object.keys(this.items[0]).length
      } else {
        return 0;
      }
    }
  }
}
</script>

<style scoped>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
.catalog-wrapper {
  overflow-x: auto;
}
.catalog {
  width: 100%;
}
thead {
  font-weight: bold;
}
thead .sorted a {
  text-decoration: underline;
}
.sort-symbol {
  text-decoration: none;
  margin-left: 0.3em;
}
</style>