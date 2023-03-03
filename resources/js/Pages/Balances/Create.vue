<template>
    <app-layout>
        <template #header>
            <h2>Create Bank Balances</h2>
        </template>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
                <Button :href="route('balances')">Back </Button>


                <div v-if="isError" class="bg-red-100 mt-2 border border-red-400 text-red-700 px-4 py-1 rounded relative" role="alert">
                    <span class="block sm:inline">{{ firstError }}</span>
                </div>

                <div v-if="isSelect" class="bg-red-100 mt-2 border border-red-400 text-red-700 px-4 py-1 rounded relative" role="alert">
                        <span class="block sm:inline">Account Select must be unique.</span>
                </div>

            <div class="relative mt-5 flex-row border-t border-b border-gray-200">
                <Form :form="form" @submit.prevent="submit">
                    <div class="ant-table-content">
                        <table
                            style="table-layout: auto"
                            class="ant-table ant-table-small w-full"
                        >
                            <thead class="ant-table-thead">
                                <tr class="ant-table-cell">
                                    <th class="ant-table-cell">Ledger</th>
                                    <th class="ant-table-cell">Account</th>
                                    <th class="ant-table-cell">Action</th>
                                </tr>
                            </thead>
                            <tbody class="ant-table-tbody">
                                <tr
                                    class="ant-table-row ant-table-row-level-0"
                                    v-for="(balance, index) in form.balances"
                                    :key="balance.id"
                                >
                                    <td class="ant-table-cell">
                                        <FormItem style="margin-bottom: 0px">
                                            <Input
                                                v-model:value="balance.ledger"
                                                type="number"
                                                class="w-full"
                                            />
                                        </FormItem>
                                    </td>
                                    <td class="ant-table-cell">
                                        <FormItem style="margin-bottom: 0px">
                                            <Select
                                                v-model:value="
                                                    balance.account_id
                                                "
                                                :options="options"
                                                :field-names="{
                                                    label: 'branch',
                                                    value: 'id',
                                                }"
                                                show-search
                                                filterOption="true"
                                                optionFilterProp="branch"
                                                mode="single"
                                                placeholder="Please select"
                                                showArrow
                                                class="w-full"
                                            />
                                        </FormItem>
                                    </td>
                                    <td class="ant-table-cell">
                                        <FormItem style="margin-bottom: 0px">
                                            <Button
                                                v-if="index > 0"
                                                danger
                                                @click="deleteRow(index)"
                                                >Delete</Button
                                            >
                                        </FormItem>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <Formitem>
                        <Button type="button" @click.prevent="addRow">
                            Add More Balances</Button
                        >
                        <Button
                            class="m-1"
                            type="primary"
                            :disabled="form.processing"
                            @click="submit"
                            >Submit</Button
                        >
                    </Formitem>
                </Form>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { useForm } from "@inertiajs/inertia-vue3";
import { Form, FormItem, Input, Button, Select, Table } from "ant-design-vue";

export default {
    components: {
        AppLayout,
        Form,
        FormItem,
        Input,
        Button,
        Select,
        Table,
    },

    props: {
        errors: Object,
        accounts: Array,
    },

    data() {
        return {
            options: this.accounts,
            isError: false,
            isSelect: false,
        };
    },

    setup(props) {
        const form = useForm({
            balances: [
                {
                    ledger: "",
                    statement: "",
                    confirmation: "",
                    account_id: props.accounts[0].id,
                    //   account_id: props.accounts[0].id,
                },
            ],
        });
        return { form };
    },

     watch: {
        errors: function () {
            if (this.errors) {
                this.firstError = this.errors[Object.keys(this.errors)[0]];
                this.isError = true;
                this.isSelect = false;
            }
        },
    },

    methods: {
        submit() {
             const accountIds = this.form.balances.map(balance => balance.account_id);
            if (new Set(accountIds).size !== accountIds.length) {
                this.isSelect = true;
                this.isError = false;
                return;
            }

            this.$inertia.post(route("balances.store"), this.form);
        },

        addRow() {
            this.form.balances.push({
                ledger: "",
                statement: "",
                confirmation: "",
                account_id: this.accounts[0].id,
            });
        },

        deleteRow(index) {
            this.form.balances.splice(index, 1);
        },
    },
};
</script>
