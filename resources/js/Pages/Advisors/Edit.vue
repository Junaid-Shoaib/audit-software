<template>
  <app-layout>
    <template #header>
      <h2>Update Advisor</h2>
    </template>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
      <div class="">
        <a-form
          :form="form"
          @submit.prevent="submit"
          :label-col="{ span: 4 }"
          :wrapper-col="{ span: 14 }"
        >
          <!-- <div
            class="
              px-4
              py-2
              bg-gray-100
              border-t border-gray-200
              flex
              justify-start
              items-center
            "
          >
            <inertia-link
              class="
                border
                rounded-xl
                px-4
                py-1
                m-1
                bg-blue-400
                hover:text-white hover:bg-blue-600
              "
              :href="route('advisors')"
              >Back
            </inertia-link>
          </div> -->
          <a-form-item>
            <a-button type="button" :href="route('advisors')">Back</a-button>
          </a-form-item>
          <a-form-item label="Name">
            <a-input v-model:value="form.name" placeholder="Enter your name" />

            <div class="text-red-700 px-4 py-2" role="alert" v-if="errors.name">
              {{ errors.name }}
            </div>
          </a-form-item>
          <a-form-item label="Address ">
            <a-text-area
              v-model:value="form.address"
              placeholder="Enter your address"
            />

            <div
              class="text-red-700 px-4 py-2"
              role="alert"
              v-if="errors.address"
            >
              {{ errors.address }}
            </div>
          </a-form-item>
          <!-- <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="w-28 inline-block text-right mr-4">Name:</label>
            <input
              type="text"
              v-model="form.name"
              class="
                uppercase
                pr-2
                pb-2
                w-full
                lg:w-1/4
                rounded-md
                leading-tight
              "
              label="name"
            />
            <div v-if="errors.name">{{ errors.name }}</div>
          </div> -->
          <!-- <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="w-28 inline-block text-right mr-4">Address:</label>
            <textarea
              v-model="form.address"
              placeholder="Enter Your Address"
              rows="4"
              cols="100"
              class="
                pr-2
                pb-2
                w-full
                lg:w-1/4
                rounded-md
                leading-tight
                text-transform:
                capitalize
              "
              label="address"
            ></textarea>
            <div v-if="errors.address">{{ errors.address }}</div>
          </div> -->
          <!-- <div class="p-2 mr-2 mb-2 ml-6 flex flex-wrap">
            <label class="w-28 inline-block text-right mr-4">Type:</label>
            <select
              v-model="form.type"
              class="pr-2 pb-2 w-full lg:w-1/4 rounded-md leading-tight"
              label="type"
            >
              <option selected value="tax">Tax Advisor</option>
              <option value="legal">Legal Advisor</option>
            </select>
            <div v-if="errors.type">{{ errors.type }}</div>
          </div> -->

          <a-form-item label="Type">
            <a-select
              v-model:value="form.type"
              :options="options"
              :field-names="{ label: 'name', value: 'value' }"
              optionFilterProp="name"
              mode="single"
              placeholder="Please select"
              showArrow
              class="w-full"
            />

            <div
              class="text-red-700 px-4 py-2"
              role="alert"
              v-if="errors.fiscal"
            >
              {{ errors.fiscal }}
            </div>
          </a-form-item>

          <!-- <div
            class="
              px-4
              py-2
              bg-gray-100
              border-t border-gray-200
              flex
              justify-start
              items-center
            "
          > -->
          <a-form-item class="text-right">
            <a-button type="primary" :htmlType="submit">
              Update Advisor
            </a-button>
          </a-form-item>
          <!-- </div> -->
        </a-form>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { Form, Input, Button, Select } from "ant-design-vue";

export default {
  components: {
    AppLayout,
    "a-form": Form,
    "a-form-item": Form.Item,
    "a-input": Input,
    "a-text-area": Input.TextArea,
    "a-button": Button,
    "a-select": Select,
  },

  props: {
    errors: Object,
    advisor: Object,
  },

  data() {
    return {
      options: [
        { name: "Legal Advisor", value: "legal" },
        { name: "Tax Advisor", value: "tax" },
      ],
      form: {
        name: this.advisor.name,
        address: this.advisor.address,
        type: this.advisor.type,
      },
    };
  },

  methods: {
    submit() {
      this.$inertia.put(route("advisors.update", this.advisor.id), this.form);
    },
  },
};
</script>
