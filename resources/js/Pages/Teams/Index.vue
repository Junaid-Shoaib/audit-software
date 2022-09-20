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

    <FlashMessage />

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-2">
      <form @submit.prevent="form.get(route('teams.create'))">
        <jet-button
          v-if="team_exists"
          type="submit"
          @click="create"
          class="ml-2 buttondesign"
          >Edit Team</jet-button
        >
        <jet-button
          v-else
          type="submit"
          @click="create"
          class="ml-2 buttondesign"
          >Add Team</jet-button
        >
        <div class="">
          <div class="obsolute mt-2 ml-2 sm:rounded-2xl">
            <table class="table2">
              <thead>
                <tr class="tablerowhead">
                  <th class="py-1 px-4 rounded-l-2xl">Name</th>
                  <th class="py-1 px-4">Email</th>
                  <th class="py-1 px-4">Role</th>
                  <!-- <th class="py-1 px-4">End</th>
                  <th class="py-1 px-4 rounded-r-2xl">Action</th> -->
                </tr>
              </thead>
              <tbody>
                <tr
                  class="tablerowbody2"
                  v-for="item in balances.data"
                  :key="item.id"
                >
                  <td class="w-4/12 px-4 border rounded-l-2xl w-2/5">
                    {{ item.name }}
                  </td>
                  <td class="w-2/12 px-4 border w-2/6 text-center">
                    {{ item.email }}
                  </td>
                  <td class="w-2/12 px-4 border w-2/6 text-center">
                    {{ item.role }}
                  </td>
                  <!-- <td class="w-2/12 px-4 border w-2/6 text-center">
                    {{ item.end }}
                  </td>
                  <td class="w-4/12px-4 border w-2/6 rounded-r-2xl text-center">
                    <button
                      class="editbutton px-4 m-1"
                      @click="edit(item.id)"
                      type="button"
                    >
                      <span>Edit</span>
                    </button>
                    <button
                      class="deletebutton px-4 m-1"
                      @click="destroy(item.id)"
                      type="button"
                      v-if="item.delete"
                    >
                      <span>Delete</span>
                    </button>
                    <button
                      v-if="item.closed == 0"
                      class="
                        border
                        bg-gray-600
                        text-white
                        font-bold
                        rounded-xl
                        px-4
                        m-1
                        hover:bg-gray-700
                      "
                      @click="close(item.id)"
                      type="button"
                    >
                      <span>Close Fiscal</span>
                    </button>
                  </td> -->
                </tr>
                <tr v-if="balances.data.length === 0">
                  <td class="border-t px-6 py-4" colspan="4">
                    No Record found.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <paginator class="mt-6" :balances="balances" />
        </div>
      </form>
    </div>
  </app-layout>
</template>

<style src="@suadelabs/vue3-multiselect/dist/vue3-multiselect.css"></style>
<script>
import AppLayout from "@/Layouts/AppLayout";
import JetButton from "@/Jetstream/Button";
import { useForm } from "@inertiajs/inertia-vue3";
import Multiselect from "@suadelabs/vue3-multiselect";
import Paginator from "@/Layouts/Paginator";
import FlashMessage from "@/Layouts/FlashMessage";
// import { Head, Link } from "@inertiajs/inertia-vue3";

export default {
  components: {
    AppLayout,
    JetButton,
    useForm,
    Multiselect,
    Paginator,
    FlashMessage,
    // Link,
    // Head,
  },

  // props: ["data", "companies", "company"],
  props: {
    balances: Object,
    companies: Object,
    company: Object,
    year: Object,
    years: Object,
    team_exists: Object,
  },

  data() {
    return {
      // co_id: this.$page.props.co_id,
      co_id: this.company,
      yr_id: this.year,
      options: this.companies,
      years: this.years,
      team_exists: this.team_exists,
    };
  },

  setup(props) {
    const form = useForm({});
    return { form };
  },

  methods: {
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
    yrch() {
      this.$inertia.get(route("years.yrch", this.yr_id));
    },
  },
};
</script>
