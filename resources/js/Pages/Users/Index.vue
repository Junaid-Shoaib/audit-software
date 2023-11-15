<template>
  <app-layout>
    <template #header>
      <div class="grid grid-cols-2 items-center">
        <h2 class="header">Users</h2>
      </div>
    </template>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-2">
      <Button @click="create" size="small">Add User</Button>

      <InputSearch
        class="ml-2"
        v-model:value="search"
        placeholder="Search here"
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
import { Button, Table, Select, InputSearch, Checkbox } from "ant-design-vue";
import { useForm } from "@inertiajs/inertia-vue3";

export default {
  components: {
    AppLayout,
    Button,
    Table,
    Select,
    InputSearch,

    useForm,
  },

  // props: ["data", "companies", "company"],
  props: {
    // balances: Object,
    companies: Object,
    company: Object,
    mapped_data: Object,
    filters: Object,
  },

  data() {
    return {
      // co_id: this.$page.props.co_id,
      co_id: this.company,
      options: this.companies,
      search: this.filters.search,
      columns: [
        {
          title: "Name",
          dataIndex: "name",
          //   width: "20%",
        },
        {
          title: "Email",
          dataIndex: "email",
          key: "approval",
        },
        {
          title: "Role",
          dataIndex: "role",
          key: "actions",
        },
      ],
    };
  },

  methods: {
    onSearch() {
      this.$inertia.get(
        route("users"),
        {
          // select: select.value,
          // search: search.value
          search: this.search,
        },
        { replace: true, preserveState: true }
      );
    },

    create() {
      this.$inertia.get(route("users.create"));
    },

    // edit(id) {
    //   this.$inertia.get(route("years.edit", id));
    // },

    // destroy(id) {
    //   this.$inertia.delete(route("years.destroy", id));
    // },

    // close(id) {
    //   this.$inertia.get(route("years.close", id));
    // },

    coch() {
      // this.$inertia.get(route("companies.coch", this.co_id));
      this.$inertia.get(route("companies.coch", this.co_id["id"]));
    },
  },
};
</script>
