<template>
  <app-layout>
    <template #header>
      <h2 class="header">Bank Accounts</h2>
    </template>

    <div class="max-w-7xl mx-auto pb-2 sm:px-6 lg:px-8 py-1">
      <Button size="small" :href="route('bank_accounts.create')">
        Add Accounts</Button
      >
      <Button
        v-if="dataEdit"
        class="ml-2"
        size="small"
        :href="route('bank_accounts.edit')"
        >Edit</Button
      >
      <InputSearch
        v-model:value="search"
        class="ml-2"
        placeholder="input search text"
        style="width: 200px"
        @search="onSearch"
        size="small"
      />
      <div v-if="isError">{{ firstError }}</div>
      <Table
        :columns="columns"
        :data-source="balances.data"
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

export default {
  components: {
    AppLayout,
    Button,
    Table,
    Select,
    InputSearch,
  },

  props: {
    errors: Object,
    dataEdit: Object,
    branches: Object,
    balances: Object,
    companies: Array,
    years: Object,
    filters: Object,
    cochange: Object,
  },

  data() {
    return {
      options: this.companies,
      co_id: this.cochange,
      yr_id: this.$page.props.yr_id,
      search: this.filters.search,
      columns: [
        {
          title: "Account Number",
          dataIndex: "name",
        },
        {
          title: "Branch",
          dataIndex: "branches",
        },
        {
          title: "Type",
          dataIndex: "type",
        },
        {
          title: "Currency",
          dataIndex: "currency",
        },
      ],
    };
  },

  watch: {
    errors: function () {
      if (this.errors) {
        this.firstError = this.errors[Object.keys(this.errors)[0]];
        this.isError = true;
      }
    },
  },

  methods: {
    onSearch() {
      this.$inertia.get(
        route("bank_accounts"),
        {
          search: this.search,
        },
        { replace: true, preserveState: true }
      );
    },
    // destroy(id) {
    //   this.$inertia.delete(route("bank_accounts.destroy", id));
    // },
  },
};
</script>
