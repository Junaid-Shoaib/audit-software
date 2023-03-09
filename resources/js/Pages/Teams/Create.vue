<template>
    <app-layout>
        <template #header>
            <h2 class="header">Create Team</h2>
        </template>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
            <Form
                :form="form"
                @submit.prevent="submit"
                :labelCol="{ span: 4 }"
                :wrapperCol="{ span: 14 }"
            >
                <FormItem label="Partner">
                    <Select
                        v-model:value="form.partner"
                        :options="partners"
                        :field-names="{ label: 'name', value: 'id' }"
                        filterOption="true"
                        optionFilterProp="name"
                        mode="single"
                        placeholder="Please select"
                        showArrow
                        class="w-full"
                    />
                    <div
                        class="text-red-700 px-4 py-2"
                        role="alert"
                        v-if="errors.partner"
                    >
                        {{ errors.partner }}
                    </div>
                </FormItem>

                <FormItem label="Manager">
                    <Select
                        v-model:value="form.manager"
                        :options="managers"
                        :field-names="{ label: 'name', value: 'id' }"
                        filterOption="true"
                        optionFilterProp="name"
                        mode="single"
                        placeholder="Please select"
                        showArrow
                        class="w-full"
                    />
                    <div
                        class="text-red-700 px-4 py-2"
                        role="alert"
                        v-if="errors.manager"
                    >
                        {{ errors.manager }}
                    </div>
                </FormItem>

                <FormItem label="Staff">
                    <Select
                        v-model:value="form.staff"
                        :options="staff"
                        :field-names="{ label: 'name', value: 'id' }"
                        filterOption="true"
                        optionFilterProp="name"
                        mode="multiple"
                        placeholder="Please select"
                        showArrow
                        class="w-full"
                    />
                    <div
                        class="text-red-700 px-4 py-2"
                        role="alert"
                        v-if="errors.staff"
                    >
                        {{ errors.staff }}
                    </div>
                </FormItem>

                <FormItem class="text-right">
                    <Button type="primary" @click="submit">Create Team</Button>
                </FormItem>
            </Form>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { useForm } from "@inertiajs/inertia-vue3";
import Multiselect from "@suadelabs/vue3-multiselect";
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
        Multiselect,
        useForm,
        TreeSelect,
        Form,
        FormItem,
        Input,
        Button,
        Select,
        DatePicker,
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
            // co_id: this.$page.props.co_id,
            partners: this.partners,
            managers: this.managers,
            staff: this.staff,

            //   partner_id: this.partner,
            //   manager_id: this.manager,
            //   staff_id: this.staf,
        };
    },

    setup(props) {
        const form = useForm({
            partner: props.partner.id,
            manager: props.manager.id,
            staff: props.staf[0].id,
        });
        return { form };
    },

    methods: {
        submit() {
            this.$inertia.post(route("teams.store"), this.form);
        },
    },
};
</script>
