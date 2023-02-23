<template>
    <app-layout>
        <template #header>
            <h2>Create Bank Accounts</h2>
        </template>

        <div class="max-w-7xl mx-auto pb-2 sm:px-6 lg:px-8 py-4">
            <Button size="small" :href="route('bank_accounts')">Back</Button>
            <Button
                class="ml-2"
                size="small"
                :href="route('banks.create', 'accounts')"
                >Create Bank</Button
            >
            <Button
                class="ml-2"
                size="small"
                :href="route('branches.create', 'accounts')"
                >Create Branch</Button
            >

            <div
                class="relative mt-5 flex-row border-t border-b border-gray-200"
            >
                <div v-if="isError">{{ firstError }}</div>
                <!-- <form @submit.prevent="form.post(route('bank_accounts.store'))"> -->
                <Form :form="form" @submit.prevent="submit">
                    <div class="ant-table-content">
                        <table class="w-full">
                            <thead class="ant-table-thead">
                                <!-- <tr class="bg-gray-700 text-white text-centre font-bold"> -->
                                <tr>
                                    <!-- <th class="ant-table-cell">Account Number</th> -->
                                    <th class="">Account Number</th>
                                    <th class="">Branch</th>
                                    <th class="">Type</th>
                                    <th class="">Currency</th>
                                    <th class="">Actions</th>
                                </tr>
                            </thead>
                            <!-- <tbody> -->
                            <tbody class="ant-table-tbody">
                                <tr
                                    class="ant-table-row ant-table-row-level-0"
                                    v-for="(account, index) in form.accounts"
                                    :key="account.id"
                                >
                                    <td class="ant-table-cell">
                                        <!-- <input
                      v-model="account.name"
                      type="number"
                      class="rounded-md w-full"
                    /> -->
                                        <Formitem style="margin-bottom: 0px">
                                            <Input
                                                v-model:value="account.name"
                                                type="number"
                                                class="w-full"
                                            />
                                        </Formitem>
                                    </td>
                                    <td class="ant-table-cell">
                                        <Formitem style="margin-bottom: 0px">
                                            <Select
                                                v-model:value="
                                                    account.branch_id.id
                                                "
                                                :options="options"
                                                show-search
                                                :field-names="{
                                                    label: 'address',
                                                    value: 'id',
                                                }"
                                                optionFilterProp="address"
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
                                    <td>
                                        <Formitem style="margin-bottom: 0px">
                                            <Button
                                                v-if="index > 0"
                                                danger
                                                @click.prevent="
                                                    deleteRow(index)
                                                "
                                            >
                                                Delete
                                            </Button>
                                        </Formitem>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <Formitem>
                        <Button type="button" @click.prevent="addRow">
                            Add More Accounts</Button
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
            <Table
                :columns="columns"
                :data-source="balances"
                :loading="loading"
                class="mt-6"
                size="small"
            >
            </Table>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { useForm } from "@inertiajs/inertia-vue3";
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
        branches: Array,
        balances: Object,
    },

    data() {
        return {
            options: this.branches,
            currency_options: [
                { name: "PKR" },
                { name: "$" },
                { name: "USD" },
                { name: "EUR" },
            ],
            type_options: [{ name: "CURRENT" }, { name: "SAVING" }],
            columns: [
                {
                    title: "Account Number",
                    dataIndex: "name",
                },
                {
                    title: "Branch",
                    dataIndex: "branches",
                },
                {
                    title: "Type",
                    dataIndex: "type",
                },
                {
                    title: "Currency",
                    dataIndex: "currency",
                },
            ],
        };
    },

    setup(props) {
        const form = useForm({
            accounts: [
                {
                    branch_id: props.branches[0],
                    type: "CURRENT",
                    name: null,
                    currency: "PKR",
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
            }
        },
    },

    methods: {
        submit() {
            this.$inertia.post(route("bank_accounts.store"), this.form);
        },

        addRow() {
            this.form.accounts.push({
                branch_id: this.branches[0],
                type: "CURRENT",
                name: null,
                currency: "PKR",
            });
        },

        deleteRow(index) {
            this.form.accounts.splice(index, 1);
        },
    },
};
</script>
