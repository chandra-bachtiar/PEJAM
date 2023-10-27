<script setup>
import { useKategoriKelasListStore } from '@/ListStore/useKategoriKelasListStore'
import {
  requiredValidator,
} from '@validators'
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'


const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
  updateid: {
    type: Number,
    required: true,
  },
  kelas: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits([
  'update:isDrawerOpen',
  'kelasData',
])

const kategoriKelasListStore = useKategoriKelasListStore()
const isFormValid = ref(false)
const refForm = ref()
const namaKelas = ref(props.kelas.nama)
const kategoriKelas = ref([])
const selectedKategoriKelas = ref(props.kelas.kategori_kelas_id)
const kelasId = ref(props.kelas.id)
const durasi = ref(props.kelas.durasi)
const harga = ref(props.kelas.harga)
const deskripsi = ref(props.kelas.deskripsi)
const image = ref('')
const status = ref(props.kelas.status)
const kesulitan = ref(props.kelas.kesulitan)

const statusList = ref([
  { text: 'Draft', value: 'draft' },
  { text: 'Publish', value: 'publish' },
])

const kesulitanList = ref([
  { text: 'Pemula', value: 'pemula' },
  { text: 'Menengah', value: 'menengah' },
  { text: 'Mahir', value: 'mahir' },
])

const fetchKategoriKelas = () => {
  kategoriKelasListStore.fetchKategoriKelasAll().then(response => {
    const res = response.data.kategori_kelas.map(item => ({
      title: item.kategori,
      value: item.id,
    }))

    kategoriKelas.value = res
  }).catch(error => {
    console.error(error)
  })
}

watchEffect(fetchKategoriKelas)

// 👉 drawer close
const closeNavigationDrawer = () => {
  emit('update:isDrawerOpen', false)
  nextTick(() => {
    refForm.value?.reset()
    refForm.value?.resetValidation()
  })
}

const handleFileChange = event => {
  const file = event.target.files[0]

  image.value = file
}

const onSubmit = () => {
  refForm.value?.validate().then(({ valid }) => {
    if (valid) {
      const form = new FormData()

      form.append('_method', 'PUT')
      form.append('id', props.kelas.id)
      form.append('nama', namaKelas.value)
      form.append('kategori', selectedKategoriKelas.value)
      form.append('durasi', durasi.value)
      form.append('harga', harga.value)
      form.append('kesulitan', kesulitan.value)
      form.append('status', status.value)
      form.append('deskripsi', deskripsi.value)
      form.append('thumbnail', image.value)

      emit('kelasData', form)
      emit('update:isDrawerOpen', false)
      nextTick(() => {
        refForm.value?.reset()
        refForm.value?.resetValidation()
      })
    }
  })
}

const handleDrawerModelValueUpdate = val => {
  emit('update:isDrawerOpen', val)
}

watchEffect(() => {
  kelasId.value = props.kelas.id
  namaKelas.value = props.kelas.nama
  selectedKategoriKelas.value = props.kelas.kategori_kelas_id
  durasi.value = props.kelas.durasi
  harga.value = props.kelas.harga
  kesulitan.value = props.kelas.kesulitan
  status.value = props.kelas.status
  deskripsi.value = props.kelas.deskripsi
  console.log(props.kelas)
})
</script>

<template>
  <VNavigationDrawer
    temporary
    :width="400"
    location="end"
    class="scrollable-content"
    :model-value="props.isDrawerOpen"
    @update:model-value="handleDrawerModelValueUpdate"
  >
    <!-- 👉 Title -->
    <AppDrawerHeaderSection
      title="Tambah Kelas"
      class="ma-4"
      @cancel="closeNavigationDrawer"
    />
    <VDivider />
    <PerfectScrollbar :options="{ wheelPropagation: false }">
      <VCard flat>
        <VCardText>
          <!-- 👉 Form -->
          <VForm
            ref="refForm"
            v-model="isFormValid"
            @submit.prevent="onSubmit"
          >
            <VRow>
              <!-- 👉 Nama -->
              <VCol cols="12">
                <AppTextField
                  v-model="namaKelas"
                  :rules="[requiredValidator]"
                  label="Nama kelas"
                  placeholder="Masukan Nama kelas"
                />
              </VCol>
              <!-- 👉 Description -->
              <VCol
                cols="12"
                class="pt-0"
              >
                <AppTextarea
                  v-model="deskripsi"
                  auto-grow
                  label="Deskripsi kelas"
                  :rules="[requiredValidator]"
                  rows="2"
                  row-height="20"
                  placeholder="Deskripsi kelas"
                />
              </VCol>
              <!-- 👉 Kategori -->
              <VCol
                cols="12"
                class="pt-0"
              >
                <AppSelect
                  v-model="selectedKategoriKelas"
                  :items="kategoriKelas"
                  label="Kategori kelas"
                  :rules="[requiredValidator]"
                  placeholder="Pilih Kategori kelas"
                />
              </VCol>
              <!-- 👉 Durasi -->
              <VCol
                cols="12"
                class="pt-0"
              >
                <AppTextField
                  v-model="durasi"
                  label="Perkiraan Durasi Kelas"
                  :rules="[requiredValidator]"
                  suffix="Menit"
                  type="number"
                />
              </VCol>
              <!-- 👉 Harga -->
              <VCol
                cols="12"
                class="pt-0"
              >
                <AppTextField
                  v-model="harga"
                  label="Harga Kelas"
                  :rules="[requiredValidator]"
                  prefix="Rp. "
                  type="number"
                />
              </VCol>
              <!-- 👉 Kesulitan -->
              <VCol
                cols="6"
                class="pt-0"
              >
                <AppSelect
                  v-model="kesulitan"
                  :items="kesulitanList"
                  label="Kesulitan Kelas"
                  :rules="[requiredValidator]"
                  item-title="text"
                  item-value="value"
                  placeholder="Pilih Kesulitan"
                />
              </VCol>
              <!-- 👉 Status -->
              <VCol
                cols="6"
                class="pt-0"
              >
                <AppSelect
                  v-model="status"
                  :items="statusList"
                  label="Status Kelas"
                  :rules="[requiredValidator]"
                  item-title="text"
                  item-value="value"
                  placeholder="Pilih Kategori kelas"
                />
              </VCol>
              <!-- 👉 Image -->
              <VCol
                cols="12"
                class="pt-0"
              >
                <VLabel>Thumbnail</VLabel>
                <VFileInput
                  label="Thumbnail Kelas"
                  accept="image/png, image/jpeg, image/bmp"
                  placeholder="Pick an avatar"
                  prepend-icon="tabler-camera"
                  @change="handleFileChange"
                />
              </VCol>
              <!-- 👉 Submit and Cancel -->
              <VCol cols="12">
                <VBtn
                  type="submit"
                  class="me-2"
                >
                  Simpan
                </VBtn>
                <VBtn
                  type="reset"
                  variant="tonal"
                  color="secondary"
                  @click="closeNavigationDrawer"
                >
                  Batal
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </PerfectScrollbar>
  </VNavigationDrawer>
</template>
