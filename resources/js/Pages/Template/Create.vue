<template>
  <app-layout>
    <template #header>
      <h2 class="header">Create Template</h2>
    </template>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
      <div class="">
        <Form
          :form="form"
          @submit.prevent="submit"
          :labelCol="{ span: 4 }"
          :wrapperCol="{ span: 14 }"
        >
          <FormItem label="File">
            <Input type="file" size="small" v-on:change="onFileChange" />

            <div
              class="text-red-700 px-4 py-2"
              role="alert"
              v-if="errors.avatar"
            >
              {{ errors.avatar }}
            </div>
          </FormItem>
          <FormItem label="Type">
            <!-- optionFilterProp="name" -->
            <Select
              :field-names="{ label: 'name', value: 'name' }"
              v-model:value="form.type"
              :options="types"
              mode="single"
              placeholder="Please select"
              showArrow
              class="w-full"
            />

            <div class="text-red-700 px-4 py-2" role="alert" v-if="errors.type">
              {{ errors.type }}
            </div>
          </FormItem>
          <FormItem class="text-right">
            <Button type="primary" :html-type="submit">Create Template</Button>
          </FormItem>
        </Form>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { useForm } from "@inertiajs/inertia-vue3";
import {
  Form,
  FormItem,
  Input,
  Button,
  Select,
  TreeSelect,
  DatePicker,
} from "ant-design-vue";

export default {
  components: {
    AppLayout,
    Form,
    FormItem,
    Input,
    Button,
    Select,
  },

  props: {
    errors: Object,
    data: Object,
    types: Array,
    // group_first: Object,
  },

  setup(props) {
    const form = useForm({
      avatar: null,
      value: null,
      type: props.types[0],
    });
    return { form };
  },

  methods: {
    submit() {
      //   this.$inertia.post(route("templates.store"), this.form);
      this.form.post(route("templates.store"));
    },

    onFileChange(e) {
      var files = e.target.files || e.dataTransfer.files;
      if (!files.length) return;
      this.form.avatar = files[0];
    },
  },

  data() {
    return {
      types: [
        { name: "Planning" },
        { name: "Completion" },
        { name: "Execution" },
      ],
    };
  },
};
</script>
