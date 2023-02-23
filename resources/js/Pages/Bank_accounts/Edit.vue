<template>
    <app-layout>
        <template #header>
            <h2>Update Bank Accounts</h2>
        </template>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
            <Button :href="route('bank_accounts')">Back </Button>

            <div
                class="relative mt-5 flex-row border-t border-b border-gray-200"
            >
                <Form :form="form" @submit.prevent="submit">
                    <div class="">
                        <div v-if="isError">{{ firstError }}</div>
                        <table class="w-full">
                            <thead class="ant-table-thead">
                                <tr>
                                    <th class="px-3 pt-3 pb-3 border">
                                        Account Number
                                    </th>
                                    <th class="px-3 pt-3 pb-3 border">
                                        Branches
                                    </th>
                                    <th class="px-3 pt-3 pb-3 border">Type</th>
                                    <th class="px-3 pt-3 pb-3 border">
                                        Currency
                                    </th>
                                    <!-- <th class="px-4 pt-4 pb-4 border">Action</th> -->
                                </tr>
                            </thead>
                            <tbody class="ant-table-tbody">
                                <tr v-for="account in data" :key="account.id">
                                    <td class="ant-table-cell">
                                        <FormItem style="margin-bottom: 0px">
                                            <Input
                                                v-model:value="account.name"
                                                type="number"
                                                class="w-full"
                                            />
                                        </FormItem>
                                    </td>
                                    <td class="ant-table-cell">
                                        <FormItem style="margin-bottom: 0px">
                                            <Input
                                                v-model:value="account.branches"
                                                type="text"
                                                disabled
                                                class="w-full"
                                            />
                                        </FormItem>
                                    </td>
                                    <td class="ant-table-cell">
                                        <Formitem style="margin-bottom: 0px">
                                            <Select
                                                v-model:value="account.type"
                                                :options="type_options"
                                                :field-names="{
                                                    label: 'name',
                                                    value: 'name',
                                                }"
                                                optionFilterProp="name"
                                                mode="single"
                                                placeholder="Please select"
                                                showArrow
                                                class="w-full"
                                            />
                                        </Formitem>
                                    </td>
                                    <td class="ant-table-cell">
                                        <Formitem style="margin-bottom: 0px">
                                            <Select
                                                v-model:value="account.currency"
                                                :options="currency_options"
                                                :field-names="{
                                                    label: 'name',
                                                    value: 'name',
                                                }"
                                                optionFilterProp="name"
                                                mode="single"
                                                placeholder="Please select"
                                                showArrow
                                                class="w-full"
                                            />
                                        </Formitem>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <FormItem>
                        <Button class="m-1" type="primary" @click="submit"
                            >Update Account</Button
                        >
                        <!-- :disabled="form.processing" -->
                    </FormItem>
                </Form>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { Button, Table, Select, Form, FormItem, Input } from "ant-design-vue";

export default {
    components: {
        AppLayout,
        Button,
        Table,
        Select,
        Form,
        FormItem,
        Input,
    },

    props: {
        errors: Object,
        data: Object,
    },

    data() {
        return {
            balances: this.data,
            isError: false,
            firstError: "",

            currency_options: [
                { name: "PKR" },
                { name: "$" },
                { name: "USD" },
                { name: "EUR" },
            ],
            type_options: [{ name: "CURRENT" }, { name: "SAVING" }],
        };
    },

    watch: {
        errors: function () {
            if (this.errors) {
                this.firstError = this.errors[Object.keys(this.errors)[0]];
                this.isError = true;
            }
        },
        data: function () {
            this.balances = this.data;
        },
    },

    methods: {
        submit() {
            this.$inertia.put(route("bank_accounts.update", this.balances[0]), {
                balances: this.balances,
            });
        },
        // doFormat($item) {
        //   var $i = format($item, "yyyy-MM-dd");
        //   return $i;
        // },

        addRow() {
            this.balances.push({
                name: null,
                type: null,
                currency: null,
                // account_id: this.accounts[0].id,
            });
        },

        deleteRow(index) {
            this.balances.splice(index, 1);
        },
    },
};
</script>
