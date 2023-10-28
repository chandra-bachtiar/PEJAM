<script setup>
import router from '@/router'
import axios from '@axios'
import { themeConfig } from "@themeConfig"
import { VForm } from 'vuetify/components/VForm'

document.title = `Login - ${themeConfig.app.title}`

import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

const form = reactive({
  username: '',
  password: '',
})


const isPasswordVisible = ref(false)
const isLoginButtonDisabled = ref(false)

const login = () => {
  isLoginButtonDisabled.value = true
  axios.post('api/auth/login', form)
    .then(function (response) {
      const { data } = response

      console.log(data)
      localStorage.setItem('accessToken', data.access_token)
      localStorage.setItem('expiresIn', data.expires_in)
      localStorage.setItem('tokenType', data.token_type)
      localStorage.setItem('userData', JSON.stringify(data.user))
      
      if(data.user.role === 'Admin') {
        router.push({ name: 'dashboard' })

      } else if(data.user.role === 'User') {
        router.push({ name: 'vote' })        
      }
    })
    .catch(function (error) {
      const data = error?.response?.data
      const code = error?.response?.status

      if(code === 401) {
        toast.error(data?.errors.message)
      }

      isLoginButtonDisabled.value = false
    })
}
</script>

<template>
  <div class="auth-wrapper d-flex align-center justify-center pa-4">
    <div class="position-relative my-sm-16">
      <!-- 👉 Auth Card -->
      <VCard
        class="auth-card pa-4"
        max-width="448"
      >
        <VCardItem class="justify-center">
          <div>
            <div class="d-flex">
              <VImg
                src="/storage/logo/voting.png"
                alt="image voting"
                width="70"
                height="70"
              />
            </div>
            <VCardTitle class="font-weight-bold text-capitalize text-h5 py-1 text-center">
              {{ themeConfig.app.title }}
            </VCardTitle>
            <span>Pemilihan Jamblang</span>
          </div>
        </VCardItem>

        <VCardText class="pt-1">
          <h5 class="text-h5 mb-1">
            Selamat datang di
            <span class="text-capitalize">{{ themeConfig.app.title }}</span>! 👋🏻
          </h5>
          <p class="mb-0">
            silahkan masuk untuk mulai menggunakan aplikasi
          </p>
        </VCardText>

        <VCardText>
          <VForm @submit.prevent="login">
            <VRow>
              <!-- email -->
              <VCol cols="12">
                <AppTextField
                  v-model="form.username"
                  autofocus
                  label="Username"
                  type="text"
                />
              </VCol>

              <!-- password -->
              <VCol cols="12">
                <AppTextField
                  v-model="form.password"
                  class="mb-4"
                  label="Password"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="
                    isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'
                  "
                  @click:append-inner="isPasswordVisible = !isPasswordVisible"
                />

                <!-- login button -->
                <VBtn
                  block
                  type="submit"
                >
                  Login
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </div>
  </div>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth.scss";
</style>

<route lang="yaml">
meta:
  layout: blank
</route>
