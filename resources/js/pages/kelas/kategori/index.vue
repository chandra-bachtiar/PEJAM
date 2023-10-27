<script setup>
import { paginationMeta } from '@/@fake-db/utils'
import { useKategoriKelasListStore } from '@/ListStore/useKategoriKelasListStore'
import { themeConfig } from '@themeConfig'
import { VDataTableServer } from 'vuetify/labs/VDataTable'
import AddNewKategoriKelasDrawer from './drawer/AddNewKategoriKelasDrawer.vue'
import EditKategoriKelasDrawer from './drawer/EditKategoriKelasDrawer.vue'

// 👉 Toasify
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

document.title = `Kategori Kelas - ${themeConfig.app.title}`

const kategoriKelasListStore = useKategoriKelasListStore()
const searchQuery = ref('')
const totalPage = ref(1)
const totalKategoriKelas = ref(0)
const kategoriKelas = ref([])

const updateId = ref(0)
const deleteId = ref(0)
const seletectedKategoriKelas = ref({})

const isDialogDeleteOpen = ref(false)

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
    title: 'ID Kategori',
    key: 'id',
  },
  {
    title: 'Kategori',
    key: 'kategori',
  },
  {
    title: 'ACTIONS',
    key: 'actions',
    sortable: false,
  },
]

// 👉 Fetching kategoriKelass
const fetchKategoriKelas = () => {
  kategoriKelasListStore.fetchKategoriKelas({
    search: searchQuery.value,
    options: options.value,
  }).then(response => {
    kategoriKelas.value = response.data.kategori_kelas.data
    totalPage.value = response.data.kategori_kelas.last_page
    totalKategoriKelas.value = response.data.kategori_kelas.total
  }).catch(error => {
    console.error(error)
  })
}

watchEffect(fetchKategoriKelas)

const isAddNewKategoriKelasDrawerVisible = ref(false)
const isEditKategoriKelasDrawerVisible = ref(false)

const openDrawerUpdate = async id => {
  updateId.value = id

  const kategoriKelas = await kategoriKelasListStore.fetchSingleKategoriKelas(id)

  seletectedKategoriKelas.value = kategoriKelas
  isEditKategoriKelasDrawerVisible.value = true
}

const addNewKategoriKelas = kategoriKelasData => {

  const create = kategoriKelasListStore.addKategoriKelas(kategoriKelasData)
    .then(res => {
      fetchKategoriKelas()
    })
    .catch(error => {
      console.error(error)
    })

  toast.promise(create, {
    loading: 'Menyimpan Data...',
    success: 'Kategori berhasil ditambahkan',
    error: 'Kategori gagal ditambahkan',
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

const updateKategoriKelas = kategoriKelasData => {
  const update = kategoriKelasListStore.updateKategoriKelas(kategoriKelasData).then(res => {
    fetchKategoriKelas()
  })
    .catch(error => {
      console.error(error)
    })

  toast.promise(update, {
    loading: 'Menyimpan Data...',
    success: 'Kategori berhasil diupdate',
    error: 'Kategori gagal diupdate',
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

const deleteKategoriKelas = () => {
  const deleted = kategoriKelasListStore.deleteKategoriKelas(deleteId.value).then(res => {
    fetchKategoriKelas()
  }).catch(error => {
    console.error(error)
  })

  toast.promise(deleted, {
    loading: 'Menghapus Data...',
    success: 'Kategori berhasil dihapus',
    error: 'Kategori gagal dihapus',
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

  // refetch KategoriKelas
  fetchKategoriKelas()
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
              <!-- 👉 Search KategoriKelas -->
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
                <!-- 👉 Add kategoriKelas button -->
                <VBtn
                  prepend-icon="tabler-plus"
                  @click="isAddNewKategoriKelasDrawerVisible = true"
                >
                  Tambah Kategori
                </VBtn>
              </VCol>
            </VRow>
          </VCardText>

          <VDivider />

          <!-- SECTION datatable -->
          <VDataTableServer
            v-model:items-per-page="options.itemsPerPage"
            v-model:page="options.page"
            :items="kategoriKelas"
            :items-length="totalKategoriKelas"
            :headers="headers"
            class="text-no-wrap"
            @update:options="options = $event"
          >
            <!-- 👉 ID -->
            <template #item.id="{ item }">
              <div class="d-flex align-center gap-4">
                <span class="text-capitalize">KTGKLS-{{ item.raw.id }}</span>
              </div>
            </template>

            <!-- 👉 Kategori -->
            <template #item.kategori="{ item }">
              <div class="d-flex align-center">
                <div class="d-flex flex-column">
                  <h6 class="text-base">
                    {{ item.raw.kategori }}
                  </h6>
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
                  {{ paginationMeta(options, totalKategoriKelas) }}
                </p>

                <VPagination
                  v-model="options.page"
                  :length="Math.ceil(totalKategoriKelas / options.itemsPerPage)"
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

        <!-- 👉 Add New KategoriKelas -->
        <AddNewKategoriKelasDrawer
          v-model:isDrawerOpen="isAddNewKategoriKelasDrawerVisible"
          @kategori-kelas-data="addNewKategoriKelas"
        />

        <!-- 👉 Edit KategoriKelas -->
        <EditKategoriKelasDrawer
          v-model:isDrawerOpen="isEditKategoriKelasDrawerVisible"
          :updateid="updateId"
          :kategori-kelas="seletectedKategoriKelas"
          @kategori-kelas-data="updateKategoriKelas"
        />
      </vcol>
    </vrow>

    <!-- 👉 Delete KategoriKelas Dialog -->
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
          Apakah anda yakin ingin menghapus data kategoriKelas tersebut?
        </VCardText>

        <VCardText class="d-flex justify-end gap-3 flex-wrap">
          <VBtn
            color="secondary"
            variant="tonal"
            @click="isDialogDeleteOpen = false"
          >
            Batal
          </VBtn>
          <VBtn @click="deleteKategoriKelas">
            Ya, Hapus!
          </VBtn>
        </VCardText>
      </VCard>
    </VDialog>
  </section>
</template>

<style lang="scss">
.app-kategoriKelas-search-filter {
  inline-size: 31.6rem;
}

.text-capitalize {
  text-transform: capitalize;
}

.kategoriKelas-list-name:not(:hover) {
  color: rgba(var(--v-theme-on-background), var(--v-medium-emphasis-opacity));
}
</style>
