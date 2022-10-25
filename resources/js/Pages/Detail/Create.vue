<template>
  <app-layout>
    <template #header>
      <h2 class="header">Create Detail</h2>
    </template>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
      <div class="">
        <form @submit.prevent="form.post(route('details.store'))">
          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 ml-40 text-right w-36 font-bold"
              >Account :</label
            >
            <multiselect
              style="width: 25%"
              class="float-right rounded-md border border-black"
              placeholder="Select Account."
              v-model="form.account_id"
              track-by="id"
              label="name"
              :options="accounts"
            >
            </multiselect>

            <div
              class="
                ml-2
                bg-red-100
                border border-red-400
                text-red-700
                px-4
                py-2
                rounded
                relative
              "
              role="alert"
              v-if="errors.account_id"
            >
              {{ errors.account_id }}
            </div>
          </div>

          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 ml-40 text-right w-36 font-bold"
              >Date :</label
            >
            <input
              type="date"
              v-model="form.date"
              class="
                pr-2
                pb-2
                w-full
                lg:w-1/4
                rounded-md
                hover:transition hover:ease-in-out
                transform
                hover:scale-110
                ease-out
                duration-200
                placeholder-indigo-300
              "
              label="date"
              placeholder="Enter date:"
            />
            <div
              class="
                ml-2
                bg-red-100
                border border-red-400
                text-red-700
                px-4
                py-2
                rounded
                relative
              "
              role="alert"
              v-if="errors.date"
            >
              {{ errors.date }}
            </div>
          </div>

          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 ml-40 text-right w-36 font-bold"
              >Description :</label
            >
            <input
              type="text"
              v-model="form.description"
              class="
                pr-2
                pb-2
                w-full
                lg:w-1/4
                rounded-md
                hover:transition hover:ease-in-out
                transform
                hover:scale-110
                ease-out
                duration-200
                placeholder-indigo-300
              "
              label="description"
              placeholder="Enter description:"
            />
            <div
              class="
                ml-2
                bg-red-100
                border border-red-400
                text-red-700
                px-4
                py-2
                rounded
                relative
              "
              role="alert"
              v-if="errors.description"
            >
              {{ errors.description }}
            </div>
          </div>

          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 ml-40 text-right w-36 font-bold"
              >cheque :</label
            >
            <input
              type="text"
              v-model="form.cheque"
              class="
                pr-2
                pb-2
                w-full
                lg:w-1/4
                rounded-md
                hover:transition hover:ease-in-out
                transform
                hover:scale-110
                ease-out
                duration-200
                placeholder-indigo-300
              "
              label="cheque"
              placeholder="Enter cheque:"
            />
            <div
              class="
                ml-2
                bg-red-100
                border border-red-400
                text-red-700
                px-4
                py-2
                rounded
                relative
              "
              role="alert"
              v-if="errors.cheque"
            >
              {{ errors.cheque }}
            </div>
          </div>

          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 ml-40 text-right w-36 font-bold"
              >Voucher no :</label
            >
            <input
              type="number"
              v-model="form.voucher_no"
              class="
                pr-2
                pb-2
                w-full
                lg:w-1/4
                rounded-md
                hover:transition hover:ease-in-out
                transform
                hover:scale-110
                ease-out
                duration-200
                placeholder-indigo-300
              "
              label="voucher_no"
              placeholder="Enter voucher no :"
            />
            <div
              class="
                ml-2
                bg-red-100
                border border-red-400
                text-red-700
                px-4
                py-2
                rounded
                relative
              "
              role="alert"
              v-if="errors.voucher_no"
            >
              {{ errors.voucher_no }}
            </div>
          </div>

          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 ml-40 text-right w-36 font-bold"
              >Amount :</label
            >
            <input
              type="number"
              v-model="form.amount"
              class="
                pr-2
                pb-2
                w-full
                lg:w-1/4
                rounded-md
                hover:transition hover:ease-in-out
                transform
                hover:scale-110
                ease-out
                duration-200
                placeholder-indigo-300
              "
              label="amount"
              placeholder="Enter amount"
            />
            <div
              class="
                ml-2
                bg-red-100
                border border-red-400
                text-red-700
                px-4
                py-2
                rounded
                relative
              "
              role="alert"
              v-if="errors.amount"
            >
              {{ errors.amount }}
            </div>
          </div>

          <div class="p-2 mr-2 mb-2 ml-6 flex flex-wrap">
            <label class="my-2 ml-40 text-right w-36 mr-8 font-bold"
              >Mode of Paymemt :</label
            >
            <select
              v-model="form.modeOfPay"
              v-bind="value"
              class="
                pr-2
                pb-2
                w-full
                lg:w-1/4
                rounded-md
                hover:transition hover:ease-in-out
                transform
                hover:scale-110
                ease-out
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
              <!-- <option v-for="fiscal in fiscals" :key="fiscal" :value="fiscal">
                {{ fiscal }}
              </option> -->
            </select>
            <div
              class="
                ml-2
                bg-red-100
                border border-red-400
                text-red-700
                px-4
                py-2
                rounded
                relative
              "
              role="alert"
              v-if="errors.modeOfPay"
            >
              {{ errors.modeOfPay }}
            </div>
          </div>

          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 ml-40 text-right w-36 font-bold"
              >Posting to Ledger (A):</label
            >
            <div class="mt-1">
              <input type="checkbox" id="checkbox" v-model="form.a" />
              <!-- <label class="ml-4" for="checkbox">{{
                form.a ? "Checked" : "Unchecked"
              }}</label> -->
            </div>
            <div
              class="
                ml-2
                bg-red-100
                border border-red-400
                text-red-700
                px-4
                py-2
                rounded
                relative
              "
              role="alert"
              v-if="errors.a"
            >
              {{ errors.a }}
            </div>
          </div>

          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 ml-40 text-right w-36 font-bold"
              >Voucher Approved (B):</label
            >
            <div class="mt-1">
              <input type="checkbox" id="checkbox" v-model="form.b" />
            </div>
            <div
              class="
                ml-2
                bg-red-100
                border border-red-400
                text-red-700
                px-4
                py-2
                rounded
                relative
              "
              role="alert"
              v-if="errors.b"
            >
              {{ errors.b }}
            </div>
          </div>

          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 ml-40 text-right w-36 font-bold"
              >Supporting Documents (C):</label
            >
            <div class="mt-1">
              <input type="checkbox" id="checkbox" v-model="form.c" />
            </div>
            <div
              class="
                ml-2
                bg-red-100
                border border-red-400
                text-red-700
                px-4
                py-2
                rounded
                relative
              "
              role="alert"
              v-if="errors.c"
            >
              {{ errors.c }}
            </div>
          </div>

          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 ml-40 text-right w-36 font-bold"
              >Bank Statement (D):</label
            >
            <div class="mt-1">
              <input type="checkbox" id="checkbox" v-model="form.d" />
            </div>
            <div
              class="
                ml-2
                bg-red-100
                border border-red-400
                text-red-700
                px-4
                py-2
                rounded
                relative
              "
              role="alert"
              v-if="errors.d"
            >
              {{ errors.d }}
            </div>
          </div>

          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 ml-40 text-right w-36 font-bold"
              >Column 1 (E) :</label
            >
            <div class="mt-1">
              <input type="checkbox" id="checkbox" v-model="form.e" />
            </div>
            <div
              class="
                ml-2
                bg-red-100
                border border-red-400
                text-red-700
                px-4
                py-2
                rounded
                relative
              "
              role="alert"
              v-if="errors.e"
            >
              {{ errors.e }}
            </div>
          </div>

          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 ml-40 text-right w-36 font-bold"
              >Column 2 (F):</label
            >
            <div class="mt-1">
              <input type="checkbox" id="checkbox" v-model="form.f" />
            </div>
            <div
              class="
                ml-2
                bg-red-100
                border border-red-400
                text-red-700
                px-4
                py-2
                rounded
                relative
              "
              role="alert"
              v-if="errors.f"
            >
              {{ errors.f }}
            </div>
          </div>

          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 ml-40 text-right w-36 font-bold"
              >Remarks :</label
            >
            <input
              type="text"
              v-model="form.remark"
              class="
                pr-2
                pb-2
                w-full
                lg:w-1/4
                rounded-md
                hover:transition hover:ease-in-out
                transform
                hover:scale-110
                ease-out
                duration-200
                placeholder-indigo-300
              "
              label="remark"
              placeholder="Enter remark:"
            />
            <div
              class="
                ml-2
                bg-red-100
                border border-red-400
                text-red-700
                px-4
                py-2
                rounded
                relative
              "
              role="alert"
              v-if="errors.remark"
            >
              {{ errors.remark }}
            </div>
          </div>

          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 ml-40 text-right w-36 font-bold"
              >Conclusion :</label
            >
            <input
              type="text"
              v-model="form.conclusion"
              class="
                pr-2
                pb-2
                w-full
                lg:w-1/4
                rounded-md
                hover:transition hover:ease-in-out
                transform
                hover:scale-110
                ease-out
                duration-200
                placeholder-indigo-300
              "
              label="conclusion"
              placeholder="Enter conclusion:"
            />
            <div
              class="
                ml-2
                bg-red-100
                border border-red-400
                text-red-700
                px-4
                py-2
                rounded
                relative
              "
              role="alert"
              v-if="errors.conclusion"
            >
              {{ errors.conclusion }}
            </div>
          </div>

          <div class="px-4 py-2 flex justify-center items-center">
            <button
              class="submitbutton p-1 px-4 mt-1 ml-2 inline-block mr-3"
              type="submit"
              :disabled="form.processing"
            >
              Create Detail
            </button>
          </div>
        </form>
      </div>
    </div>
  </app-layout>
</template>

<style src="@suadelabs/vue3-multiselect/dist/vue3-multiselect.css"></style>
<script>
import AppLayout from "@/Layouts/AppLayout";
import { useForm } from "@inertiajs/inertia-vue3";
import Multiselect from "@suadelabs/vue3-multiselect";

export default {
  components: {
    AppLayout,
    useForm,
    Multiselect,
  },

  props: {
    errors: Object,
    accounts: Object,
    account_first: Object,
  },

  setup(props) {
    const form = useForm({
      account_id: props.account_first,
      modeOfPay: "0",
      date: null,
      description: null,
      cheque: null,
      voucher_no: null,
      amount: null,
      cash: null,
      bank: null,
      adjustment: null,
      a: null,
      b: null,
      c: null,
      d: null,
      e: null,
      f: null,
      remark: null,
      conclusion: null,
    });
    return { form };
  },
};
</script>

