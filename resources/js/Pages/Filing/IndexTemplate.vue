<template>
    <app-layout>
        <template #header>
            <h2 class="header">{{ type }} Templates</h2>
        </template>

        <div
            class="ml-2 bg-red-100 border border-red-400 text-red-700 px-4 py-1 rounded relative text-center"
            role="alert"
            v-if="errors.folder"
        >
            {{ errors.folder }}
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-1">
            <InputSearch
                v-model:value="search"
                placeholder="input search text"
                style="width: 200px"
                @search="onSearch"
                size="small"
            />
            <input
                hidden
                id="selected"
                @click="checkAll()"
                v-model="isCheckAll"
            />
            <label
                v-if="isCheckAll"
                class="ant-btn ant-btn-sm ml-2"
                for="selected"
            >
                Un-Select All</label
            >
            <label v-else class="ant-btn ant-btn-sm ml-2" for="selected">
                Select All</label
            >
            <form
                class="inline"
                @submit.prevent="submitValue"
                v-bind:action="'/multiple-template-download'"
                ref="form_range"
            >
                <input
                    class="inline-block"
                    hidden
                    v-model="form.selected_arr"
                    name="selected_arr"
                />
                <Select
                    v-if="type == 'Execution'"
                    v-model:value="form.folder"
                    allowClear
                    placeholder="Select folder"
                    show-search
                    style="width: 200px; margin-left: 0.5rem"
                    :options="folders"
                    :field-names="{ label: 'name', value: 'id' }"
                    mode="single"
                    size="small"
                />
                <Button @click="includeTemps()" class="ml-2" size="small"
                    >Include Templates</Button
                >
                <Button type="primary" class="ml-2 ant-btn-sm" htmlType="submit"
                    >Download</Button
                >
            </form>

            <Table
                :columns="columns"
                :data-source="balances"
                :loading="loading"
                class="mt-2"
                size="small"
            >
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key === 'checked'">
                        <input
                            type="checkbox"
                            v-bind:value="record.name"
                            v-model="form.selected_arr"
                            @change="updateCheckall()"
                            name="selected_arr"
                        />
                    </template>
                </template>
            </Table>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { Button, Table, Select, InputSearch } from "ant-design-vue";

import JetButton from "@/Jetstream/Button";
import Paginator from "@/Layouts/Paginator";
import { pickBy } from "lodash";
import { useForm } from "@inertiajs/inertia-vue3";
import { throttle } from "lodash";
import Multiselect from "@suadelabs/vue3-multiselect";

export default {
    components: {
        AppLayout,
        Button,
        Table,
        Select,
        InputSearch,

        JetButton,
        useForm,
        // Paginator,
        throttle,
        pickBy,
        Multiselect,
    },

    props: {
        type: Object,
        data: Object,
        balances_name: Object,
        balances: Object,
        filters: Object,
        can: Object,
        companies: Array,
        company: Object,
        folders: Object,
        errors: Object,
        mapped_data: Object,
    },

    data() {
        return {
            selected: [],
            isCheckAll: false,
            //    selectedlang: "",
            co_id: this.company,
            options: this.companies,
            folders: this.folders,
            //   type: this.balances_name,
            form: {
                selected_arr: [],
                folder: ``,
                type: this.type,
            },
            search: "",
            columns: [
                {
                    title: "Check",
                    dataIndex: "name",
                    key: "checked",
                    //   width: "20%",
                },
                {
                    title: "Name of File",
                    dataIndex: "name",
                },
                {
                    title: "Name of Folder",
                    dataIndex: "type",
                },
            ],
            params: {
                search: this.filters.search,
            },
        };
    },

    methods: {
        onSearch() {
            console.log(this.type);
            if (this.type == "Planing" || this.type == "Completion") {
                this.$inertia.get(
                    route("index_temp", this.type),
                    {
                        // select: select.value,
                        // search: search.value
                        search: this.search,
                    },
                    { replace: true, preserveState: true }
                );
            } else {
                this.$inertia.get(
                    route("index_temp", "Execution"),
                    {
                        // select: select.value,
                        // search: search.value
                        search: this.search,
                    },
                    { replace: true, preserveState: true }
                );
            }
            //   this.$inertia.get(
            //     route("index_temp"),
            //     {
            //       // select: select.value,
            //       // search: search.value
            //       search: this.search,
            //     },
            //     { replace: true, preserveState: true }
            //   );
        },

        checkAll: function () {
            this.isCheckAll = !this.isCheckAll;
            this.form.selected_arr = [];
            if (this.isCheckAll) {
                // Check all
                for (var key in this.balances_name) {
                    this.form.selected_arr.push(this.balances_name[key]);
                }
            }
        },
        updateCheckall: function () {
            if (this.form.selected_arr.length == this.balances_name.length) {
                this.isCheckAll = true;
            } else {
                this.isCheckAll = false;
            }
        },
        submitValue: function () {
            this.$refs.form_range.submit();
        },

        includeTemps: function () {
            this.$inertia.post(route("include_templates"), this.form);
        },

        sort(field) {
            this.params.field = field;
            this.params.direction =
                this.params.direction === "asc" ? "desc" : "asc";
        },
        search_data() {
            let params = pickBy(this.params);
            this.$inertia.get(this.route("index_temp", this.type), params, {
                replace: true,
                preserveState: true,
            });
        },
    },
    watch: {
        params: {
            handler: throttle(function () {
                let params = pickBy(this.params);
                if (params.search == null) {
                    this.$inertia.get(
                        this.route("index_temp", this.type),
                        params,
                        {
                            replace: true,
                            preserveState: true,
                        }
                    );
                }
            }, 150),
            deep: true,
        },
    },
};
</script>
