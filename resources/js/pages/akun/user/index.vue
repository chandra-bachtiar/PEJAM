<script setup>
import { paginationMeta } from '@/@fake-db/utils'
import { useUserListStore } from '@/ListStore/useUserListStore'
import { avatarText } from '@core/utils/formatters'
import { themeConfig } from '@themeConfig'
import moment from 'moment'
import { VDataTableServer } from 'vuetify/labs/VDataTable'
import AddNewUserDrawer from './drawer/AddNewUserDrawer.vue'
import EditUserDrawer from './drawer/EditUserDrawer.vue'

// 👉 Toasify
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

document.title = `User - ${themeConfig.app.title}`

const userListStore = useUserListStore()
const searchQuery = ref('')
const totalPage = ref(1)
const totalUsers = ref(0)
const users = ref([])

const updateId = ref(0)
const deleteId = ref(0)
const seletectedUser = ref({})

const isDialogDeleteOpen = ref(false)

const errorFetching = ref({
  status: false,
  message: '',
})

const options = ref({
  page: 1,
  itemsPerPage: 10,
  sortBy: [],
  groupBy: [],
  search: undefined,
})

// Headers
const headers = [
  {
    title: 'ID ADMIN',
    key: 'id',
  },
  {
    title: 'ADMIN',
    key: 'name',
  },
  {
    title: 'No HP',
    key: 'phone_number',
  },
  {
    title: 'DIBUAT OLEH',
    key: 'created_by',
  },
  {
    title: 'ACTIONS',
    key: 'actions',
    sortable: false,
  },
]

// 👉 Fetching users
const fetchUsers = () => {
  userListStore.fetchUsers({
    search: searchQuery.value,
    options: options.value,
  }).then(response => {
    users.value = response.data.user.data
    totalPage.value = response.data.user.last_page
    totalUsers.value = response.data.user.total
  }).catch(error => {
    console.error(error)
  })
}

watchEffect(fetchUsers)

const isAddNewUserDrawerVisible = ref(false)
const isEditUserDrawerVisible = ref(false)

const openDrawerUpdate = async id => {
  updateId.value = id

  const user = await userListStore.fetchUser(id)

  seletectedUser.value = user
  isEditUserDrawerVisible.value = true
}

const addNewUser = userData => {
  console.log(userData)

  const create = userListStore.addUser(userData)
    .then(res => {
      fetchUsers()
    })
    .catch(error => {
      console.error(error)
    })

  toast.promise(create, {
    loading: 'Menyimpan Data...',
    success: 'User berhasil ditambahkan',
    error: 'User gagal ditambahkan',
  }, {
    position: toast.POSITION.TOP_RIGHT,
    success: {
      icon: 'tabler-check',
    },
    error: {
      icon: 'tabler-x',
    },
    autoClose: 1000,
  })
}

const updateUser = userData => {
  const update = userListStore.updateUser(userData).then(res => {
    fetchUsers()
  })
    .catch(error => {
      console.error(error)
    })

  toast.promise(update, {
    loading: 'Menyimpan Data...',
    success: 'User berhasil diupdate',
    error: 'User gagal diupdate',
  }, {
    position: toast.POSITION.TOP_RIGHT,
    success: {
      icon: 'tabler-check',
    },
    error: {
      icon: 'tabler-x',
    },
    autoClose: 1000,
  })
}

const openModalDelete = id => {
  deleteId.value = id
  isDialogDeleteOpen.value = true
}

const deleteUser = () => {
  const deleted = userListStore.deleteUser(deleteId.value).then(res => {
    fetchUsers()
  }).catch(error => {
    console.error(error)
  })

  toast.promise(deleted, {
    loading: 'Menghapus Data...',
    success: 'User berhasil dihapus',
    error: 'User gagal dihapus',
  }, {
    position: toast.POSITION.TOP_RIGHT,
    success: {
      icon: 'tabler-check',
    },
    error: {
      icon: 'tabler-x',
    },
    autoClose: 1000,
  }).then(() => {
    isDialogDeleteOpen.value = false
  })

  // refetch User
  fetchUsers()
}
</script>

<template>
  <section>
    <VRow>
      <VCol cols="12">
        <VCard>
          <!-- 👉 Filters -->
          <VCardText>
            <VRow>
              <!-- 👉 Search User -->
              <VCol
                cols="12"
                sm="4"
              >
                <AppTextField
                  v-model="searchQuery"
                  placeholder="Search"
                  density="compact"
                />
              </VCol>
              <VSpacer />
              <VCol
                cols="12"
                sm="4"
                class="text-right"
              >
                <!-- 👉 Add user button -->
                <VBtn
                  prepend-icon="tabler-plus"
                  @click="isAddNewUserDrawerVisible = true"
                >
                  Tambah User
                </VBtn>
              </VCol>
            </VRow>
          </VCardText>

          <VDivider />

          <!-- SECTION datatable -->
          <VDataTableServer
            v-model:items-per-page="options.itemsPerPage"
            v-model:page="options.page"
            :items="users"
            :items-length="totalUsers"
            :headers="headers"
            class="text-no-wrap"
            @update:options="options = $event"
          >
            <!-- 👉 ID -->
            <template #item.id="{ item }">
              <div class="d-flex align-center gap-4">
                <span class="text-capitalize">ADM-{{ item.raw.id }}</span>
              </div>
            </template>

            <!-- User -->
            <template #item.name="{ item }">
              <div class="d-flex align-center">
                <VAvatar
                  size="34"
                  :variant="!item.raw.avatar ? 'tonal' : undefined"
                  class="me-3"
                >
                  <VImg
                    v-if="item.raw.image"
                    :src="item.raw.image"
                  />
                  <span v-else>{{ avatarText(item.raw.name) }}</span>
                </VAvatar>

                <div class="d-flex flex-column">
                  <h6 class="text-base">
                    {{ item.raw.name }}
                  </h6>

                  <span class="text-sm text-medium-emphasis">{{ item.raw.email }}</span>
                </div>
              </div>
            </template>

            <!-- 👉 No HP -->
            <template #item.phone_number="{ item }">
              <div class="d-flex align-center">
                <div class="d-flex flex-column">
                  <h6 class="text-base">
                    {{ item.raw.phone_number }}
                  </h6>

                  <span class="text-sm text-medium-emphasis">Whatsapp</span>
                </div>
              </div>
            </template>

            <!-- 👉 Created By -->
            <template #item.created_by="{ item }">
              <div class="d-flex align-center">
                <div class="d-flex flex-column">
                  <h6 class="text-base">
                    {{ item.raw.created_by }}
                  </h6>

                  <span class="text-sm text-medium-emphasis">{{ moment(item.raw.created_at).format('DD-MM-YYYY HH:mm') }}</span>
                </div>
              </div>
            </template>

            <!-- Actions -->
            <template #item.actions="{ item }">
              <IconBtn @click="openDrawerUpdate(item.raw.id)">
                <VIcon icon="tabler-edit" />
              </IconBtn>
              
              <IconBtn @click="openModalDelete(item.raw.id)">
                <VIcon icon="tabler-trash" />
              </IconBtn>
            </template>

            <!-- pagination -->
            <template #bottom>
              <VDivider />
              <div class="d-flex align-center justify-sm-space-between justify-center flex-wrap gap-3 pa-5 pt-3">
                <p class="text-sm text-disabled mb-0">
                  {{ paginationMeta(options, totalUsers) }}
                </p>

                <VPagination
                  v-model="options.page"
                  :length="Math.ceil(totalUsers / options.itemsPerPage)"
                  :total-visible="$vuetify.display.xs ? 1 : 6"
                >
                  <template #prev="slotProps">
                    <VBtn
                      variant="tonal"
                      color="default"
                      v-bind="slotProps"
                      :icon="false"
                    >
                      Previous
                    </VBtn>
                  </template>

                  <template #next="slotProps">
                    <VBtn
                      variant="tonal"
                      color="default"
                      v-bind="slotProps"
                      :icon="false"
                    >
                      Next
                    </VBtn>
                  </template>
                </VPagination>
              </div>
            </template>
          </VDataTableServer>
          <!-- SECTION -->
        </VCard>

        <!-- 👉 Add New User -->
        <AddNewUserDrawer
          v-model:isDrawerOpen="isAddNewUserDrawerVisible"
          @user-data="addNewUser"
        />

        <!-- 👉 Edit User -->
        <EditUserDrawer
          v-model:isDrawerOpen="isEditUserDrawerVisible"
          :updateid="updateId"
          :user="seletectedUser"
          @user-data="updateUser"
        />
      </vcol>
    </vrow>

    <!-- 👉 Delete User Dialog -->
    <VDialog
      v-model="isDialogDeleteOpen"
      persistent
      class="v-dialog-sm"
    >
      <!-- Dialog close btn -->
      <DialogCloseBtn @click="isDialogDeleteOpen = !isDialogDeleteOpen" />

      <!-- Dialog Content -->
      <VCard title="Hapus data">
        <VCardText>
          Apakah anda yakin ingin menghapus data user tersebut?
        </VCardText>

        <VCardText class="d-flex justify-end gap-3 flex-wrap">
          <VBtn
            color="secondary"
            variant="tonal"
            @click="isDialogDeleteOpen = false"
          >
            Batal
          </VBtn>
          <VBtn @click="deleteUser">
            Ya, Hapus!
          </VBtn>
        </VCardText>
      </VCard>
    </VDialog>
  </section>
</template>

<style lang="scss">
.app-user-search-filter {
  inline-size: 31.6rem;
}

.text-capitalize {
  text-transform: capitalize;
}

.user-list-name:not(:hover) {
  color: rgba(var(--v-theme-on-background), var(--v-medium-emphasis-opacity));
}
</style>
