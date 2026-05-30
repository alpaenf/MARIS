<template>
  <div class="min-h-screen bg-slate-50">
    <header class="border-b border-slate-200 bg-white">
      <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
        <div class="text-lg font-semibold text-slate-900">MARIS</div>
        <nav class="text-sm text-slate-600">Dashboard</nav>
      </div>
    </header>
    <main class="mx-auto max-w-6xl px-6 py-8">
      <slot />
    </main>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    default: "",
  },
});
</script>
