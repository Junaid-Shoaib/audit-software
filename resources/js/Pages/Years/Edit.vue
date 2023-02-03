<template>
  <app-layout>
    <template #header>
      <h2 class="header">Year</h2>
    </template>
    <div class="">
      <a-form
        :form="form"
        @submit.prevent="submit"
        :label-col="{ span: 4 }"
        :wrapper-col="{ span: 14 }"
      >
        <a-form-item label="Begin Date">
          <!-- :locale="form.locale" -->
          <a-date-picker
            class="w-full"
            mode="date"
            v-model:value="form.begin"
          />

          <div class="text-red-700 px-4 py-2" role="alert" v-if="errors.begin">
            {{ errors.begin }}
          </div>
        </a-form-item>

        <a-form-item label="End Date">
          <!-- :locale="form.locale" -->
          <a-date-picker class="w-full" v-model:value="form.end" />

          <div class="text-red-700 px-4 py-2" role="alert" v-if="errors.end">
            {{ errors.end }}
          </div>
        </a-form-item>
        <a-form-item :wrapper-col="{ span: 14, offset: 4 }">
          <a-button type="primary" @click="submit">Update Year</a-button>
        </a-form-item>
      </a-form>
      <!-- <form @submit.prevent="submit">
        <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
          <label class="my-2 mr-8 ml-40 text-right w-36 font-bold"
            >Begin Date :</label
          >
          <input
            type="date"
            v-model="form.begin"
            label="date"
            placeholder="Enter Begin date:"
            class="
              pr-2
              pb-2
              rounded-md
              hover:transition hover:ease-in-out
              transform
              hover:scale-110
              ease-out
              duration-200
              placeholder-indigo-300
            "
          />
          <div v-if="errors.begin">{{ errors.begin }}</div>
        </div>

        <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
          <label class="my-2 mr-8 ml-40 text-right w-36 font-bold"
            >End Date :</label
          >
          <input
            type="date"
            v-model="form.end"
            class="
              pr-2
              pb-2
              rounded-md
              hover:transition hover:ease-in-out
              transform
              hover:scale-110
              ease-out
              duration-200
              placeholder-indigo-300
            "
            label="date"
            placeholder="Enter End date:"
          />
          <div v-if="errors.end">{{ errors.end }}</div>
        </div>
        <div class="px-4 py-2 flex ml-60 items-center">
          <button
            class="submitbutton p-1 px-4 mt-1 ml-32 inline-block"
            type="submit"
          >
            Update Year
          </button>
        </div>
      </form> -->
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import moment, * as moments from "moment";
import { Form, Input, Button, DatePicker } from "ant-design-vue";
// import Datepicker from "vue3-datepicker";
// // import format from "date-fns/format";
// import Input from "../../Jetstream/Input.vue";

export default {
  components: {
    AppLayout,
    "a-form": Form,
    "a-form-item": Form.Item,
    "a-input": Input,
    "a-button": Button,
    "a-date-picker": DatePicker,
    moment,
  },

  props: {
    errors: Object,
    year: Object,
  },

  data() {
    return {
      form: {
        // begin: moment(this.year.begin, "YYYY-MM-DD"),
        // end: moment(this.year.end, "YYYY-MM-DD"),
        begin: null,
        end: null,
      },
    };
  },

  methods: {
    submit() {
      this.$inertia.put(route("years.update", this.year.id), this.form);
    },
  },
};
</script>
