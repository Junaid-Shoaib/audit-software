<template>
  <app-layout>
    <template #header>
      <h2 class="header">Year</h2>
    </template>
    <div class="">
      <form @submit.prevent="submit">
        <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
          <label class="my-2 mr-8 ml-40 text-right w-36 font-bold"
            >Begin Date :</label
          >
          <input
            type="date"
            v-model="form.begin"
            label="date"
            placeholder="Enter Begin date:"
            class="pr-2 pb-2 rounded-md hover:transition hover:ease-in-out transform hover:scale-110 ease-out duration-200 placeholder-indigo-300"
          />
          <div v-if="errors.begin">{{ errors.begin }}</div>
        </div>

        <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
          <label class="my-2 mr-8 ml-40 text-right w-36 font-bold">End Date :</label>
          <input
            type="date"
            v-model="form.end"
            class="pr-2 pb-2 rounded-md hover:transition hover:ease-in-out transform hover:scale-110 ease-out duration-200 placeholder-indigo-300"
            label="date"
            placeholder="Enter End date:"
          />
          <div v-if="errors.end">{{ errors.end }}</div>
        </div>
        <div
          class="
            px-4
            py-2
            flex
            ml-60
            items-center
          "
        >
          <button
            class="
              submitbutton
              p-1
              px-4
              mt-1
              ml-32
              inline-block
             "
            type="submit"
          >
            Update Year
          </button>
        </div>
      </form>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
// import Datepicker from "vue3-datepicker";
// import format from "date-fns/format";
import Input from "../../Jetstream/Input.vue";

export default {
  components: {
    AppLayout,
    // Datepicker,
    Input,
  },

  props: {
    errors: Object,
    year: Object,
  },

  data() {
    return {
      form: {
        begin: this.year.begin,
        end: this.year.end,
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
