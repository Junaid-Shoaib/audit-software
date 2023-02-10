<template>
  <app-layout>
    <template #header>
      <h2 class="header">Account Groups</h2>
    </template>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-1">
      <InputSearch
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

export default {
  components: {
    AppLayout,
    Table,
    InputSearch,
  },

  props: {
    // data: Object,
    // balances: Object,
    filters: Object,
    companies: Object,
    company: Object,
    exists: Object,
    mapped_data: Object,
  },

  data() {
    return {
      co_id: this.$page.props.co_id,
      co_id: this.company,
      options: this.companies,
      search: "",

      columns: [
        {
          title: "Group Name",
          dataIndex: "name",
          width: "40%",
        },
        {
          title: "Group Type",
          dataIndex: "type_name",
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
        route("accountgroups"),
        {
          // select: select.value,
          // search: search.value
          search: this.search,
        },
        { replace: true, preserveState: true }
      );
    },

    create() {
      this.$inertia.get(route("accountgroups.create"));
    },

    edit(id) {
      this.$inertia.get(route("accountgroups.edit", id));
    },

    destroy(id) {
      this.$inertia.delete(route("accountgroups.destroy", id));
    },

    generate() {
      this.$inertia.get(route("accountgroups.generate"));
    },

    coch() {
      this.$inertia.get(route("companies.coch", this.co_id["id"]));
    },

    sort(field) {
      this.params.field = field;
      this.params.direction = this.params.direction === "asc" ? "desc" : "asc";
    },

    // check() {
    //   console.log("click");

    //   setTimeout(() => {
    //     console.log("timer");
    //     // this.postRecordSolo('clientStore/UPDATE_RECORDS_NO_TAB', this.endPoint, true)
    //   }, 1000);
    // },

    // addRecord () {
    //   setTimeout(() => {
    //     this.postRecordSolo('clientStore/UPDATE_RECORDS_NO_TAB', this.endPoint, true)
    //   }, 1000)
    // }

    // search_data() {
    //   let params = pickBy(this.params);
    //   this.$inertia.get(this.route("accountgroups"), params, {
    //     replace: true,
    //     preserveState: true,
    //   });
    // },
  },
};
</script>
