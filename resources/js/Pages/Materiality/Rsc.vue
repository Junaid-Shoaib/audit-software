<template>
    <app-layout>
        <template #header>
            <div class="grid grid-cols-2 items-center">
                <h2 class="header">RISK ASSESSMENT, SAMPLE AND CONCLUSION</h2>
            </div>
        </template>
        <div class="p-2">
             <a-button @click="materiality" size="small">Materiality</a-button>
            <a-button @click="risk_level" size="small">Risk Level</a-button>
            <div class="p-2 bg-gray-200 text-center rounded-xl">
                <h2 class="header">RISK ASSESSMENT, SAMPLE WORK-OUT AND CONCLUSION</h2>
            </div>
            <br>
            <form @submit.prevent="submit_rsc" v-bind:action="'rsc-download'" ref="form_rsc">

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Subject">
                    <select  name="account_id" v-model="formData.account_id">
                        <option v-for="option in options" :value="option.id">
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
                    <strong style="background-color: rgb(240 242 245); padding:10px">Performance Materiality (PM) - Assertion wise</strong>
                </a-form-item>

                                <!-- Start End -->

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 1">
                    <strong>Classification</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="classification" v-model:value="formData.classification">
                        <a-radio value="Low">Low</a-radio>
                        <a-radio value="Medium">Medium</a-radio>
                        <a-radio value="High">High</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.classification">
                    {{ errors.classification }}
                </div>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 2">
                    <strong>Completeness</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="completeness" v-model:value="formData.completeness">
                         <a-radio value="Low">Low</a-radio>
                        <a-radio value="Medium">Medium</a-radio>
                        <a-radio value="High">High</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.completeness">
                    {{ errors.completeness }}
                </div>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 3">
                    <strong>Accuracy</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="accuracy" v-model:value="formData.accuracy">
                         <a-radio value="Low">Low</a-radio>
                        <a-radio value="Medium">Medium</a-radio>
                        <a-radio value="High">High</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.accuracy">
                    {{ errors.accuracy }}
                </div>


                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 4">
                    <strong>Cut off</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="cut_off" v-model:value="formData.cut_off">
                         <a-radio value="Low">Low</a-radio>
                        <a-radio value="Medium">Medium</a-radio>
                        <a-radio value="High">High</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.cut_off">
                    {{ errors.cut_off }}
                </div>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 5">
                    <strong>Presentation & Disclosure </strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="pre_dis" v-model:value="formData.pre_dis">
                         <a-radio value="Low">Low</a-radio>
                            <a-radio value="Medium">Medium</a-radio>
                            <a-radio value="High">High</a-radio>
                        </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.pre_dis">
                    {{ errors.pre_dis }}
                </div>

                <!-- Start End-->


                <!-- high Start -->
                <!-- <div class="Comment Code">
                    <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 1">
                        <strong>Classification</strong>
                    </a-form-item>
                    <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                        <a-radio-group name="high_classification" v-model:value="high_classification">
                            <a-radio value="1.2">Low</a-radio>
                            <a-radio value="1.4">Medium</a-radio>
                            <a-radio value="1.6">High</a-radio>
                        </a-radio-group>
                    </a-form-item>
                    <div class="text-red-700 px-4" role="alert" v-if="errors.high_classification">
                        {{ errors.high_classification }}
                    </div>

                    <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 2">
                        <strong>Completeness</strong>
                    </a-form-item>
                    <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                        <a-radio-group name="high_completeness" v-model:value="high_completeness">
                            <a-radio value="1.2">Low</a-radio>
                            <a-radio value="1.4">Medium</a-radio>
                            <a-radio value="1.6">High</a-radio>
                        </a-radio-group>
                    </a-form-item>
                    <div class="text-red-700 px-4" role="alert" v-if="errors.high_completeness">
                        {{ errors.high_completeness }}
                    </div>

                    <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 3">
                        <strong>Accuracy</strong>
                    </a-form-item>
                    <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                        <a-radio-group name="high_accuracy" v-model:value="high_accuracy">
                            <a-radio value="1.2">Low</a-radio>
                            <a-radio value="1.4">Medium</a-radio>
                            <a-radio value="1.6">High</a-radio>
                        </a-radio-group>
                    </a-form-item>
                    <div class="text-red-700 px-4" role="alert" v-if="errors.high_accuracy">
                        {{ errors.high_accuracy }}
                    </div>


                    <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 4">
                        <strong>Cut off</strong>
                    </a-form-item>
                    <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                        <a-radio-group name="high_cut_off" v-model:value="high_cut_off">
                            <a-radio value="1.2">Low</a-radio>
                            <a-radio value="1.4">Medium</a-radio>
                            <a-radio value="1.6">High</a-radio>
                        </a-radio-group>
                    </a-form-item>
                    <div class="text-red-700 px-4" role="alert" v-if="errors.high_cut_off">
                        {{ errors.high_cut_off }}
                    </div>

                    <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 5">
                        <strong>Presentation & Disclosure </strong>
                    </a-form-item>
                    <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                        <a-radio-group name="high_pre_dis" v-model:value="high_pre_dis">
                            <a-radio value="1.2">Low</a-radio>
                            <a-radio value="1.4">Medium</a-radio>
                            <a-radio value="1.6">High</a-radio>
                        </a-radio-group>
                    </a-form-item>
                    <div class="text-red-700 px-4" role="alert" v-if="errors.high_pre_dis">
                        {{ errors.high_pre_dis }}
                    </div>

                </div> -->
                <!-- High End-->
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label=" ">
                        <strong style="background-color: rgb(240 242 245); padding:10px">Particulars</strong>
                    </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 1">
                    <strong>Observed no exceptions to the identified risks.</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer1" v-model:value="formData.answer1">
                        <a-radio value="Yes">Yes</a-radio>
                        <a-radio value="Not-Applicable">Not Applicable</a-radio>
                        <a-radio value="No">No</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer1">
                    {{ errors.answer1 }}
                </div>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 2">
                    <strong>Performed the work as per Plan and documented all the findings and results.</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer2" v-model:value="formData.answer2">
                       <a-radio value="Yes">Yes</a-radio>
                            <a-radio value="Not-Applicable">Not Applicable</a-radio>
                            <a-radio value="No">No</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer2">
                    {{ errors.answer2 }}
                </div>


                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 3">
                    <strong>Obtained Sufficient and Appropriate Audit evidences has been obtained to support the Audit objectives and disclosures.</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer3" v-model:value="formData.answer3">
                    <a-radio value="Yes">Yes</a-radio>
                            <a-radio value="Not-Applicable">Not Applicable</a-radio>
                            <a-radio value="No">No</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer3">
                    {{ errors.answer3 }}
                </div>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 4">
                    <strong>The balances are appropriately presented and disclosed in the Financial statements and are in accordance with the Companies Act, 2017 and the applicable Financial Reporting Framework.</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer4" v-model:value="formData.answer4">
                        <a-radio value="Yes">Yes</a-radio>
                        <a-radio value="Not-Applicable">Not Applicable</a-radio>
                        <a-radio value="No">No</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer4">
                    {{ errors.answer4 }}
                </div>


                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 5">
                    <strong>All the related accounting policies has been reviewed and ensured they are in line with the applicable reporting framework and consistent with prior year.</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer5" v-model:value="formData.answer5">
                        <a-radio value="Yes">Yes</a-radio>
                        <a-radio value="Not-Applicable">Not Applicable</a-radio>
                        <a-radio value="No">No</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer5">
                    {{ errors.answer5 }}
                </div>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 6">
                    <strong>Considered and concluded that there is no need to revise the materiality in view of Audit evidence obtained.</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer6" v-model:value="formData.answer6">
                        <a-radio value="Yes">Yes</a-radio>
                        <a-radio value="Not-Applicable">Not Applicable</a-radio>
                        <a-radio value="No">No</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer6">
                    {{ errors.answer6 }}
                </div>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 7">
                    <strong>No material misstatement identified.</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer7" v-model:value="formData.answer7">
                        <a-radio value="Yes">Yes</a-radio>
                        <a-radio value="Not-Applicable">Not Applicable</a-radio>
                        <a-radio value="No">No</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer7">
                    {{ errors.answer7 }}
                </div>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 8">
                    <strong>Based on the above, conclude whether the amount is fairly stated.</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer8" v-model:value="formData.answer8">
                        <a-radio value="Yes">Yes</a-radio>
                        <a-radio value="Not-Applicable">Not Applicable</a-radio>
                        <a-radio value="No">No</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer8">
                    {{ errors.answer8 }}
                </div>

                <a-form-item :wrapper-col="{ span: 14, offset: 4 }">
                    <a-button type="primary" @click="submit_rsc()">Download</a-button>
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
                 classification: 'Low',
                 completeness: 'Low',
                 accuracy: 'Low',
                 cut_off: 'Low',
                 pre_dis: 'Low',
                 answer1: 'Yes',
                 answer2: 'Yes',
                 answer3: 'Yes',
                 answer4: 'Yes',
                 answer5: 'Yes',
                 answer6: 'Yes',
                 answer7: 'Yes',
                 answer8: 'Yes',
             }
        };
    },

    methods: {

        submit_rsc: function () {

            this.$refs.form_rsc.submit();
        },
        risk_level() {
            this.$inertia.get(route("risk_level"));
        },
        materiality() {
            this.$inertia.get(route("materialities"));
        },
    },
};
</script>
