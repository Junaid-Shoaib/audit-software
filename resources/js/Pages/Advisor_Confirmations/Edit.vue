<template>
  <app-layout>
    <template #header>
      <h2>
        Update Advisor Confirmations
      </h2>
    </template>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
    <a-form-item style="margin-bottom:10px">
        <a-button  :href="route('advisor_confirmations')">Back </a-button>
    </a-form-item>

      <div class="relative mt-5 flex-row border-t border-b border-gray-200">
        <a-form :form="form"  @submit.prevent="submit">
          <div class="ant-table-content">
            <table class="w-full">
              <thead class="ant-table-thead">
                <tr>
                  <th style="width:40%" class="ant-table-cell" colstart="0" colend="0">Bank</th>
                  <th style="width:15%" class="ant-table-cell" colstart="0" colend="0">Create Date</th>
                  <th style="width:15%" class="ant-table-cell" colstart="0" colend="0">Sent Date</th>
                  <th style="width:15%" class="ant-table-cell" colstart="0" colend="0">Reminder Date</th>
                  <th style="width:15%" class="ant-table-cell" colstart="0" colend="0">Received Date</th>
                </tr>
              </thead>
              <tbody class="ant-table-tbody">
                <tr class="ant-table-row ant-table-row-level-0"  v-for="confirm in data" :key="confirm.id">
                  <td class="ant-table-cell">
                    <a-form-item style="margin-bottom:0px">
                      <a-input
                        v-model:value="confirm.name"
                        type="text"
                        disabled
                        class="w-full"
                      />
                    </a-form-item>
                  </td>
                  <td class="ant-table-cell">
                    <a-form-item style="margin-bottom:0px">
                        <a-input
                            v-model:value="confirm.confirm_create"
                            type="date"
                            readonly
                            class="w-full"
                        />
                    </a-form-item>
                  </td>

                <td class="ant-table-cell">
                    <a-form-item style="margin-bottom:0px">
                        <a-input
                            v-model:value="confirm.sent"
                            type="date"
                             :min="lower"
                             :max="upper"
                            class="w-full"
                        />
                    </a-form-item>
                  </td>
                  <td class="ant-table-cell">
                    <a-form-item style="margin-bottom:0px">
                        <a-input
                      v-model:value="confirm.reminder"
                      type="date"
                        :min="lower"
                        :max="upper"
                      class="rounded-md w-full my-1"
                    />
                    </a-form-item>
                  </td>
                  <td class="ant-table-cell">
                    <a-form-item style="margin-bottom:0px">
                        <a-input
                      v-model:value="confirm.received"
                      type="date"
                        :min="lower"
                        :max="upper"
                    />
                    </a-form-item>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
            <div>
                <a-form-item style="margin-Top:10px">
                    <a-button  type="primary" htmlType="submit">Update Confirmation</a-button>
                </a-form-item>
            </div>
        </a-form>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { Form, Input, Button, Select ,Table } from "ant-design-vue";

// import Datepicker from "vue3-datepicker";

export default {
  components: {
    AppLayout,
    "a-form": Form,
    "a-table": Table,
    "a-form-item": Form.Item,
    "a-input": Input,
    "a-text-area": Input.TextArea,
    "a-button": Button,
    "a-select": Select,
  },

  props: {
    errors: Object,
    data: Object,
    branches: Object,
    year: Object,
  },

  data() {
    return {
        balances: this.data,
        lower: this.year.begin
            ? this.year.begin
            : null  ,
        upper: this.year.end
            ? this.year.end
            : null,
    };
  },

  methods: {
    submit() {
      this.$inertia.put(
        route("advisor_confirmations.update", this.balances[0]),
        {
          balances: this.balances,
        }
      );
    },



    addRow() {
      this.balances.push({
        sent: null,
        confirm_create: null,
        reminder: null,
        received: null,
      });
    },
    deleteRow(index) {
      this.balances.splice(index, 1);
    },
  },
};
</script>
