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
})

const emit = defineEmits([
  'update:isDrawerOpen',
  'userData',
])


const rules = [fileList => !fileList || !fileList.length || fileList[0].size < 1000000 || 'Foto Profil harus kurang dari 1 MB!']

const isFormValid = ref(false)
const isPasswordVisible = ref(false)
const refForm = ref()
const name = ref('')
const email = ref('')
const phoneNumber = ref('')
const password = ref('')
const ProfileImage = ref(null)
const imageUser = ref(null)


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

const handleFileChange = event => {
  const file = event.target.files[0]

  ProfileImage.value = file
  imageUser.value = file
}

const handlePhoneNumber = event => {
  const value = event.target.value

  phoneNumber.value = value.replace(/\D/g, '')
}

const onSubmit = () => {
  refForm.value?.validate().then(({ valid }) => {
    if (valid) {
      const form = new FormData()

      form.append('name', name.value)
      form.append('email', email.value)
      form.append('password', password.value)
      form.append('phone_number', phoneNumber.value)
      form.append('image', imageUser.value)
      emit('userData', form)
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

onMounted(() => {
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
      title="Tambah User"
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
                  ref="ProfileImage"
                  :rules="rules"
                  label="Foto Profil"
                  accept="image/png, image/jpeg, image/bmp"
                  placeholder="Foto Profil"
                  prepend-icon="tabler-camera"
                  @change="handleFileChange"
                />
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

              <!-- 👉 Phone Number -->
              <VCol cols="12">
                <AppTextField
                  v-model="phoneNumber"
                  :rules="[requiredValidator]"
                  label="Nomor Handphone"
                  placeholder="Masukan nomor handphone"
                  @keyup="handlePhoneNumber"
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
                      :rules="[requiredValidator]"
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
