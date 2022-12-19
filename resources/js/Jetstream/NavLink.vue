<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/inertia-vue3";

const props = defineProps({
  href: String,
  active: Boolean,
});

const classes = computed(() => {
  return props.active
    ? "flex items-center text-sm  mb-3 px-6 py-2.5 rounded-r-full  text-gray-500 bg-slate-300 focus:outline-none group"
    : // ? 'inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition'
      "flex items-center  text-sm mb-3 px-6 py-2.5 rounded-r-full text-white focus:outline-none   hover:text-gray-500 hover:bg-white group";
});
</script>

<template>
  <Link :href="href" :class="classes">
    <slot />
  </Link>
</template>
