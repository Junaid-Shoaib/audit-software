<template>
  <app-layout>
    <template #header>
      <h2 class="header">Bank Balances</h2>
    </template>

    <div class="max-w-7xl mx-auto pb-2 sm:px-6 lg:px-8 py-1">
      <Button size="small" :href="route('balances.create')"
        >Create Balances</Button
      >
      <Button
        v-if="dataEdit"
        size="small"
        class="ml-2"
        :href="route('bal.edit')"
        >Edit</Button
      >
      <Table
        :columns="columns"
        :data-source="balances"
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
    companies: Object,
    years: Object,
    balances: Object,
    dataEdit: Object,
    cochange: Object,
    // filters: Object,
  },

  data() {
    return {
      options: this.companies,
      co_id: this.cochange,
      yr_id: this.$page.props.yr_id,

      //   search: this.filters.search,
      columns: [
        {
          title: "Account Number",
          dataIndex: "number",
        },
        {
          title: "Ledger",
          dataIndex: "ledger",
        },
        {
          title: "Statement",
          dataIndex: "statement",
        },
        {
          title: "Confirmation",
          dataIndex: "confirmation",
        },
        // {
        //   title: "Actions",
        //   dataIndex: "actions",
        //   key: "actions",
        // },
      ],
    };
  },

  methods: {
    // onSearch() {
    //   this.$inertia.get(
    //     route("balances"),
    //     {
    //       search: this.search,
    //     },
    //     { replace: true, preserveState: true }
    //   );
    // },
    // destroy(id) {
    //   this.$inertia.delete(route("balances.destroy", id));
    // },
  },
};
</script>
