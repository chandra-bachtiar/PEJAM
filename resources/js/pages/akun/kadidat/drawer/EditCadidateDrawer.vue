<script setup>
import { requiredValidator } from '@validators'
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
  cadidate: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits([
  'update:isDrawerOpen',
  'cadidateData',
])

const rules = [fileList => !fileList || !fileList.length || fileList[0].size < 1000000 || 'Foto Profil harus kurang dari 1 MB!']

// 👉 Utils
const isDialogUpdateVisible = ref(false)

// 👉 Form
const isFormValid = ref(false)
const refForm = ref()
const chairman = ref(props.cadidate.ketua)
const viceChairman = ref(props.cadidate.wakil)
const description = ref(props.cadidate.description)
const image = ref(props.cadidate.image)
const imageCadidate = ref(null)

// 👉 drawer close
const closeNavigationDrawer = () => {
  emit('update:isDrawerOpen', false)
  nextTick(() => {
    refForm.value?.reset()
    refForm.value?.resetValidation()
  })
}

const submitForm = () => {
  isDialogUpdateVisible.value = false

  onSubmit()
}

const onSubmit = () => {
  refForm.value?.validate().then(({ valid }) => {
    if (valid) {
      const form = new FormData()

      form.append('_method', 'PUT')
      form.append('id', props.updateid)
      form.append('ketua', chairman.value)
      form.append('wakil', viceChairman.value)
      form.append('description', description.value)
      form.append('image', imageCadidate.value)
      emit('cadidateData', form)
      emit('update:isDrawerOpen', false)
      nextTick(() => {
        refForm.value?.reset()
        refForm.value?.resetValidation()
      })
    }
  })
}

const fileInput = ref(null)

const handleUploadFile = () => {
  const input = fileInput.value

  input.click()
}

const handleResetFile = () => {
  image.value = props.cadidate.image
}

const handleFileChange = event => {
  const file = event.target.files[0]

  imageCadidate.value = file

  //change preview image from ref image
  const reader = new FileReader()

  reader.onload = e => {
    image.value = e.target.result
  }

  reader.readAsDataURL(file)
}

const handleDrawerModelValueUpdate = val => {
  emit('update:isDrawerOpen', val)
}

watchEffect(() => {
  if(props.cadidate) {
    console.log(props.cadidate)
    chairman.value = props.cadidate.ketua
    viceChairman.value = props.cadidate.wakil
    description.value = props.cadidate.description
    image.value = props.cadidate.image
  }
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
      title="Rubah Kadidat"
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
                <VLabel>Foto Profil</VLabel>
                <VRow>
                  <VCol
                    cols="12"
                    sm="4"
                  >
                    <VImg 
                      :src="image"
                      :width="100"
                      :height="100"
                      cover
                      style="border-radius: 6px;"
                    />
                  </VCol>  
                  <VCol
                    cols="12"
                    sm="8"
                  >
                    <VRow>
                      <VCol cols="12">
                        <VBtn
                          class="me-3"
                          @click="handleUploadFile"
                        >
                          Unggah
                        </VBtn>
                        <VBtn
                          variant="tonal"
                          color="secondary"
                          @click="handleResetFile"
                        >
                          Reset
                        </VBtn>
                        <VFileInput
                          ref="fileInput"
                          label="File input"
                          class="d-none"
                          @change="handleFileChange"
                        />
                      </VCol>
                      <VCol
                        cols="12"
                        style="padding: 0 12px;"
                      >
                        Allowed JPG or PNG. Max size of 1MB
                      </VCol>
                    </VRow>
                  </VCol>
                </VRow>
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
                <VBtn
                  class="me-3"
                  @click="isDialogUpdateVisible = true"
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

  <VDialog
    v-model="isDialogUpdateVisible"
    persistent
    class="v-dialog-sm"
  >
    <!-- Dialog close btn -->
    <DialogCloseBtn @click="isDialogUpdateVisible = !isDialogUpdateVisible" />

    <!-- Dialog Content -->
    <VCard title="Simpan Dengan Perubahan">
      <VCardText>
        Apakah anda yakin ingin melakukan perubahan pada data tersebut?
      </VCardText>

      <VCardText class="d-flex justify-end gap-3 flex-wrap">
        <VBtn
          color="secondary"
          variant="tonal"
          @click="isDialogUpdateVisible = false"
        >
          Batal
        </VBtn>
        <VBtn @click="submitForm">
          Simpan
        </VBtn>
      </VCardText>
    </VCard>
  </VDialog>
</template>
