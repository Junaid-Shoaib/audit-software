<template>
  <app-layout>
    <template #header>
      <h2 class="header">Create Banks</h2>
    </template>

    <div class="max-w-7xl mx-auto pb-2 sm:px-6 lg:px-8 py-4">
      <Form
        :form="form"
        @submit.prevent="submit"
        :label-col="{ span: 4 }"
        :wrapper-col="{ span: 14 }"
      >
        <FormItem label="Name">
          <Input v-model:value="form.name" placeholder="Enter your name" />

          <div class="text-red-700 px-4 py-2" role="alert" v-if="errors.name">
            {{ errors.name }}
          </div>
        </FormItem>
        <FormItem class="text-right">
          <Button type="primary" @click="submit">Submit</Button>
        </FormItem>
      </Form>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { useForm } from "@inertiajs/inertia-vue3";
import { Form, FormItem, Input, Button } from "ant-design-vue";

export default {
  components: {
    AppLayout,
    Form,
    FormItem,
    Input,
    Button,
  },

  props: {
    errors: Object,
    accounts: Object,
  },

  setup(props) {
    const form = useForm({
      name: null,
      accounts: props.accounts,
    });
    return { form };
  },

  methods: {
    submit() {
      this.$inertia.post(route("banks.store"), this.form);
    },
  },
};
</script>
