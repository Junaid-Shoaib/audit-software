<template>
  <app-layout>
    <template #header>
      <div class="grid grid-cols-2 items-center">
        <h2 class="header">Accounts</h2>
        <div class="justify-end">
          <multiselect
            style="width: 50%"
            class="float-right rounded-md border border-black float-right"
            placeholder="Select Company."
            v-model="co_id"
            track-by="id"
            label="name"
            :options="options"
            @update:model-value="coch"
          >
          </multiselect>
        </div>
      </div>
    </template>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-1">
      <InputSearch
        class="ml-2"
        v-model:value="search"
        placeholder="input search text"
        style="width: 200px"
        @search="onSearch"
        size="small"
      />

      <Table
        :columns="columns"
        :data-source="mapped_data"
        :loading="loading"
        class="mt-2"
        size="small"
      >
      </Table>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { Button, Table, Select, InputSearch } from "ant-design-vue";

import Multiselect from "@suadelabs/vue3-multiselect";

export default {
  components: {
    AppLayout,
    Table,
    InputSearch,
    Multiselect,
  },

  props: {
    // data: Object,
    // balances: Object,
    filters: Object,
    can: Object,
    companies: Array,
    company: Object,
    mapped_data: Object,
  },

  data() {
    return {
      // co_id: this.$page.props.co_id,
      co_id: this.company,
      options: this.companies,

      search: "",
      columns: [
        {
          title: "Name of Account",
          dataIndex: "name",
          width: "40%",
        },
        {
          title: "Group of Account",
          dataIndex: "group_name",
          width: "40%",
        },
      ],

      params: {
        search: this.filters.search,
        // field: this.filters.field,
        // direction: this.filters.direction,
      },
    };
  },

  methods: {
    onSearch() {
      this.$inertia.get(
        route("accounts"),
        {
          // select: select.value,
          // search: search.value
          search: this.search,
        },
        { replace: true, preserveState: true }
      );
    },

    create() {
      this.$inertia.get(route("accounts.create"));
    },

    edit(id) {
      this.$inertia.get(route("accounts.edit", id));
    },

    destroy(id) {
      this.$inertia.delete(route("accounts.destroy", id));
    },
    coch() {
      this.$inertia.get(route("companies.coch", this.co_id["id"]));
    },

    sort(field) {
      this.params.field = field;
      this.params.direction = this.params.direction === "asc" ? "desc" : "asc";
    },
    // search_data() {
    //   let params = pickBy(this.params);
    //   this.$inertia.get(this.route("accounts"), params, {
    //     replace: true,
    //     preserveState: true,
    //   });
    // },
  },
};
</script>
