<template>
    <app-layout>
        <template #header>
            <h2>Execution Directory</h2>
        </template>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-1">
            <Button @click="createFolder" size="small">Create Folder</Button>

            <InputSearch
                class="ml-2"
                v-model:value="search"
                placeholder="input search text"
                style="width: 200px"
                @search="onSearch"
                size="small"
            />
            <Table
                :columns="columns"
                :data-source="mapped_data"
                :loading="loading"
                class="mt-2"
                size="small"
            >
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key === 'actions'">
                        <Button
                            size="small"
                            type="primary"
                            @click="viewFolder(record.id)"
                            class="mr-2"
                            >View</Button
                        >
                        <Button
                            v-if="record.delete"
                            class="mr-2"
                            size="small"
                            danger
                            @click="deleteFileFolder(record.id)"
                            >Delete</Button
                        >
                    </template>
                </template>
            </Table>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { Button, Table, Select, InputSearch } from "ant-design-vue";

import { useForm } from "@inertiajs/inertia-vue3";

export default {
    components: {
        AppLayout,
        Button,
        Table,
        InputSearch,
        useForm,
    },

    props: {
        filters: Object,
        selected_folder: Object,
        balances: Object,
        companies: Object,
        company: Object,
        mapped_data: Object,
    },

    data() {
        return {
            co_id: this.company,
            options: this.companies,
            selected_folder2: this.selected_folder,

            search: "",
            columns: [
                {
                    title: "Folder Name",
                    dataIndex: "name",
                    //   width: "20%",
                },
                {
                    title: "Actions",
                    dataIndex: "actions",
                    key: "actions",
                },
            ],
            params: {
                search: this.filters.search,
                // field: this.filters.field,
                // direction: this.filters.direction,
            },
        };
    },

    setup(props) {
        const form = useForm({});
        return { form };
    },

    methods: {
        onSearch() {
            this.$inertia.get(
                route("filing.folder"),
                {
                    // select: select.value,
                    // search: search.value
                    search: this.search,
                },
                { replace: true, preserveState: true }
            );
        },
        createFolder() {
            this.$inertia.get(route("filing.createFolder"));
        },

        viewFolder(id) {
            this.$inertia.get(route("filing", id));
        },

        deleteFileFolder: function (id) {
            if (confirm("Are you Sure want to Delete this Folder")) {
                this.$inertia.get(route("filing.deleteFileFolder", id));
            }
        },

        coch() {
            this.$inertia.get(route("companies.coch", this.co_id["id"]));
        },
    },
};
</script>
