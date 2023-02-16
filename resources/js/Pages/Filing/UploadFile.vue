<template>
  <app-layout>
    <template #header>
      <h2 class="header">Upload File in {{ parent.name }}</h2>
    </template>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-2">
      <Form :form="form" @submit.prevent="submit" layout="inline">
        <FormItem>
          <Input type="file" size="small" v-on:change="onFileChange" />
          <div class="text-red-700 px-4 py-2" role="alert" v-if="errors.avatar">
            {{ errors.avatar }}
          </div>
        </FormItem>
        <FormItem>
          <Button type="primary" :html-type="submit">Upload File</Button>
        </FormItem>
      </Form>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { Form, FormItem, Input, Button, DatePicker } from "ant-design-vue";
import { useForm } from "@inertiajs/inertia-vue3";

export default {
  setup() {
    const form = useForm({
      avatar: null,
    });
    return { form };
  },

  components: {
    AppLayout,
    Form,
    FormItem,
    Input,
    Button,
  },

  props: {
    errors: Object,
    data: Object,
    fold: Object,
    parent: Object,
    show_folder: Boolean,
    show_upload: Boolean,
    show_groups: Boolean,
  },

  data() {
    return {
      value: null,
      parent_id: this.parent.id,
    };
  },

  methods: {
    submit() {
      this.form.post(route("filing.store.file", this.parent.id));
    },

    onFileChange(e) {
      var files = e.target.files || e.dataTransfer.files;
      if (!files.length) return;
      this.form.avatar = files[0];
      this.form.parent_id = this.parent.id;
    },
  },
};
</script>
