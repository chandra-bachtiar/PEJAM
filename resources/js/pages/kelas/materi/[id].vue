<script setup>
import { useMateriListStore } from '@/ListStore/useMateriListStore'
import router from '@/router'
import { themeConfig } from '@themeConfig'
import AddNewMateriDrawer from './drawer/AddNewMateriDrawer.vue'
import EditMateriDrawer from './drawer/EditMateriDrawer.vue'

// 👉 Toasify
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

const props = defineProps({
  id: {
    type: String,
    required: true,
  },
})

document.title = `Materi Kelas - ${themeConfig.app.title}`

const materiListStore = useMateriListStore()
const materi = ref({})
const selectedMateri= ref({})
const updateId = ref(0)
const deleteId = ref(0)
const isDialogDeleteOpen = ref(false)

// 👉 Fetching materi
const fetchMateri = () => {
  materiListStore.fetchMateri(props.id)
    .then(res => {
      materi.value = res.data.data
      console.log(materi.value)
    }).catch(error => {
      console.error(error)
    })
}

watchEffect(fetchMateri)

const isAddNewMateriDrawerVisible = ref(false)
const isEditMateriDrawerVisible = ref(false)

const openDrawerUpdate = async id => {
  updateId.value = id

  const fetchMateri = await materiListStore.fetchMateriById(props.id, id)

  selectedMateri.value = fetchMateri.data.materi
  isEditMateriDrawerVisible.value = true
}

const addNewMateri = materiData => {
  const create = materiListStore.addMateri(materiData).then(res => {
    fetchMateri()
  })
    .catch(error => {
      console.error(error)
    })

  toast.promise(create, {
    loading: 'Menyimpan Data...',
    success: 'Materi berhasil ditambahkan',
    error: 'Materi gagal ditambahkan',
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

const updateMateri = materiData => {
  const update = materiListStore.updateMateri(materiData).then(res => {
    fetchMateri()
  })
    .catch(error => {
      console.error(error)
    })

  toast.promise(update, {
    loading: 'Menyimpan Data...',
    success: 'Materi berhasil diupdate',
    error: 'Materi gagal diupdate',
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

const deleteMateri = () => {
  const deleted = materiListStore.deleteMateri(deleteId.value).then(res => {
    fetchMateri()
  }).catch(error => {
    console.error(error)
  })

  toast.promise(deleted, {
    loading: 'Menghapus Data...',
    success: 'Materi berhasil dihapus',
    error: 'Materi gagal dihapus',
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
}

onMounted(() => {
  //check localStorage
  if (localStorage.getItem('isSaved') === 'true') {
    toast.success('Data berhasil disimpan')

    localStorage.removeItem('isSaved')
  }
})
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
                sm="6"
              >
                <h3 class="mb-0 text-h3">
                  {{ materi?.nama }}
                </h3>
              </VCol>
              <VSpacer />
              <VCol
                cols="12"
                sm="6"
                class="text-right"
              >
                <!-- 👉 Add kategoriKelas button -->
                <VBtn
                  prepend-icon="tabler-plus"
                  @click="isAddNewMateriDrawerVisible = true"
                >
                  Tambah Materi
                </VBtn>
              </VCol>
            </VRow>

            <VRow>
              <VCol
                v-for="(mat,index) in materi?.materi"
                :key="index"
                cols="12"
              >
                <VCard>
                  <VCardText>
                    <VRow>
                      <VCol cols="6">
                        <p class="body-4 mb-0">
                          {{ mat.tipe }} - {{ mat.tipe == 'quiz' ? mat.tipe_quiz : mat.tipe_konten }}
                        </p>
                        <h3>{{ mat.judul }}</h3>
                      </VCol>
                      <VCol
                        cols="6"
                        class="text-right d-flex align-center justify-end"
                      >
                        <VBtn
                          v-if="mat.tipe == 'quiz'"
                          color="success"
                          variant="tonal"
                          size="small"
                          rounded="lg"
                          class="me-2"
                          @click="router.push({path: `/kelas/materi/text/${mat.id}`})"
                        >
                          Isi Quiz
                        </VBtn>
                        <VBtn
                          v-if="mat.tipe_konten == 'teks'"
                          color="success"
                          variant="tonal"
                          size="small"
                          rounded="lg"
                          class="me-2"
                          @click="router.push({path: `/kelas/materi/text/${mat.id}`})"
                        >
                          Isi Materi
                        </VBtn>
                        <VBtn
                          color="primary"
                          variant="tonal"
                          size="small"
                          rounded="lg"
                          class="me-2"
                          @click="openDrawerUpdate(mat.id)"
                        >
                          Rubah
                        </VBtn>
                        <VBtn
                          color="error"
                          variant="tonal"
                          size="small"
                          rounded="lg"
                          @click="openModalDelete(mat.id)"
                        >
                          Hapus
                        </VBtn>
                      </VCol>
                    </VRow>
                  </VCardText>
                </VCard>
              </VCol>
            </VRow>
          </VCardText>
        </VCard>

        <!-- 👉 Add New Kelas -->
        <AddNewMateriDrawer
          v-model:isDrawerOpen="isAddNewMateriDrawerVisible"
          :id-kelas="props.id"
          @materi-data="addNewMateri"
        />

        <!-- 👉 Edit Kelas -->
        <EditMateriDrawer
          v-model:isDrawerOpen="isEditMateriDrawerVisible"
          :materi-id="updateId"
          :kelas-id="props.id"
          :materi="selectedMateri"
          @materi-data="updateMateri"
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
          Apakah anda yakin ingin menghapus data Materi tersebut?
        </VCardText>

        <VCardText class="d-flex justify-end gap-3 flex-wrap">
          <VBtn
            color="secondary"
            variant="tonal"
            @click="isDialogDeleteOpen = false"
          >
            Batal
          </VBtn>
          <VBtn @click="deleteMateri">
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
