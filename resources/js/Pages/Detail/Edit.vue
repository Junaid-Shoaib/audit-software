<template>
  <app-layout>
    <template #header>
      <h2 class="header">Modify Details</h2>
    </template>
    <div class="mt-5 ml-7 flex-row">
      <jet-button @click.prevent="back" class="ml-2 buttondesign"
        >Back</jet-button
      >
      <jet-button @click.prevent="addRow" class="ml-2 buttondesign"
        >Add row</jet-button
      >
      <multiselect
        style="width: 10%"
        class="float-left rounded-md border border-black"
        placeholder="Select Account."
        v-model="form.account"
        track-by="id"
        label="name"
        :options="accounts"
        @update:model-value="accch"
      >
      </multiselect>
    </div>

    <div class="sm:px-6 lg:px-8 py-2">
      <form @submit.prevent="submit">
        <div class="obsolute mt-2 ml-2 sm:rounded-2xl">
          <!-- <div class=""> -->
          <table class="table2">
            <!-- <thead class="bg-gray-700 text-white text-centre font-bold"> -->
            <thead>
              <tr class="tablerowhead">
                <th class="py-1 px-4 rounded-l-2xl">Action</th>
                <th class="py-1 px-4">Date</th>
                <th class="py-1 px-4">Description</th>
                <th class="py-1 px-4">Cheque</th>
                <th class="py-1 px-4">Voucher No</th>
                <th class="py-1 px-4">Amount</th>
                <th class="py-1 px-4">Mode of Payment</th>
                <!-- <th class="py-1 px-4">Cash</th>
                <th class="py-1 px-4">Bank</th>
                <th class="py-1 px-4">Adjustment</th> -->
                <th class="py-1 px-4">Posting to Ledger</th>
                <th class="py-1 px-4">Voucher Approved</th>
                <th class="py-1 px-4">Supporting Document</th>
                <th class="py-1 px-4">Bank Statement</th>
                <th class="py-1 px-4">E</th>
                <th class="py-1 px-4">F</th>
                <th class="py-1 px-4 rounded-r-2xl">Remark</th>
                <!-- <th class="py-1 px-4">Conclusion</th>
                <th class="py-1 px-4">Company</th>
                  <th class="py-1 px-4">Year</th>
                  <th class="py-1 px-4">Account</th> -->
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(detail, index) in this.form.balances"
                :key="detail.id"
              >
                <!-- <tr class="table2"> -->
                <td>
                  <button
                    @click.prevent="deleteRow(index)"
                    class="deletebutton px-4 m-1"
                    type="button"
                  >
                    <span>Delete</span>
                  </button>
                </td>
                <td>
                  <input
                    type="date"
                    v-model="detail.date"
                    class="
                      w-48
                      pr-2
                      pb-2
                      rounded-md
                      duration-200
                      placeholder-indigo-300
                    "
                    label="date"
                    placeholder="Enter date:"
                  />
                </td>
                <td>
                  <input
                    type="text"
                    v-model="detail.description"
                    class="
                      w-64
                      pr-2
                      pb-2
                      rounded-md
                      duration-200
                      placeholder-indigo-300
                    "
                    label="description"
                    placeholder="Enter description:"
                  />
                </td>
                <td>
                  <input
                    type="text"
                    v-model="detail.cheque"
                    class="
                      w-64
                      pr-2
                      pb-2
                      rounded-md
                      duration-200
                      placeholder-indigo-300
                    "
                    label="cheque"
                    placeholder="Enter cheque:"
                  />
                </td>
                <td>
                  <input
                    type="text"
                    v-model="detail.voucher_no"
                    class="
                      w-64
                      pr-2
                      pb-2
                      rounded-md
                      duration-200
                      placeholder-indigo-300
                    "
                    label="voucher_no"
                    placeholder="Enter voucher no :"
                  />
                </td>
                <td>
                  <input
                    type="number"
                    v-model="detail.amount"
                    class="
                      w-64
                      pr-2
                      pb-2
                      rounded-md
                      duration-200
                      placeholder-indigo-300
                    "
                    label="amount"
                    placeholder="Enter amount"
                  />
                </td>
                <td>
                  <select
                    v-model="detail.modeOfPay"
                    v-bind="value"
                    class="
                      w-64
                      pr-2
                      pb-2
                      rounded-md
                      duration-200
                      placeholder-indigo-300
                    "
                    label="modeOfPay"
                    placeholder="Select Mode of Payment:"
                  >
                    <option hidden value="0">Select Mode of Payment</option>
                    <option value="cash">Cash</option>
                    <option value="bank">Bank</option>
                    <option value="adjustment">Adjustment</option>
                  </select>
                </td>
                <td>
                  <div class="w-32 text-center">
                    <input type="checkbox" id="checkbox" v-model="detail.a" />
                  </div>
                </td>
                <td>
                  <div class="w-32 text-center">
                    <input type="checkbox" id="checkbox" v-model="detail.b" />
                  </div>
                </td>
                <td>
                  <div class="w-32 text-center">
                    <input type="checkbox" id="checkbox" v-model="detail.c" />
                  </div>
                </td>
                <td>
                  <div class="w-32 text-center">
                    <input type="checkbox" id="checkbox" v-model="detail.d" />
                  </div>
                </td>
                <td>
                  <div class="w-32 text-center">
                    <input type="checkbox" id="checkbox" v-model="detail.e" />
                  </div>
                </td>
                <td>
                  <div class="w-32 text-center">
                    <input type="checkbox" id="checkbox" v-model="detail.f" />
                  </div>
                </td>
                <td>
                  <input
                    type="text"
                    v-model="detail.remark"
                    class="
                      w-64
                      pr-2
                      pb-2
                      rounded-md
                      duration-200
                      placeholder-indigo-300
                    "
                    label="remarks"
                    placeholder="Enter remarks:"
                  />
                </td>

              </tr>
            </tbody>
          </table>
        </div>
        <div class="px-4 py-2 flex justify-start items-center">
          <button class="submitbutton p-1 px-4 mt-1 ml-2 mr-3" type="submit">
            Update Details
          </button>
        </div>
      </form>
    </div>
    <!-- </div> -->
  </app-layout>
</template>

<style src="@suadelabs/vue3-multiselect/dist/vue3-multiselect.css"></style>
<script>
import AppLayout from "@/Layouts/AppLayout";
import Label from "../../Jetstream/Label.vue";
import JetButton from "@/Jetstream/Button";
import Multiselect from "@suadelabs/vue3-multiselect";

export default {
  components: {
    AppLayout,
    JetButton,
    Multiselect,
  },

  props: {
    errors: Object,
    types: Object,
    company: Object,
    balances: Array,
    account: Object,
    accounts: Object,
  },

  data() {
    return {
      form: this.$inertia.form({
        accounts: this.accounts,
        account: this.account,
        account_id: this.account["id"],
        balances: this.balances,
      }),
    };
  },

  methods: {
    submit() {
      this.$inertia.put(
        route("details.update", this.form.account["id"]),
        this.form
      );
    },
    accch() {
      this.$inertia.get(route("details.edit", this.form.account["id"]));
    },
    back() {
      this.$inertia.get(route("details"));
    },

    addRow() {
      this.form.balances.push({
        date: null,
        description: null,
        cheque: null,
        voucher_no: null,
        amount: null,
        // cash: null,
        // bank: null,
        // adjustment: null,
        modeOfPay: "0",
        a: false,
        b: false,
        c: false,
        d: false,
        e: false,
        f: false,
        remark: null,
        account_id: this.form.account["id"],
      });
    },

    deleteRow(index) {
      this.form.balances.splice(index, 1);
    },
  },
};
</script>
