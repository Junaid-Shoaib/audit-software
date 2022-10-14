
<template>
  <app-layout>
    <template #header>
      <div class="grid grid-cols-2 items-center">
        <h2 class="header">{{ parent.name }} - {{ parent.type }}</h2>
        <div class="justify-end">
          <multiselect
            style="width: 50%; z-index: 10"
            class="float-right rounded-md border border-black"
            placeholder="Select Company."
            v-model="co_id"
            track-by="id"
            label="name"
            :options="options"
            @update:model-value="coch"
          >
          </multiselect>
        </div>
      </div>
    </template>

    <FlashMessage />

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-2">
      <jet-button
        v-if="parent.type == 'File'"
        @click="uploadFile"
        class="ml-2 buttondesign"
        >Upload File</jet-button
      >
      <jet-button
        v-else
        type="button"
        @click="createFolder"
        class="ml-2 buttondesign"
        >Create Folder</jet-button
      >
      <jet-button type="button" @click="templates" class="ml-2 buttondesign"
        >Templates</jet-button
      >
      <input hidden id="selected" @click="checkAll()" v-model="isCheckAll" />
      <label class="px-2 py-2 ml-2 submitbutton" for="selected">
        Select All</label
      >
      <!-- <div class="flex-1">
        <form
          class=""
          @submit.prevent="submitValue"
          v-bind:action="'/multiple-template-download'"
          ref="form_range"
        > -->
      <!-- <input hidden v-model="form.selected_arr" name="selected_arr" /> -->

      <button
        class="ml-2 px-2 py-2 submitbutton"
        type="button"
        @click="Approve()"
      >
        Approve
      </button>

      <button
        v-if="this.user_role != 'staff'"
        class="ml-2 px-2 py-2 submitbutton"
        type="button"
        @click="Reject()"
      >
        Reject
      </button>
      <!-- </form>
      </div> -->

      <div class="">
        <div class="obsolute mt-2 ml-2 sm:rounded-2xl">
          <table class="table2">
            <thead>
              <tr class="tablerowhead">
                <th class="py-1 px-4 rounded-l-2xl">{{ parent.type }} Name</th>
                <th class="py-1 px-4">Approval</th>
                <th v-if="this.user_role != 'partner'" class="py-1 px-4">
                  Review
                </th>
                <th class="py-1 px-4 rounded-r-2xl">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr
                class="tablerowbody2"
                v-for="item in balances.data"
                :key="item.id"
              >
                <td class="w-4/12 px-4 border rounded-l-2xl w-2/5">
                  {{ item.name }}
                </td>
                <td class="w-1/12 px-4 border text-center">
                  <input
                    class="text-green-600"
                    v-if="item.approve"
                    type="checkbox"
                    v-bind:value="item.name"
                    name="selected_arr"
                    :disabled="item.approve"
                    checked
                  />
                  <input
                    class="focus:ring-green-500"
                    v-else
                    type="checkbox"
                    v-bind:value="item.name"
                    v-model="form.selected_arr"
                    @change="updateCheckall()"
                    name="selected_arr"
                  />
                </td>
                <td
                  v-if="this.user_role != 'partner'"
                  class="px-4 border w-3/12 text-center"
                >
                  <Popper v-if="item.review" :content="item.review">
                    <button>Open Review</button>
                  </Popper>

                  <!-- <label class="ml-2">{{ item.review }}</label> -->
                  <!-- <label v-else class="ml-2">{{ item.review }}</label> -->
                </td>
                <td
                  v-if="parent.type == 'File'"
                  class="w-4/12px-4 border w-2/6 text-center rounded-r-2xl"
                >
                  <a
                    class="
                      border
                      bg-indigo-300
                      rounded-md
                      px-4
                      text-white
                      font-bold
                      m-1
                      hover:text-white hover:bg-indigo-400
                    "
                    :href="'/filing/downloadFile/' + item.id"
                    >Download</a
                  >
                  <button
                    class="
                      border
                      bg-red-500
                      rounded-md
                      px-4
                      m-1
                      text-white
                      font-bold
                      hover:text-white hover:bg-red-600
                    "
                    @click="deleteFileFolder(item.id)"
                    type="button"
                  >
                    <!-- v-if="item.delete" -->
                    <span>Delete</span>
                  </button>
                </td>

                <td
                  v-else
                  class="w-4/12px-4 border w-2/6 text-center rounded-r-2xl"
                >
                  <button
                    class="
                      border
                      bg-indigo-300
                      rounded-md
                      px-4
                      m-1
                      text-white
                      font-bold
                      hover:text-white hover:bg-indigo-400
                    "
                    @click="viewFolder(item.id)"
                    type="button"
                  >
                    <span>View</span>
                  </button>
                  <button
                    class="deletebutton px-4 m-1"
                    @click="deleteFileFolder(item.id)"
                    type="button"
                  >
                    <!-- v-if="item.delete" -->
                    <span>Delete</span>
                  </button>
                </td>
              </tr>
              <tr v-if="balances.data.length === 0">
                <td class="border-t px-6 py-4 bg-gray-100" colspan="4">
                  No Record found.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <paginator class="mt-6" :balances="balances" />
      </div>
      <!-- </form> -->
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import JetButton from "@/Jetstream/Button";
import { useForm } from "@inertiajs/inertia-vue3";
import Multiselect from "@suadelabs/vue3-multiselect";
import Paginator from "@/Layouts/Paginator";
import FlashMessage from "@/Layouts/FlashMessage";
import Popper from "vue3-popper";
import "/css/theme.css";
// import { Head, Link } from "@inertiajs/inertia-vue3";

export default {
  components: {
    AppLayout,
    JetButton,
    useForm,
    Multiselect,
    Paginator,
    FlashMessage,
    Popper,
    // Link,
    // Head,
  },

  props: {
    type: Object,
    balances: Object,
    companies: Object,
    company: Object,
    parent: Object,
    user_role: Object,
    balances_name: Object,
  },

  data() {
    return {
      co_id: this.company,
      options: this.companies,
      folder_id: this.parent.id,
      selected: [],
      isCheckAll: false,
      form: {
        selected_arr: [],
        type: this.parent.name,
      },
    };
  },

  //   setup(props) {
  //     const form = useForm({});
  //     return { form };
  //   },

  methods: {
    checkAll: function () {
      this.isCheckAll = !this.isCheckAll;
      this.form.selected_arr = [];
      if (this.isCheckAll) {
        // Check all

        for (var key in this.balances_name) {
          if (!this.balances.data[key].approve) {
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
        } else {
          alert("Please enter some review for rejecting files");
        }
      } else {
        alert("Please select file");
      }
    },

    uploadFile() {
      this.$inertia.get(route("filing.uploadFile", this.folder_id));
    },

    createFolder() {
      this.$inertia.get(route("filing.createFolder"));
    },

    templates() {
      this.$inertia.get(route("index_temp", this.parent.name));
    },

    viewFolder(id) {
      this.$inertia.get(route("filing", id));
    },

    downloadFile: function (id) {
      this.$inertia.get(route("filing.downloadFile", id));
    },

    deleteFileFolder: function (id) {
      this.$inertia.get(route("filing.deleteFileFolder", id));
    },

    // edit(id) {
    //   this.$inertia.get(route("years.edit", id));
    // },

    // destroy(id) {
    //   this.$inertia.delete(route("years.destroy", id));
    // },

    coch() {
      this.$inertia.get(route("companies.coch", this.co_id["id"]));
    },
  },
};
</script>
