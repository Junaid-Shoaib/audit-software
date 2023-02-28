<template>
    <app-layout>
        <template #header>
            <h2>Create Advisor Accounts</h2>
        </template>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
            <a-form-item>
                <a-button :href="route('advisor_accounts')">Back </a-button>
            </a-form-item>

            <div
                class="relative mt-5 flex-row border-t border-b border-gray-200"
            >
                <div v-if="isError">{{ firstError }}</div>
                <a-form :form="form" @submit.prevent="submit">
                    <div class="ant-table-content">
                        <!-- @submit.prevent="form.post(route('advisor_accounts.store'))"> -->
                        <table class="w-full">
                            <!-- class="shadow-lg border mt-4 mb-4 ml-12 rounded-xl w-11/12 -->
                            <thead class="ant-table-thead">
                                <tr>
                                    <!-- class="bg-gray-700 text-white text-centre font-bold" -->
                                    <th
                                        style="width: 40%"
                                        class="ant-table-cell"
                                        colstart="0"
                                        colend="0"
                                    >
                                        Company Name
                                    </th>
                                    <th
                                        style="width: 40%"
                                        class="ant-table-cell"
                                        colstart="1"
                                        colend="1"
                                    >
                                        Advisor
                                    </th>
                                    <!-- <th class="p-1 border">Type</th>
                  <th class="p-1 border">Currency</th> -->
                                    <th
                                        style="width: 20%"
                                        class="ant-table-cell"
                                        colstart="2"
                                        colend="2"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="ant-table-tbody">
                                <tr
                                    class="ant-table-row ant-table-row-level-0"
                                    v-for="(account, index) in form.accounts"
                                    :key="account.id"
                                >
                                    <td class="ant-table-cell">
                                        <a-form-item style="margin-bottom: 0px">
                                            <a-input
                                                v-model:value="account.name"
                                                type="text"
                                                disabled
                                                class="w-full"
                                            />
                                        </a-form-item>
                                    </td>
                                    <td class="ant-table-cell">
                                        <a-form-item style="margin-bottom: 0px">
                                            <a-select
                                                v-model:value="
                                                    account.advisor_id
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
                                        </a-form-item>
                                    </td>
                                    <td class="ant-table-cell">
                                        <a-form-item style="margin-bottom: 0px">
                                            <a-button
                                                v-if="index > 0"
                                                danger
                                                @click.prevent="
                                                    deleteRow(index)
                                                "
                                            >
                                                Delete
                                            </a-button>
                                        </a-form-item>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <a-form-item>
                        <a-button type="button" @click.prevent="addRow">
                            Add More Accounts</a-button
                        >
                        <a-button
                            class="m-1"
                            type="primary"
                            :disabled="form.processing"
                            @click="submitForm"
                            >Submit</a-button
                        >
                    </a-form-item>
                </a-form>
            </div>

            <div class="">
                <div class="">
                    <a-table
                        :columns="columns"
                        :data-source="balances"
                        :loading="loading"
                        class="mt-2"
                        size="small"
                    >
                    </a-table>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { useForm } from "@inertiajs/inertia-vue3";
import Multiselect from "@suadelabs/vue3-multiselect";
import { Form, Input, Button, Select, Table } from "ant-design-vue";

export default {
    components: {
        AppLayout,
        "a-form": Form,
        "a-table": Table,
        "a-form-item": Form.Item,
        "a-input": Input,
        "a-text-area": Input.TextArea,
        "a-button": Button,
        "a-select": Select,
        Multiselect,
    },

    props: {
        errors: Object,
        company_name: Object,
        advisors: Array,
        balances: Object,
    },

    data() {
        return {
            columns: [
                {
                    title: "Company Name",
                    dataIndex: "company_name",
                    width: "50%",
                },
                {
                    title: "Advisors",
                    dataIndex: "advisors",
                },
            ],
            options: this.advisors,
        };
    },

    setup(props) {
        const form = useForm({
            accounts: [
                {
                    advisor_id: props.advisors[0].id,
                    //   type: "CURRENT",
                    name: props.company_name,
                    //   currency: "PKR",
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
        submitForm() {
            //   console.log(this.form);
            this.$inertia.post(route("advisor_accounts.store"), this.form);
            // Send form data to server using axios or fetch
        },
        addRow() {
            this.form.accounts.push({
                advisor_id: this.advisors[0],
                // type: "CURRENT",
                name: this.company_name,
                // currency: "PKR",
            });
        },

        deleteRow(index) {
            this.form.accounts.splice(index, 1);
        },
    },
};
</script>
