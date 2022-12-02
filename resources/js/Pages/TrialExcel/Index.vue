<template>
  <app-layout>
    <template #header>
      <div class="grid grid-cols-2 items-center">
        <h2 class="header">Upload Trial in Excel</h2>
      </div>
    </template>

    <FlashMessage />

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-2">
      <!-- <jet-button @click="create" class="ml-2">Create Account</jet-button> -->

      <div class="">
        <!-- overflow-x-auto -->
        <div class="obsolute mt-2 ml-2 sm:rounded-2xl">
          <div
            class="
              ml-2
              bg-red-100
              border border-red-400
              text-red-700
              px-4
              py-2
              rounded
              obsolute
            "
            role="alert"
            v-if="errors.file"
          >
            {{ errors.file }}
          </div>
          <form @submit.prevent="submit">
            <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
              <input
                class="
                  border-solid border-2 border-indigo-300
                  rounded-xl
                  px-2
                  py-2
                  m-4
                "
                type="file"
                v-on:change="onFileChange"
              />
              <!-- <progress
                v-if="form2.progress"
                :value="form2.progress.percentage"
                max="100"
              >
                {{ form2.progress.percentage }}%
              </progress> -->
              <button class="trailbutton" type="submit">
                Upload Trial Balance
              </button>
              <a class="trailbutton py-3" href="/trialpattern"
                >Download Trail Template</a
              >

              <a class="trailbutton py-3" href="/lead">Lead Schedule</a>

              <!-- <button
                class="border bg-indigo-300 rounded-xl px-4 py-2 m-4"
                type="button"
              >
                Download Trail Template
              </button> -->
            </div>
          </form>
        </div>
        <!-- <paginator class="mt-6" :balances="balances" /> -->
      </div>
    </div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-2">
      <div class="p-2 grid grid-cols-2 bg-gray-200 items-center">
        <h2 class="header">Download Materiality Report</h2>
      </div>
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-2">
        <div class="">
          <!-- target="_blank" -->
          <form
            @submit.prevent="submit_materiality"
            v-bind:action="'materiality-download'"
            ref="form_materiality"
          >
            <div class="p-4 -mr-6 -mb-8 flex flex-wrap">
              <table class="table2">
                <tr class="tablerowhead bg-gray-700 text-white">
                  <th class="py-1 px-4 rounded-l-md">Particular</th>
                  <th class="py-1 px-4 rounded-r-md">Percentage %</th>
                </tr>
                <tr>
                  <td>
                    <input
                      type="text"
                      value="Pre Tax Income"
                      class="pr-2 pb-2 w-full rounded-md"
                      disabled
                    />
                  </td>
                  <td>
                    <input
                      type="text"
                      name="preTax"
                      class="text-center pr-2 pb-2 w-full rounded-md"
                      value="5"
                    />
                  </td>
                </tr>

                <tr>
                  <td>
                    <input
                      type="text"
                      value="Total Assets"
                      class="pr-2 pb-2 w-full rounded-md"
                      disabled
                    />
                  </td>
                  <td>
                    <input
                      type="text"
                      name="tAsset"
                      class="text-center pr-2 pb-2 w-full rounded-md"
                      value="0.5"
                    />
                  </td>
                </tr>
                <tr>
                  <td>
                    <input
                      type="text"
                      value="Equity"
                      class="pr-2 pb-2 w-full rounded-md"
                      disabled
                    />
                  </td>
                  <td>
                    <input
                      type="text"
                      name="equity"
                      class="text-center pr-2 pb-2 w-full rounded-md"
                      value="1"
                    />
                  </td>
                </tr>
                <tr>
                  <td>
                    <input
                      type="text"
                      value="Total Net Revenues"
                      class="pr-2 pb-2 w-full rounded-md"
                      disabled
                    />
                  </td>
                  <td>
                    <input
                      type="text"
                      name="netRevenue"
                      class="text-center pr-2 pb-2 w-full rounded-md"
                      value="0.5"
                    />
                  </td>
                </tr>
              </table>
            </div>
            <button class="float-right trailbutton" type="submit">
              Donwload
            </button>
          </form>
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
import { throttle } from "lodash";
import Multiselect from "@suadelabs/vue3-multiselect";
import { useForm } from "@inertiajs/inertia-vue3";

export default {
  setup() {
    const form = useForm({
      file: null,
    });
    return { form };
  },

  components: {
    AppLayout,
    FlashMessage,
    // Treeselect,
  },

  props: {
    errors: Object,
    data: Object,
    fold: Object,
    show_folder: Boolean,
    show_upload: Boolean,
    show_groups: Boolean,
  },

  data() {
    return {
      value: null,
      form_mt: {
        preTax: null,
        tAsset: null,
        equity: null,
        netRevenue: null,
      },
    };
  },

  methods: {
    submit_materiality: function () {
      this.$refs.form_materiality.submit();
    },
    submit() {
      this.form.post(route("trial.read"));
    },

    onFileChange(e) {
      var files = e.target.files || e.dataTransfer.files;
      if (!files.length) return;
      this.form.file = files[0];
    },
  },
};
</script>
