<template>
  <app-layout>
    <template #header>
      <!-- <div class="grid grid-cols-2 items-center"> -->
      <h2 class="header">Create Users</h2>
      <!-- <div class="justify-end">
          <multiselect
            style="width: 50%; z-index: 10"
            class="float-right rounded-md border border-black"
            placeholder="Select Company."
            v-model="co_id"
            track-by="id"
            label="name"
            :options="options"
            @update:model-value="coch"
          >
          </multiselect>
        </div>
      </div> -->
    </template>

    <!-- <FlashMessage /> -->

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
      <div class="">
        <!-- <JetAuthenticationCard> -->
        <!-- <template #logo>
          <JetAuthenticationCardLogo />
        </template> -->

        <!-- <JetValidationErrors class="mb-4" /> -->

        <form @submit.prevent="form.post(route('users.store'))">
          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 ml-40 text-right w-36 font-bold"
              >Name :</label
            >
            <input
              type="text"
              v-model="form.name"
              class="
                pr-2
                pb-2
                w-full
                lg:w-1/4
                rounded-md
                hover:transition hover:ease-in-out
                transform
                hover:scale-110
                ease-out
                duration-200
                placeholder-indigo-300
              "
              label="name"
              placeholder="Enter User name:"
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
          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 ml-40 text-right w-36 font-bold"
              >Email :</label
            >
            <input
              type="text"
              v-model="form.email"
              class="
                pr-2
                pb-2
                w-full
                lg:w-1/4
                rounded-md
                hover:transition hover:ease-in-out
                transform
                hover:scale-110
                ease-out
                duration-200
                placeholder-indigo-300
              "
              label="email"
              placeholder="Enter email:"
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
              v-if="errors.email"
            >
              {{ errors.email }}
            </div>
          </div>
          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 ml-40 text-right w-36 font-bold"
              >Password :</label
            >
            <input
              type="password"
              v-model="form.password"
              class="
                pr-2
                pb-2
                w-full
                lg:w-1/4
                rounded-md
                hover:transition hover:ease-in-out
                transform
                hover:scale-110
                ease-out
                duration-200
                placeholder-indigo-300
              "
              label="password"
              placeholder="Enter password:"
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
              v-if="errors.password"
            >
              {{ errors.password }}
            </div>
          </div>
          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 ml-40 text-right w-36 font-bold"
              >Confirm Password:</label
            >
            <input
              id="password_confirmation"
              type="password"
              required
              v-model="form.password_confirmation"
              class="
                pr-2
                pb-2
                w-full
                lg:w-1/4
                rounded-md
                hover:transition hover:ease-in-out
                transform
                hover:scale-110
                ease-out
                duration-200
                placeholder-indigo-300
              "
              label="password"
              placeholder="Enter confirm password:"
            />
            <div v-if="errors.password_confirmation">
              {{ errors.password_confirmation }}
            </div>
          </div>
          <div class="p-2 mr-2 mb-2 ml-6 flex flex-wrap">
            <label class="my-2 ml-40 text-right w-36 mr-8 font-bold"
              >Role :</label
            >
            <select
              v-model="form.role"
              class="
                pr-2
                pb-2
                w-full
                lg:w-1/4
                rounded-md
                hover:transition hover:ease-in-out
                transform
                hover:scale-110
                ease-out
                duration-200
                placeholder-indigo-300
              "
              label="ficsal"
              placeholder="Select Role:"
            >
              <option value="staff">Staff</option>
              <option value="manager">Manager</option>
              <option value="partner">Partner</option>
              <!-- <option value="super-admin">Super-Admin</option> -->
            </select>
            <div v-if="errors.role">{{ errors.role }}</div>
          </div>
          <div class="px-4 py-2 flex justify-center items-center">
            <button
              class="submitbutton p-1 px-4 mt-1 ml-2 inline-block mr-3"
              type="submit"
              :disabled="form.processing"
            >
              Create User
            </button>
          </div>
          <!-- <div>
            <JetLabel for="name" value="Name" />
            <JetInput
              id="name"
              v-model="form.name"
              type="text"
              class="mt-1 block w-full"
              required
              autofocus
              autocomplete="name"
            />
          </div>

          <div class="mt-4">
            <JetLabel for="email" value="Email" />
            <JetInput
              id="email"
              v-model="form.email"
              type="email"
              class="mt-1 block w-full"
              required
            />
          </div>

          <div class="mt-4">
            <JetLabel for="password" value="Password" />
            <JetInput
              id="password"
              v-model="form.password"
              type="password"
              class="mt-1 block w-full"
              required
              autocomplete="new-password"
            />
          </div>

          <div class="mt-4">
            <JetLabel for="password_confirmation" value="Confirm Password" />
            <JetInput
              id="password_confirmation"
              v-model="form.password_confirmation"
              type="password"
              class="mt-1 block w-full"
              required
              autocomplete="new-password"
            />
          </div>

          <div class="mt-4">
            <JetLabel for="role" value="Role" />
            <select
              id="role"
              required
              autofocus
              class="form-control mt-1 block w-full"
              v-model="form.role"
            >
              <option value="staff">Staff</option>
              <option value="manager">Manager</option>
              <option value="partner">Partner</option>
              <option value="super-admin">Super-Admin</option>
            </select>
          </div> -->

          <div
            v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature"
            class="mt-4"
          >
            <JetLabel for="terms">
              <div class="flex items-center">
                <JetCheckbox
                  id="terms"
                  v-model:checked="form.terms"
                  name="terms"
                />

                <div class="ml-2">
                  I agree to the
                  <a
                    target="_blank"
                    :href="route('terms.show')"
                    class="underline text-sm text-gray-600 hover:text-gray-900"
                    >Terms of Service</a
                  >
                  and
                  <a
                    target="_blank"
                    :href="route('policy.show')"
                    class="underline text-sm text-gray-600 hover:text-gray-900"
                    >Privacy Policy</a
                  >
                </div>
              </div>
            </JetLabel>
          </div>

          <!-- <div class="flex items-center justify-end mt-4">
            <Link
              :href="route('login')"
              class="underline text-sm text-gray-600 hover:text-gray-900"
            >
              Already registered?
            </Link>

            <JetButton
              class="ml-4"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
            >
              Register
            </JetButton>
          </div> -->
        </form>
        <!-- </JetAuthenticationCard> -->
      </div>
    </div>
  </app-layout>
</template>

<script >
import { Head, Link, useForm } from "@inertiajs/inertia-vue3";
import JetAuthenticationCard from "@/Jetstream/AuthenticationCard.vue";
import JetAuthenticationCardLogo from "@/Jetstream/AuthenticationCardLogo.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";
import JetDropdown from "@/Jetstream/Dropdown.vue";

import AppLayout from "@/Layouts/AppLayout";

export default {
  components: {
    JetButton,
    JetAuthenticationCard,
    JetAuthenticationCardLogo,
    JetInput,
    JetCheckbox,
    JetLabel,
    JetValidationErrors,
    JetDropdown,
    AppLayout,
    useForm,
  },

  props: {
    errors: Object,
    // fiscals: Object,
    // fiscal_first: Object,
  },

  setup() {
    const form = useForm({
      name: "",
      email: "",
      password: "",
      role: "staff",
      password_confirmation: "",
      terms: false,
    });
    return { form };
  },

  //   const submit = () => {
  //     form.post(route("user.create"), {
  //       onFinish: () => form.reset("password", "password_confirmation"),
  //     });
  //   };
};
</script>
