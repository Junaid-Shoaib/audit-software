<template>
  <app-layout>
    <template #header>
      <h2>Advisors</h2>
    </template>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-1">
      <a-button :href="route('advisors.create')" size="small" type="buttin"
        >Add Advisor
      </a-button>
      <!-- <input
          type="search"
          v-model="params.search"
          aria-label="Search"
          placeholder="Search"
          class="border rounded-xl px-4 py-1 m-1"
        /> -->

      <!-- DataTable Ant-design  -->
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
                :href="route('advisors.edit', record.id)"
                class="mr-2"
                >Edit</a-button
              >
              <a-popconfirm title="Are you sure？" ok-text="Yes" cancel-text="No" @confirm="destroy(record.id)">
    <a-button
       size="small"
       v-if="record.delete"
       danger
       >
       <!-- @click="destroy(record.id)" -->
       Delete</a-button>
  </a-popconfirm>

              <!-- v-if="item.delete && can['delete']" -->
            </template>
          </template>
        </a-table>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { Button, Table, Select, InputSearch ,Popconfirm } from "ant-design-vue";

export default {
  components: {
    AppLayout,
    "a-button": Button,
    "a-table": Table,
    "a-select": Select,
    "a-inputSearch": InputSearch,
    "a-popconfirm": Popconfirm,
},

  props: {
    mapped_data: Object,
    filters: Object,
  },

  data() {
    return {
      columns: [
        {
          title: "Name",
          dataIndex: "name",
          width: "30%",
        },
        {
          title: "Address",
          dataIndex: "address",
        },
        {
          title: "Actions",
          dataIndex: "actions",
          key: "actions",
        },
      ],
      params: {
        search: this.filters.search,
        field: "name",
        direction: "asc",
      },
    };
  },

  //   watch: {
  //     params: {
  //       handler: throttle(function () {
  //         let params = pickBy(this.params);
  //         this.$inertia.get(this.route("advisors"), params, {
  //           replace: true,
  //           preserveState: true,
  //         });
  //       }, 150),
  //       deep: true,
  //     },
  //   },

  methods: {
    destroy(id) {
      this.$inertia.delete(route("advisors.destroy", id));
    },
  },
};
</script>
