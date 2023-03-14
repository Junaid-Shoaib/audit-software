<template>
    <app-layout>
        <template #header>
            <h2>Modify Details</h2>
        </template>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
            <a-button @click="back" size="small" class="mr-2">Back</a-button>
            <a-select
                :options="accounts"
                :field-names="{ label: 'name', value: 'id' }"
                show-search
                filterOption="true"
                v-model:value="form.account.id"
                optionFilterProp="name"
                mode="single"
                placeholder="Please select Account"
                showArrow
                @change="accch"
                size="small"
            />

            <div class="relative mt-5 flex-row border-b border-gray-200">
                <div v-if="isError" class="bg-red-600 text-white text-center">
                    {{ firstError }}
                </div>
                <a-form :form="form" @submit.prevent="submit">
                    <div class="ant-table-content">
                        <table
                            style="table-layout: auto"
                            class="ant-table ant-table-small w-full"
                        >
                            <!-- <thead class="bg-gray-700 text-white text-centre font-bold"> -->
                            <thead class="ant-table-thead">
                                <tr class="ant-table-cell">
                                    <th class="ant-table-cell">Action</th>
                                    <th class="ant-table-cell">Date</th>
                                    <th class="ant-table-cell">Description</th>
                                    <th class="ant-table-cell">Cheque</th>
                                    <th class="ant-table-cell">Voucher No</th>
                                    <th class="ant-table-cell">Amount</th>
                                    <th class="ant-table-cell">
                                        Mode of Payment
                                    </th>
                                    <!-- <th class="ant-table-cell">Cash</th>
                            <th class="ant-table-cell">Bank</th>
                            <th class="ant-table-cell">Adjustment</th> -->
                                    <th class="ant-table-cell">
                                        Posting to Ledger
                                    </th>
                                    <th class="ant-table-cell">
                                        Voucher Approved
                                    </th>
                                    <th class="ant-table-cell">
                                        Supporting Document
                                    </th>
                                    <th class="ant-table-cell">
                                        Bank Statement
                                    </th>
                                    <th class="ant-table-cell">E</th>
                                    <th class="ant-table-cell">F</th>
                                    <th class="ant-table-cell">Remark</th>
                                    <!-- <th class="py-1 px-4">Conclusion</th>
                            <th class="py-1 px-4">Company</th>
                            <th class="py-1 px-4">Year</th>
                            <th class="py-1 px-4">Account</th> -->
                                </tr>
                            </thead>
                            <tbody class="ant-table-tbody">
                                <tr
                                    v-for="(detail, index) in this.form
                                        .balances"
                                    :key="detail.id"
                                >
                                    <!-- <tr class="table2"> -->
                                    <td class="ant-table-cell">
                                        <a-form-item style="margin-bottom: 0px">
                                            <a-button
                                                v-if="index >> 0"
                                                @click.prevent="
                                                    deleteRow(index)
                                                "
                                                danger
                                                size="small"
                                                >Delete</a-button
                                            >
                                        </a-form-item>
                                    </td>
                                    <td class="ant-table-cell">
                                        <a-form-item style="margin-bottom: 0px">
                                            <a-input
                                                type="date"
                                                v-model:value="detail.date"
                                                label="date"
                                                placeholder="Enter date:"
                                                class="w-full"
                                            />
                                        </a-form-item>
                                    </td>
                                    <td class="ant-table-cell">
                                        <a-form-item style="margin-bottom: 0px">
                                            <a-input
                                                type="text"
                                                v-model:value="
                                                    detail.description
                                                "
                                                label="description"
                                                placeholder="Enter description:"
                                            />
                                        </a-form-item>
                                    </td>
                                    <td class="ant-table-cell">
                                        <a-form-item style="margin-bottom: 0px">
                                            <a-input
                                                type="text"
                                                v-model:value="detail.cheque"
                                                label="cheque"
                                                placeholder="Enter cheque:"
                                            />
                                        </a-form-item>
                                    </td>
                                    <td class="ant-table-cell">
                                        <a-form-item style="margin-bottom: 0px">
                                            <a-input
                                                type="text"
                                                v-model:value="
                                                    detail.voucher_no
                                                "
                                                label="voucher_no"
                                                placeholder="Enter voucher no :"
                                            />
                                        </a-form-item>
                                    </td>
                                    <td class="ant-table-cell">
                                        <a-form-item style="margin-bottom: 0px">
                                            <a-input
                                                type="number"
                                                v-model:value="detail.amount"
                                                label="amount"
                                                placeholder="Enter amount"
                                            />
                                        </a-form-item>
                                    </td>
                                    <td class="ant-table-cell">
                                        <a-form-item style="margin-bottom: 0px">
                                            <a-select
                                                v-model:value="detail.modeOfPay"
                                                :options="modeOfPays"
                                                :field-names="{
                                                    label: 'name',
                                                    value: 'val',
                                                }"
                                                optionFilterProp="name"
                                                mode="single"
                                                placeholder="Please select"
                                                showArrow
                                                class="w-full"
                                            />
                                        </a-form-item>
                                    </td>
                                    <td class="ant-table-cell">
                                        <div class="w-32 text-center">
                                            <a-form-item
                                                style="margin-bottom: 0px"
                                            >
                                                <a-checkbox
                                                    type="checkbox"
                                                    id="checkbox"
                                                    v-model:checked="detail.a"
                                                />
                                            </a-form-item>
                                            <!-- <a-input type="checkbox" id="checkbox" v-model="detail.a" /> -->
                                        </div>
                                    </td>
                                    <td class="ant-table-cell">
                                        <div class="w-32 text-center">
                                            <a-form-item
                                                style="margin-bottom: 0px"
                                            >
                                                <a-checkbox
                                                    type="checkbox"
                                                    id="checkbox"
                                                    v-model:checked="detail.b"
                                                />
                                            </a-form-item>
                                            <!-- <a-input type="checkbox" id="checkbox" v-model="detail.b" /> -->
                                        </div>
                                    </td>
                                    <td class="ant-table-cell">
                                        <div class="w-32 text-center">
                                            <a-form-item
                                                style="margin-bottom: 0px"
                                            >
                                                <a-checkbox
                                                    type="checkbox"
                                                    id="checkbox"
                                                    v-model:checked="detail.c"
                                                />
                                            </a-form-item>
                                            <!-- <a-input type="checkbox" id="checkbox" v-model="detail.c" /> -->
                                        </div>
                                    </td>
                                    <td class="ant-table-cell">
                                        <div class="w-32 text-center">
                                            <a-form-item
                                                style="margin-bottom: 0px"
                                            >
                                                <a-checkbox
                                                    type="checkbox"
                                                    id="checkbox"
                                                    v-model:checked="detail.d"
                                                />
                                            </a-form-item>
                                        </div>
                                    </td>
                                    <td class="ant-table-cell">
                                        <div class="w-32 text-center">
                                            <a-form-item
                                                style="margin-bottom: 0px"
                                            >
                                                <a-checkbox
                                                    type="checkbox"
                                                    id="checkbox"
                                                    v-model:checked="detail.e"
                                                />
                                            </a-form-item>
                                            <!-- <a-input type="checkbox" id="checkbox" v-model="detail.e" /> -->
                                        </div>
                                    </td>
                                    <td class="ant-table-cell">
                                        <div class="w-32 text-center">
                                            <a-form-item
                                                style="margin-bottom: 0px"
                                            >
                                                <a-checkbox
                                                    type="checkbox"
                                                    id="checkbox"
                                                    v-model:checked="detail.f"
                                                />
                                            </a-form-item>
                                            <!-- <a-input type="checkbox" id="checkbox" v-model="detail.f" /> -->
                                        </div>
                                    </td>
                                    <td class="ant-table-cell">
                                        <a-form-item style="margin-bottom: 0px">
                                            <a-input
                                                type="text"
                                                v-model:value="detail.remark"
                                                label="remarks"
                                                placeholder="Enter remarks:"
                                            />
                                        </a-form-item>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <a-form-item style="margin-top: 10px">
                        <a-button @click="addRow">Add row</a-button>
                        <a-button class="ml-2" type="primary" htmlType="submit">
                            Update Details
                        </a-button>
                    </a-form-item>
                </a-form>
            </div>
        </div>
    </app-layout>
</template>

<style src="@suadelabs/vue3-multiselect/dist/vue3-multiselect.css"></style>
<script>
import AppLayout from "@/Layouts/AppLayout";
import JetButton from "@/Jetstream/Button";
import Multiselect from "@suadelabs/vue3-multiselect";
import { Form, Input, Button, Select, Checkbox } from "ant-design-vue";

export default {
    components: {
        AppLayout,
        JetButton,
        Multiselect,
        "a-form": Form,
        "a-form-item": Form.Item,
        "a-input": Input,
        "a-text-area": Input.TextArea,
        "a-button": Button,
        "a-select": Select,
        "a-checkbox": Checkbox,
    },

    props: {
        errors: Object,
        types: Object,
        company: Object,
        balances: Array,
        account: Object,
        accounts: Object,
    },

    data() {
        return {
            modeOfPays: [
                { name: "Cash", val: "cash" },
                { name: "Bank", val: "bank" },
                { name: "Adjustment", val: "adjustment" },
            ],
            form: this.$inertia.form({
                accounts: this.accounts,
                account: this.account,
                account_id: this.account["id"],
                balances:
                    this.balances != ""
                        ? this.balances
                        : [
                              {
                                  date: null,
                                  description: null,
                                  cheque: null,
                                  voucher_no: null,
                                  amount: null,
                                  // cash: null,
                                  // bank: null,
                                  // adjustment: null,
                                  modeOfPay: "Cash",
                                  a: false,
                                  b: false,
                                  c: false,
                                  d: false,
                                  e: false,
                                  f: false,
                                  remark: null,
                                  account_id: this.account["id"],
                              },
                          ],
            }),
        };
    },

    methods: {
        submit() {
            this.$inertia.put(
                route("details.update", this.form.account["id"]),
                this.form
            );
        },
        accch() {
            this.$inertia.get(route("details.edit", this.form.account["id"]));
        },
        back() {
            this.$inertia.get(route("details"));
        },

        addRow() {
            this.form.balances.push({
                date: null,
                description: null,
                cheque: null,
                voucher_no: null,
                amount: null,
                // cash: null,
                // bank: null,
                // adjustment: null,
                modeOfPay: "Cash",
                a: false,
                b: false,
                c: false,
                d: false,
                e: false,
                f: false,
                remark: null,
                account_id: this.form.account["id"],
            });
        },

        deleteRow(index) {
            this.form.balances.splice(index, 1);
        },
    },
    watch: {
        errors: function () {
            if (this.errors) {
                this.firstError = this.errors[Object.keys(this.errors)[0]];
                this.isError = true;
            }
        },
    },
};
</script>
<style>
input[type="text"],
input[type="password"],
input[type="number"],
textarea {
    width: 200px !important;
}
</style>
