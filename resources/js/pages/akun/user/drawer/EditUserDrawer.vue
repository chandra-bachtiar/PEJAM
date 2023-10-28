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
  user: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits([
  'update:isDrawerOpen',
  'userData',
])

const rules = [fileList => !fileList || !fileList.length || fileList[0].size < 1000000 || 'Foto Profil harus kurang dari 1 MB!']

// 👉 Utils
const isDialogUpdateVisible = ref(false)

// 👉 Form
const isFormValid = ref(false)
const isPasswordVisible = ref(false)
const refForm = ref()
const nis = ref(props.user.nis)
const username = ref(props.user.username)
const name = ref(props.user.nama)
const sex = ref(props.user.jenis_kelamin)
const major = ref(props.user.jurusan)
const classs = ref(props.user.kelas)
const role = ref(props.user.role)
const status = ref(props.user.status)
const password = ref('')
const image = ref(null)
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


const submitForm = () => {
  isDialogUpdateVisible.value = false

  onSubmit()
}

const handlePhoneNumber = event => {
  const value = event.target.value

  phoneNumber.value = value.replace(/\D/g, '')
}

const onSubmit = () => {
  refForm.value?.validate().then(({ valid }) => {
    if (valid) {
      const form = new FormData()

      form.append('_method', 'PUT')
      form.append('id', props.updateid)
      form.append('nis', nis.value)
      form.append('username', username.value)
      form.append('nama', name.value)
      form.append('jenis_kelamin', sex.value)
      form.append('jurusan', major.value)
      form.append('kelas', classs.value)
      form.append('role', role.value)
      form.append('status', status.value)
      form.append('password', password.value)
      form.append('image', imageUser.value)
      form.append('is_active', true)
      emit('userData', form)
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
  image.value = props.user.image
}

const handleFileChange = event => {
  const file = event.target.files[0]

  imageUser.value = file

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

const sexList = [
  {
    title: 'Laki-laki',
    value: 'L',
  },
  {
    title: 'Perempuan',
    value: 'P',
  },
]

const roleList = ['Admin', 'User']
const statusList = ['Murid', 'Guru', 'Warga Sekolah']

watchEffect(() => {
  if(props.user) {
    console.log(props.user)
    nis.value = props.user.nis
    username.value = props.user.username
    name.value = props.user.nama
    sex.value = props.user.jenis_kelamin
    major.value = props.user.jurusan
    classs.value = props.user.kelas
    role.value = props.user.role
    status.value = props.user.status
    image.value = props.user.image
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
      title="Rubah User"
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

              <!-- 👉 NIS -->
              <VCol cols="12">
                <AppTextField
                  v-model="nis"
                  :rules="[requiredValidator]"
                  label="Nomor Induk Siswa"
                  placeholder="Masukan Nomor Induk Siswa"
                />
              </VCol>

              <!-- 👉 username -->
              <VCol cols="12">
                <AppTextField
                  v-model="username"
                  :rules="[requiredValidator]"
                  label="Username"
                  placeholder="Masukan Username"
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

              <!-- Jenis Kelamin -->
              <VCol cols="12">
                <AppSelect
                  v-model="sex"
                  :items="sexList"
                  item-title="title"
                  item-value="value"
                  placehodler="Pilih Jenis Kelamin"
                  label="Jenis Kelamin"
                />
              </VCol>

              <!-- Jurusan -->
              <VCol cols="12">
                <AppTextField
                  v-model="major"
                  :rules="[requiredValidator]"
                  label="Jurusan"
                  placeholder="Masukan Nama Jurusan"
                />
              </VCol>

              <!-- Kelas -->
              <VCol cols="12">
                <AppTextField
                  v-model="classs"
                  :rules="[requiredValidator]"
                  label="Kelas"
                  placeholder="Masukan Kelas"
                />
              </VCol>

              <!-- Jenis Role -->
              <VCol cols="12">
                <AppSelect
                  v-model="role"
                  :items="roleList"
                  placehodler="Pilih Jenis Role"
                  label="Pilih Role"
                />
              </VCol>

              <!-- Jenis Status -->
              <VCol cols="12">
                <AppSelect
                  v-model="status"
                  :items="statusList"
                  placehodler="Pilih Jenis Status"
                  label="Pilih Status"
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
