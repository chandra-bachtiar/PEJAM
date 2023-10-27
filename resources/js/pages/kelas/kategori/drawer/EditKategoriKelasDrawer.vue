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
  updateid: {
    type: Number,
    required: true,
  },
  kategoriKelas: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits([
  'update:isDrawerOpen',
  'kategoriKelasData',
])

// 👉 Utils
const isDialogUpdateVisible = ref(false)

// 👉 Form
const isFormValid = ref(false)
const isPasswordVisible = ref(false)
const refForm = ref()
const kategori = ref('')

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

const handlePhoneNumber = event => {
  const value = event.target.value

  phoneNumber.value = value.replace(/\D/g, '')
}

const onSubmit = () => {
  refForm.value?.validate().then(({ valid }) => {
    if (valid) {
      const form = new FormData()

      form.append('_method', 'PUT')
      form.append('kategori', kategori.value)
      form.append('id', props.updateid)
      emit('kategoriKelasData', form)
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
  if(props.kategoriKelas) {
    kategori.value = props.kategoriKelas.kategori
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
      title="Rubah Kategori Kelas"
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
              <!-- 👉 Kategori Kelas -->
              <VCol cols="12">
                <AppTextField
                  v-model="kategori"
                  :rules="[requiredValidator]"
                  label="Kategori Kelas"
                  placeholder="Masukan Kategori Kelas"
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
