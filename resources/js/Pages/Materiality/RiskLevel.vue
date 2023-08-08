<template>
    <app-layout>
        <template #header>
            <div class="grid grid-cols-2 items-center">
                <h2 class="header">Overall Financial Statement Risk level</h2>
            </div>
        </template>
        <div class="p-2">
            <a-button @click="materiality" size="small">Materiality</a-button>
            <a-button @click="rsc" size="small">RSC</a-button>
            <div class="p-2 bg-gray-200 text-center rounded-xl">
                <h2 class="header">Overall Financial Statement Risk level</h2>
            </div>
            <br>
            <form @submit.prevent="submit_risk_level" v-bind:action="'risk-level-download'" ref="form_risk_level">
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 1">
                    <strong>Overall financial statement risk level</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer1" v-model:value="answer1">
                        <a-radio value="Low">Low</a-radio>
                        <a-radio value="Medium">Medium</a-radio>
                        <a-radio value="High">High</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer1">
                    {{ errors.answer1 }}
                </div>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label=" ">
                    <strong style="background-color: rgb(240 242 245); padding:10px">External Interest in accounts</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 2">
                    <strong>Share ownership?</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer2" v-model:value="answer2">
                        <a-radio value="Director/Management">Director/ Management</a-radio>
                        <a-radio value="Small-Minority">Small Minority</a-radio>
                        <a-radio value="External-Interest">External Interest</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer2">
                    {{ errors.answer2 }}
                </div>


                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 3">
                    <strong>Third party users?</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer3" v-model:value="answer3">
                        <a-radio value="No">No</a-radio>
                        <a-radio value="Possibly">Possibly</a-radio>
                        <a-radio value="Yes">Yes</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer3">
                    {{ errors.answer3 }}
                </div>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 4">
                    <strong>Potential sale of business?</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer4" v-model:value="answer4">
                        <a-radio value="No">No</a-radio>
                        <a-radio value="Possibly">Possibly</a-radio>
                        <a-radio value="Yes">Yes</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer4">
                    {{ errors.answer4 }}
                </div>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label=" ">
                    <strong style="background-color: rgb(240 242 245); padding:10px">Management of Business</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 5">
                    <strong>Is the owner/manager sufficiently involved in the day to day running of the business?</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer5" v-model:value="answer5">
                        <a-radio value="Yes">Yes</a-radio>
                        <a-radio value="Sometimes">Sometimes</a-radio>
                        <a-radio value="No">No</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer5">
                    {{ errors.answer5 }}
                </div>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 6">
                    <strong>Management competence</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer6" v-model:value="answer6">
                        <a-radio value="Good">Good</a-radio>
                        <a-radio value="Adequate">Adequate</a-radio>
                        <a-radio value="Poor">Poor</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer6">
                    {{ errors.answer6 }}
                </div>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 7">
                    <strong>Management changes or key staff leaving?</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer7" v-model:value="answer7">
                        <a-radio value="None">None</a-radio>
                        <a-radio value="Some">Some</a-radio>
                        <a-radio value="Many">Many</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer7">
                    {{ errors.answer7 }}
                </div>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 8">
                    <strong>The owner/manager monitors the performance of the business on an ongoing basis</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer8" v-model:value="answer8">
                        <a-radio value="Yes">Yes</a-radio>
                        <a-radio value="Sometimes">Sometimes</a-radio>
                        <a-radio value="No">No</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer8">
                    {{ errors.answer8 }}
                </div>


                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 9">
                    <strong>The company has a strong ethical culture</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer9" v-model:value="answer9">
                        <a-radio value="Yes">Yes</a-radio>
                        <a-radio value="Not-Applicable">Not Applicable</a-radio>
                        <a-radio value="No">No</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer9">
                    {{ errors.answer9 }}
                </div>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label=" ">
                    <strong style="background-color: rgb(240 242 245); padding:10px">Going Concern</strong>
                </a-form-item>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 10">
                    <strong>Nature of business/industry</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer10" v-model:value="answer10">
                        <a-radio value="Low-Risk">Low Risk</a-radio>
                        <a-radio value="Medium-Risk">Medium Risk</a-radio>
                        <a-radio value="High-Risk">High Risk</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer10">
                    {{ errors.answer10 }}
                </div>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 11">
                    <strong>Has the company lost any major customers or suppliers, where the impact on the business could be
                        significant?</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer11" v-model:value="answer11">
                        <a-radio value="No">No</a-radio>
                        <a-radio value="Not-Applicable">Not Applicable</a-radio>
                        <a-radio value="Yes">Yes</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer11">
                    {{ errors.answer11 }}
                </div>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 12">
                    <strong>What doubts are there regarding the company’s ability to continue as a going concern?</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer12" v-model:value="answer12">
                        <a-radio value="No">No</a-radio>
                        <a-radio value="Yes">Yes</a-radio>
                        <a-radio value="Not-Applicable">Not Applicable</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer12">
                    {{ errors.answer12 }}
                </div>



                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 13">
                    <strong>Is the company dependent on a few major customers and/or suppliers?</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer13" v-model:value="answer13">
                        <a-radio value="No">No</a-radio>
                        <a-radio value="Insignificant">Insignificant</a-radio>
                        <a-radio value="Major">Major</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer13">
                    {{ errors.answer13 }}
                </div>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 14">
                    <strong>Is there a risk of technical obsolescence of products or services?</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer14" v-model:value="answer14">
                        <a-radio value="No">No</a-radio>
                        <a-radio value="Possibly">Possibly</a-radio>
                        <a-radio value="Yes">Yes</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer14">
                    {{ errors.answer14 }}
                </div>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 15">
                    <strong>Are there a large number of business locations and/or a wide geographical spread of
                        activities?</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer15" v-model:value="answer15">
                        <a-radio value="No">No</a-radio>
                        <a-radio value="Some">Some</a-radio>
                        <a-radio value="Yes">Yes, a lot</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer15">
                    {{ errors.answer15 }}
                </div>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 16">
                    <strong>Does the business operate in a failing or declining sector?</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer16" v-model:value="answer16">
                        <a-radio value="No">No</a-radio>
                        <a-radio value="Not-Applicable">Not Applicable</a-radio>
                        <a-radio value="Yes">Yes</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer16">
                    {{ errors.answer16 }}
                </div>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label=" ">
                    <strong style="background-color: rgb(240 242 245); padding:10px">Audit and Accounting Issues</strong>
                </a-form-item>


                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 17">
                    <strong>Have generally accepted accounting principles been complied with in the past few years?</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer17" v-model:value="answer17">
                        <a-radio value="Always">Always</a-radio>
                        <a-radio value="Sometimes">Sometimes</a-radio>
                        <a-radio value="Rarely">Rarely</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer17">
                    {{ errors.answer17 }}
                </div>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 18">
                    <strong>Are there any contentious accounting treatments?</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer18" v-model:value="answer18">
                        <a-radio value="No">No</a-radio>
                        <a-radio value="Not-Applicable">Not Applicable</a-radio>
                        <a-radio value="Yes">Yes</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer18">
                    {{ errors.answer18 }}
                </div>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 19">
                    <strong>Have there been problems making adjustments in the past?</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer19" v-model:value="answer19">
                        <a-radio value="No">No</a-radio>
                        <a-radio value="Often">Often</a-radio>
                        <a-radio value="Yes">Yes</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer19">
                    {{ errors.answer19 }}
                </div>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 20">
                    <strong>Is this the first year you have undertaken an audit?</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer20" v-model:value="answer20">
                        <a-radio value="No">No</a-radio>
                        <a-radio value="Not-Applicable">Not Applicable</a-radio>
                        <a-radio value="Yes">Yes</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer20">
                    {{ errors.answer20 }}
                </div>


                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 21">
                    <strong>Do the management change professional advisors regularly?</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer21" v-model:value="answer21">
                        <a-radio value="No">No</a-radio>
                        <a-radio value="Not-Applicable">Not Applicable</a-radio>
                        <a-radio value="Yes">Yes</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer21">
                    {{ errors.answer21 }}
                </div>


                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 22">
                    <strong>Do management seek to limit the scope of the audit and/or impose unreasonable
                        deadlines?</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer22" v-model:value="answer22">
                        <a-radio value="No">No</a-radio>
                        <a-radio value="Not-Applicable">Not Applicable</a-radio>
                        <a-radio value="Yes">Yes</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer22">
                    {{ errors.answer22 }}
                </div>


                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 23">
                    <strong>Do management try to restrict our ability to communicate with other key stakeholders in the
                        business?</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer23" v-model:value="answer23">
                        <a-radio value="No">No</a-radio>
                        <a-radio value="Not-Applicable">Not Applicable</a-radio>
                        <a-radio value="Yes">Yes</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer23">
                    {{ errors.answer23 }}
                </div>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label=" ">
                    <strong style="background-color: rgb(240 242 245); padding:6px">IFRS implementation (in cases where IFRS
                        was not previously used) and changes to IFRS (in cases where new IFRS are applicable)</strong>
                </a-form-item>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 24">
                    <strong>Are there any changes to IFRS this year which have a significant impact on the company’s
                        financial reporting?</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer24" v-model:value="answer24">
                        <a-radio value="No">No</a-radio>
                        <a-radio value="Not-Applicable">Not Applicable</a-radio>
                        <a-radio value="Yes">Yes</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer24">
                    {{ errors.answer24 }}
                </div>


                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 25">
                    <strong>If IFRS has been implemented for the first time, does it result in significant changes to the
                        statement of financial position?</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer25" v-model:value="answer25">
                        <a-radio value="No">No</a-radio>
                        <a-radio value="Not-Applicable">Not Applicable</a-radio>
                        <a-radio value="Yes">Yes</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer25">
                    {{ errors.answer25 }}
                </div>


                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 26">
                    <strong>If IFRS has been applied for the first time, has management rigorously considered the importance
                        of the changes that will affect the entity on transition (e.g. identification and classification of
                        financial instruments)?</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer26" v-model:value="answer26">
                        <a-radio value="No">No</a-radio>
                        <a-radio value="Not-Applicable">Not Applicable</a-radio>
                        <a-radio value="Yes">Yes</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer26">
                    {{ errors.answer26 }}
                </div>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 27">
                    <strong>If IFRS has been applied for the first time, has the entity undertaken a thorough review to
                        understand any accounting policy changes necessary on transition?</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer27" v-model:value="answer27">
                        <a-radio value="No">No</a-radio>
                        <a-radio value="Not-Applicable">Not Applicable</a-radio>
                        <a-radio value="Yes">Yes</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer27">
                    {{ errors.answer27 }}
                </div>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 28">
                    <strong>If IFRS has been applied for the first time, were the systems and controls adequate to capture
                        the required data and information for the transition?</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer28" v-model:value="answer28">
                        <a-radio value="No">No</a-radio>
                        <a-radio value="Not-Applicable">Not Applicable</a-radio>
                        <a-radio value="Yes">Yes</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer28">
                    {{ errors.answer28 }}
                </div>

                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Question 29">
                    <strong>Does the application of IFRS involve any significant valuations to be carried out e.g. fair
                        value?</strong>
                </a-form-item>
                <a-form-item :label-col="{ span: 4 }" :wrapper-col="{ span: 14 }" label="Answer">
                    <a-radio-group name="answer29" v-model:value="answer29">
                        <a-radio value="No">No</a-radio>
                        <a-radio value="Not-Applicable">Not Applicable</a-radio>
                        <a-radio value="Yes">Yes</a-radio>
                    </a-radio-group>
                </a-form-item>
                <div class="text-red-700 px-4" role="alert" v-if="errors.answer29">
                    {{ errors.answer29 }}
                </div>

                <a-form-item :wrapper-col="{ span: 14, offset: 4 }">
                    <a-button type="primary" htmlType="submit">Download</a-button>
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
        "a-checkbox": Checkbox,
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
            answer1: 'Low',
            answer2: 'Director/Management',
            answer3: 'No',
            answer4: 'No',
            answer5: 'Yes',
            answer6: 'Good',
            answer7: 'None',
            answer8: 'Yes',
            answer9: 'Yes',
            answer10: 'Low-Risk',
            answer11: 'No',
            answer12: 'No',
            answer13: 'No',
            answer14: 'No',
            answer15: 'No',
            answer16: 'No',
            answer17: 'Always',
            answer18: 'No',
            answer19: 'No',
            answer20: 'No',
            answer21: 'No',
            answer22: 'No',
            answer23: 'No',
            answer24: 'No',
            answer25: 'No',
            answer26: 'No',
            answer27: 'No',
            answer28: 'No',
            answer29: 'No',
        };
    },

    methods: {
        submit_risk_level: function () {
            this.$refs.form_risk_level.submit();
        },
        materiality() {
            this.$inertia.get(route("materialities"));
        },
        rsc() {
            this.$inertia.get(route("rsc"));
        },
    },
};
</script>
