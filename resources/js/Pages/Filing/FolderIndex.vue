<template>
  <app-layout>
    <template #header>
      <div class="grid grid-cols-2 items-center">
        <h2 class="header">Execution Directory</h2>
        <div class="justify-end">
          <multiselect
            style="width: 50%; z-index: 10"
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

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-1">
      <jet-button type="button" @click="createFolder" class="buttondesign"
        >Create Folder</jet-button
      >

      <div class="">
        <div class="obsolute sm:rounded-2xl">
          <table class="table2">
            <thead>
              <tr class="tablerowhead">
                <th class="py-1 px-4 rounded-l-2xl">Folder Name</th>
                <th class="py-1 px-4 rounded-r-2xl">Action</th>
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
                <td class="w-4/12px-4 border w-2/6 text-center rounded-r-2xl">
                  <button
                    class="
                      border
                      bg-indigo-300
                      rounded-md
                      px-4
                      text-white
                      font-bold
                      hover:text-white hover:bg-indigo-400
                    "
                    @click="viewFolder(item.id)"
                    type="button"
                  >
                    <span>View</span>
                  </button>
                  <button
                    class="deletebutton px-4"
                    @click="deleteFileFolder(item.id)"
                    type="button"
                  >
                    <span>Delete</span>
                  </button>
                </td>
              </tr>
              <tr v-if="balances.data.length === 0">
                <td class="border-t px-6 py-4" colspan="4">No Record found.</td>
              </tr>
            </tbody>
          </table>
        </div>
        <paginator class="mt-6" :balances="balances" />
      </div>
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

  props: {
    selected_folder: Object,
    balances: Object,
    companies: Object,
    company: Object,
  },

  data() {
    return {
      co_id: this.company,
      options: this.companies,
      selected_folder2: this.selected_folder,
    };
  },

  setup(props) {
    const form = useForm({});
    return { form };
  },

  methods: {
    createFolder() {
      this.$inertia.get(route("filing.createFolder"));
    },

    viewFolder(id) {
      this.$inertia.get(route("filing", id));
    },

    deleteFileFolder: function (id) {
      if (confirm("Are you Sure want to Delete this Folder")) {
        this.$inertia.get(route("filing.deleteFileFolder", id));
      }
    },

    coch() {
      this.$inertia.get(route("companies.coch", this.co_id["id"]));
    },
  },
};
</script>
