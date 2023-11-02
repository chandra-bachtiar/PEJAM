<script setup>
import axios from '@axios'

const voteData = ref([])
const totalVote = ref(0)

//set interval everty 15 seconds to hit API /publicvote 
const fetchData = () => {
  axios.get('/api/publicvote')
    .then(response => {
      voteData.value = response.data.vote
      totalVote.value = voteData.value.reduce((acc, curr) => acc + curr.votes_count, 0)
    })
    .catch(error => {
      console.log(error)
    })
}

fetchData()

const login = () => {
  window.location.href = '/login'
}
</script>

<template>
  <div class="pa-2 text-right">
    <p class="pa-0">
      Anda belum masuk
      <span
        class="links"
        @click="login"
      >
        Klik disini untuk masuk
      </span>
    </p>
  </div>
  <div class="misc-wrapper pt-0 mt-0">
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
          Hasil Pemilihan Sementara
        </h4>
        <p>Selamat datang di PEJAM (Pemilihan Jamblang) berikut hasil pemilihan OSIS sementara :</p>
        <h3>Total Suara Masuk : {{ totalVote }} Suara</h3>
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
          v-for="vote in voteData"
          :key="vote"
          cols="12"
          md="6"
          lg="4"
        >
          <VCard class="text-left h-100">
            <VImg
              :src="vote.image"
              width="100%"
              style="max-height: 20rem;"
            />
            <div class="d-flex flex-column justify-space-between">
              <VCardText class="flex-grow-1">
                <h3 class="font-weight-bold text-h4">
                  {{ `${vote.ketua} dan ${vote.wakil}` }}
                </h3>
                <VDivider />
                <div
                  class="html-renderer"
                  v-html="vote.description"
                />
              </VCardText>
    
              <VCardText class="justify-center">
                <h2 class="text-h1 font-weight-bold">
                  {{ parseInt(vote.percentage) }} %
                </h2>
                <h4>
                  {{ vote.votes_count }} Suara
                </h4>
              </VCardText>
            </div>
          </VCard>
        </VCol>
      </VRow>
    </div>
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
.html-renderer {
  p {
    margin-block: 0;
  }
}
</style>

<route lang="yaml">
meta:
  layout: blank
</route>
