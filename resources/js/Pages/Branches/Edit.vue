<template>
  <app-layout>
    <template #header>
      <h2 class="header">Update Bank Branches</h2>
    </template>

    <div class="max-w-7xl mx-auto pb-2 sm:px-6 lg:px-8 py-4">
      <Form
        :form="form"
        @submit.prevent="submit"
        :label-col="{ span: 4 }"
        :wrapper-col="{ span: 14 }"
      >
        <FormItem label="Branch">
          <Textarea
            v-model:value="form.address"
            placeholder="Enter branch address"
          />
          <div
            class="text-red-700 px-4 py-2"
            role="alert"
            v-if="errors.address"
          >
            {{ errors.address }}
          </div>
        </FormItem>
        <FormItem class="text-right">
          <Button type="primary" @click="submit">Update Branch</Button>
        </FormItem>
      </Form>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { Form, FormItem, Textarea, Input, Button } from "ant-design-vue";

export default {
  components: {
    AppLayout,
    Form,
    FormItem,
    Textarea,
    Button,
  },

  props: {
    errors: Object,
    branch: Object,
    banks: Object,
  },

  data() {
    return {
      form: {
        bank_id: this.branch.bank_id,
        address: this.branch.address,
      },
    };
  },

  methods: {
    submit() {
      this.$inertia.put(route("branches.update", this.branch.id), this.form);
    },
  },
};
</script>
