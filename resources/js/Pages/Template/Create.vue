<template>
  <app-layout>
  <template #header>
      <h2 class="header">Create Template</h2>
    </template>
    <div v-if="$page.props.flash.success" class="bg-yellow-600 text-white">
      {{ $page.props.flash.success }}
    </div>

   <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
      <div class="">
        <form @submit.prevent="form.post(route('templates.store'))">
         <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 ml-40 text-right w-36 font-bold">File :</label>
           <input class="border-solid border-2 border-indigo-300 rounded-xl px-6 py-2 m-4" type="file" v-on:change="onFileChange" />
            <div
              class="
                ml-2
                bg-red-100
                border border-red-400
                text-red-700
                px-4
                py-2
                rounded
                relative
              "
              role="alert"
              v-if="errors.avatar"
            >
              {{ errors.avatar }}
            </div>
          </div>
          <div class="p-2 mr-2 mb-2 ml-6 flex flex-wrap">
            <label class="my-2 ml-40 text-right w-36 mr-8 font-bold">Type :</label>
            <select
              v-model="form.type"
              class="
                pr-2
                pb-2
                w-full
                lg:w-1/4
                rounded-md
                hover:transition hover:ease-in-out transform hover:scale-110 ease-out duration-200
                placeholder-indigo-300
              "
              label="ficsal"
              placeholder="Select Fiscal:"
            >
              <option v-for="type in types" :key="type" :value="type">
                {{ type }}
              </option>
            </select>
            <div v-if="errors.type">{{ errors.type }}</div>
          </div>

          <!-- <div class="p-2 mr-2 mb-2 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 text-right w-36 font-bold"
              >Account Group :</label
            >
            <treeselect
              v-model="form.group"
              max-height="150"
              :multiple="false"
              :options="option"
              :normalizer="normalizer"
              v-on:select="treeChange"
              style="max-width: 300px"
            />
            <div
              class="
                ml-2
                bg-red-100
                border border-red-400
                text-red-700
                px-4
                py-2
                rounded
                relative
              "
              role="alert"
              v-if="errors.group"
            >
              {{ errors.group }}
            </div>
          </div> -->
          <div
            class="
              px-4
              py-2
              flex
              justify-center
              items-center
            "
          >
            <button
              class="
                submitbutton
                p-1
                px-4
                mt-1
                ml-2
                inline-block
                mr-3
              "
              type="submit"
              :disabled="form.processing"
            >
              Create Template
            </button>
          </div>
        </form>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { useForm } from "@inertiajs/inertia-vue3";
import Multiselect from "@suadelabs/vue3-multiselect";

export default {
  components: {
    AppLayout,
    Multiselect,
    // Treeselect,
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
      type: props.types[0],
    });

    return { form };
  },

methods: {
    onFileChange(e) {
      var files = e.target.files || e.dataTransfer.files;
      if (!files.length) return;
      this.form.avatar = files[0];
    },
},

  //   data() {
  //     return {
  //       form: this.$inertia.form({
  //         name: null,
  //         number: null,
  //         group: this.group_first.id,
  //       }),
  //     };
  //   },

  //   methods: {
  //     submit() {
  //       this.$inertia.post(route("accounts.store"), this.form);
  //     },
  //   },
};
</script>
