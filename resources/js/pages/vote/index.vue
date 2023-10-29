<script setup>
import { useVoteListStore } from '@/ListStore/useVoteListStore'
import axios from '@axios'

const user = JSON.parse(localStorage.getItem('userData'))
const voteListStore = useVoteListStore()
const isDialogOpen = ref(false)
const textConfirmation = ref('')
const selectedCandidate = ref(null)
const isAlreadyVoted = ref(false)
const isDialogFinishOpen = ref(false)
const textLoop = ref('')

const kandidat = ref([])

//fetch data from api/vote
const fetchData = () => {
  voteListStore.fetchCadidate()
    .then(response => {
      kandidat.value = response.data.cadidate
      console.log(kandidat.value)
    })
    .catch(error => {
      console.log(error)
    })
}

watchEffect(fetchData)

const checkVoted = () => {
  voteListStore.checkIsAlreadyVoted()
    .then(response => {
      isAlreadyVoted.value = response.data.isVoted ?? false
    })
    .catch(error => {
      console.log(error)
    })
}

watchEffect(checkVoted)

const confirmVote = () => {
  voteListStore.addVote({
    cadidate_id: selectedCandidate.value.id,
  }).then(response => {
    console.log(response)
    isDialogOpen.value = false

    isDialogFinishOpen.value = true

    //create loop text anda akan diarahkan ke halaman login dalam 5 detik
    let i = 5

    const interval = setInterval(() => {
      textLoop.value = `Anda akan diarahkan ke halaman login dalam ${i} detik`
      i--
      if(i === 0) {
        clearInterval(interval)
        logout()
      }
    }, 1000)

    fetchData()
  }).catch(error => {
    console.log(error)
  })
}

const logout = () => {
  axios.post('api/auth/logout')
    .then(function (response) {
      const { data } = response

      localStorage.removeItem('accessToken')
      localStorage.removeItem('expiresIn')
      localStorage.removeItem('tokenType')
      localStorage.removeItem('userData')

      console.log("Local Storage Cleared")

      router.push({ name: 'login' })
    })
    .catch(function (error) {
      console.log(error)
    })
}

const pilihKandidate = kandidat => {
  selectedCandidate.value = kandidat
  textConfirmation.value = `Apakah anda yakin memilih ${kandidat.ketua} dan ${kandidat.wakil} sebagai ketua dan wakil ketua OSIS?`
  isDialogOpen.value = true
}
</script>

<template>
  <div class="pa-2 text-right">
    <p class="pa-0">
      Anda masuk sebagai 
      <span class="font-weight-bold text-uppercase">
        {{ user.nama }}
      </span> |
      <span
        class="links"
        @click="logout"
      >
        Klik disini untuk keluar
      </span>
    </p>
  </div>
  <div class="misc-wrapper pt-0 mt-0 justify-start">
    <div>
      <!-- 👉 Title and subtitle -->
      <VImg
        src="/storage/logo/voting.png"
        alt="image voting"
        width="100"
        height="100"
        style="float: left;"
        class=""
      />
      <VImg
        src="/storage/logo/osis.png"
        alt="image osis"
        width="100"
        height="100"
        style="float: right;"
        class=""
      />
      <div class="text-center mb-4 mx-10">
        <h4 class="text-h3 font-weight-medium mb-1">
          Tentukan Pilihan Anda
        </h4>
        <p>Selamat datang {{ user.nama }} silahkan memilih ketua OSIS dari kandidat berikut</p>
      </div>
    </div>

    <!-- 👉 Image -->
    <div
      class="misc-avatar w-100 mt-10"
      style="padding-left: 10rem; padding-right: 10rem;"
    >
      <VRow
        align-content="center"
        justify="center"
      >
        <VCol
          v-for="candidate in kandidat"
          :key="candidate"
          cols="12"
          md="6"
          lg="4"
        >
          <VCard class="text-center">
            <VImg
              :src="candidate.image"
              width="100%"
              style="max-height: 20rem;"
            />
            <VCardText>
              <h3 class="font-weight-bold text-h4">
                {{ `${candidate.ketua} dan ${candidate.wakil}` }}
              </h3>
              <VDivider />
              <p class="pt-2">
                {{ candidate.description }}
              </p>
            </VCardText>

            <VCardText class="justify-center">
              <VBtn
                variant="elevated"
                :disabled="isAlreadyVoted"
                @click="pilihKandidate(candidate)"
              >
                <span v-if="isAlreadyVoted">Anda Telah Memilih</span>
                <span v-else>Pilih Kandidat</span>
              </VBtn>
            </VCardText>
          </VCard>
        </VCol>
      </VRow>
    </div>

    <VDialog
      :model-value="isDialogOpen"
      width="450"
      persistent
    >
      <!-- Dialog close btn -->
      <DialogCloseBtn @click="isDialogOpen = !isDialogOpen" />

      <VCard class="pa-5 pa-sm-8">
        <!-- 👉 Title -->
        <VCardItem class="text-center">
          <VCardTitle class="text-h5 font-weight-medium mb-3">
            Konfirmasi Pilihan!
          </VCardTitle>
          <p class="mb-0">
            {{ textConfirmation }}
          </p>
        </VCardItem>

        <VCardText class="pt-6">
          <VRow>
            <VCol
              cols="12"
              class="text-center"
            >
              <VBtn
                class="me-3"
                @click="confirmVote"
              >
                Yakin
              </VBtn>
              <VBtn
                color="secondary"
                variant="tonal"
                @click="isDialogOpen = !isDialogOpen"
              >
                kembali
              </VBtn>
            </VCol>
          </VRow>
        </VCardText>
      </VCard>
    </VDialog>

    <VDialog
      :model-value="isDialogFinishOpen"
      width="450"
      persistent
    >
      <VCard class="pa-5 pa-sm-8">
        <!-- 👉 Title -->
        <VCardItem class="text-center">
          <VCardTitle class="text-h5 font-weight-medium mb-3">
            Terima Kasih
          </VCardTitle>
          <p class="mb-0">
            {{ textLoop }}
          </p>
        </VCardItem>
      </VCard>
    </VDialog>
  </div>
</template>

<style lang="scss">
@use "@core-scss/template/pages/misc.scss";

.links {
  cursor: pointer;
  color: blue
}

.misc-email-input {
  margin-inline: auto;
  max-inline-size: 21.875rem;
  min-inline-size: 12.5rem;
}
</style>

<route lang="yaml">
meta:
  layout: blank
</route>
