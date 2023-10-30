<script setup>
import axios from '@axios'

const user = ref(JSON.parse(localStorage.getItem('userData')))

//logout function post to api/auth/logout
const logout = () => {
  axios.post('/api/auth/logout')
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
</script>

<template>
  <VBadge
    dot
    location="bottom right"
    offset-x="3"
    offset-y="3"
    bordered
    color="success"
  >
    <VAvatar
      class="cursor-pointer"
      color="primary"
      variant="tonal"
    >
      <VImg :src="user.image" />

      <!-- SECTION Menu -->
      <VMenu
        activator="parent"
        width="230"
        location="bottom end"
        offset="14px"
      >
        <VList>
          <!-- 👉 User Avatar & Name -->
          <VListItem>
            <template #prepend>
              <VListItemAction start>
                <VBadge
                  dot
                  location="bottom right"
                  offset-x="3"
                  offset-y="3"
                  color="success"
                >
                  <VAvatar
                    color="primary"
                    variant="tonal"
                  >
                    <VImg :src="user.image" />
                  </VAvatar>
                </VBadge>
              </VListItemAction>
            </template>

            <VListItemTitle class="font-weight-semibold">
              {{ user.nama }}
            </VListItemTitle>
            <VListItemSubtitle>{{ user.role }}</VListItemSubtitle>
          </VListItem>

          <!-- Divider -->
          <VDivider class="my-2" />

          <!-- 👉 Logout -->
          <VListItem to="/login">
            <template #prepend>
              <VIcon
                class="me-2"
                icon="tabler-logout"
                size="22"
              />
            </template>

            <VListItemTitle @click="logout">
              Logout
            </VListItemTitle>
          </VListItem>
        </VList>
      </VMenu>
      <!-- !SECTION -->
    </VAvatar>
  </VBadge>
</template>
