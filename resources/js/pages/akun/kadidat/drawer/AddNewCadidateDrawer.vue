<script setup>
import { requiredValidator } from '@validators'
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'

const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
})

const emit = defineEmits([
  'update:isDrawerOpen',
  'cadidateData',
])


const rules = [fileList => !fileList || !fileList.length || fileList[0].size < 1000000 || 'Foto Profil harus kurang dari 1 MB!']

const isFormValid = ref(false)
const refForm = ref()
const chairman = ref(null)
const viceChairman = ref(null)
const description = ref(null)
const cadidateImage = ref(null)
const image = ref(null)

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

  cadidateImage.value = file
  image.value = file
}

const onSubmit = () => {
  refForm.value?.validate().then(({ valid }) => {
    if (valid) {
      const form = new FormData()

      form.append('ketua', chairman.value)
      form.append('wakil', viceChairman.value)
      form.append('description', description.value)
      form.append('image', image.value)
      emit('cadidateData', form)
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
      title="Tambah Kadidat"
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
              <!-- 👉 Foto Profil -->
              <VCol cols="12">
                <VFileInput
                  ref="cadidateImage"
                  :rules="rules"
                  label="Foto Kadidat"
                  accept="image/png, image/jpeg, image/bmp"
                  placeholder="Foto Kadidat"
                  prepend-icon="tabler-camera"
                  @change="handleFileChange"
                />
              </VCol>

              <!-- 👉 ketua -->
              <VCol cols="12">
                <AppTextField
                  v-model="chairman"
                  :rules="[requiredValidator]"
                  label="Nama Ketua"
                  placeholder="Masukan Nama Ketua"
                />
              </VCol>

              <!-- 👉 wakil -->
              <VCol cols="12">
                <AppTextField
                  v-model="viceChairman"
                  :rules="[requiredValidator]"
                  label="Nama Wakil"
                  placeholder="Masukan Nama Wakil"
                />
              </VCol>

              <!-- 👉 Deksripsi -->
              <VCol cols="12">
                <AppTextarea
                  v-model="description"
                  label="Visi & Misi"
                />
              </VCol>

              <!-- 👉 Submit and Cancel -->
              <VCol cols="12">
                <VBtn type="submit">
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
