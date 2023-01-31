<template>
  <app-layout>
    <template #header>
      <h2 class="header">Edit Team</h2>
    </template>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
      <div class="">
        <Form :form="form" @submit.prevent="submit">
          <FormItem label="Partner">
            <Select
              v-model:value="form.partner"
              :options="partners"
              :field-names="{ label: 'name', value: 'id' }"
              filterOption="true"
              optionFilterProp="name"
              mode="multiple"
              placeholder="Please select"
              showArrow
              class="w-full"
            />
            <div
              class="text-red-700 px-4 py-2"
              role="alert"
              v-if="errors.partner"
            >
              {{ errors.partner }}
            </div>
          </FormItem>

          <FormItem label="Manager">
            <Select
              v-model:value="form.manager"
              :options="managers"
              :field-names="{ label: 'name', value: 'id' }"
              filterOption="true"
              optionFilterProp="name"
              mode="multiple"
              placeholder="Please select"
              showArrow
              class="w-full"
            />
            <div
              class="text-red-700 px-4 py-2"
              role="alert"
              v-if="errors.manager"
            >
              {{ errors.manager }}
            </div>
          </FormItem>

          <FormItem label="Staff">
            <Select
              v-model:value="form.staff"
              :options="staff"
              :field-names="{ label: 'name', value: 'id' }"
              filterOption="true"
              optionFilterProp="name"
              mode="multiple"
              placeholder="Please select"
              showArrow
              class="w-full"
            />
            <div
              class="text-red-700 px-4 py-2"
              role="alert"
              v-if="errors.staff"
            >
              {{ errors.staff }}
            </div>
          </FormItem>

          <FormItem>
            <Button type="primary" @click="submit">Update Team</Button>
          </FormItem>
        </Form>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Multiselect from "@suadelabs/vue3-multiselect";
import { Form, FormItem, Select, Button } from "ant-design-vue";

export default {
  components: {
    AppLayout,
    Multiselect,
    Form,
    FormItem,
    Select,
    Button,
  },

  props: {
    errors: Object,
    partners: Object,
    managers: Object,
    staff: Object,
    partner: Object,
    manager: Object,
    staf: Object,
  },

  data() {
    return {
      form: this.$inertia.form({
        partners: this.partners,
        managers: this.managers,
        staff: this.staff,

        partner: this.partner.map((item) => item.id),
        manager: this.manager.map((item) => item.id),
        // staff: this.staf,
        staff: this.staf.map((item) => item.id),
      }),
    };
  },

  methods: {
    submit() {
      this.$inertia.put(route("teams.update"), this.form);
    },
  },
};
</script>
