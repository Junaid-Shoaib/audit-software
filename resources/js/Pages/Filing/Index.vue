
<template>
  <app-layout>
    <template #header>
      <div class="grid grid-cols-2 items-center">
        <div v-if="folders" class="items-center">
          <!-- <multiselect
            v-model="form.folder"
            placeholder="Select Folder."
            style="width: 50%"
            :options="folders"
            label="name"
            class="float-left rounded-md border border-black"
            track-by="id"
            @update:model-value="foch"
          >
          </multiselect> -->
          <Select
            show-search
            optionFilterProp="name"
            v-model:value="form.folder"
            placeholder="Please select"
            style="width: 200px; margin-left: 0.5rem"
            :options="folders"
            :field-names="{ label: 'name', value: 'id' }"
            @change="foch"
            mode="single"
            size="small"
          />
          <Button @click="folderModification" class="ml-2" size="small"
            >Folder Modification</Button
          >
        </div>
        <h2 v-else class="float-left header">
          {{ parent.name == "Planing" ? "Planning" : parent.name }} -
          {{ parent.type }}
        </h2>
      </div>
    </template>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-1">
      <InputSearch
        v-model:value="search"
        placeholder="input search text"
        style="width: 200px"
        @search="onSearch"
        size="small"
      />
      <Button @click="uploadFile" class="ml-2" size="small">Upload File</Button>
      <Button @click="templates" class="ml-2" size="small">Templates</Button>

      <input hidden id="selected" @click="checkAll()" v-model="isCheckAll" />
      <label v-if="isCheckAll" class="ant-btn ant-btn-sm ml-2" for="selected">
        Un-Select All</label
      >
      <label v-else class="ant-btn ant-btn-sm ml-2" for="selected">
        Select All</label
      >
      <Button @click="Approve()" class="ml-2" size="small">Approve</Button>

      <Button
        v-if="this.user_role != 'staff'"
        @click="Reject()"
        class="ml-2"
        size="small"
        >Reject</Button
      >

      <div class="">
        <Table
          :columns="columns"
          :data-source="mapped_data"
          :loading="loading"
          class="mt-2"
          size="small"
        >
          <template #bodyCell="{ column, record }">
            <template v-if="column.key === 'approval'">
              <!-- <Checkbox
                v-if="record.approve"
                v-model:value="record.name"
                checked
              />
              <Checkbox
                v-else
                v-model:value="record.name"
                v-model:checked="unchecked"
                @change="updateCheckall()"
              /> -->
              <input
                class="text-green-600"
                v-if="record.approve"
                type="checkbox"
                v-bind:value="record.name"
                name="selected_arr"
                :disabled="record.approve"
                checked
              />
              <input
                class="focus:ring-green-500"
                v-else
                type="checkbox"
                v-bind:value="record.name"
                v-model="form.selected_arr"
                @change="updateCheckall()"
                name="selected_arr"
              />
            </template>
            <template v-if="column.key === 'review'">
              <Popper v-if="record.review" :content="record.review">
                <Button size="small" class="mr-2">Open Review</Button>
              </Popper>
            </template>
            <template v-if="column.key === 'actions'">
              <a
                class="ant-btn ant-btn-sm ant-btn-primary mr-2"
                size="small"
                :href="'/filing/downloadFile/' + record.id"
                >Download</a
              >
              <Button
                size="small"
                v-if="this.user_role == 'partner'"
                danger
                @click="deleteFileFolder(record.id)"
                >Delete</Button
              >
            </template>
          </template>
        </Table>
      </div>
    </div>
  </app-layout>
</template>

<style src="@suadelabs/vue3-multiselect/dist/vue3-multiselect.css"></style>
<script>
import AppLayout from "@/Layouts/AppLayout";
import { Button, Table, Select, InputSearch, Checkbox } from "ant-design-vue";

import { useForm } from "@inertiajs/inertia-vue3";
import Multiselect from "@suadelabs/vue3-multiselect";
import Popper from "vue3-popper";
import "/css/theme.css";

export default {
  components: {
    AppLayout,
    Button,
    Table,
    Select,
    InputSearch,
    Checkbox,
    useForm,
    Popper,

    Multiselect,
  },

  props: {
    type: Object,
    balances_name: Object,
    balances: Object,
    companies: Object,
    company: Object,
    folders: Object,
    parent: Object,
    user_role: Object,
    mapped_data: Object,
    filters: Object,
  },

  data() {
    return {
      co_id: this.company,
      options: this.companies,
      folder_id: this.parent.id,
      selected: [],
      isCheckAll: false,
      folders: this.folders,
      search: this.filters.search,
      columns:
        this.user_role == "partner"
          ? [
              {
                title: this.parent.type + " Name",
                dataIndex: "name",
                //   width: "20%",
              },
              {
                title: "Approval",
                dataIndex: "approve",
                key: "approval",
              },
              {
                title: "Action",
                dataIndex: "role",
                key: "actions",
              },
            ]
          : [
              {
                title: this.parent.type + " Name",
                dataIndex: "name",
                //   width: "20%",
              },
              {
                title: "Approval",
                dataIndex: "approve",
                key: "approval",
              },
              {
                title: "Review",
                dataIndex: "review",
                key: "review",
              },
              {
                title: "Action",
                dataIndex: "role",
                key: "actions",
              },
            ],

      form: {
        selected_arr: [],
        folder: this.parent.id,
        type: this.parent.name,
      },
    };
  },

  methods: {
    onSearch() {
      if (this.parent.name == "Planing" || this.parent.name == "Completion") {
        this.$inertia.get(
          route("filing", [this.parent.name.toLowerCase()]),
          {
            search: this.search,
          },
          { replace: true, preserveState: true }
        );
      } else {
        this.$inertia.get(
          route("filing", [this.form.folder]),
          {
            search: this.search,
          },
          { replace: true, preserveState: true }
        );
      }
    },

    checkAll: function () {
      this.isCheckAll = !this.isCheckAll;
      this.form.selected_arr = [];
      if (this.isCheckAll) {
        // Check all
        for (var key in this.balances_name) {
          //   if (!this.balances.data[key].approve) {
          if (!this.mapped_data[key].approve) {
            this.form.selected_arr.push(this.balances_name[key]);
          }
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

    Approve: function () {
      if (this.form.selected_arr.length >> 0) {
        this.$inertia.post(route("approve_files"), this.form);
        this.form.selected_arr = [];
      } else {
        alert("Please select file");
      }
    },

    Reject: function () {
      if (this.form.selected_arr.length >> 0) {
        let review = prompt(
          "Review for rejecting files"
          //   "Reason for rejecting file"
        );
        review = review.trim();
        if (review != null && review != "") {
          this.$inertia.post(route("reject_files", review), this.form);
          this.form.selected_arr = [];
        } else {
          alert("Please enter some review for rejecting files");
        }
      } else {
        alert("Please select file");
      }
    },

    foch() {
      //   this.$inertia.get(route("filing", this.form.folder["id"]));
      this.$inertia.get(route("filing", this.form.folder));
    },

    uploadFile() {
      this.$inertia.get(route("filing.uploadFile", this.folder_id));
    },

    folderModification: function () {
      this.$inertia.get(route("filing.folder"));
    },

    templates() {
      if (this.parent.name == "Planing" || this.parent.name == "Completion") {
        this.$inertia.get(route("index_temp", this.parent.name));
      } else {
        this.$inertia.get(route("index_temp", "Execution"));
      }
    },

    downloadFile: function (id) {
      this.$inertia.get(route("filing.downloadFile", id));
    },

    deleteFileFolder: function (id) {
      this.$inertia.get(route("filing.deleteFileFolder", id));
    },

    coch() {
      this.$inertia.get(route("companies.coch", this.co_id["id"]));
    },
  },
};
</script>
