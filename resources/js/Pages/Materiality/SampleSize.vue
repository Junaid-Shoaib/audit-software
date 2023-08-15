<template>
    <app-layout>
        <template #header>
            <div class="grid grid-cols-2 items-center">
                <h2 class="header">SAMPLE SIZE DETERMINATION FOR TEST OF DETAILS</h2>
            </div>
        </template>
            <div class="p-2">
                <a-button class="m-1" @click="materiality" size="small">Materiality</a-button>
                <a-button class="m-1" @click="risk_level" size="small">Risk Level</a-button>
                <a-button class="m-1" @click="rsc" size="small">RSC</a-button>
                      <a class="ant-btn ant-btn-sm m-1"
                                    href="/sampleData"> Sample Data Example</a>
                <br>
            </div>
            <div class="p-2">
            <form @submit.prevent="submit_sample" v-bind:action="'sample-size-download'" ref="form_sample">

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Subject">
                    <select  class="w-full ant-select-selection-item" name="account_id" v-model="formData.account_id">
                        <option class="ant-select-selection-item" v-for="option in options" :value="option.id">
                            {{ option.name }}
                        </option>
                    </select>
                    <!-- <a-select
                        v-model:value="formData.account_id"
                        :options="options"
                        show-search
                        :field-names="{
                            label: 'name',
                            value: 'id',
                        }"
                        optionFilterProp="name"
                        mode="single"
                        placeholder="Please select"
                        showArrow
                        class="w-full"
                    /> -->
                </a-form-item>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label=" ">
                    <strong style="background-color: rgb(240 242 245); padding:10px">Population</strong>
                </a-form-item>

                                <!-- Start End -->

                <!-- <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 1">
                    <strong>Classification</strong>
                </a-form-item> -->
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="No of Items">
                    <a-input  placeholder="Enter No of Items" type="number" name="no_of_items" v-model:value="formData.no_of_items" />
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.no_of_items">
                    {{ errors.no_of_items }}
                </div>

                <!-- <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 2">
                    <strong>Completeness</strong>
                </a-form-item> -->
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="PKR">
                    <a-input  placeholder="Enter PKR" name="pkr" type="number" v-model:value="formData.pkr" />
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.pkr">
                    {{ errors.pkr }}
                </div>

                <a-form-item :wrapper-col="{ span: 14, offset: 4 }">
                    <a-button type="primary" @click="submit_sample()">Download</a-button>
                </a-form-item>

            </form>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { Form, Input, Button, DatePicker, Checkbox, Radio, Switch, Textarea, Select } from "ant-design-vue";
import FlashMessage from "@/Layouts/FlashMessage";
import { useForm } from "@inertiajs/inertia-vue3";
import Multiselect from "@suadelabs/vue3-multiselect";
export default {

    components: {
        AppLayout,
        FlashMessage,
        "a-form": Form,
        "a-form-item": Form.Item,
        "a-input": Input,
        "a-textarea": Textarea,
        "a-button": Button,
        "a-date-picker": DatePicker,
        "a-checkbox": Checkbox,
        "a-radio": Radio,
        "a-radio-group": Radio.Group,
        "a-switch": Switch,
        "a-select": Select,
        "a-select-option": Select.Option,
         Multiselect,
        // Treeselect,
    },

    props: {
        errors: Object,
        data: Object,
        accounts: Object,
    },

    data() {
        return {
            options: this.accounts,
            formData: {
                 account_id: this.accounts[0].id,
             }
        };
    },

    methods: {

        submit_sample: function () {
            this.$refs.form_sample.submit();
        },
        risk_level() {
            this.$inertia.get(route("risk_level"));
        },
        rsc() {
            this.$inertia.get(route("rsc"));
        },
        materiality() {
            this.$inertia.get(route("materialities"));
        },
    },
};
</script>
