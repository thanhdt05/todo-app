<template>
    <div class="relative w-full max-w-md mb-5">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    </div>
    <input
        type="text"
        :placeholder="placeholder || 'Tìm kiếm ...'"
        :value="modelValue"
        @input="handleInput"
        class="block w-full pl-10 pr-4 py-2 border border-slate-300 rounded-xl bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-200 focus:border-cyan-300 text-sm transition"
    />
    <button 
      v-if="modelValue"
      type="button" 
      @click="clearSearch"
      class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600 transition cursor-pointer"
      title="Xóa tìm kiếm"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>
    </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  modelValue: string
  placeholder?: string
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
}>()

function handleInput(event:Event) {
    const target = event.target as HTMLInputElement
    emit('update:modelValue', target.value)
}

const clearSearch = () => {
    emit('update:modelValue', '')
}
</script>