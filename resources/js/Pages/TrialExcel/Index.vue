<template>
  <app-layout>
    <template #header>
      <div class="grid grid-cols-2 items-center">
        <h2 class="header">Upload TB in Excel</h2>
      </div>
    </template>

    <!-- <FlashMessage /> -->

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-2">
      <!-- <jet-button @click="create" class="ml-2">Create Account</jet-button> -->

      <div class="">
        <!-- overflow-x-auto -->
        <div class="obsolute sm:rounded-2xl">
          <div
            class="
              bg-red-100
              border border-red-400
              text-red-700
              px-4
              py-1
              rounded
              obsolute
            "
            role="alert"
            v-if="errors.file"
          >
            {{ errors.file }}
          </div>

          <a-form
            :form="form"
            @submit.prevent="submit"
            :label-col="{ span: 2 }"
            :wrapper-col="{ span: 24 }"
          >
            <!-- :label-col="{ span: 2 }"
            :wrapper-col="{ span: 14 }" -->
            <a-form-item>
              <a-input type="file" v-on:change="onFileChange" />
            </a-form-item>
            <a-form-item class="text-center">
              <a-button type="primary" :html-type="submit">Submit</a-button>
              <a class="ant-btn ant-btn-primary mx-1" href="/trialpattern"
                >Download TB Template</a
              >
              <a class="ant-btn ant-btn-primary" href="/lead">Lead Schedule</a>
            </a-form-item>
          </a-form>

          <!-- Old upload form -->
          <!-- <form @submit.prevent="submit">
            <div class="flex flex-wrap">
              <input
                class="
                  border-solid border-2 border-indigo-300
                  rounded-xl
                  px-2
                  py-1
                "
                type="file"
                v-on:change="onFileChange"
              />

              <button class="trailbutton" type="submit">Upload TB</button>
              <a class="trailbutton items-center" href="/trialpattern"
                >Download TB Template</a
              >

              <a class="trailbutton" href="/lead">Lead Schedule</a>

              <
            </div>
          </form> -->
        </div>
        <!-- <paginator class="mt-6" :balances="balances" /> -->
      </div>
    </div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-1">
      <div class="p-2 bg-gray-200 text-center rounded-xl">
        <h2 class="header">Download Materiality Schedule</h2>
      </div>
      <div class="max-w-7xl m-4 sm:px-6 lg:px-8">
        <div class="">
          <!-- target="_blank" -->
          <a-form
            :form="form"
            @submit.prevent="submit_materiality"
            v-bind:action="'/materiality-download'"
            ref="form_materiality"
            :label-col="{ span: 4 }"
            :wrapper-col="{ span: 14 }"
          >
            <a-form-item label="Pre Tax Income :">
              <a-input
                type="text"
                name="preTax"
                class="text-center pr-2 pb-2 w-full rounded-md"
                value="5"
              >
              </a-input>
            </a-form-item>
            <a-form-item label="Total Assets :">
              <a-input
                type="text"
                name="tAsset"
                class="text-center pr-2 pb-2 w-full rounded-md"
                value="0.5"
              ></a-input>
            </a-form-item>
            <a-form-item label="Equity :">
              <a-input
                type="text"
                name="equity"
                class="text-center pr-2 pb-2 w-full rounded-md"
                value="1"
              ></a-input>
            </a-form-item>
            <a-form-item label="Total Net Revenues :">
              <a-input
                type="text"
                name="tAsset"
                class="text-center pr-2 pb-2 w-full rounded-md"
                value="0.5"
              ></a-input>
            </a-form-item>
            <!-- <div class="p-2 flex flex-wrap">
              <table class="table2">
                <tr class="tablerowhead bg-gray-700 text-white">
                  <th class="py-1 px-4 rounded-l-md">Particular</th>
                  <th class="py-1 px-4 rounded-r-md">Percentage %</th>
                </tr>
                <tr>
                  <td>
                    <a-form-item>
                      <a-input value="Pre Tax Income" disabled></a-input>
                    </a-form-item>
                  </td>
                  <td>
                    <a-form-item>
                      <a-input
                        type="text"
                        name="preTax"
                        class="text-center pr-2 pb-2 w-full rounded-md"
                        value="5"
                      >
                      </a-input>
                    </a-form-item>
                  </td>
                </tr>

                <tr>
                  <td>
                    <a-form-item>
                      <a-input value="Total Assets" disabled></a-input>
                    </a-form-item>
                  </td>
                  <td>
                    <a-form-item>
                      <a-input
                        type="text"
                        name="tAsset"
                        class="text-center pr-2 pb-2 w-full rounded-md"
                        value="0.5"
                      ></a-input>
                    </a-form-item>
                  </td>
                </tr>
                <tr>
                  <td>
                    <a-form-item>
                      <a-input value="Equity" disabled></a-input>
                    </a-form-item>
                  </td>
                  <td>
                    <a-form-item>
                      <a-input
                        type="text"
                        name="equity"
                        class="text-center pr-2 pb-2 w-full rounded-md"
                        value="1"
                      ></a-input>
                    </a-form-item>
                  </td>
                </tr>
                <tr>
                  <td>
                    <a-form-item>
                      <a-input value="Total Net Revenues" disabled></a-input>
                    </a-form-item>
                  </td>
                  <td>
                    <a-form-item>
                      <a-input
                        type="text"
                        name="netRevenue"
                        class="text-center pr-2 pb-2 w-full rounded-md"
                        value="0.5"
                      ></a-input>
                    </a-form-item>
                  </td>
                </tr>
              </table>
            </div> -->
            <a-form-item class="text-right">
              <a-button type="primary" :html-type="submit">Download</a-button>
            </a-form-item>
          </a-form>

          <!-- <form
            @submit.prevent="submit_materiality"
            v-bind:action="'materiality-download'"
            ref="form_materiality"
          >
            <div class="p-2 flex flex-wrap">
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
            <button class="float-right trailbutton px-4" type="submit">
              Download
            </button>
          </form> -->
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { Form, Input, Button, DatePicker } from "ant-design-vue";
import FlashMessage from "@/Layouts/FlashMessage";
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
    "a-form": Form,
    "a-form-item": Form.Item,
    "a-input": Input,
    "a-button": Button,
    "a-date-picker": DatePicker,
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
