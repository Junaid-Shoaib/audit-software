<template>
    <app-layout>
        <template #header>
            <h2 class="header">Create Users</h2>
        </template>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
            <Form
                :form="form"
                @submit.prevent="form.post(route('users.store'))"
                :labelCol="{ span: 4 }"
                :wrapperCol="{ span: 14 }"
            >
                <FormItem label="Name">
                    <Input
                        v-model:value="form.name"
                        placeholder="Enter User name"
                    />

                    <div
                        class="text-red-700 px-4 py-2"
                        role="alert"
                        v-if="errors.name"
                    >
                        {{ errors.name }}
                    </div>
                </FormItem>

                <FormItem label="Email">
                    <Input
                        v-model:value="form.email"
                        placeholder="Enter Email"
                    />

                    <div
                        class="text-red-700 px-4 py-2"
                        role="alert"
                        v-if="errors.email"
                    >
                        {{ errors.email }}
                    </div>
                </FormItem>

                <FormItem label="Password">
                    <Input
                        v-model:value="form.password"
                        placeholder="Enter Password"
                    />

                    <div
                        class="text-red-700 px-4 py-2"
                        role="alert"
                        v-if="errors.password"
                    >
                        {{ errors.password }}
                    </div>
                </FormItem>

                <FormItem label="Password">
                    <Input
                        v-model:value="form.password_confirmation"
                        placeholder="Enter confirm password"
                    />

                    <div
                        class="text-red-700 px-4 py-2"
                        role="alert"
                        v-if="errors.password_confirmation"
                    >
                        {{ errors.password_confirmation }}
                    </div>
                </FormItem>

                <FormItem label="Role">
                    <Select
                        v-model:value="form.role"
                        :options="roles"
                        :field-names="{ label: 'label', value: 'value' }"
                        optionFilterProp="name"
                        mode="single"
                        placeholder="Please select"
                        showArrow
                        class="w-full"
                    />

                    <div
                        class="text-red-700 px-4 py-2"
                        role="alert"
                        v-if="errors.role"
                    >
                        {{ errors.role }}
                    </div>
                </FormItem>

                <FormItem class="text-right">
                    <Button
                        type="primary"
                        htmlType="submit"
                        :disabled="form.processing"
                        >Create User</Button
                    >
                </FormItem>

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
            </Form>
            <!-- </JetAuthenticationCard> -->
        </div>
    </app-layout>
</template>

<script>
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
        Form,
        FormItem,
        Input,
        Button,
        Select,
    },

    props: {
        errors: Object,
    },

    data() {
        return {
            roles: [
                { value: "staff", label: "Staff" },
                { value: "manager", label: "Manager" },
                { value: "partner", label: "Partner" },
                // { value: "super-admin", label: "Admin" },
            ],
        };
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

    methods: {
        // submit() {
        //     this.$inertia.post(route("users.store"), this.form);
        // },
    },

    //   const submit = () => {
    //     form.post(route("user.create"), {
    //       onFinish: () => form.reset("password", "password_confirmation"),
    //     });
    //   };
};
</script>
