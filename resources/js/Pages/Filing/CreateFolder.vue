<template>
    <app-layout>
        <template #header>
            <h2 class="header">Create Folder</h2>
        </template>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
            <div class="">
                <!-- <form @submit.prevent="form.post(route('filing.store.folder'))"> -->
                <Form
                    :form="form"
                    @submit.prevent="form.post(route('filing.store.folder'))"
                    :labelCol="{ span: 4 }"
                    :wrapperCol="{ span: 14 }"
                >
                    <FormItem label="Folder Name">
                        <Input
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
                    </FormItem>
                    <FormItem class="text-right">
                        <Button
                            type="primary"
                            htmlType="submit"
                            :disabled="form.processing"
                            >Create Folder</Button
                        >
                    </FormItem>
                </Form>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { useForm } from "@inertiajs/inertia-vue3";
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
        AppLayout,
        Form,
        FormItem,
        Input,
        Button,
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

    methods: {
        submit() {
            this.$inertia.post(route("filing.store.folder"), this.form);
        },
    },
};
</script>
