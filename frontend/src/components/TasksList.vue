<template>
    <div>
        <div class="flex items-center justify-between">
            <TaskSearch v-model="keyword" placeholder="Tìm kiếm công việc..." />
            <button 
            type="button" 
            @click="openCreateModal"
            class="px-5 py-2.5 bg-cyan-500 hover:bg-cyan-600 text-white font-bold rounded-3xl shadow-md transition cursor-pointer flex items-center space-x-2 mb-5"
            >
            <span>+ Add</span>
            </button>
        </div>
        <!-- Filter Tabs -->
        <div class="flex items-center space-x-8 border-b border-slate-100 mb-6 text-xs sm:text-sm font-bold uppercase tracking-wider">
          <button type="button" @click="currentTab = 'ALL'" :class="currentTab === 'ALL' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-slate-400 hover:text-slate-600'" class="pb-3 transition cursor-pointer">
            ALL
          </button>
          <button type="button" @click="currentTab = 'TODO'" :class="currentTab === 'TODO' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-slate-400 hover:text-slate-600'" class="pb-3 transition cursor-pointer">
            TODO
          </button>
          <button type="button" @click="currentTab = 'DOING'" :class="currentTab === 'DOING' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-slate-400 hover:text-slate-600'" class="pb-3 transition cursor-pointer">
            DOING
          </button>
          <button type="button" @click="currentTab = 'DONE'" :class="currentTab === 'DONE' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-slate-400 hover:text-slate-600'" class="pb-3 transition cursor-pointer">
            DONE
          </button>
        </div>

        <!-- Task List Items -->
        <ul class="space-y-2.5">
          <li v-for="task in filterTasks" :key="task.id" @click="openDetailModal(task)" class="flex items-center justify-between px-4 py-3.5 bg-[#f4f6f7] rounded-xl hover:bg-[#eaeef1] transition cursor-pointer">
            <!-- Left: Checkbox & Title -->
            <div class="flex items-center space-x-3.5 flex-1 min-w-0 pr-3">
              <input type="checkbox" :checked="task.status === 'done'" @change="handleToggleTask(task)" class="w-5 h-5 accent-blue-600 rounded cursor-pointer shrink-0" />
              <span :class="task.status === 'done' ? 'line-through text-slate-400' : 'text-sm sm:text-base text-slate-700 font-medium'" class="truncate">
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
                @click.stop="openEditModal(task)"
                class="p-1.5 text-cyan-500 hover:text-cyan-600 hover:bg-cyan-50 rounded-lg transition cursor-pointer"
                title="Sửa công việc"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              </button>

              <button 
                type="button" 
                @click.stop="openDeleteModal(task)"
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
    </div>

    <!-- Modal Tạo Mới -->
    <div 
        v-if="isModalOpen" 
        class="fixed inset-0 bg-black/50 backdrop-blur-xs z-50 flex items-center justify-center p-4"
    >
    <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-4 border border-slate-100">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h3 class="text-lg font-bold text-slate-800">{{ modalTitle }}</h3>
        <button @click="closeModal" class="text-slate-400 hover:text-slate-600 font-bold text-xl cursor-pointer">
            &times;
        </button>
        </div>

        <form @submit.prevent="handleSubmitTask" class="space-y-4">
        <div>
            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Tiêu đề (*)</label>
            <input 
            v-model="taskForm.title"
            :disabled="!isEditMode"
            type="text" 
            placeholder="Nhập tiêu đề công việc..." 
            required
            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 text-sm"
            />
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Mô tả</label>
            <textarea 
            v-model="taskForm.description"
            :disabled="!isEditMode"
            placeholder="Nhập mô tả chi tiết..." 
            rows="3"
            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 text-sm"
            ></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Hạn hoàn thành</label>
                <input 
                v-model="taskForm.due_date"
                :disabled="!isEditMode"
                type="date" 
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 text-sm"
                />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Trạng thái</label>
                <select 
                v-model="taskForm.status"
                :disabled="!isEditMode"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 text-sm"
                >
                <option value="todo">Todo</option>
                <option value="doing">Doing</option>
                <option value="done">Done</option>
                </select>
            </div>
        </div>
        
        <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
            <button 
                v-if="!isEditMode"
                type="button" 
                @click="isEditMode = true"
                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-bold text-sm shadow-md"
            >
                Chỉnh sửa thông tin
            </button>
            <button 
            v-if="isEditMode"
            type="button" 
            @click="closeModal"
            class="px-4 py-2 text-slate-500 hover:bg-slate-100 rounded-xl font-medium text-sm transition cursor-pointer"
            >
            Hủy
            </button>
            <button 
            v-if="isEditMode"
            type="submit" 
            class="px-5 py-2 bg-cyan-500 hover:bg-cyan-600 text-white font-bold rounded-xl text-sm shadow-md transition cursor-pointer"
            >
            Lưu công việc
            </button>
        </div>
        </form>

    </div>
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

            <h3 class="text-lg font-bold text-slate-800">Xác nhận xóa</h3>
            
            <p class="text-sm text-slate-600">
                Bạn có chắc chắn muốn xóa ?
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
                    Xóa ngay
                </button>
            </div>
        </div>
    </div>

</template>

<script setup lang="ts">
import axios from 'axios'; 
import { useRouter } from 'vue-router';
import {ref, computed, onMounted} from 'vue';
import TaskSearch from './TaskSearch.vue';

const errorMessage = ref('')
const successMessage = ref('')

const formatDate = (dateStr: string | null) => {
    if (!dateStr) return ''
    return dateStr.split('T')[0]?.split(' ')[0] ?? ''
}

const selectedTaskId = ref<number | null>(null)

const isModalOpen = ref(false)
const taskForm = ref({
    'title': '',
    'description': '',
    'due_date': '',
    'status': 'todo'
})

const isEditMode = ref(false)
const openDetailModal = (task: Task) => {
    isEditMode.value = false
    selectedTaskId.value = task.id
    taskForm.value = {
        title: task.title,
        description: task.description || '',
        due_date: formatDate(task.due_date),
        status: task.status
    }
    isModalOpen.value = true
}

const openCreateModal = () => {
    isEditMode.value = true
    selectedTaskId.value = null
    taskForm.value = {
        title: '',
        description: '',
        due_date: '',
        status: 'todo'
    }
    isModalOpen.value = true
}

const openEditModal = (task: Task) => {
    isEditMode.value = true
    selectedTaskId.value = task.id
    taskForm.value = {
        title: task.title,
        description: task.description || '',
        due_date: formatDate(task.due_date),
        status: task.status
    }
    isModalOpen.value = true
}

const closeModal = () => {
    isModalOpen.value = false,
    selectedTaskId.value = null
    taskForm.value = {
        title: '',
        description: '',
        due_date: '',
        status: 'todo'
    }
}

const modalTitle = computed(() => {
  if (!selectedTaskId.value) {
    return 'Thêm công việc mới'
  }
  return isEditMode.value ? 'Chỉnh sửa công việc' : 'Chi tiết công việc' 
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

const currentTab = ref<'ALL' | 'TODO' | 'DOING' | 'DONE'>('ALL')

interface Task {
    id: number
    title: string
    description: string | null
    status: 'todo' | 'doing' | 'done'
    due_date: string | null
    is_overdue: boolean 
}

const tasks = ref<Task[]>([])

const keyword = ref('')
const filterTasks = computed(() => {
    return tasks.value.filter(task => {
        const query = keyword.value.toLowerCase().trim()
        const matchSearch = !query || task.title.toLowerCase().includes(query) || 
            task.description?.toLowerCase().includes(query)

        const matchTab = currentTab.value === 'ALL' || task.status.toUpperCase() === currentTab.value

        return matchSearch && matchTab
    })
})

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

const handleGetTasksList = async() => {
    try {
        errorMessage.value = ''
        successMessage.value = ''
        
        const token = localStorage.getItem('token')

        if (!token) {
            errorMessage.value = 'Bạn chưa đăng nhập'
            return
        }

        const response = await axios.get('http://localhost:8000/api/tasks', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })
        
        if (response.data?.data){
            tasks.value = response.data.data.data
            successMessage.value = response.data.message || 'Lấy danh sách công việc thành công'
        }
    } catch (error: any) {
        handleApiError(error, 'Lấy danh sách công việc thất bại')
    }
}

const handleGetSingleTask = async(taskId : number) => {
    try {
        errorMessage.value = ''
        successMessage.value = ''

        const token = localStorage.getItem('token')

        if (!token) {
            errorMessage.value = 'Bạn chưa đăng nhập'
            return
        }

        const response = await axios.get(`http://localhost:8000/api/tasks/${taskId}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })

        if (response.data?.data) {
            return response.data.data.data
        }
    } catch (error: any) {
        handleApiError(error, 'Lấy chi tiết công việc thất bại')
    }
}

const handleToggleTask = async(task: Task) => {
    try {
        const token = localStorage.getItem('token')

        if (!token) {
            errorMessage.value = 'Bạn chưa đăng nhập'
            return
        }

        const response = await axios.put(
            `http://localhost:8000/api/tasks/${task.id}/complete`, 
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
        handleApiError(error, 'Cập nhật công việc thất bại')
    }
}

const handleSubmitTask = async() => {
    if (selectedTaskId.value)
    {
        if (isEditMode.value)
        {
            await handleEditTask(selectedTaskId.value!)
        }
        else
        {
            await handleGetSingleTask(selectedTaskId.value!)
        }
    } else {
        await handleAddNewTask()
    }
}

const handleAddNewTask = async() => {
    try {
        errorMessage.value = ''
        successMessage.value = ''

        const token = localStorage.getItem('token')
        
        if (!token) {
            errorMessage.value = 'Bạn chưa đăng nhập'
            return
        }

        const response = await axios.post(
            'http://localhost:8000/api/tasks',
            taskForm.value,
            {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            }
        )

        if (response.data) {
            successMessage.value = response.data.message
            closeModal()
            await handleGetTasksList()            
        }
    } catch (error : any) {
        handleApiError(error, 'Thêm mới công việc thất bại')
    }
}

const handleEditTask = async(taskId: number) => {
    try {
        errorMessage.value = ''
        successMessage.value = ''

        const token = localStorage.getItem('token')

        if (!token) {
            errorMessage.value = 'Bạn chưa đăng nhập'
            return
        }

        const response = await axios.put(
            `http://localhost:8000/api/tasks/${taskId}`,
            taskForm.value,
            {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            }
        )

        if (response.data) {
            successMessage.value = response.data.message
            closeModal()
            await handleGetTasksList()            
        }
    } catch (error : any) {
        handleApiError(error, 'Cập nhật công việc thất bại')
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
            `http://localhost:8000/api/tasks/${task.id}`,
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

onMounted(() => {
    handleGetTasksList()
})


</script>