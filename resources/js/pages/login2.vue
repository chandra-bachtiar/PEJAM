<script setup>
import axios from '@axios'
import { useGenerateImageVariant } from '@core/composable/useGenerateImageVariant'
import { default as authV2LoginIllustrationBorderedDark, default as authV2LoginIllustrationBorderedLight, default as authV2LoginIllustrationDark, default as authV2LoginIllustrationLight } from '@images/illustrations/login.png'
import authV2MaskDark from '@images/pages/misc-mask-dark.png'
import authV2MaskLight from '@images/pages/misc-mask-light.png'
import { themeConfig } from '@themeConfig'
import { VForm } from 'vuetify/components/VForm'

const authThemeImg = useGenerateImageVariant(authV2LoginIllustrationLight, authV2LoginIllustrationDark, authV2LoginIllustrationBorderedLight, authV2LoginIllustrationBorderedDark, true)
const authThemeMask = useGenerateImageVariant(authV2MaskLight, authV2MaskDark)
const isPasswordVisible = ref(false)
const isLoginButtonDisabled = ref(false)

const isError = ref({
  status: false,
  message: '',
})

import router from '@/router'
import { emailValidator, requiredValidator } from '@validators'

const form = reactive({
  email: '',
  password: '',
  rememberMe: false,
})

const errors = ref({
  email: undefined,
  password: undefined,
})

const login = () => {
  isLoginButtonDisabled.value = true
  axios.post('api/auth/login', form)
    .then(function (response) {
      const { data } = response

      isError.value = {
        status: false,
        message: '',
      }

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
      console.log(error)

      const { data } = error.response
      const code = error.response.status

      isLoginButtonDisabled.value = false

      if(code === 401) {
        isError.value = {
          status: true,
          message: data.errors.message,
        }
      }
    })
}

async function handleLogin() {
  //login with axios
  try {
    const response = await axios.post('/api/login', form)

    console.log(response)
  } catch (error) {
    console.log(error)
  }
}

watch(isError, () => {
  console.log(isError.value)
})

document.title = `Login - ${themeConfig.app.title}`
</script>

<template>
  <VRow
    no-gutters
    class="auth-wrapper bg-surface"
  >
    <VCol
      lg="8"
      class="d-none d-lg-flex"
    >
      <div class="position-relative bg-background rounded-lg w-100 ma-8 me-0">
        <div class="d-flex align-center justify-center w-100 h-100">
          <VImg
            max-width="505"
            :src="authThemeImg"
            class="auth-illustration mt-16 mb-2"
          />
        </div>
      </div>
    </VCol>

    <VCol
      cols="12"
      lg="4"
      class="auth-card-v2 d-flex align-center justify-center"
    >
      <VCard
        flat
        :max-width="500"
        class="mt-12 mt-sm-0 pa-4"
      >
        <VCardText>
          <h5 class="text-h5 mb-1">
            Selamat datang di <span class="text-capitalize"> {{ themeConfig.app.title }} </span>! 👋🏻
          </h5>

          <p class="mb-0">
            Silahkan Login untuk mulai belajar 📖
          </p>
        </VCardText>
        <VCardText>
          <VAlert
            v-if="isError.status"
            variant="tonal"
            color="error"
          >
            {{ isError.message }}
          </VAlert>
        </VCardText>
        <VCardText>
          <VForm
            ref="refVForm"
            @submit.prevent="login"
          >
            <VRow>
              <!-- email -->
              <VCol cols="12">
                <AppTextField
                  v-model="form.email"
                  label="Email"
                  type="email"
                  autofocus
                  :rules="[requiredValidator, emailValidator]"
                  :error-messages="errors.email"
                  placeholder="Masukan Email"
                />
              </VCol>

              <!-- password -->
              <VCol cols="12">
                <AppTextField
                  v-model="form.password"
                  label="Kata Sandi"
                  :rules="[requiredValidator]"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  :error-messages="errors.password"
                  :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  placeholder="Masukan Kata Sandi"
                  @click:append-inner="isPasswordVisible = !isPasswordVisible"
                />

                <div class="d-flex align-center flex-wrap justify-space-between mt-2 mb-4">
                  <VCheckbox
                    v-model="form.rememberMe"
                    label="Ingat Saya"
                  />
                  <a
                    class="text-primary ms-2 mb-1"
                    href="#"
                  >
                    Lupa Password?
                  </a>
                </div>

                <VBtn
                  :loading="isLoginButtonDisabled"
                  :disabled="isLoginButtonDisabled"
                  block
                  type="submit"
                >
                  Login
                </VBtn>
              </VCol>

              <!-- create account -->
              <VCol
                cols="12"
                class="text-center"
              >
                <span>Belum Punya Akun?</span>

                <a
                  class="text-primary ms-2"
                  href="#"
                >
                  Buat Akun
                </a>
              </VCol>

              <VCol
                cols="12"
                class="d-flex align-center"
              />

              <!-- auth providers -->
              <VCol
                cols="12"
                class="text-center"
              />
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth.scss";
</style>

<route lang="yaml">
meta:
  layout: blank
</route>
