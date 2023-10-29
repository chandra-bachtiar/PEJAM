<script setup>
import { paginationMeta } from '@/@fake-db/utils'
import { useCadidateListStore } from '@/ListStore/useCadidateListStore'
import { themeConfig } from '@themeConfig'
import { VDataTableServer } from 'vuetify/labs/VDataTable'
import AddNewCadidateDrawer from './drawer/AddNewCadidateDrawer.vue'
import EditCadidateDrawer from './drawer/EditCadidateDrawer.vue'

// 👉 Toasify
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

document.title = `Kadidat - ${themeConfig.app.title}`

const cadidateListStore = useCadidateListStore()
const searchQuery = ref('')
const totalPage = ref(1)
const totalCadidates = ref(0)
const cadidates = ref([])

const updateId = ref(0)
const deleteId = ref(0)
const seletectedCadidate = ref({})
const isDialogDeleteOpen = ref(false)
const visiMisi = ref('')

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
    title: 'KADIDAT ID',
    key: 'kadidat_id',
  },
  {
    title: 'NAMA KETUA',
    key: 'ketua',
  },
  {
    title: 'NAMA WAKIL',
    key: 'wakil',
  },
  {
    title: 'VISI MISI',
    key: 'visimisi',
  },
  {
    title: 'FOTO',
    key: 'foto',
  },
  {
    title: 'ACTIONS',
    key: 'actions',
    sortable: false,
  },
]

// 👉 Fetching cadidates
const fetchCadidates = () => {
  cadidateListStore.fetchCadidates({
    search: searchQuery.value,
    options: options.value,
  }).then(response => {
    cadidates.value = response?.data?.cadidate.data
    totalPage.value = response?.data?.cadidate.last_page
    totalCadidates.value = response?.data?.cadidate.total
  }).catch(error => {
    console.error(error)
  })
}

watchEffect(fetchCadidates)

const isAddNewCadidateDrawerVisible = ref(false)
const isEditCadidateDrawerVisible = ref(false)
const isDialogViewVisible = ref(false)

const openDrawerUpdate = async id => {
  updateId.value = id
  console.log(id)

  const cadidate = await cadidateListStore.fetchCadidate(id)

  seletectedCadidate.value = cadidate
  isEditCadidateDrawerVisible.value = true
}

const addNewCadidate = cadidateData => {
  const create = cadidateListStore.addCadidate(cadidateData)
    .then(res => {
      fetchCadidates()
    })
    .catch(error => {
      console.error(error)
    })

  toast.promise(create, {
    loading: 'Menyimpan Data...',
    success: 'Kadidat berhasil ditambahkan',
    error: 'Kadidat gagal ditambahkan',
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

const updateCadidate = cadidateData => {
  const update = cadidateListStore.updateCadidate(cadidateData).then(res => {
    fetchCadidates()
  })
    .catch(error => {
      console.error(error)
    })

  toast.promise(update, {
    loading: 'Menyimpan Data...',
    success: 'Kadidat berhasil diupdate',
    error: 'Kadidat gagal diupdate',
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

const deleteCadidate = () => {
  const deleted = cadidateListStore.deleteCadidate(deleteId.value).then(res => {
    fetchCadidates()
  }).catch(error => {
    console.error(error)
  })

  toast.promise(deleted, {
    loading: 'Menghapus Data...',
    success: 'Kadidat berhasil dihapus',
    error: 'Kadidat gagal dihapus',
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
  fetchCadidates()
}

const openImage = image => {
  window.open(image, '_blank')
}

const openVisiMisi = description => {
  visiMisi.value = description
  isDialogViewVisible.value = true
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
              <!-- 👉 Search Cadidate -->
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
                <!-- 👉 Add cadidate button -->
                <VBtn
                  prepend-icon="tabler-plus"
                  @click="isAddNewCadidateDrawerVisible = true"
                >
                  Tambah Kadidat
                </VBtn>
              </VCol>
            </VRow>
          </VCardText>

          <VDivider />

          <!-- SECTION datatable -->
          <VDataTableServer
            v-model:items-per-page="options.itemsPerPage"
            v-model:page="options.page"
            :items="cadidates"
            :items-length="totalCadidates"
            :headers="headers"
            class="text-no-wrap"
            @update:options="options = $event"
          >
            <!-- 👉 ID -->
            <template #item.kadidat_id="{ item }">
              <div class="d-flex align-center">
                <div class="d-flex flex-column">
                  <h6 class="text-base">
                    KID-{{ item.raw.id }}
                  </h6>
                </div>
              </div>
            </template>

            <!-- 👉 Ketua -->
            <template #item.ketua="{ item }">
              <div class="d-flex align-center">
                <div class="d-flex flex-column">
                  <h6 class="text-base">
                    {{ item.raw.ketua }}
                  </h6>
                </div>
              </div>
            </template>

            <!-- 👉 Wakil -->
            <template #item.wakil="{ item }">
              <div class="d-flex align-center">
                <div class="d-flex flex-column">
                  <h6 class="text-base">
                    {{ item.raw.wakil }}
                  </h6>
                </div>
              </div>
            </template>

            <!-- 👉 Wakil -->
            <template #item.visimisi="{ item }">
              <div class="d-flex align-center">
                <div class="d-flex flex-column">
                  <h6
                    class="text-base links"
                    @click="openVisiMisi(item.raw.description)"
                  >
                    Lihat Visi & Misi
                  </h6>
                </div>
              </div>
            </template>

            <!-- 👉 Wakil -->
            <template #item.foto="{ item }">
              <div class="d-flex align-center">
                <div class="d-flex flex-column">
                  <h6
                    class="text-base links"
                    @click="openImage(item.raw.image)"
                  >
                    Lihat Foto
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
                  {{ paginationMeta(options, totalCadidates) }}
                </p>

                <VPagination
                  v-model="options.page"
                  :length="Math.ceil(totalCadidates / options.itemsPerPage)"
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

        <!-- 👉 Add New Cadidate -->
        <AddNewCadidateDrawer
          v-model:isDrawerOpen="isAddNewCadidateDrawerVisible"
          @cadidate-data="addNewCadidate"
        />

        <!-- 👉 Edit Cadidate -->
        <EditCadidateDrawer
          v-model:isDrawerOpen="isEditCadidateDrawerVisible"
          :updateid="updateId"
          :cadidate="seletectedCadidate"
          @cadidate-data="updateCadidate"
        />
      </vcol>
    </vrow>

    <!-- 👉 Delete cadidate Dialog -->
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
          Apakah anda yakin ingin menghapus data kadidat tersebut?
        </VCardText>

        <VCardText class="d-flex justify-end gap-3 flex-wrap">
          <VBtn
            color="secondary"
            variant="tonal"
            @click="isDialogDeleteOpen = false"
          >
            Batal
          </VBtn>
          <VBtn @click="deleteCadidate">
            Ya, Hapus!
          </VBtn>
        </VCardText>
      </VCard>
    </VDialog>

    <VDialog
      v-model="isDialogViewVisible"
      width="500"
    >
      <!-- Dialog close btn -->
      <DialogCloseBtn @click="isDialogViewVisible = !isDialogViewVisible" />

      <!-- Dialog Content -->
      <VCard title="Visi & Misi">
        <VCardText>
          {{ visiMisi }}
        </VCardText>

        <VCardText class="d-flex justify-end">
          <VBtn @click="isDialogViewVisible = false">
            Tutup
          </VBtn>
        </VCardText>
      </VCard>
    </VDialog>
  </section>
</template>

<style lang="scss">
.links {
  cursor: pointer;
  color: blue;
}

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
