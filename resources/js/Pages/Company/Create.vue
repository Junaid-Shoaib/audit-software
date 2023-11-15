<template>
    <app-layout>
        <template #header>
            <h2 class="header">Create Company</h2>
        </template>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
            <div class="">
                <a-form
                    :form="form"
                    @submit.prevent="form.post(route('companies.store'))"
                    :label-col="{ span: 4 }"
                    :wrapper-col="{ span: 14 }"
                >
                    <a-form-item label="Name">
                        <a-input
                            v-model:value="form.name"
                            placeholder="Enter your name"
                        />
                        <!--  -->

                        <div
                            class="text-red-700 px-4 py-2"
                            role="alert"
                            v-if="errors.name"
                        >
                            {{ errors.name }}
                        </div>
                    </a-form-item>

                    <a-form-item label="Address ">
                        <a-textarea
                            v-model:value="form.address"
                            placeholder="Enter your address"
                        />

                        <div
                            class="text-red-700 px-4 py-2"
                            role="alert"
                            v-if="errors.address"
                        >
                            {{ errors.address }}
                        </div>
                    </a-form-item>
                    <!-- <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 ml-40 text-right w-36 font-bold"
              >Address :</label
            >
            <input
              type="text"
              v-model="form.address"
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
              label="address"
              placeholder="Enter address:"
            />
            <div v-if="errors.address">{{ errors.address }}</div>
          </div> -->

                    <a-form-item label="Email">
                        <a-input
                            v-model:value="form.email"
                            placeholder="Enter your email"
                        />

                        <div
                            class="text-red-700 px-4 py-2"
                            role="alert"
                            v-if="errors.email"
                        >
                            {{ errors.email }}
                        </div>
                    </a-form-item>
                    <!-- <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
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
            <div v-if="errors.email">{{ errors.email }}</div>
          </div> -->

                    <a-form-item label="Web Address">
                        <a-input
                            v-model:value="form.web"
                            placeholder="Enter your web"
                        />

                        <div
                            class="text-red-700 px-4 py-2"
                            role="alert"
                            v-if="errors.web"
                        >
                            {{ errors.web }}
                        </div>
                    </a-form-item>
                    <!-- <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 ml-40 text-right w-36 font-bold"
              >Web Address :</label
            >
            <input
              type="text"
              v-model="form.web"
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
              label="website"
              placeholder="Enter web address:"
            />
            <div v-if="errors.web">{{ errors.website }}</div>
          </div> -->

                    <a-form-item label="Phone No">
                        <a-input
                            v-model:value="form.phone"
                            placeholder="Enter your phone"
                        />

                        <div
                            class="text-red-700 px-4 py-2"
                            role="alert"
                            v-if="errors.phone"
                        >
                            {{ errors.phone }}
                        </div>
                    </a-form-item>
                    <!-- <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 ml-40 text-right w-36 font-bold"
              >Phone No :</label
            >
            <input
              type="text"
              v-model="form.phone"
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
              label="phone"
              placeholder="Enter phone no:"
            />
            <div v-if="errors.phone">{{ errors.phone }}</div>
          </div> -->

                    <a-form-item label="Month End">
                        <!-- <a-input
              v-model:value="form.fiscal"
              placeholder="Enter your fiscal"

            /> -->
                        <a-select
                            v-model:value="form.fiscal"
                            :options="fiscals"
                            :field-names="{ label: 'name', value: 'name' }"
                            optionFilterProp="name"
                            mode="single"
                            placeholder="Please select"
                            showArrow
                            class="w-full"
                        />
                        <!-- filterOption="true"
              @change="onChange" -->

                        <div
                            class="text-red-700 px-4 py-2"
                            role="alert"
                            v-if="errors.fiscal"
                        >
                            {{ errors.fiscal }}
                        </div>
                    </a-form-item>
                    <!-- <div class="p-2 mr-2 mb-2 ml-6 flex flex-wrap">
            <label class="my-2 ml-40 text-right w-36 mr-8 font-bold"
              >Fiscal :</label
            >
            <select
              v-model="form.fiscal"
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
              placeholder="Select Fiscal:"
            >
              <option v-for="fiscal in fiscals" :key="fiscal" :value="fiscal">
                {{ fiscal }}
              </option>
            </select>
            <div v-if="errors.fiscal">{{ errors.fiscal }}</div>
          </div> -->
                    <a-form-item label="Incorporation Date">
                        <!-- <a-input
              v-model:value="form.incorp"
              placeholder="Enter your incorp"

            /> -->

                        <a-datePicker v-model:value="form.incorp" />

                        <div
                            class="text-red-700 px-4 py-2"
                            role="alert"
                            v-if="errors.incorp"
                        >
                            {{ errors.incorp }}
                        </div>
                    </a-form-item>
                    <!-- <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 ml-40 text-right w-36 font-bold"
              >Incorp :</label
            >
            <input
              type="date"
              v-model="form.incorp"
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
              label="incorp"
              placeholder="Enter Incorp:"
            />
            <div v-if="errors.incorp">{{ errors.incorp }}</div>
          </div> -->
                    <a-form-item class="text-right">
                        <a-button
                            type="primary"
                            :disabled="form.processing"
                            htmlType="submit"
                            > Create Company</a-button
                        >
                    </a-form-item>
                    <!-- <div class="px-4 py-2 flex justify-center items-center">
            <button
              class="submitbutton p-1 px-4 mt-1 ml-2 inline-block mr-3"
              type="submit"
              :disabled="form.processing"
            >
              Create Company
            </button>
          </div> -->
                </a-form>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { useForm } from "@inertiajs/inertia-vue3";
import {
    Form,
    Input,
    Button,
    Select,
    TreeSelect,
    DatePicker,
} from "ant-design-vue";

export default {
    components: {
        AppLayout,
        useForm,
        "a-tree-select": TreeSelect,
        "a-form": Form,
        "a-form-item": Form.Item,
        "a-input": Input,
        "a-textarea": Input.TextArea,
        "a-button": Button,
        "a-select": Select,
        "a-datePicker": DatePicker,
    },

    props: {
        errors: Object,
        // fiscals: Object,
        fiscal_first: Object,
    },

    data() {
        return {
            fiscals: [
                { name: "June" },
                { name: "July" },
                { name: "September" },
                { name: "December" },
            ],
        };
    },

    setup(props) {
        const form = useForm({
            name: null,
            address: null,
            email: null,
            web: null,
            phone: null,
            //   fiscal: props.fiscal_first,
            fiscal: "June",
            incorp: null,
        });
        return { form };
    },

    //   data() {
    //     return {
    //       form: this.$inertia.form({
    //         name: null,
    //         address: null,
    //         email: null,
    //         website: null,
    //         phone: null,
    //         fiscal: this.fiscal_first,
    //         incorp: null,
    //       }),
    //     };
    //   },

    methods: {
        submitForm() {
            //   console.log(this.form);
            this.$inertia.post(route("companies.store"), this.form);
            // Send form data to server using axios or fetch
        },
        //     submit() {
        //       this.$inertia.post(route("companies.store"), this.form);
        //     },
    },
};
</script>
