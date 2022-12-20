<template>
  <app-layout>
    <template #header>
      <div class="grid grid-cols-2 items-center">
        <h2 class="header">Upload File in {{ parent.name }}</h2>
      </div>
    </template>
    <FlashMessage />
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-2">
      <!-- <jet-button @click="create" class="ml-2">Create Account</jet-button> -->

      <div class="">
        <div class="relative mt-2 ml-2 sm:rounded-2xl">
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
          <form @submit.prevent="submit">
            <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
              <input
                class="
                  border-solid border-2 border-indigo-300
                  rounded-xl
                  px-6
                  py-1
                  mr-2
                "
                type="file"
                v-on:change="onFileChange"
              />
              <!-- <progress
                v-if="form2.progress"
                :value="form2.progress.percentage"
                max="100"
              >
                {{ form2.progress.percentage }}%
              </progress> -->
              <button class="trailbutton" type="submit">Upload File</button>
            </div>
          </form>
        </div>
        <!-- <paginator class="mt-6" :balances="balances" /> -->
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import FlashMessage from "@/Layouts/FlashMessage";
import JetButton from "@/Jetstream/Button";
import Paginator from "@/Layouts/Paginator";
import { pickBy } from "lodash";
import { throttle } from "lodash";
import Multiselect from "@suadelabs/vue3-multiselect";
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
    FlashMessage,
    // Treeselect,
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
