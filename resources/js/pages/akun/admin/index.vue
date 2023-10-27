<script setup>
import { paginationMeta } from '@/@fake-db/utils'
import { useAdminListStore } from '@/ListStore/useAdminListStore'
import { avatarText } from '@core/utils/formatters'
import { themeConfig } from '@themeConfig'
import moment from 'moment'
import { VDataTableServer } from 'vuetify/labs/VDataTable'
import AddNewAdminDrawer from './drawer/AddNewAdminDrawer.vue'
import EditAdminDrawer from './drawer/EditAdminDrawer.vue'

// 👉 Toasify
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

document.title = `Admin - ${themeConfig.app.title}`

const adminListStore = useAdminListStore()
const searchQuery = ref('')
const totalPage = ref(1)
const totalAdmins = ref(0)
const admins = ref([])

const updateId = ref(0)
const deleteId = ref(0)
const seletectedAdmin = ref({})

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
    title: 'DIBUAT OLEH',
    key: 'created_by',
  },
  {
    title: 'ACTIONS',
    key: 'actions',
    sortable: false,
  },
]

// 👉 Fetching admins
const fetchAdmins = () => {
  adminListStore.fetchAdmins({
    search: searchQuery.value,
    options: options.value,
  }).then(response => {
    admins.value = response.data.admin.data
    totalPage.value = response.data.admin.last_page
    totalAdmins.value = response.data.admin.total
  }).catch(error => {
    console.error(error)
  })
}

watchEffect(fetchAdmins)

const isAddNewAdminDrawerVisible = ref(false)
const isEditAdminDrawerVisible = ref(false)

const openDrawerUpdate = async id => {
  updateId.value = id

  const admin = await adminListStore.fetchAdmin(id)

  seletectedAdmin.value = admin
  isEditAdminDrawerVisible.value = true
}

const addNewAdmin = adminData => {
  console.log(adminData)

  const create = adminListStore.addAdmin(adminData)
    .then(res => {
      fetchAdmins()
    })
    .catch(error => {
      console.error(error)
    })

  toast.promise(create, {
    loading: 'Menyimpan Data...',
    success: 'Admin berhasil ditambahkan',
    error: 'Admin gagal ditambahkan',
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

const updateAdmin = adminData => {
  const update = adminListStore.updateAdmin(adminData).then(res => {
    fetchAdmins()
  })
    .catch(error => {
      console.error(error)
    })

  toast.promise(update, {
    loading: 'Menyimpan Data...',
    success: 'Admin berhasil diupdate',
    error: 'Admin gagal diupdate',
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

const deleteAdmin = () => {
  const deleted = adminListStore.deleteAdmin(deleteId.value).then(res => {
    fetchAdmins()
  }).catch(error => {
    console.error(error)
  })

  toast.promise(deleted, {
    loading: 'Menghapus Data...',
    success: 'Admin berhasil dihapus',
    error: 'Admin gagal dihapus',
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

  // refetch Admin
  fetchAdmins()
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
              <!-- 👉 Search Admin -->
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
                <!-- 👉 Add admin button -->
                <VBtn
                  prepend-icon="tabler-plus"
                  @click="isAddNewAdminDrawerVisible = true"
                >
                  Tambah Admin
                </VBtn>
              </VCol>
            </VRow>
          </VCardText>

          <VDivider />

          <!-- SECTION datatable -->
          <VDataTableServer
            v-model:items-per-page="options.itemsPerPage"
            v-model:page="options.page"
            :items="admins"
            :items-length="totalAdmins"
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

            <!-- Admin -->
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
                  {{ paginationMeta(options, totalAdmins) }}
                </p>

                <VPagination
                  v-model="options.page"
                  :length="Math.ceil(totalAdmins / options.itemsPerPage)"
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

        <!-- 👉 Add New Admin -->
        <AddNewAdminDrawer
          v-model:isDrawerOpen="isAddNewAdminDrawerVisible"
          @admin-data="addNewAdmin"
        />

        <!-- 👉 Edit Admin -->
        <EditAdminDrawer
          v-model:isDrawerOpen="isEditAdminDrawerVisible"
          :updateid="updateId"
          :admin="seletectedAdmin"
          @admin-data="updateAdmin"
        />
      </vcol>
    </vrow>

    <!-- 👉 Delete Admin Dialog -->
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
          Apakah anda yakin ingin menghapus data admin tersebut?
        </VCardText>

        <VCardText class="d-flex justify-end gap-3 flex-wrap">
          <VBtn
            color="secondary"
            variant="tonal"
            @click="isDialogDeleteOpen = false"
          >
            Batal
          </VBtn>
          <VBtn @click="deleteAdmin">
            Ya, Hapus!
          </VBtn>
        </VCardText>
      </VCard>
    </VDialog>
  </section>
</template>

<style lang="scss">
.app-admin-search-filter {
  inline-size: 31.6rem;
}

.text-capitalize {
  text-transform: capitalize;
}

.admin-list-name:not(:hover) {
  color: rgba(var(--v-theme-on-background), var(--v-medium-emphasis-opacity));
}
</style>
