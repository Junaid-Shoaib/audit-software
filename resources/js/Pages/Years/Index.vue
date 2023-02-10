<template>
  <app-layout>
    <template #header>
      <h2 class="header">Years</h2>
    </template>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-1">
      <form @submit.prevent="form.get(route('years.create'))">
        <Button @click="create" size="small">Add Year</Button>
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
                type="primary"
                @click="edit(record.id)"
                class="mr-2"
                >Edit</Button
              >
              <Button
                v-if="record.delete"
                class="mr-2"
                size="small"
                danger
                @click="destroy(record.id)"
                >Delete</Button
              >
            </template>
          </template>
        </Table>
      </form>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { Button, Table, Select, InputSearch } from "ant-design-vue";
import { useForm } from "@inertiajs/inertia-vue3";

export default {
  components: {
    AppLayout,
    // "a-button": Button,
    // "a-table": Table,
    // "a-select": Select,
    // "a-inputSearch": InputSearch,
    Button,
    Table,
    Select,
    InputSearch,
    useForm,
  },

  props: {
    balances: Object,
    companies: Object,
    company: Object,
    mapped_data: Object,
  },

  data() {
    return {
      // co_id: this.$page.props.co_id,
      co_id: this.company,
      options: this.companies,
      columns: [
        {
          title: "Company",
          dataIndex: "company_name",
          width: "20%",
        },
        {
          title: "Begin",
          dataIndex: "begin",
        },
        {
          title: "End",
          dataIndex: "end",
        },
        {
          title: "Actions",
          dataIndex: "actions",
          key: "actions",
        },
      ],
    };
  },

  setup(props) {
    const form = useForm({});
    return { form };
  },

  methods: {
    create() {
      this.$inertia.get(route("years.create"));
    },

    edit(id) {
      this.$inertia.get(route("years.edit", id));
    },

    destroy(id) {
      this.$inertia.delete(route("years.destroy", id));
    },

    close(id) {
      this.$inertia.get(route("years.close", id));
    },

    coch() {
      this.$inertia.get(route("companies.coch", this.co_id["id"]));
    },
  },
};
</script>
