<script setup>
import {
  emailValidator,
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
  admin: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits([
  'update:isDrawerOpen',
  'adminData',
])

const rules = [fileList => !fileList || !fileList.length || fileList[0].size < 1000000 || 'Foto Profil harus kurang dari 1 MB!']

// 👉 Utils
const isDialogUpdateVisible = ref(false)

// 👉 Form
const isFormValid = ref(false)
const isPasswordVisible = ref(false)
const refForm = ref()
const name = ref('')
const email = ref('')
const password = ref('')
const imageTrainer = ref(null)
const image = ref('https://api.dicebear.com/6.x/adventurer-neutral/svg?seed=Felix')

// 👉 drawer close
const closeNavigationDrawer = () => {
  emit('update:isDrawerOpen', false)
  nextTick(() => {
    refForm.value?.reset()
    refForm.value?.resetValidation()
  })
}

const generatePassword = () => {
  const length = 8
  const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
  let retVal = ''
  for (let i = 0, n = charset.length; i < length; ++i) {
    retVal += charset.charAt(Math.floor(Math.random() * n))
  }
  password.value = retVal
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
      form.append('name', name.value)
      form.append('email', email.value)
      form.append('password', password.value)
      form.append('image', imageTrainer.value)
      form.append('id', props.updateid)
      emit('adminData', form)
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
  image.value = props.admin.image

}

const handleFileChange = event => {
  const file = event.target.files[0]

  imageTrainer.value = file

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
  if(props.admin) {
    console.log(props.admin)
    name.value = props.admin.name
    email.value = props.admin.email
    image.value = props.admin.image
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
      title="Rubah Trainer"
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

              <!-- 👉 Nama Lengkap -->
              <VCol cols="12">
                <AppTextField
                  v-model="name"
                  :rules="[requiredValidator]"
                  label="Nama Lengkap"
                  placeholder="Masukan Nama Lengkap"
                />
              </VCol>

              <!-- 👉 Email -->
              <VCol cols="12">
                <AppTextField
                  v-model="email"
                  :rules="[requiredValidator, emailValidator]"
                  label="Alamat Email"
                  placeholder="Masukan alamat email"
                />
              </VCol>

              <!-- 👉 Password -->
              <VCol cols="12">
                <VLabel>Kata Sandi</VLabel>
                <VRow>
                  <VCol
                    cols="12"
                    sm="8"
                  >
                    <AppTextField
                      v-model="password"
                      :type="isPasswordVisible ? 'text' : 'password'"
                      :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                      placeholder="Masukan Kata Sandi"
                      autocomplete="off"
                      @click:append-inner="isPasswordVisible = !isPasswordVisible"
                    />
                  </VCol>
                  <VCol
                    cols="12"
                    sm="4"
                    class="text-end"
                  >
                    <VBtn
                      variant="tonal"
                      @click="generatePassword"
                    >
                      Generate
                    </VBtn>
                  </VCol>
                </VRow>
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
