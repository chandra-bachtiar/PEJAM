<script setup>
import {
  requiredValidator,
} from '@validators'
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'


const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
  materiId: {
    type: Number,
    required: true,
  },
  kelasId: {
    type: String,
    required: true,
  },
  materi: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits([
  'update:isDrawerOpen',
  'materiData',
])

const isFormValid = ref(false)
const refForm = ref()
const judul = ref(props.materi?.judul)
const konten_video = ref(props.materi?.konten_video)
const konten_berkas = ref(props.materi?.konten_berkas)
const selectedType = ref(props.materi?.tipe)
const selectedMateriType = ref(props.materi?.tipe_kolom)
const selectedQuizType = ref(props.materi?.tipe_quiz)



const type = [
  {
    title: 'Pendahuluan',
    value: 'pendahuluan',
  },
  {
    title: 'Materi',
    value: 'materi',
  },
  {
    title: 'Test',
    value: 'test',
  },
]

const quizType = [
  {
    title: 'Pilihan Ganda',
    value: 'pilihan_ganda',
  },
  {
    title: 'Esai',
    value: 'esai',
  },
]


const materiType = [
  {
    title: 'Video',
    value: 'video',
  },
  {
    title: 'Teks',
    value: 'teks',
  },
  {
    title: 'Berkas',
    value: 'berkas',
  },
]


// 👉 drawer close
const closeNavigationDrawer = () => {
  emit('update:isDrawerOpen', false)
  nextTick(() => {
    refForm.value?.reset()
    refForm.value?.resetValidation()
  })
}

const onSubmit = () => {
  refForm.value?.validate().then(({ valid }) => {
    if (valid) {
      const form = new FormData()

      form.append('_method', 'PUT')
      form.append('id', props.materiId)
      form.append('judul', judul.value)
      form.append('konten_video', konten_video.value || '')
      form.append('konten_berkas', konten_berkas.value || '')
      form.append('tipe', selectedType.value)
      form.append('tipe_konten', selectedMateriType.value || '')
      form.append('tipe_quiz', selectedQuizType.value || '')
      form.append('kelas_id', props.kelasId)

      emit('materiData', form)
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
  judul.value = props.materi?.judul
  selectedType.value = props.materi?.tipe
  selectedMateriType.value = props.materi?.tipe_konten
  selectedQuizType.value = props.materi?.tipe_quiz
  konten_video.value = props.materi?.konten_video
  konten_berkas.value = props.materi?.konten_berkas
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
      title="Tambah Materi"
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
                  v-model="judul"
                  :rules="[requiredValidator]"
                  label="Judul materi"
                  placeholder="Masukan Judul materi"
                />
              </VCol>
              <!-- 👉 Jenis -->
              <VCol
                cols="12"
                class="pt-0"
              >
                <AppSelect
                  v-model="selectedType"
                  :items="type"
                  label="Jenis Materi"
                  :rules="[requiredValidator]"
                  item-title="title"
                  item-value="value"
                  placeholder="Pilih jenis materi"
                />
              </VCol>

              <!-- 👉 Tipe Materi -->
              <VCol
                v-if="selectedType != 'quiz'"
                cols="12"
                class="pt-0"
              >
                <AppSelect
                  v-model="selectedMateriType"
                  :items="materiType"
                  label="Data Materi"
                  item-title="title"
                  item-value="value"
                  placeholder="Pilih Data materi"
                />
              </VCol>

              <!-- Quiz -->
              <VCol
                v-if="selectedType == 'quiz'"
                cols="12"
                class="pt-0"
              >
                <AppSelect
                  v-model="selectedQuizType"
                  :items="quizType"
                  label="Tipe Quiz"
                  :rules="selectedType == 'quiz' ? [requiredValidator] : []"
                  item-title="title"
                  item-value="value"
                  placeholder="Pilih tipe quiz"
                />
              </VCol>

              <!-- 👉 video -->
              <VCol 
                v-if="selectedMateriType == 'video' && selectedType != 'quiz'"
                cols="12"
                class="pt-0"
              >
                <AppTextField
                  v-model="konten_video"
                  :rules="selectedType != 'quiz' && selectedMateriType == 'video' ? [requiredValidator] : []"
                  label="Link Video (youtube)"
                  placeholder="Masukan Link Video dari youtube"
                />
              </VCol>

              <!-- 👉 berkas -->
              <VCol 
                v-if="selectedMateriType == 'berkas' && selectedType != 'quiz'"
                cols="12"
                class="pt-0"
              >
                <VLabel>Berkas</VLabel>
                <VFileInput
                  prepend-icon="tabler-file-description"
                  accept="application/pdf"
                  :rules="selectedMateriType == 'berkas' && selectedType != 'quiz' ? [requiredValidator] : []"
                  @change="e => konten_berkas = e.target.files[0]"
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
