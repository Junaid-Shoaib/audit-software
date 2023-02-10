<template>
  <app-layout>
    <template #header>
      <h2>Advisor Accounts</h2>
    </template>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-1">
      <a-button
        :href="route('advisor_accounts.create')"
        size="small"
        type="buttin"
        >Add Advisor
      </a-button>

      <div class="">
        <a-table
          :columns="columns"
          :data-source="mapped_data"
          :loading="loading"
          class="mt-2"
          size="small"
        >
          <template #bodyCell="{ column, record }">
            <template v-if="column.key === 'actions'">
              <a-button
                size="small"
                type="primary"
                :href="route('advisors.edit', record.id)"
                class="mr-2"
                >Edit</a-button
              >
              <a-button
                size="small"
                v-if="record.delete"
                danger
                @click="destroy(record.id)"
              >
                Delete</a-button
              >
            </template>
          </template>
        </a-table>

        <paginator class="mt-6" :balances="balances" />
      </div>
    </div>
  </app-layout>
</template>







<script>
import AppLayout from "@/Layouts/AppLayout";
import Paginator from "@/Layouts/Paginator";
import { Button, Table, Select, InputSearch } from "ant-design-vue";

export default {
  components: {
    AppLayout,
    "a-button": Button,
    "a-table": Table,
    "a-select": Select,
  },

  props: {
    // errors: Object,
    dataEdit: Object,
    branches: Object,
    mapped_data: Object,
    filters: Object,
  },
  data() {
    return {
      columns: [
        {
          title: "Company",
          dataIndex: "company_id",
          width: "30%",
        },
        {
          title: "Advisor",
          dataIndex: "advisor_id",
        },
        {
          title: "Actions",
          dataIndex: "actions",
          key: "actions",
        },
      ],
      //   params: {
      //     search: this.filters.search,
      //     field: "company_id",
      //     direction: "asc",
      //   },
    };
  },

  watch: {
    errors: function () {
      if (this.errors) {
        this.firstError = this.errors[Object.keys(this.errors)[0]];
        this.isError = true;
      }
    },

    // params: {
    //   handler: throttle(function () {
    //     let params = pickBy(this.params);
    //     this.$inertia.get(this.route("advisor_accounts"), params, {
    //       replace: true,
    //       preserveState: true,
    //     });
    //   }, 150),
    //   deep: true,
    // },
  },

  methods: {
    // sort(field) {
    //   this.params.field = field;
    //   this.params.direction = this.params.direction === "asc" ? "desc" : "asc";
    // },
    destroy(id) {
      this.$inertia.delete(route("advisor_accounts.destroy", id));
    },
  },
};
</script>
