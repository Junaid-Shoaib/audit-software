<template>
  <app-layout>
    <template #header>
      <h2 class="header">Create Folder</h2>
    </template>
    <div
      v-if="$page.props.flash.success"
      class="bg-yellow-600 text-white text-center"
    >
      {{ $page.props.flash.success }}
    </div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
      <div class="">
        <form @submit.prevent="form.post(route('filing.store.folder'))">
          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 ml-40 text-right w-36 font-bold"
              >Folder Name :</label
            ><input
              type="text"
              v-model="form.name"
              class="
                pr-2
                pb-2
                w-full
                lg:w-1/4
                rounded-md
                hover:transition hover:ease-in-out transform hover:scale-110 ease-out duration-200
                placeholder-indigo-300
              "
              label="name"
              placeholder="Enter Folder name:"
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
              v-if="errors.name"
            >
              {{ errors.name }}
            </div>
          </div>

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
                mr-8
                mt-1
                ml-2
                inline-block
                 "
              type="submit"
              :disabled="form.processing"
            >
              Create Folder
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

export default {
  components: {
    AppLayout,
  },

  props: {
    errors: Object,
  },

  setup() {
    const form = useForm({
      name: null,
    });
    return { form };
  },
};
</script>
