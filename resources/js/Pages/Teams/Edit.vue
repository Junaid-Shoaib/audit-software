<template>
  <app-layout>
    <template #header>
      <h2 class="header">Edit Team</h2>
    </template>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
      <div class="">
        <form @submit.prevent="submit">
          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 ml-40 text-right w-36 font-bold"
              >Partner :</label
            >
            <multiselect
              style="width: 50%; z-index: 10"
              class="float-right rounded-md border border-black"
              placeholder="Select Partner."
              v-model="form.partner"
              track-by="id"
              label="name"
              :options="partners"
            >
              <!-- @update:model-value="coch" -->
            </multiselect>

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
              v-if="errors.partner"
            >
              {{ errors.partner }}
            </div>
          </div>

          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 ml-40 text-right w-36 font-bold"
              >Manager :</label
            >
            <multiselect
              style="width: 50%; z-index: 10"
              class="float-right rounded-md border border-black"
              placeholder="Select Manager."
              v-model="form.manager"
              track-by="id"
              label="name"
              :options="managers"
            >
              <!-- @update:model-value="coch" -->
            </multiselect>

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
              v-if="errors.manager"
            >
              {{ errors.manager }}
            </div>
          </div>

          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 ml-40 text-right w-36 font-bold"
              >Staff :</label
            >
            <multiselect
              style="width: 50%; z-index: 10"
              class="float-right rounded-md border border-black"
              placeholder="Select Staff."
              v-model="form.staff"
              track-by="id"
              label="name"
              :multiple="true"
              :options="staff"
            >
              <!-- @update:model-value="coch" -->
            </multiselect>

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
              v-if="errors.staff"
            >
              {{ errors.staff }}
            </div>
          </div>

          <div class="px-4 py-2 flex justify-center items-center">
            <button class="submitbutton p-1 px-4 mt-1 ml-2 mr-3" type="submit">
              Update Team
            </button>
          </div>
        </form>
      </div>
    </div>
  </app-layout>
</template>

<style src="@suadelabs/vue3-multiselect/dist/vue3-multiselect.css"></style>
<script>
import AppLayout from "@/Layouts/AppLayout";
import Label from "../../Jetstream/Label.vue";
import Multiselect from "@suadelabs/vue3-multiselect";

export default {
  components: {
    AppLayout,
    Multiselect,
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

        partner: this.partner,
        manager: this.manager,
        staff: this.staf,
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
