<template>
    <app-layout>
        <template #header>
            <h2>Update Bank Balances</h2>
        </template>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
            <Button :href="route('balances')">Back </Button>

            <div
                class="relative mt-5 flex-row border-t border-b border-gray-200"
            >
                <Form :form="form" @submit.prevent="submit">
                    <div class="ant-table-content">
                        <!-- <div
            class="
              px-4
              py-2
              bg-gray-100
              border-t border-gray-200
              flex
              justify-start
              items-center
            "
          >
            <inertia-link
              class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
              :href="route('balances')"
              >Back
            </inertia-link>
          </div> -->
                        <!-- <button
            class="border bg-indigo-300 rounded-xl px-4 py-2 m-4"
            @click.prevent="addRow"
          >
            Add row
          </button> -->
                        <div v-if="isError">{{ firstError }}</div>
                        <table class="w-full">
                            <thead class="ant-table-thead">
                                <tr>
                                    <th class="px-3 pt-3 pb-3 border">
                                        Branches
                                    </th>
                                    <th class="px-3 pt-3 pb-3 border">
                                        Ledger
                                    </th>
                                    <th class="px-3 pt-3 pb-3 border">
                                        Statement
                                    </th>
                                    <th class="px-3 pt-3 pb-3 border">
                                        Confirmation
                                    </th>
                                    <!-- <th class="px-4 pt-4 pb-4 border">Action</th> -->
                                </tr>
                            </thead>
                            <tbody class="ant-table-tbody">
                                <tr
                                    class="ant-table-row ant-table-row-level-0"
                                    v-for="balance in data"
                                    :key="balance.id"
                                >
                                    <td class="ant-table-cell">
                                        <FormItem style="margin-bottom: 0px">
                                            <Input
                                                v-model:value="balance.branches"
                                                disabled
                                                type="text"
                                                class="w-full"
                                            />
                                        </FormItem>
                                    </td>
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
                                            <Input
                                                v-model:value="
                                                    balance.statement
                                                "
                                                type="number"
                                                class="w-full"
                                            />
                                        </FormItem>
                                    </td>
                                    <td class="ant-table-cell">
                                        <FormItem style="margin-bottom: 0px">
                                            <Input
                                                v-model:value="
                                                    balance.confirmation
                                                "
                                                type="number"
                                                class="w-full"
                                            />
                                        </FormItem>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <FormItem>
                        <Button class="m-1" type="primary" @click="submit"
                            >Update Balance</Button
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
            this.$inertia.put(route("balances.update", this.balances[0]), {
                balances: this.balances,
            });
        },
        // doFormat($item) {
        //   var $i = format($item, "yyyy-MM-dd");
        //   return $i;
        // },

        addRow() {
            this.balances.push({
                ledger: "",
                statement: "",
                confirmation: "",
                account_id: this.accounts[0].id,
            });
        },

        deleteRow(index) {
            this.balances.splice(index, 1);
        },
    },
};
</script>
