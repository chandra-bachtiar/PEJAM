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
  'userData',
])


const rules = [fileList => !fileList || !fileList.length || fileList[0].size < 1000000 || 'Foto Profil harus kurang dari 1 MB!']

const isFormValid = ref(false)
const isPasswordVisible = ref(false)
const refForm = ref()
const nis = ref('')
const username = ref('')
const name = ref('')
const sex = ref('L')
const major = ref('')
const classs = ref('')
const role = ref('')
const status = ref('')
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

const handleFileChange = event => {
  const file = event.target.files[0]

  ProfileImage.value = file
  imageUser.value = file
}

const onSubmit = () => {
  refForm.value?.validate().then(({ valid }) => {
    if (valid) {
      const form = new FormData()

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
      emit('userData', form)
      emit('update:isDrawerOpen', false)
      nextTick(() => {
        refForm.value?.reset()
        refForm.value?.resetValidation()
      })
    }
  })
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
const classsList = ['X', 'XI', 'XII']

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
                <AppSelect
                  v-model="classs"
                  :items="classsList"
                  placehodler="Pilih Kelas"
                  label="Pilih Kelas"
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

              <!-- 👉 Password -->
              <VCol cols="12">
                <AppTextField
                  v-model="password"
                  :rules="[requiredValidator]"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  placeholder="Masukan Kata Sandi"
                  autocomplete="off"
                  label="Kata Sandi"
                  @click:append-inner="isPasswordVisible = !isPasswordVisible"
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
