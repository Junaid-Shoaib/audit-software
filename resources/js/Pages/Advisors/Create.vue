<template>
    <app-layout>
        <template #header>
            <h2>Create Advisors</h2>
        </template>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
            <div class="">
                <a-form
                    :form="form"
                    @submit.prevent="form.post(route('advisors.store'))"
                    :label-col="{ span: 4 }"
                    :wrapper-col="{ span: 14 }"
                >
                    <a-form-item>
                        <a-button :href="route('advisors')">Back </a-button>
                    </a-form-item>
                    <a-form-item label="Name">
                        <a-input
                            v-model:value="form.name"
                            placeholder="Enter your name"
                        />
                        <div
                            class="text-red-700 px-4 py-2"
                            role="alert"
                            v-if="errors.name"
                        >
                            {{ errors.name }}
                        </div>
                    </a-form-item>
                    <a-form-item label="Address">
                        <a-text-area
                            v-model:value="form.address"
                            placeholder="Enter your Address"
                        />

                        <div
                            class="text-red-700 px-4 py-2"
                            role="alert"
                            v-if="errors.address"
                        >
                            {{ errors.address }}
                        </div>
                    </a-form-item>

                    <a-form-item label="Type">
                        <a-select
                            v-model:value="form.type"
                            :options="options"
                            :field-names="{ label: 'name', value: 'value' }"
                            optionFilterProp="name"
                            mode="single"
                            placeholder="Please select"
                            showArrow
                            class="w-full"
                        />

                        <div
                            class="text-red-700 px-4 py-2"
                            role="alert"
                            v-if="errors.type"
                        >
                            {{ errors.type }}
                        </div>
                    </a-form-item>
                    <a-form-item class="text-right">
                        <a-button
                            type="primary"
                            htmlType="submit"
                            :disabled="form.processing"
                        >
                            Create Advisor
                        </a-button>
                    </a-form-item>
                </a-form>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { useForm } from "@inertiajs/inertia-vue3";
import { Form, Input, Button, Select } from "ant-design-vue";

export default {
    components: {
        AppLayout,
        "a-form": Form,
        "a-form-item": Form.Item,
        "a-input": Input,
        "a-text-area": Input.TextArea,
        "a-button": Button,
        "a-select": Select,
    },

    props: {
        errors: Object,
    },

    data() {
        return {
            options: [
                { name: "Legal Advisor", value: "legal" },
                { name: "Tax Advisor", value: "tax" },
            ],
        };
    },

    setup(props) {
        const form = useForm({
            name: null,
            address: null,
            type: "legal",
        });
        return { form };
    },

    methods: {
        // submitForm() {
        //     this.$inertia.post(route("advisors.store"), this.form);
        // },
    },
};
</script>
