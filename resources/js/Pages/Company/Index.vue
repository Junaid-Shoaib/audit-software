<template>
  <app-layout>
    <template #header>
      <h2 class="header">Company</h2>
    </template>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-1">
      <a-button @click="create" size="small">Create</a-button>
      <a-inputSearch
        v-model:value="search"
        class="ml-2"
        placeholder="input search text"
        style="width: 200px"
        @search="onSearch"
        size="small"
      />
      <a-button @click="isOpen2 = true" size="small" class="float-right"
        >Generate PDF</a-button
      >

      <div
        v-if="isOpen2"
        class="fixed inset-0 w-full h-screen z-20 bg-black opacity-25"
        @click="isOpen2 = false"
      ></div>
      <div class="absolute z-30 right-0 mt-2" :class="{ hidden: !isOpen2 }">
        <div class="bg-white rounded-lg shadow-lg py-2 w-48">
          <a
            :href="route('companypdf', 'all')"
            target="_blank"
            class="
              block
              text-blue-600
              font-semibold
              px-4
              py-2
              |
              hover:text-white hover:bg-blue-700
            "
            >All Fiscal</a
          >
          <a
            :href="route('companypdf', 'march')"
            target="_blank"
            class="
              block
              text-blue-600
              font-semibold
              px-4
              py-2
              |
              hover:text-white hover:bg-blue-700
            "
            >March</a
          >
          <a
            :href="route('companypdf', 'june')"
            target="_blank"
            class="
              block
              text-blue-600
              font-semibold
              px-4
              py-2
              |
              hover:text-white hover:bg-blue-700
            "
            >June</a
          >
          <a
            :href="route('companypdf', 'september')"
            target="_blank"
            class="
              block
              text-blue-600
              font-semibold
              px-4
              py-2
              |
              hover:text-white hover:bg-blue-700
            "
            >September</a
          >
          <a
            :href="route('companypdf', 'december')"
            target="_blank"
            class="
              block
              text-blue-600
              font-semibold
              px-4
              py-2
              |
              hover:text-white hover:bg-blue-700
            "
            >December</a
          >
        </div>
      </div>
      <!-- <div class="relative overflow-x-auto mt-2 ml-2 sm:rounded-2xl"> -->
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
              <!-- v-if="can['edit'] || can['delete']" -->
              <!-- v-if="can['edit']" -->
              <a-button
                size="small"
                type="primary"
                @click="edit(record.id)"
                class="mr-2"
                >Edit</a-button
              >
              <a-button
                size="small"
                v-if="record.delete"
                danger
                @click="destroy(record.id)"
                >Delete</a-button
              >
              <!-- v-if="item.delete && can['delete']" -->
            </template>
          </template>
        </a-table>
      </div>
    </div>
    <!-- </div> -->
  </app-layout>
</template>


<script>
import AppLayout from "@/Layouts/AppLayout";
import { Button, Table, Select, InputSearch } from "ant-design-vue";

export default {
  components: {
    AppLayout,
    "a-button": Button,
    "a-table": Table,
    "a-select": Select,
    "a-inputSearch": InputSearch,
  },

  //   props: ["data"],
  props: {
    balances: Object,
    filters: Object,
    can: Object,
    companies: Array,
    //FOR MULTI-SELECT
    cochange: Object,
    mapped_data: Object,
  },

  data() {
    return {
      co_id: this.$page.props.co_id,
      isOpen1: true,
      isOpen2: false,
      // co_id: this.cochange,
      // options: this.companies,
      search: this.filters.search,
      columns: [
        // {
        //   title: "ID",
        //   dataIndex: "id",
        //   // sorter: (a, b) => a.id - b.id,
        //   width: "10%",
        // },
        {
          title: "Name",
          dataIndex: "name",
          // sorter: (a, b) => {
          //     const nameA = a.name.toUpperCase();
          //     const nameB = b.name.toUpperCase();
          //     if (nameA < nameB) {
          //         return -1;
          //     }
          //     if (nameA > nameB) {
          //         return 1;
          //     }
          //     return 0;
          //     },
          width: "20%",
        },
        {
          title: "Address",
          dataIndex: "address",
        },
        {
          title: "Email",
          dataIndex: "email",
        },
        {
          title: "Website",
          dataIndex: "web",
        },
        {
          title: "Phone",
          dataIndex: "phone",
        },
        {
          title: "Actions",
          dataIndex: "actions",
          key: "actions",
        },
      ],
      params: {
        search: this.filters.search,
        field: this.filters.field,
        direction: this.filters.direction,
      },
    };
  },

  methods: {
    onSearch() {
      this.$inertia.get(
        route("companies"),
        {
          // select: select.value,
          // search: search.value
          search: this.search,
        },
        { replace: true, preserveState: true }
      );
    },
    create() {
      this.$inertia.get(route("companies.create"));
    },

    edit(id) {
      this.$inertia.get(route("companies.edit", id));
    },

    destroy(id) {
      this.$inertia.delete(route("companies.destroy", id));
    },

    coch() {
      this.$inertia.get(route("companies.coch", this.co_id));
    },

    sort(field) {
      this.params.field = field;
      this.params.direction = this.params.direction === "asc" ? "desc" : "asc";
    },
  },
  //   watch: {
  //     params: {
  //       //   handler() {
  //       //     // let params = this.params;
  //       //     // Object.keys(params).forEach((key) => {
  //       //     //   if (params[key] == "") {
  //       //     //     delete params[key];
  //       //     //   }
  //       //     // });

  //       //     this.$inertia.get(this.route("companies"), params, {
  //       //       replace: true,
  //       //       preserveState: true,
  //       //     });
  //       //   },
  //       //   deep: true,
  //       // },
  //       handler: throttle(function () {
  //         let params = pickBy(this.params);
  //         this.$inertia.get(this.route("companies"), params, {
  //           replace: true,
  //           preserveState: true,
  //         });
  //       }, 150),
  //       deep: true,
  //     },
  //   },
};
</script>
