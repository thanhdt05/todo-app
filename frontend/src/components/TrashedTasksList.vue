<template>
    <div>
        <TaskSearch v-model="keyword" placeholder="Tìm kiếm công việc..." />

        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
        <label class="flex items-center space-x-2 text-sm text-slate-600 font-semibold cursor-pointer">
            <input 
            type="checkbox" 
            :checked="isSelectAll" 
            @change="toggleSelectAll"
            class="w-4 h-4 accent-cyan-600 rounded cursor-pointer"
            />
            <span>Chọn tất cả ({{ selectedTaskIds.length }}/{{ tasks.length }})</span>
        </label>

        <div class="flex items-center space-x-2">
            <!-- Nút khôi phục các mục đã tick -->
            <button 
            type="button" 
            @click="handleRestoreSelectedTasks"
            class="px-3.5 py-1.5 bg-cyan-500 hover:bg-cyan-600 text-white font-bold text-xs rounded-xl shadow-md transition cursor-pointer flex items-center space-x-1"
            >
            <span>Khôi phục mục đã chọn</span>
            </button>
        </div>
        </div>

        <!-- Task List Items -->
        <ul class="space-y-2.5">
          <li v-for="task in tasks" :key="task.id" class="flex items-center justify-between px-4 py-3.5 bg-[#f4f6f7] rounded-xl hover:bg-[#eaeef1] transition">
            <div class="flex items-center space-x-3.5 flex-1 min-w-0 pr-3">
              <input type="checkbox" :value="task.id" v-model="selectedTaskIds" class="w-5 h-5 accent-blue-600 rounded cursor-pointer shrink-0" />
              <span class="truncate text-sm sm:text-base text-slate-700 font-medium">
                {{ task.title }}
              </span>
            </div>

            <div class="mx-3 shrink-0">
              <span 
                class="px-2.5 py-1 text-xs font-bold rounded-full uppercase"
                :class="{
                  'bg-amber-100 text-amber-700': task.status === 'todo',
                  'bg-blue-100 text-blue-700': task.status === 'doing',
                  'bg-emerald-100 text-emerald-700': task.status === 'done'
                }"
              >
                {{ task.status }}
              </span> 
            </div>

            <div v-if="task.due_date" class="mx-2 shrink-0">
              <span 
                class="text-xs flex items-center space-x-1"
                :class="(task.is_overdue && task.status !== 'done') ? 'text-red-500 font-semibold' : 'text-slate-400'"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>{{ formatDate(task.due_date) }}</span>
              </span>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center space-x-1 shrink-0">
                <button 
                type="button" 
                @click="handleRestoreTask(task)"
                class="p-1.5 text-emerald-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition cursor-pointer"
                title="Khôi phục công việc"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
              </button>
              <button 
                type="button" 
                @click="openDeleteModal(task)"
                class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition cursor-pointer"
                title="Xóa công việc"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </div>
          </li>
        </ul>

        <Pagination 
        :current-page="currentPage" 
        :total-pages="lastPage" 
        :total="totalTasks" 
        @change="goTo"
        />
    </div>

    <div 
        v-if="isDeleteModalOpen" 
        class="fixed inset-0 bg-black/50 backdrop-blur-xs z-50 flex items-center justify-center p-4"
    >
        <div class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-2xl space-y-4 border border-slate-100 text-center">
            <div class="w-12 h-12 rounded-full bg-red-100 text-red-500 mx-auto flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>

            <h3 class="text-lg font-bold text-slate-800">Xác nhận xóa vĩnh viễn</h3>
            
            <p class="text-sm text-slate-600">
                Bạn có chắc chắn muốn xóa vĩnh viễn?
            </p>

            <div class="flex items-center justify-center space-x-3 pt-2">
                <button 
                    type="button" 
                    @click="closeDeleteModal"
                    class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-xl font-semibold text-sm transition cursor-pointer"
                >
                    Hủy
                </button>
                <button 
                    type="button" 
                    @click="confirmDelete"
                    class="px-5 py-2 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl text-sm shadow-md transition cursor-pointer"
                >
                    Xóa vĩnh viễn
                </button>
            </div>
        </div>
    </div>

</template>

<script setup lang="ts">
import axios from 'axios'; 
import {ref, computed, onMounted, watch} from 'vue';
import TaskSearch from './TaskSearch.vue';
import Pagination from './Pagination.vue';

const errorMessage = ref('')
const successMessage = ref('')

const formatDate = (dateStr: string | null) => {
    if (!dateStr) return ''
    return dateStr.split('T')[0]?.split(' ')[0] ?? ''
}

interface Task {
    id: number
    title: string
    description: string | null
    status: 'todo' | 'doing' | 'done'
    due_date: string | null
    is_overdue: boolean 
}

const tasks = ref<Task[]>([])
const selectedTaskIds = ref<number[]>([])
const keyword = ref('')

const filterTasks = computed(()=> {
    return tasks.value.filter(task => {
        const query = keyword.value.toLowerCase().trim()
        const matchSearch = !query || task.title.toLowerCase().includes(query) || 
            task.description?.toLowerCase().includes(query)
        
        return matchSearch
    })
})

const isDeleteModalOpen = ref(false)
const taskToDelete = ref<Task | null>(null)

const openDeleteModal = (task: Task) => {
    taskToDelete.value = task
    isDeleteModalOpen.value = true
}

const closeDeleteModal = () => {
    isDeleteModalOpen.value = false
    taskToDelete.value = null
}


const confirmDelete = async () => {
    if (taskToDelete.value) {
        await handleDeleteTask(taskToDelete.value)
        closeDeleteModal()
    }
}

const isSelectAll = computed(() => {
    return tasks.value.length > 0 && selectedTaskIds.value.length === tasks.value.length
})

const toggleSelectAll = () => {
    if (isSelectAll.value) {
        selectedTaskIds.value = []
    } else {
        selectedTaskIds.value = tasks.value.map(task => task.id)
    }
}

const handleApiError = (error: any, defaultMessage: string) => {
    if (error.response?.data) {
        if (error.response.data.errors) {
            const firstErrorKey = Object.keys(error.response.data.errors)[0]
            if (firstErrorKey) {
                errorMessage.value = error.response.data.errors[firstErrorKey][0]
            }
        } else {
            errorMessage.value =
                error.response.data.message || defaultMessage
        }
    } else {
        errorMessage.value =
            error.message || 'Không thể kết nối đến máy chủ API'
    }
}

const currentPage = ref(1)
const lastPage = ref(1)
const totalTasks = ref(0)

const goTo = (page: number) => {
  handleGetTasksList(page)
}

let searchTimer: any = null

watch(keyword, () => {
    if (searchTimer) clearTimeout(searchTimer)

    searchTimer = setTimeout(() => {
        handleGetTasksList(1)
    }, 300)
})


const handleGetTasksList = async(page: number = 1) => {
    try {
        errorMessage.value = ''
        successMessage.value = ''
        
        const token = localStorage.getItem('token')

        if (!token) {
            errorMessage.value = 'Bạn chưa đăng nhập'
            return
        }

        const params: any = { page }
        if (keyword.value) {
            params.q = keyword.value
        }

        const response = await axios.get('http://localhost:8000/api/tasks/trashed', {
            params,
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })
        
        if (response.data?.data){
            tasks.value = response.data.data.data
            currentPage.value = response.data.data.current_page
            totalTasks.value = response.data.data.total
            lastPage.value = response.data.data.last_page
            selectedTaskIds.value = []
            successMessage.value = response.data.message || 'Lấy danh sách công việc thành công'
        }
    } catch (error: any) {
        handleApiError(error, 'Lấy danh sách công việc thất bại')
    }
}

const handleDeleteTask = async(task : Task) => {
    try {
        errorMessage.value = ''
        successMessage.value = ''

        const token = localStorage.getItem('token')

        if (!token) {
            errorMessage.value = 'Bạn chưa đăng nhập'
            return
        }

        const response = await axios.delete(
            `http://localhost:8000/api/tasks/${task.id}/force`,
            {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            }
        )

        if (response.data) {
            successMessage.value = response.data.message
            await handleGetTasksList()
        }
    } catch (error : any) {
        handleApiError(error, 'Xóa công việc thất bại')
    }
}

const handleRestoreTask = async(task : Task) => {
    try {
        errorMessage.value = ''
        successMessage.value = ''

        const token = localStorage.getItem('token')

        if (!token) {
            errorMessage.value = 'Bạn chưa đăng nhập'
            return
        }

        const response = await axios.put(
            `http://localhost:8000/api/tasks/${task.id}/restore`,
            {},
            {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            }
        )

        if (response.data) {
            successMessage.value = response.data.message
            await handleGetTasksList()
        }
    } catch (error : any) {
        handleApiError(error, 'Khôi phục công việc thất bại')
    }
}

const handleRestoreSelectedTasks = async () => {
    try {
        errorMessage.value = ''
        successMessage.value = ''

        const token = localStorage.getItem('token')

        if (!token) {
            errorMessage.value = 'Bạn chưa đăng nhập'
            return
        }

        await Promise.all(selectedTaskIds.value.map(async (id) => {
            return axios.put(
                `http://localhost:8000/api/tasks/${id}/restore`,
                {},
                {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                }
            )
        }))

        selectedTaskIds.value = []
        successMessage.value = "Khôi phục thành công"
        await handleGetTasksList()
    } catch (error : any) {
        handleApiError(error, 'Khôi phục công việc thất bại')
    }
} 

onMounted(() => {
    handleGetTasksList()
})


</script>

