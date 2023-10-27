<script setup>
import { useMateriListStore } from '@/ListStore/useMateriListStore'
import router from '@/router'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'
import BlotFormatter from 'quill-blot-formatter'

const props = defineProps({
  id: {
    type: String,
    required: true,
  },
})

const materiListStore = useMateriListStore()
const materi = ref({})
const content = ref('')

const fetchMateri = () => {
  materiListStore.fetchMateriById('', props.id)
    .then(res => {
      materi.value = res.data.materi
      content.value = res.data.materi.konten_teks
    }).catch(error => {
      console.error(error)
    })
}

watchEffect(fetchMateri)

const updateContent = () => {
  const data = {
    id: materi.value.id,
    konten_teks: content.value,
  }

  materiListStore.updateMateriText(data)
    .then(res => {
      console.log(res)
      localStorage.setItem('isSaved', 'true')
      router.push( {
        path: `/kelas/materi/${materi.value.kelas_id}`,
      })
    }).catch(error => {
      console.error(error)
    })
}

const onTextChange = e => {
  console.log(content.value)
}

const modules = {
  name: 'blotFormatter',
  module: BlotFormatter,
  options: {},
}
</script>

<template>
  <div>
    <VCard :title="materi?.judul">
      <VCardText>
        <QuillEditor
          v-model:content="content"
          theme="snow"
          toolbar="full"
          content-type="html"
          :modules="modules"
          style="min-height: 300px;"
          @text-change="onTextChange"
        />
      </VCardText>
      <VCardText>
        <VRow>
          <VCol
            cols="12"
            class="text-right"
          >
            <VBtn
              variant="tonal"
              class="me-2"
              color="secondary"
              @click="router.push({path: `/kelas/materi/${materi.kelas_id}`})"
            >
              Batal
            </VBtn>
            <VBtn
              variant="tonal"
              @click="updateContent"
            >
              Simpan
            </VBtn>
          </VCol>
        </VRow>
      </VCardText>
    </VCard>
  </div>
</template>
