<script setup>
import { paginationMeta } from '@/@fake-db/utils'
import { useVoteListStore } from '@/ListStore/useVoteListStore'
import { avatarText } from '@core/utils/formatters'
import { themeConfig } from '@themeConfig'
import moment from 'moment'
import { VDataTableServer } from 'vuetify/labs/VDataTable'

document.title = `History - ${themeConfig.app.title}`

const voteListStore = useVoteListStore()
const searchQuery = ref('')
const totalPage = ref(1)
const totalVotes = ref(0)
const votes = ref([])

const options = ref({
  page: 1,
  itemsPerPage: 10,
  sortBy: [],
  groupBy: [],
  search: undefined,
})

// Headers
const headers = [
  {
    title: 'NAMA',
    key: 'nama',
  },
  {
    title: 'KELAS/JURUSAN',
    key: 'kelas',
  },
  {
    title: 'DIPILIH',
    key: 'dipilih',
  },
  {
    title: 'STATUS',
    key: 'status',
  },
  {
    title: 'WAKTU',
    key: 'waktu',
  },
]

// 👉 Fetching votes
const fetchVotes = () => {
  voteListStore.fetchVotes({
    search: searchQuery.value,
    page: options.value.page,
    options: options.value,
  }).then(response => {
    votes.value = response?.data?.vote.data
    totalPage.value = response?.data?.vote.last_page
    totalVotes.value = response?.data?.vote.total
  }).catch(error => {
    console.error(error)
  })
}

const exportData = isEncrypted => {
  url = isEncrypted ? '/api/vote/export?encrypt=true' : '/api/vote/export'
  window.open(url, '_blank')
}

watchEffect(fetchVotes)
</script>

<template>
  <section>
    <VRow>
      <VCol cols="12">
        <VCard>
          <!-- 👉 Filters -->
          <VCardText>
            <VRow>
              <!-- 👉 Search Vote -->
              <VCol
                cols="12"
                sm="4"
              >
                <AppTextField
                  v-model="searchQuery"
                  placeholder="Search"
                  density="compact"
                />
              </VCol>
              <VSpacer />
              <VCol
                cols="12"
                sm="4"
                class="text-right"
              >
                <!-- 👉 Add cadidate button -->
                <VBtn
                  prepend-icon="tabler-file-export"
                  @click="exportData(false)"
                >
                  Export
                </VBtn>
                <VBtn
                  prepend-icon="tabler-file-export"
                  class="ms-3"
                  @click="exportData(true)"
                >
                  Export Encrypted
                </VBtn>
              </VCol>
            </VRow>
          </VCardText>

          <VDivider />

          <!-- SECTION datatable -->
          <VDataTableServer
            v-model:items-per-page="options.itemsPerPage"
            v-model:page="options.page"
            :items="votes"
            :items-length="totalVotes"
            :headers="headers"
            class="text-no-wrap"
            @update:options="options = $event"
          >
            <!-- Vote -->
            <template #item.nama="{ item }">
              <div class="d-flex align-center">
                <VAvatar
                  size="34"
                  :variant="!item.raw.user.avatar ? 'tonal' : undefined"
                  class="me-3"
                >
                  <VImg
                    v-if="item.raw.user.image"
                    :src="item.raw.user.image"
                  />
                  <span v-else>{{ avatarText(item.raw.user.name) }}</span>
                </VAvatar>

                <div class="d-flex flex-column">
                  <h6 class="text-base text-capitalize">
                    {{ item.raw.user.nama }}
                  </h6>

                  <span class="text-sm text-medium-emphasis">{{ item.raw.user.username }}</span>
                </div>
              </div>
            </template>

            <!-- 👉 Kelas -->
            <template #item.kelas="{ item }">
              <div class="d-flex align-center">
                <div class="d-flex flex-column">
                  <h6 class="text-base">
                    {{ item.raw.user.kelas }} - {{ item.raw.user.jurusan }}
                  </h6>
                </div>
              </div>
            </template>

            <!-- 👉 Kelas -->
            <template #item.dipilih="{ item }">
              <div class="d-flex align-center">
                <div class="d-flex flex-column">
                  <h6 class="text-base">
                    {{ item.raw.cadidate.ketua }} - {{ item.raw.cadidate.wakil }}
                  </h6>
                </div>
              </div>
            </template>

            <!-- 👉 Kelas -->
            <template #item.status="{ item }">
              <div class="d-flex align-center">
                <div class="d-flex flex-column">
                  <h6 class="text-base">
                    {{ item.raw.user.status }}
                  </h6>
                </div>
              </div>
            </template>

            <!-- 👉 Role -->
            <template #item.waktu="{ item }">
              <div class="d-flex align-center">
                <div class="d-flex flex-column">
                  <h6 class="text-base">
                    {{ moment(item.raw.created_at).format('DD MMMM YYYY HH:mm') }}
                  </h6>
                </div>
              </div>
            </template>

            <!-- pagination -->
            <template #bottom>
              <VDivider />
              <div class="d-flex align-center justify-sm-space-between justify-center flex-wrap gap-3 pa-5 pt-3">
                <p class="text-sm text-disabled mb-0">
                  {{ paginationMeta(options, totalVotes) }}
                </p>

                <VPagination
                  v-model="options.page"
                  :length="Math.ceil(totalVotes / options.itemsPerPage)"
                  :total-visible="$vuetify.display.xs ? 1 : 6"
                >
                  <template #prev="slotProps">
                    <VBtn
                      variant="tonal"
                      color="default"
                      v-bind="slotProps"
                      :icon="false"
                    >
                      Previous
                    </VBtn>
                  </template>

                  <template #next="slotProps">
                    <VBtn
                      variant="tonal"
                      color="default"
                      v-bind="slotProps"
                      :icon="false"
                    >
                      Next
                    </VBtn>
                  </template>
                </VPagination>
              </div>
            </template>
          </VDataTableServer>
        </VCard>
      </vcol>
    </vrow>
  </section>
</template>

<style lang="scss">
.app-vote-search-filter {
  inline-size: 31.6rem;
}

.text-capitalize {
  text-transform: capitalize;
}

.vote-list-name:not(:hover) {
  color: rgba(var(--v-theme-on-background), var(--v-medium-emphasis-opacity));
}
</style>
