<template>
  <app-layout>
      <template #header>
      <h2>Bank Confirmations</h2>
    </template>
    <div  class="bg-red-600 text-white text-center" v-if="errors.file">{{ errors.file }}</div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-1">
            <a-button
                type="button"
                size="small"
                 :href="route('confirmations.create')"
                v-if="create"
                >Create Confiramtion
              </a-button>

            <a-button v-if="balances.data.length > 0"
              size="small"
                type="button"
                 :href="route('confirmations.edit')"
              >
                Edit
            </a-button>



            <a-button size="small" class="ml-1" href="word">Generate Bank Letters</a-button>
            <a-button size="small" class="ml-1" href="bankConfig">Generate Remaining Pages</a-button>
                <a-button size="small" class="ml-1" href="ex">Generate Control Sheet</a-button>
                <a-button size="small" class="ml-1" href="branchespdf" target="_blank">Generate Branch Pdf</a-button>

    <div class="">
        <a-table
            :columns="columns"
            :data-source="balances.data"
            :loading="loading"
            class="mt-2"
            size="small"
        >
            <template #bodyCell="{ column, record }">
                <template v-if="column.key === 'actions'">
                        <a-button size="small" @click="chooseFile">Click To Upload</a-button>
                        <input
                        type="file"
                        accept=".pdf"
                        size="small"
                        v-on:change="onFileChange($event, record.id)"
                        ref="fileInput" style="display:none;"/>
                        <a-button v-if="record.path"
                        size="small"
                            type="button"
                         :href="'/bankconfirmUpload/' + record.id"
                         >Download</a-button>

                </template>
            </template>
        </a-table>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Paginator from "@/Layouts/Paginator";
import Multiselect from "@suadelabs/vue3-multiselect";
import { Button, Table, Input } from "ant-design-vue";
import { ref } from 'vue';

export default {
  components: {
    AppLayout,
        "a-button": Button,
        "a-table": Table,
        "a-input": Input,
    Paginator,
    Multiselect,
    ref,
},
  props: {
     errors: Object,
    balances: Object,
    create: Object,
  },

   setup() {
        const fileInput = ref(null);

        const chooseFile = () => {
            fileInput.value.click();
        };
          return {
            fileInput,
            chooseFile,
        };
    },


  data() {
    return {
      options: this.companies,
             columns: [
                {
                    title: "Bank - Branch",
                    dataIndex: "branch",
                    width: "30%",
                },
                {
                    title: "Create Date",
                    dataIndex: "confirm_create",
                    width: "10%",

                },
                {
                    title: "Sent Date",
                    dataIndex: "sent",
                    width: "10%",

                },
                {
                    title: "Reminder Date",
                    dataIndex: "reminder",
                    width: "10%",

                },
                {
                    title: "Received Date",
                    dataIndex: "received",
                    width: "10%",
                },
                {
                    title: "Actions",
                    dataIndex: "actions",
                    key: "actions",
                    width: "30%",
                },
            ],
    };
  },
  methods: {
      onFileChange(e, index) {

      var files = e.target.files || e.dataTransfer.files;
            //   console.log(files[0].size);
       if(files[0].size > 4194304){
       alert("File size should be less than 4 MB!");
       return
    //    this.value = "";
    };
      if (!files.length) return;

        console.log(files[0]);

        var id = index;
       this.$inertia.post(route('balances.updated', id),
            {
            _method: 'put',
            file: files[0],
            },
            {
            preserveState: true,
            preserveScroll: true,
            }
        )
    },
    destroy(id) {
      this.$inertia.delete(route("confirmations.destroy", id));
    },

  },
};
</script>
