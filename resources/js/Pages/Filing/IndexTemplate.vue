<template>
  <app-layout>
    <template #header>
      <div class="grid grid-cols-2 items-center">
        <h2 class="header">{{ type }} Templates</h2>
        <!-- <div class="justify-end">
          <multiselect
            style="width: 50%"
            class="float-right rounded-md border border-black float-right"
            placeholder="Select Company."
            v-model="co_id"
            track-by="id"
            label="name"
            :options="options"
            @update:model-value="coch"
          >
          </multiselect>
        </div> -->
      </div>
    </template>

    <FlashMessage />
    <div
      class="
        ml-2
        bg-red-100
        border border-red-400
        text-red-700
        px-4
        py-1
        rounded
        relative
        text-center
      "
      role="alert"
      v-if="errors.folder"
    >
      {{ errors.folder }}
    </div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-1">
      <div class="flex flex-row items-center">
        <div class="flex-none">
          <input
            type="text"
            class="
              ml-4
              h-8
              px-2
              w-50
              border-gray-800
              ring-gray-800 ring-1
              outline-none
            "
            v-model="params.search"
            @change="search_data"
            aria-label="Search"
            placeholder="Search File Name"
          />
          <button
            @click="search_data"
            class="
              border-2
              pb-2.5
              pt-1
              bg-gray-800
              border-gray-800
              px-1
              hover:bg-gray-700
            "
          >
            <svg
              class="w-8 h-4 text-white"
              fill="currentColor"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 25 20"
            >
              <path
                d="M16.32 14.9l5.39 5.4a1 1 0 0 1-1.42 1.4l-5.38-5.38a8 8 0 1 1 1.41-1.41zM10 16a6 6 0 1 0 0-12 6 6 0 0 0 0 12z"
              />
            </svg>
          </button>

          <input
            hidden
            id="selected"
            @click="checkAll()"
            v-model="isCheckAll"
          />
          <label
            v-if="isCheckAll"
            class="px-2 py-1 ml-2 submitbutton"
            for="selected"
          >
            Un-Select All</label
          >
          <label v-else class="px-2 py-1 ml-2 submitbutton" for="selected">
            Select All</label
          >
        </div>
        <div class="flex-1">
          <form
            class=""
            @submit.prevent="submitValue"
            v-bind:action="'/multiple-template-download'"
            ref="form_range"
          >
            <input hidden v-model="form.selected_arr" name="selected_arr" />
            <multiselect
              v-if="type == 'Execution'"
              style="
                width: 44%;
                min-height: 20px !important;
                padding: 0 8px !important;
                margin-left: 4px;
              "
              class="float-left"
              placeholder="Select Folder."
              v-model="form.folder"
              track-by="id"
              label="name"
              :options="folders"
            >
            </multiselect>

            <button
              class="ml-2 px-2 py-1 submitbutton"
              type="button"
              @click="includeTemps()"
            >
              Include Templates
            </button>
            <button class="ml-2 px-2 py-1 submitbutton" type="submit">
              Download
            </button>
          </form>
        </div>
      </div>
      <div class="">
        <div class="obsolute sm:rounded-2xl">
          <table class="table2">
            <thead>
              <tr class="tablerowhead">
                <!-- <th class="py-2 px-4 border">ID</th> -->
                <th class="py-1 px-4 rounded-l-2xl">Check</th>
                <th class="py-1 px-4">Name of File</th>
                <th class="py-1 px-4 rounded-r-2xl">Name of Folder</th>
                <!-- <th class="py-1 px-4 rounded-r-2xl">Action</th> -->
              </tr>
            </thead>
            <tbody>
              <tr class="tablerowbody2" v-for="item in balances" :key="item.id">
                <td style="width: 10%" class="px-4 border rounded-l-2xl">
                  <input
                    type="checkbox"
                    v-bind:value="item.name"
                    v-model="form.selected_arr"
                    @change="updateCheckall()"
                    name="selected_arr"
                  />
                </td>
                <!-- <td class="py-1 px-4 border text-center">{{ item.id }}</td> -->
                <td style="width: 40%" class="px-4 border">
                  {{ item.name }}
                </td>
                <td
                  style="width: 27%"
                  class="px-4 border text-center rounded-r-2xl"
                >
                  {{ item.type == "planing" ? "Planning" : item.type }}
                </td>
                <!-- <td class=" px-4 border">{{ item.accountGroup.name }}</td> -->
                <!-- <td
                  style="width: 23%"
                  class="px-4 border text-center rounded-r-2xl"
                >
                  <a
                    v-if="item.path"
                    class="
                      border
                      bg-indigo-300
                      rounded-xl
                      px-4
                      text-white
                      font-bold
                      m-1
                      hover:text-white hover:bg-indigo-400
                    "
                    :href="'/template-download/' + item.id"
                    >Download</a
                  >
                  <button
                    class="deletebutton px-4 m-1"
                    @click="destroy(item.id)"
                    v-if="item.delete"
                  >
                    <span>Delete</span>
                  </button>
                </td> -->
              </tr>
              <tr v-if="balances.length == 0">
                <td class="border-t px-6 py-4" colspan="4">No Record found.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import JetButton from "@/Jetstream/Button";
import Paginator from "@/Layouts/Paginator";
import FlashMessage from "@/Layouts/FlashMessage";
import { pickBy } from "lodash";
import { useForm } from "@inertiajs/inertia-vue3";
import { throttle } from "lodash";
import Multiselect from "@suadelabs/vue3-multiselect";

export default {
  components: {
    AppLayout,
    JetButton,
    useForm,
    // Paginator,
    throttle,
    pickBy,
    Multiselect,
    FlashMessage,
  },

  props: {
    type: Object,
    data: Object,
    balances_name: Object,
    balances: Object,
    filters: Object,
    can: Object,
    companies: Array,
    company: Object,
    folders: Object,
    errors: Object,
  },

  data() {
    return {
      selected: [],
      isCheckAll: false,
      //    selectedlang: "",
      co_id: this.company,
      options: this.companies,
      folders: this.folders,
      //   type: this.balances_name,
      form: {
        selected_arr: [],
        folder: null,
        type: this.type,
      },
      params: {
        search: this.filters.search,
      },
    };
  },

  methods: {
    checkAll: function () {
      this.isCheckAll = !this.isCheckAll;
      this.form.selected_arr = [];
      if (this.isCheckAll) {
        // Check all
        for (var key in this.balances_name) {
          this.form.selected_arr.push(this.balances_name[key]);
        }
      }
    },
    updateCheckall: function () {
      if (this.form.selected_arr.length == this.balances_name.length) {
        this.isCheckAll = true;
      } else {
        this.isCheckAll = false;
      }
    },
    submitValue: function () {
      this.$refs.form_range.submit();
    },

    includeTemps: function () {
      this.$inertia.post(route("include_templates"), this.form);
    },

    sort(field) {
      this.params.field = field;
      this.params.direction = this.params.direction === "asc" ? "desc" : "asc";
    },
    search_data() {
      let params = pickBy(this.params);
      this.$inertia.get(this.route("index_temp", this.type), params, {
        replace: true,
        preserveState: true,
      });
    },
  },
  watch: {
    params: {
      handler: throttle(function () {
        let params = pickBy(this.params);
        if (params.search == null) {
          this.$inertia.get(this.route("index_temp", this.type), params, {
            replace: true,
            preserveState: true,
          });
        }
      }, 150),
      deep: true,
    },
  },
};
</script>
