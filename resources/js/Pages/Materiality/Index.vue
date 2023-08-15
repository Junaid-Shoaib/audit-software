<template>
    <app-layout>
        <template #header>
            <div class="grid grid-cols-2 items-center">
                <h2 class="header">Materiality</h2>
            </div>
        </template>
            <div class="p-2">
                    <a-button class="m-1" @click="risk_level" size="small">Risk Level</a-button>
                    <a-button class="m-1" @click="rsc" size="small">RSC</a-button>
                    <a-button class="m-1" @click="sample_size" size="small">Sample Size</a-button>
                    <br>
                </div>
                <div class="p-2">

                    <form
                        @submit.prevent="submit_materiality"
                        v-bind:action="'materiality-download'"
                        ref="form_materiality"
                    >

                    <a-form-item  :wrapper-col="{ span: 14, offset: 4 }">
                        <a-checkbox name="preTaxSelect" value="1"  v-model:checked="preTaxChecked">Pre Tax Income</a-checkbox>
                        <a-input class="text-center" name="preTax" v-model:value="preTaxValue" style="margin-left: 10px; width: 70%" />
                        <div class="text-red-700 px-4"  role="alert"   v-if="errors.preTaxSelect">
                            {{ errors.preTaxSelect }}
                        </div>
                    </a-form-item>
                    <a-form-item  :wrapper-col="{ span: 14, offset: 4 }">
                        <a-checkbox name="TotalAssetSelect" value="1" v-model:checked="tAssetChecked">Total Assets</a-checkbox>
                        <a-input class="text-center" name="tAsset" v-model:value="tAssetValue" style="margin-left: 10px; width: 75%" />
                        <div class="text-red-700 px-4"  role="alert"   v-if="errors.TotalAssetSelect">
                                {{ errors.TotalAssetSelect }}
                        </div>
                    </a-form-item>

                    <a-form-item  :wrapper-col="{ span: 14, offset: 4 }">
                        <a-checkbox name="equitySelect" value="1" v-model:checked="equityChecked">Equity</a-checkbox>
                        <a-input class="text-center" name="equity" v-model:value="equityValue" style="margin-left: 10px; width: 80%" />
                        <div class="text-red-700 px-4"  role="alert"   v-if="errors.equitySelect">
                                {{ errors.equitySelect }}
                        </div>
                    </a-form-item>

                    <a-form-item  :wrapper-col="{ span: 14, offset: 4 }">
                        <a-checkbox name="netRevenueSelect" value="1" v-model:checked="netRevenueChecked">Total Net Revenues</a-checkbox>
                        <a-input class="text-center" name="netRevenue" v-model:value="netRevenueValue" style="margin-left: 10px; width: 70%" />
                        <div class="text-red-700 px-4"  role="alert"   v-if="errors.netRevenueSelect">
                            {{ errors.netRevenueSelect }}
                        </div>
                    </a-form-item>

                    <a-form-item :wrapper-col="{ span: 14, offset: 4 }">
                        <a-checkbox  name="adptSel" disabled checked value="1">ADPT as a Percentage</a-checkbox>
                        <a-input class="text-center" name="adpt" v-model:value="adptValue" style="margin-left: 10px; width:60%" />
                        <div class="text-red-700 px-4"  role="alert"   v-if="errors.adpt">
                            {{ errors.adpt }}
                        </div>
                    </a-form-item>

                    <a-form-item :label-col="{ span: 4 }"
                            :wrapper-col="{ span: 14 }" label="Question 1">
                        <strong>Document the source of relevant financial data used for determining Materiality and, if relevant, the amount and rationale of any adjustment(s) to the amount of the benchmark:</strong>
                    </a-form-item>
                    <a-form-item :label-col="{ span: 4 }"
                            :wrapper-col="{ span: 14 }" label="Answer">
                        <a-textarea
                           name="answer1"
                           v-model:value="answer1"
                            placeholder="Document the source of relevant financial"
                            :auto-size="{ minRows: 2, maxRows: 5 }"
                            > 2022</a-textarea>
                            <div class="text-red-700 px-4"  role="alert"   v-if="errors.answer1">
                                    {{ errors.answer1 }}
                            </div>
                    </a-form-item>

                    <a-form-item :label-col="{ span: 4 }"
                            :wrapper-col="{ span: 14 }" label="Question 2">
                        <strong>Did the entity has misstatement that were accumulated in prior period audits?</strong>
                    </a-form-item>
                    <a-form-item :label-col="{ span: 4 }"
                            :wrapper-col="{ span: 14 }" label="Answer">
                        <a-radio-group name="answer2" v-model:value="answer2" >
                                <a-radio  value="yes">Yes</a-radio>
                                <a-radio value="no">No</a-radio>
                                <a-radio value="not_applicable">Not applicable</a-radio>
                        </a-radio-group>
                        <div class="text-red-700 px-4"  role="alert"   v-if="errors.answer2">
                                   {{ errors.answer2 }}
                           </div>
                    </a-form-item>

                    <a-form-item :label-col="{ span: 4 }"
                            :wrapper-col="{ span: 14 }" label="Question 3">
                        <strong>Document factor(s) considered in determining Materiality and, if relevant, the rationale for significant change in percentage of the benchmark from the previous audit(s):</strong>
                    </a-form-item>
                    <a-form-item :label-col="{ span: 4 }"
                            :wrapper-col="{ span: 14 }" label="Answer">
                        <a-textarea
                            name="answer3"
                            v-model:value="answer3"
                            placeholder="Document factor(s) considered in determining"
                            :auto-size="{ minRows: 2, maxRows: 5 }"
                            />
                            <div class="text-red-700 px-4"  role="alert"   v-if="errors.answer3">
                                               {{ errors.answer3 }}
                                       </div>
                    </a-form-item>

                    <a-form-item :label-col="{ span: 4 }"
                        :wrapper-col="{ span: 14 }" label="Question 4">
                        <strong>Are there any account(s) and disclosure(s) that will be audited using lower materiality level than the one set above, due to users' expectation / needs?</strong>
                    </a-form-item>
                    <a-form-item :label-col="{ span: 4 }"
                        :wrapper-col="{ span: 14 }" label="Answer"
                     >
                        <a-radio-group    name="answer4" v-model:value="answer4" >
                            <a-radio  value="yes">Yes</a-radio>
                            <a-radio value="no">No</a-radio>
                            <a-radio value="not_applicable">Not applicable</a-radio>
                        </a-radio-group>
                        <div class="text-red-700 px-4"  role="alert"   v-if="errors.answer4">
                                               {{ errors.answer4 }}
                                       </div>
                    </a-form-item>

                    <a-form-item :label-col="{ span: 4 }"
                        :wrapper-col="{ span: 14 }" label="Question 5">
                            <strong>Document the factors considered in determining PM</strong>
                    </a-form-item>
                    <a-form-item :label-col="{ span: 4 }"
                        :wrapper-col="{ span: 14 }" label="Answer">
                                <a-textarea
                                name="answer5"
                                v-model:value="answer5"
                                placeholder="Document the factors considered in determining PM"
                                :auto-size="{ minRows: 2, maxRows: 5 }"
                                />
                                <div class="text-red-700 px-4"  role="alert"   v-if="errors.answer5">
                                                           {{ errors.answer5 }}
                                                   </div>
                    </a-form-item>

                    <a-form-item :label-col="{ span: 4 }"
                        :wrapper-col="{ span: 14 }" label="Question 6">
                        <strong>Are there any significant account(s) and disclosure(s) that will be audited using lower PM, than the one set above, due to aggregation of risk?</strong>
                    </a-form-item>
                    <a-form-item :label-col="{ span: 4 }"
                        :wrapper-col="{ span: 14 }" label="Answer">
                        <a-radio-group name="answer6" v-model:value="answer6">
                            <a-radio  value="yes">Yes</a-radio>
                            <a-radio value="no">No</a-radio>
                            <a-radio value="not_applicable">Not applicable</a-radio>
                        </a-radio-group>
                        <div class="text-red-700 px-4"  role="alert"   v-if="errors.answer6">
                                {{ errors.answer6 }}
                        </div>
                    </a-form-item>
                    <a-form-item :label-col="{ span: 4 }"
                        :wrapper-col="{ span: 14 }" label="Question 7">
                                <strong> Has the engagement team determined a higher ADPT for accumulating reclassification misstatements?</strong>
                    </a-form-item>
                    <a-form-item :label-col="{ span: 4 }"
                        :wrapper-col="{ span: 14 }" label="Answer">
                        <a-radio-group  name="answer7" v-model:value="answer7" >
                            <a-radio  value="yes">Yes</a-radio>
                            <a-radio value="no">No</a-radio>
                        </a-radio-group>
                        <div class="text-red-700 px-4"  role="alert"   v-if="errors.answer7">
                                {{ errors.answer7 }}
                        </div>
                    </a-form-item>

                    <a-form-item :wrapper-col="{ span: 14, offset: 4 }">
                        <a-button type="primary"  htmlType="submit">Download</a-button>
                    </a-form-item>

                <!-- </a-form> -->
                </form>
            </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { Form, Input, Button, DatePicker , Checkbox , Radio, Switch , Textarea , Select} from "ant-design-vue";
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
        "a-textarea": Textarea,
        "a-button": Button,
        "a-date-picker": DatePicker,
        "a-checkbox":Checkbox,
        "a-radio": Radio,
        "a-radio-group": Radio.Group,
        "a-switch": Switch,
        "a-select": Select,
        "a-select-option": Select.Option,
        // Treeselect,
    },

    props: {
        errors: Object,
        data: Object,
    },

    data() {
        return {
                answer1: 'Source of financial data: Draft Financial Statements for the year ended June 30,',
                answer2: 'yes',
                answer3: 'Materiality has been determined in consistency with last year.',
                answer4: 'yes',
                answer5: 'We have selected the PM to be consistent with last year as we have not noted any significant change in the circumstances of the Company. Moreover, we have noted that the Company has a strong control environment and has no history of material weakness / control deficiency.',
                answer6: 'yes',
                answer7: 'yes',
                allCheckedValue: 5,
                preTaxChecked: false,
                preTaxValue: 5,
                tAssetChecked: false,
                tAssetValue: 0.5,
                equityChecked: false,
                equityValue: 1,
                netRevenueChecked: false,
                netRevenueValue: 0.5,
                adptValue: 5
            // form_mt: {
            //     preTaxSelect: null,
            //     TotalAssetSelect: null,
            //     equitySelect: null,
            //     netRevenueSelect: null,
            //     adptSel: null,
            //     preTax: 5,
            //     tAsset: 0.5,
            //     equity: 1,
            //     netRevenue: 0.5,
            //     adpt: 5,
            // },
        };
    },

    methods: {
        submit_materiality: function () {
            this.$refs.form_materiality.submit();
        },
        risk_level() {
            this.$inertia.get(route("risk_level"));
        },
        rsc() {
            this.$inertia.get(route("rsc"));
        },
        sample_size() {
            this.$inertia.get(route("sample_size"));
        },
    },
};
</script>
