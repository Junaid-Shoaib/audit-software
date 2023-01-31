<template>
  <app-layout>
    <template #header>
      <div class="grid grid-cols-3 items-center">
        <h2 class="header">Team</h2>
        <div class="justify-end">
          <multiselect
            style="width: 90%; z-index: 10"
            class="float-right rounded-md border border-black"
            placeholder="Select Year."
            v-model="yr_id"
            track-by="id"
            label="end"
            :options="years"
            @update:model-value="yrch"
          >
          </multiselect>
        </div>
        <div class="justify-end">
          <multiselect
            style="width: 90%; z-index: 10"
            class="float-right rounded-md border border-black"
            placeholder="Select Company."
            v-model="co_id"
            track-by="id"
            label="name"
            :options="options"
            @update:model-value="coch"
          >
          </multiselect>
        </div>
      </div>
    </template>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-1">
      <Button v-if="team_exists" @click="edit" class="ml-2" size="small"
        >Edit your Team</Button
      >
      <Button v-else @click="create" class="ml-2" size="small">Add Team</Button>
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
import { useForm } from "@inertiajs/inertia-vue3";
import Multiselect from "@suadelabs/vue3-multiselect";

export default {
  components: {
    AppLayout,
    Button,
    Table,
    Select,
    InputSearch,

    useForm,
    Multiselect,
  },

  props: {
    balances: Object,
    companies: Object,
    company: Object,
    year: Object,
    years: Object,
    team_exists: Object,
    mapped_data: Object,
  },

  data() {
    return {
      // co_id: this.$page.props.co_id,
      co_id: this.company,
      yr_id: this.year,
      options: this.companies,
      years: this.years,
      team_exists: this.team_exists,
      columns: [
        {
          title: "Name",
          dataIndex: "name",
          width: "20%",
        },
        {
          title: "Email",
          dataIndex: "email",
        },
        {
          title: "Role",
          dataIndex: "role",
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
      this.$inertia.get(route("teams.create"));
    },

    edit() {
      this.$inertia.get(route("teams.edit"));
    },

    coch() {
      this.$inertia.get(route("companies.coch", this.co_id["id"]));
    },
    yrch() {
      this.$inertia.get(route("years.yrch", this.yr_id));
    },
  },
};
</script>
