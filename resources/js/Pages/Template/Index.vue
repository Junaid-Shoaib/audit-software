<template>
  <app-layout>
    <template #header>
      <h2 class="header">Template</h2>
    </template>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-2">
      <Button @click="create" size="small">Create Template</Button>
      <InputSearch
        v-model:value="search"
        placeholder="Search here"
        style="width: 200px"
        @search="onSearch"
        size="small"
        class="ml-2"
      />

      <Table
        :columns="columns"
        :data-source="mapped_data"
        :loading="loading"
        class="mt-2"
        size="small"
      >
        <template #bodyCell="{ column, record }">
          <template v-if="column.key === 'actions'">
            <a
              class="ant-btn ant-btn-sm ant-btn-primary mr-2"
              size="small"
              :href="'template/download/' + record.id"
              >Download</a
            >
            <Button size="small" danger @click="destroy(record.id)"
              >Delete</Button
            >
          </template>
        </template>
      </Table>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { Button, Table, Select, InputSearch, Checkbox } from "ant-design-vue";

export default {
  components: {
    AppLayout,
    Button,
    Table,
    InputSearch,
  },

  props: {
    data: Object,
    balances: Object,
    mapped_data: Object,
    filters: Object,
    can: Object,
    companies: Array,
    company: Object,
  },

  data() {
    return {
      // co_id: this.$page.props.co_id,
      co_id: this.company,
      options: this.companies,

      search: this.filters.search,
      columns: [
        {
          title: "Name of File",
          dataIndex: "name",
          //   width: "20%",
        },
        {
          title: "Name of Folder",
          dataIndex: "type",
        },
        {
          title: "Action",
          dataIndex: "actions",
          key: "actions",
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
        route("templates"),
        {
          search: this.search,
        },
        { replace: true, preserveState: true }
      );
    },

    create() {
      this.$inertia.get(route("templates.create"));
    },

    destroy(id) {
      if (confirm("Do you really want to delete?")) {
        this.$inertia.delete(route("templates.destroy", id));
      }
    },
    coch() {
      this.$inertia.get(route("companies.coch", this.co_id["id"]));
    },

    downloadFile: function (id) {
      this.$inertia.get(route("temp_download", id));
    },

    sort(field) {
      this.params.field = field;
      this.params.direction = this.params.direction === "asc" ? "desc" : "asc";
    },
  },
};
</script>
