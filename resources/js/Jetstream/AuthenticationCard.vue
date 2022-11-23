<template>
  <div
    class="
      min-h-screen
      flex flex-col
      sm:justify-center
      items-center
      pt-6
      sm:pt-0
      bg-gradient-to-r
      from-[#F6F0EA]
      to-[#F1DFD1]
    "
  >
    <div>
      <!-- <slot name="logo" /> -->
      <p class="text-gray-700 text-3xl font-sans mx-10 mt-10">MZ-AUDIT</p>
    </div>

    <div
      class="
        w-full
        sm:max-w-md
        mt-6
        px-6
        py-4
        bg-white
        shadow-md
        overflow-hidden
        sm:rounded-lg
      "
    >
      <slot />
    </div>
  </div>
</template>
