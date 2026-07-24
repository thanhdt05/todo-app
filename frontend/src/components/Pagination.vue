
<template>
  <div v-if="totalPages > 1" class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6 pt-4 border-t border-slate-100">
    
    <div class="text-xs sm:text-sm text-slate-500 font-medium">
      Trang <span class="font-bold text-slate-800">{{ currentPage }}</span> / {{ totalPages }}
      <span v-if="total !== undefined" class="text-slate-400"> (Tổng {{ total }} công việc)</span>
    </div>
    <nav class="flex items-center space-x-1">
      <button 
        type="button" 
        :disabled="isFirstPage" 
        @click="goToFirstPage"
        class="px-2.5 py-1.5 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 disabled:opacity-30 disabled:cursor-not-allowed rounded-lg transition cursor-pointer"
        title="Trang đầu"
      >
        «
      </button>
      <button 
        type="button" 
        :disabled="isFirstPage" 
        @click="goToPreviousPage"
        class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 disabled:opacity-30 disabled:cursor-not-allowed rounded-lg transition cursor-pointer"
      >
        ‹ Trước
      </button>
      <button 
        v-for="page in pages" 
        :key="page.name"
        type="button" 
        @click="goTo(page.name)"
        :class="[
          'px-3 py-1.5 text-xs font-bold rounded-lg transition cursor-pointer',
          page.name === currentPage
            ? 'bg-cyan-500 text-white shadow-xs'
            : 'bg-slate-100 hover:bg-slate-200 text-slate-700'
        ]"
      >
        {{ page.name }}
      </button>
      <button 
        type="button" 
        :disabled="isLastPage" 
        @click="goToNextPage"
        class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 disabled:opacity-30 disabled:cursor-not-allowed rounded-lg transition cursor-pointer"
      >
        Sau ›
      </button>
      <button 
        type="button" 
        :disabled="isLastPage" 
        @click="goToLastPage"
        class="px-2.5 py-1.5 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 disabled:opacity-30 disabled:cursor-not-allowed rounded-lg transition cursor-pointer"
        title="Trang cuối"
      >
        »
      </button>
    </nav>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
  const props = withDefaults ( 
    defineProps<{
        maxVisibleButtons?: number
        totalPages: number
        total?: number
        currentPage: number
    }>(),
    {
      maxVisibleButtons: 3
    }
  )

    const emit = defineEmits<{
        (e: 'change', page: number) : void
    }>()

    const startPage = computed(() => {
        if (props.currentPage === 1) return 1;
        if (props.currentPage === props.totalPages) {
            return Math.max(1, props.totalPages - props.maxVisibleButtons + 1)
        };
        return Math.max(1, props.currentPage - 1);
    
    })

    const endPage = computed(() => {
        return Math.min(props.totalPages, startPage.value + props.maxVisibleButtons - 1);
    })

    const pages = computed(() => {
        const range = [];
        for (let i = startPage.value; i <= endPage.value; i++) {
            range.push({
                name: i,
                isDisabled: i === props.currentPage
            })
        }
        return range;
    })


    const goTo = (page: number) => {
        if (page >= 1 && page <= props.totalPages && page !== props.currentPage) {
            emit('change', page)
        }
    }

    const isFirstPage = computed(() => {
        return props.currentPage === 1;
    })

    const isLastPage = computed(() => {
        return props.currentPage === props.totalPages;
    })

    const goToFirstPage = () => {
        goTo(1);
    }

    const goToPreviousPage = () => {
        goTo(props.currentPage - 1)
    }

    const goToNextPage = () => {
        goTo(props.currentPage + 1)
    }

    const goToLastPage = () => {
        goTo(props.totalPages)
    }
</script>
