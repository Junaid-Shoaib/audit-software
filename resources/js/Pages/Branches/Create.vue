<template>
    <app-layout>
        <template #header>
            <h2 class="header">Create Bank Branches</h2>
        </template>

        <div class="max-w-7xl mx-auto pb-2 sm:px-6 lg:px-8 py-4">
            <Form
                :form="form"
                @submit.prevent="form.post(route('branches.store'))"
                :label-col="{ span: 4 }"
                :wrapper-col="{ span: 14 }"
            >
                <FormItem label="Bank">
                    <!-- optionFilterProp="name" -->
                    <Select
                        size="small"
                        v-model:value="form.bank_id"
                        :options="this.banks"
                        :field-names="{ label: 'name', value: 'id' }"
                        mode="single"
                        optionFilterProp="name"
                        placeholder="Please select"
                        show-search
                        showArrow
                        class="w-full"
                    />
                    <div
                        class="text-red-700 px-4 py-2"
                        role="alert"
                        v-if="errors.bank_id"
                    >
                        {{ errors.bank_id }}
                    </div>
                </FormItem>
                <FormItem label="Address">
                    <Textarea
                        v-model:value="form.address"
                        placeholder="Enter branch address"
                        size="small"
                    />

                    <div
                        class="text-red-700 px-4 py-2"
                        role="alert"
                        v-if="errors.address"
                    >
                        {{ errors.address }}
                    </div>
                </FormItem>
                <FormItem class="text-right">
                    <Button
                        type="primary"
                        htmlType="submit"
                        :disabled="form.processing"
                        >Submit</Button
                    >
                </FormItem>
            </Form>

            <!-- <Table
        :columns="columns"
        :data-source="branches"
        :loading="loading"
        :custom-row="customRow"
        class="mt-2"
        size="small"
      >
        <template #bodyCell="{ column, record }">
          <template v-if="column.key === 'address'">
            <span v-if="record.bank_id == form.bank_id"> {{ record.add }}</span>
          </template>
        </template>
      </Table> -->

            <div class="">
                <table class="ant-table ant-table-small w-full">
                    <thead class="ant-table-thead">
                        <tr class="ant-table-cell">
                            <th class="ant-design-head">Branches</th>
                        </tr>
                    </thead>
                    <tbody class="ant-table-tbody">
                        <tr
                            class="ant-design-row"
                            v-for="item in branches"
                            :key="item.id"
                        >
                            <td
                                v-if="item.bank_id == form.bank_id"
                                class="ant-design-column"
                            >
                                {{ item.add }}
                            </td>
                        </tr>
                        <tr v-if="branches.length === 0">
                            <td class="border-t px-6 py-4" colspan="4">
                                No Record found.
                            </td>
                        </tr>
                    </tbody>
                </table>
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
    Select,
    Textarea,
    Input,
    Button,
    Table,
} from "ant-design-vue";
import Multiselect from "@suadelabs/vue3-multiselect";

export default {
    components: {
        AppLayout,
        Form,
        FormItem,
        Select,
        Textarea,
        Input,
        Button,
        Table,
        Multiselect,
    },

    props: {
        branches: Object,
        errors: Object,
        banks: Object,
        accounts: Object,
    },

    data() {
        return {
            options: this.banks,

            columns: [
                {
                    title: "Branches",
                    dataIndex: "add",
                    key: "address",
                },
            ],
        };
    },

    setup(props) {
        const form = useForm({
            address: null,
            accounts: props.accounts,
            bank_id: props.banks[0].id,
            //   bank_id: null,
        });
        return { form };
    },

    methods: {
        submit() {
            this.$inertia.post(route("branches.store"), this.form);
        },
        customRow(row) {
            if (row.bank_id == this.form.bank_id) {
                return {
                    attrs: {
                        "data-row-key": row.key,
                        class: "highlight-row",
                    },
                };
            } else {
                return {
                    attrs: {
                        "data-row-key": row.key,
                        class: "hidden",
                    },
                };
            }
        },
    },
};
</script>
