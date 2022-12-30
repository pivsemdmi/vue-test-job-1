<template>
  <nav class="pagination"
       v-if="pages > 1">
    <router-link :to="{path: this.$route.path,query:{...this.$route.query,p:prePage}}"
                 @click.prevent="setPage(prePage)"
                 v-if="current_page > 0"
                 class="btn pre-btn">
      &lt;
    </router-link>
    <ul class="list">
      <li class="item"
          v-for="index in pages" :key="index"
          :class="{active: index-1 == current_page}">
        <router-link :to="{path: this.$route.path,query:{...this.$route.query,p:index-1}}"
                     @click.prevent="setPage(index-1)">
          {{index}}
        </router-link>
      </li>
    </ul>
    <router-link :to="{path: this.$route.path,query:{...this.$route.query,p:nextPage}}"
                 @click.prevent="setPage(nextPage)"
                 v-if="current_page < (pages - 1)"
                 class="btn next-btn">
      >
    </router-link>
  </nav>
</template>

<script>
export default {
  name: "CustomPagination",
  props: {
    current_page: Int16Array,
    pages: Int16Array,
  },
  methods: {
    setPage(page) {
      this.$emit('setPage', page);
    }
  },
  computed: {
    prePage() {
      return this.current_page - 1
    },
    nextPage() {
      return Number(this.current_page) + 1
    }
  }
}
</script>

<style scoped>

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
}
.pagination ul {
  padding: 0;
  list-style-type: none;
  display: flex;
  justify-content: center;
  align-items: center;
}
.pagination .btn {
  margin: 10px;
}
.pagination ul > li:not(:last-child) {
  margin-right: 10px;
}
.active {
  font-weight: bold;
  pointer-events: none;
}
.active a {
  text-decoration: none;
  color: black;
}
.btn {}
.pre-btn {}
.next-btn {}
.item {}
</style>