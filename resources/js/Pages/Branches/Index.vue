<template>
  <app-layout>
    <template #header>
      <h2 class="header">Bank Branches</h2>
    </template>

    <div class="max-w-7xl mx-auto pb-2 sm:px-6 lg:px-8 py-1">
      <Button size="small" :href="route('branches.create', 'create')">
        Create Branch</Button
      >
      <InputSearch
        v-model:value="search"
        class="ml-2"
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
        <template #bodyCell="{ column, record }">
          <template v-if="column.key === 'actions'">
            <Button
              size="small"
              :href="route('branches.edit', record.id)"
              type="primary"
              class="mr-2"
              >Edit</Button
            >
            <Button
              size="small"
              v-if="record.delete"
              danger
              @click="destroy(record.id)"
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
    balances: Object,
    filters: Object,
    mapped_data: Object,
  },

  data() {
    return {
      search: this.filters.search,
      columns: [
        {
          title: "Bank Name",
          dataIndex: "name",
        },
        {
          title: "Branch Name & Address",
          dataIndex: "address",
        },
        {
          title: "Actions",
          dataIndex: "actions",
          key: "actions",
        },
      ],
    };
  },

  methods: {
    onSearch() {
      this.$inertia.get(
        route("branches"),
        {
          search: this.search,
        },
        { replace: true, preserveState: true }
      );
    },
    destroy(id) {
      this.$inertia.delete(route("branches.destroy", id));
    },
  },
};
</script>
