<template>
  <app-layout>
    <template #header>
      <h2 class="header">Details</h2>
    </template>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-1">
      <Button @click="create" size="small">Create</Button>

      <div class="">
        <div class="obsolute sm:rounded-2xl">
          <Table
            :columns="columns"
            :data-source="mapped_data"
            :loading="loading"
            class="mt-2"
            size="small"
          >
            <template #bodyCell="{ column, record }">
              <template v-if="column.key === 'features'">
                <Button
                  size="small"
                  type="primary"
                  @click="importexcel(record.account_id)"
                  class="mr-2"
                  >Import</Button
                >
                <a
                  class="downloadbuttons"
                  :href="'download-details/' + record.account_id"
                  >Download</a
                >
              </template>
              <template v-if="column.key === 'actions'">
                <Button
                  size="small"
                  type="primary"
                  @click="edit(record.account_id)"
                  class="mr-2"
                  >Edit</Button
                >
                <!-- v-if="record.delete" -->
                <Button
                  class="mr-2"
                  size="small"
                  danger
                  @click="destroy(record.account_id)"
                  >Delete</Button
                >
              </template>
            </template>
          </Table>
          <div
            class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400"
            v-if="isOpen"
          >
            <div
              class="
                flex
                items-end
                justify-center
                min-h-screen
                pt-4
                px-4
                pb-20
                text-center
                sm:block sm:p-0
              "
            >
              <div class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
              </div>
              <!-- This element is to trick the browser into centering the modal contents. -->
              <span
                class="hidden sm:inline-block sm:align-middle sm:h-screen"
              ></span
              >​
              <div
                class="
                  inline-block
                  align-bottom
                  bg-white
                  rounded-lg
                  text-left
                  overflow-hidden
                  shadow-xl
                  transform
                  transition-all
                  sm:my-8 sm:align-middle sm:max-w-lg sm:w-full
                "
                role="dialog"
                aria-modal="true"
                aria-labelledby="modal-headline"
              >
                <form>
                  <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="">
                      <div class="mb-4">
                        <label
                          for="exampleFormControlInput1"
                          class="block text-gray-700 text-sm font-bold mb-2"
                          >Folder:</label
                        >
                        <multiselect
                          style="z-index: 20"
                          class="rounded-md w-full border border-black"
                          placeholder="Select Folder."
                          v-model="form.file_id"
                          track-by="id"
                          label="name"
                          :options="option"
                        >
                          <!-- @update:model-value="coch" -->
                        </multiselect>
                        <br />
                        <br />
                        <br />
                      </div>
                    </div>
                  </div>
                  <div
                    class="
                      bg-gray-50
                      px-4
                      py-3
                      sm:px-6 sm:flex sm:flex-row-reverse
                    "
                  >
                    <span
                      class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto"
                    >
                      <button
                        wire:click.prevent="store()"
                        type="button"
                        class="
                          inline-flex
                          justify-center
                          w-full
                          rounded-md
                          border border-transparent
                          px-4
                          py-2
                          bg-green-600
                          text-base
                          leading-6
                          font-medium
                          text-white
                          shadow-sm
                          hover:bg-green-500
                          focus:outline-none
                          focus:border-green-700
                          focus:shadow-outline-green
                          transition
                          ease-in-out
                          duration-150
                          sm:text-sm sm:leading-5
                        "
                        v-show="editMode"
                        @click="save(form)"
                      >
                        Save
                      </button>
                    </span>
                    <!-- <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                            <button wire:click.prevent="store()" type="button" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5" v-show="editMode" @click="update(form)">
                                Update
                            </button>
                            </span> -->
                    <span
                      class="
                        mt-3
                        flex
                        w-full
                        rounded-md
                        shadow-sm
                        sm:mt-0 sm:w-auto
                      "
                    >
                      <button
                        @click="closeModal()"
                        type="button"
                        class="
                          inline-flex
                          justify-center
                          w-full
                          rounded-md
                          border border-gray-300
                          px-4
                          py-2
                          bg-white
                          text-base
                          leading-6
                          font-medium
                          text-gray-700
                          shadow-sm
                          hover:text-gray-500
                          focus:outline-none
                          focus:border-blue-300
                          focus:shadow-outline-blue
                          transition
                          ease-in-out
                          duration-150
                          sm:text-sm sm:leading-5
                        "
                      >
                        Cancel
                      </button>
                    </span>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>


<script>
import AppLayout from "@/Layouts/AppLayout";
import { Button, Table, Select, InputSearch } from "ant-design-vue";

import JetButton from "@/Jetstream/Button";
import Paginator from "@/Layouts/Paginator";
import FlashMessage from "@/Layouts/FlashMessage";
import { pickBy } from "lodash";
import { throttle } from "lodash";
import Multiselect from "@suadelabs/vue3-multiselect";

export default {
  components: {
    AppLayout,
    Button,
    Table,
    Select,
    InputSearch,
    // useForm,
    Multiselect,

    JetButton,
    Paginator,
    throttle,
    pickBy,
    FlashMessage,
    Multiselect,
  },

  //   props: ["data"],
  props: {
    balances: Object,
    filters: Object,
    can: Object,
    files: Object,
    companies: Array,
    //FOR MULTI-SELECT
    cochange: Object,
    mapped_data: Object,
  },

  data() {
    return {
      //   co_id: this.$page.props.co_id,
      option: this.files,
      editMode: false,
      isOpen: false,
      // co_id: this.cochange,
      // options: this.companies,
      form: {
        file_id: null,
        account_id: null,
      },

      columns: [
        {
          title: "Date",
          dataIndex: "date",
          //   width: "20%",
        },
        {
          title: "Description",
          dataIndex: "description",
        },
        {
          title: "Cheque",
          dataIndex: "cheque",
        },
        {
          title: "Voucher No",
          dataIndex: "voucher_no",
          //   width: "20%",
        },
        {
          title: "Amount",
          dataIndex: "amount",
        },
        {
          title: "Features",
          dataIndex: "features",
          key: "features",
        },
        {
          title: "Actions",
          dataIndex: "actions",
          key: "actions",
        },
      ],

      params: {
        search: this.filters.search,
        field: this.filters.field,
        direction: this.filters.direction,
      },
    };
  },

  methods: {
    openModal: function () {
      this.isOpen = true;
    },
    closeModal: function () {
      this.isOpen = false;
      this.editMode = false;
    },
    importexcel: function (data) {
      this.editMode = true;
      this.form.account_id = data;
      this.openModal();
    },
    reset: function () {
      this.form = {
        file_id: null,
        account_id: null,
      };
    },
    save: function (data) {
      //   if (confirm("Are you sure?")) {
      if (data.file_id != null) {
        this.$inertia.post(route("import.details"), data);
        this.editMode = false;
        this.reset();
        this.closeModal();
      } else {
        alert("please Select Folder First");
      }
      //   }
    },

    create() {
      this.$inertia.get(route("details.create"));
    },

    edit(id) {
      this.$inertia.get(route("details.edit", id));
    },

    destroy(id) {
      if (confirm("Are you sure want to delete this Detail")) {
        this.$inertia.delete(route("details.destroy", id));
      }
    },

    coch() {
      this.$inertia.get(route("companies.coch", this.co_id));
    },

    sort(field) {
      this.params.field = field;
      this.params.direction = this.params.direction === "asc" ? "desc" : "asc";
    },
  },
  watch: {
    params: {
      handler: throttle(function () {
        let params = pickBy(this.params);
        this.$inertia.get(this.route("companies"), params, {
          replace: true,
          preserveState: true,
        });
      }, 150),
      deep: true,
    },
  },
};
</script>
