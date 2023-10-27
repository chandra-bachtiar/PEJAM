<script setup>
import { useKelasListStore } from '@/ListStore/useKelasListStore'
import router from '@/router'
import { themeConfig } from '@themeConfig'
import AddNewKelasDrawer from './drawer/AddNewKelasDrawer.vue'
import EditKelasDrawer from './drawer/EditKelasDrawer.vue'

// 👉 Toasify
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

document.title = `Daftar Kelas - ${themeConfig.app.title}`

const kelasListStore = useKelasListStore()
const searchQuery = ref('')
const totalPage = ref(1)
const totalKelas = ref(0)
const kategoriKelas = ref([])
const kelas = ref([])

const updateId = ref(0)
const deleteId = ref(0)
const seletectedKelas = ref({})

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
const fetchKelas = () => {
  kelasListStore.fetchKelas({
    search: searchQuery.value,
    options: options.value,
  }).then(response => {
    kelas.value = response.data.kelas.data
    totalPage.value = response.data.kelas.last_page
    totalKelas.value = response.data.kelas.total
  }).catch(error => {
    console.error(error)
  })
}

watchEffect(fetchKelas)

const isAddNewKelasDrawerVisible = ref(false)
const isEditKelasDrawerVisible = ref(false)

const openDrawerUpdate = async id => {
  updateId.value = id

  const kelas = await kelasListStore.fetchSingleKelas(id)

  seletectedKelas.value = kelas
  isEditKelasDrawerVisible.value = true
}

const addNewKelas = kategoriKelasData => {
  const create = kelasListStore.addKelas(kategoriKelasData).then(res => {
    fetchKelas()
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

const updateKelas = kelasData => {
  const update = kelasListStore.updateKelas(kelasData).then(res => {
    fetchKelas()
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

const deleteKelas = () => {
  const deleted = kelasListStore.deleteKelas(deleteId.value).then(res => {
    fetchKelas()
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

  // refetch Kelas
  fetchKelas()
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
              <!-- 👉 Search Kelas -->
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
                  @click="isAddNewKelasDrawerVisible = true"
                >
                  Tambah Kelas
                </VBtn>
              </VCol>
            </VRow>

            <VRow>
              <!-- Start loop -->
              <VCol
                v-for="(kls, index) in kelas"
                :key="index"
                cols="12"
                sm="6"
                md="4"
              >
                <VCard>
                  <VImg
                    :src="kls.thumbnail"
                    cover
                    :height="250"
                    aspect-ratio="16/9"
                  >
                    <div
                      class="pa-2"
                      style="float: right;"
                    >
                      <VChip
                        v-if="kls.status === 'publish'"
                        color="success"
                        variant="elevated"
                      >
                        {{ kls.status }}
                      </VChip>
                      <VChip
                        v-else-if="kls.status === 'draft'"
                        color="warning"
                        variant="elevated"
                      >
                        {{ kls.status }}
                      </VChip>
                    </div>
                  </VImg>

                  <VCardItem class="pb-0">
                    <VRow>
                      <VCol cols="6">
                        <VChip
                          color="primary"
                          class="mb-2"
                        >
                          {{ kls.kategori_kelas.kategori }}
                        </VChip>
                      </VCol>
                      <VCol
                        cols="6"
                        class="text-right"
                      >
                        <VCardTitle>Rp. {{ kls.harga.toLocaleString('id-ID', {style:"currency", currency: "IDR"}) }}</VCardTitle>
                      </VCol>
                    </VRow>
                    <VCardTitle>{{ kls.nama }}</VCardTitle>
                  </VCardItem>

                  <VCardText v-if="kls.deskripsi.length>120">
                    {{ kls.deskripsi.slice(0, 120) }}...
                  </VCardText>
                  <VCardText v-else>
                    {{ kls.deskripsi }}
                  </VCardText>
                  <VCardText class="text-right">
                    <div>
                      <VBtn
                        color="info"
                        variant="tonal"
                        size="small"
                        rounded="lg"
                        class="me-2"
                        @click="router.push({path: `/kelas/materi/${kls.id}`})"
                      >
                        Materi
                      </VBtn>
                      <VBtn
                        color="primary"
                        variant="tonal"
                        size="small"
                        rounded="lg"
                        class="me-2"
                        @click="openDrawerUpdate(kls.id)"
                      >
                        Rubah
                      </VBtn>
                      <VBtn
                        color="error"
                        variant="tonal"
                        size="small"
                        rounded="lg"
                        @click="openModalDelete(kls.id)"
                      >
                        Hapus
                      </VBtn>
                    </div>
                  </VCardText>
                </VCard>
              </VCol>
              <!-- End Loop -->
            </VRow>
            
            <VRow>
              <VCol cols="12">
                <VPagination
                  v-model="options.page"
                  :length="10"
                  :total-visible="5"
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
              </VCol>
            </VRow>
          </VCardText>
        </VCard>

        <!-- 👉 Add New Kelas -->
        <AddNewKelasDrawer
          v-model:isDrawerOpen="isAddNewKelasDrawerVisible"
          @kelas-data="addNewKelas"
        />

        <!-- 👉 Edit Kelas -->
        <EditKelasDrawer
          v-model:isDrawerOpen="isEditKelasDrawerVisible"
          :updateid="updateId"
          :kelas="seletectedKelas"
          @kelas-data="updateKelas"
        />
      </vcol>
    </vrow>

    <!-- 👉 Delete Kelas Dialog -->
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
          Apakah anda yakin ingin menghapus dataKelas tersebut?
        </VCardText>

        <VCardText class="d-flex justify-end gap-3 flex-wrap">
          <VBtn
            color="secondary"
            variant="tonal"
            @click="isDialogDeleteOpen = false"
          >
            Batal
          </VBtn>
          <VBtn @click="deleteKelas">
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
