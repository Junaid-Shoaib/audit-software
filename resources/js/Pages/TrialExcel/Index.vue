<template>
    <app-layout>
        <template #header>
            <div class="grid grid-cols-2 items-center">
                <h2 class="header">Upload TB in Excel</h2>
            </div>
        </template>

        <!-- <FlashMessage /> -->

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-2">
            <!-- <jet-button @click="create" class="ml-2">Create Account</jet-button> -->

            <div class="">
                <!-- overflow-x-auto -->
                <div class="obsolute sm:rounded-2xl">
                    <div
                        class="bg-red-100 border border-red-400 text-red-700 px-4 py-1 rounded obsolute"
                        role="alert"
                        v-if="errors.file"
                    >
                        {{ errors.file }}
                    </div>

                    <a-form
                        :form="form"
                        @submit.prevent="submit"
                        :label-col="{ span: 2 }"
                        :wrapper-col="{ span: 24 }"
                    >
                        <!-- :label-col="{ span: 2 }"
            :wrapper-col="{ span: 14 }" -->
                        <a-form-item>
                            <a-input type="file" v-on:change="onFileChange" />
                        </a-form-item>
                        <a-form-item class="text-center">
                            <a
                                v-if="acc_grp"
                                class="ant-btn ant-btn-primary mx-1"
                                href="/uploadedTB"
                                >Download uploaded file</a
                            >
                            <a-button
                                v-if="acc_grp"
                                danger
                                @click.prevent="destroy"
                                >Delete</a-button
                            >

                            <a-button
                                type="primary"
                                :html-type="submit"
                                class="ml-1"
                                >Submit</a-button
                            >
                            <a
                                class="ant-btn ant-btn-primary mx-1"
                                href="/trialpattern"
                                >Download TB Template</a
                            >
                            <a class="ant-btn ant-btn-primary" href="/lead"
                                >Lead Schedule</a
                            >
                        </a-form-item>
                    </a-form>
                </div>
                <!-- <paginator class="mt-6" :balances="balances" /> -->
            </div>
        </div>
        <!-- <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-1">
            <div class="p-2 bg-gray-200 text-center rounded-xl">
                <h2 class="header">Download Materiality Schedule</h2>
            </div>
            <div class="max-w-7xl m-4 sm:px-6 lg:px-8">
                <div class="">
                    <form
                        @submit.prevent="submit_materiality"
                        v-bind:action="'materiality-download'"
                        ref="form_materiality"
                    >

                    <a-form-item
                    >
                         <a-form-item name="remember" no-style>
                            <a-checkbox v-model:value="form_mt.preTaxSel"> </a-checkbox>
                        </a-form-item>
                        <a-form-item
                            :label-col="{ span: 4 }"
                            :wrapper-col="{ span: 14 }"
                            label="Pre Tax Income :"
                            >
                            <a-input
                                v-model:value="form_mt.preTax"
                                type="text"
                                name="preTax"
                                class="text-center pr-2 pb-2 rounded-md"
                                style="width: 200px"
                                value="5"
                            >
                            </a-input>
                        </a-form-item>

                            <div
                                class="text-red-700 px-4 py-2"
                                role="alert"
                                v-if="errors.preTax"
                            >
                                {{ errors.preTax }}
                            </div>
                        </a-form-item>
                        <a-form-item
                            :label-col="{ span: 4 }"
                            :wrapper-col="{ span: 14 }"
                        >
                             <a-checkbox v-model:value="form_mt.tAssetSel">Total Assets : </a-checkbox>

                            <a-input
                                v-model:value="form_mt.tAsset"
                                type="text"
                                name="tAsset"
                                class="text-center pr-2 pb-2 rounded-md"
                                style="width: 200px"
                                value="0.5"
                            ></a-input>
                            <div
                                class="text-red-700 px-4 py-2"
                                role="alert"
                                v-if="errors.tAsset"
                            >
                                {{ errors.tAsset }}
                            </div>
                        </a-form-item>
                        <a-form-item
                            :label-col="{ span: 4 }"
                            :wrapper-col="{ span: 14 }"
                        >
                             <a-checkbox v-model:value="form_mt.equitySel">Equity : </a-checkbox>

                            <a-input
                                type="text"
                                name="equity"
                                class="text-center pr-2 pb-2 rounded-md"
                                style="width: 200px"
                                value="1"
                            ></a-input>
                            <div
                                class="text-red-700 px-4 py-2"
                                role="alert"
                                v-if="errors.equity"
                            >
                                {{ errors.equity }}
                            </div>
                        </a-form-item>
                        <a-form-item
                            :label-col="{ span: 4 }"
                            :wrapper-col="{ span: 14 }"
                        >
                            <a-checkbox v-model:value="form_mt.netRevenueSel">Total Net Revenues : </a-checkbox>
                            <a-input
                                v-model:value="form_mt.netRevenue"
                                type="text"
                                name="netRevenue"
                                class="text-center pr-2 pb-2 rounded-md"
                                style="width: 200px"
                                value="0.5"
                            ></a-input>
                            <div
                                class="text-red-700 px-4 py-2"
                                role="alert"
                                v-if="errors.netRevenue"
                            >
                                {{ errors.netRevenue }}
                            </div>
                        </a-form-item>

                        <a-form-item
                            :label-col="{ span: 4 }"
                            :wrapper-col="{ span: 14 }"
                            class="text-right"
                        >
                            <a-button
                                class="float-right trailbutton px-4"
                                htmlType="submit"
                                >Download</a-button
                            >
                        </a-form-item>
                    </form>
                </div>
            </div>
        </div> -->
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { Form, Input, Button, DatePicker , Checkbox } from "ant-design-vue";
import FlashMessage from "@/Layouts/FlashMessage";
import { useForm } from "@inertiajs/inertia-vue3";

export default {
    setup() {
            const form = useForm({
                file: null,
            });
            return { form };
        },
    components: {
        AppLayout,
        FlashMessage,
        "a-form": Form,
        "a-form-item": Form.Item,
        "a-input": Input,
        "a-button": Button,
        "a-date-picker": DatePicker,
        "a-checkbox":Checkbox,
        // Treeselect,
    },

    props: {
        errors: Object,
        data: Object,
        fold: Object,
        show_folder: Boolean,
        show_upload: Boolean,
        show_groups: Boolean,
        acc_grp: Object,
    },

    data() {
        return {
            value: null,
            form_mt: {
                preTaxSel: null,
                tAssetSel: null,
                equitySel: null,
                netRevenueSel: null,
                preTax: 5,
                tAsset: 0.5,
                equity: 1,
                netRevenue: 0.5,
            },
        };
    },

    methods: {
        submit() {
            this.form.post(route("trial.read"));
        },
        destroy() {
            this.$inertia.delete(route("excel.destroy"));
        },
        submit_materiality: function () {
            this.$refs.form_materiality.submit();
        },

        onFileChange(e) {
            var files = e.target.files || e.dataTransfer.files;
            if (!files.length) return;
            this.form.file = files[0];
        },
    },
};
</script>
