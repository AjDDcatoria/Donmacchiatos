<script setup lang="ts">
import Button from 'primevue/button'
import Divider from 'primevue/divider'
import InputText from 'primevue/inputtext'
import { onMounted, watchEffect } from 'vue'
import BaseLayout from '@/components/partials/BaseLayout.vue'
import AppTitle from '@/components/ui/AppTitle.vue'
import TextError from '@/components/ui/TextError.vue'
import { useAuth } from '@/composable/useAuth'
import { useToast } from 'vue-toastification'
import { useUserStore } from '@/stores/userStore'
import { useRouter } from 'vue-router'

const toast = useToast()
const router = useRouter()
const userStore = useUserStore()
const {
  formData,
  otpSent,
  isLoading,
  message,
  errors,
  requestSendOTP,
  requestVerifyOTP,
} = useAuth()

onMounted(() => {
  // Retrieve saved email & verification_id in sessionStorage.
  const savedEmail = sessionStorage.getItem('otp_email')
  const savedVerificationId = sessionStorage.getItem('verification_id')

  // If an email & verification_id is found, set it in formData and mark OTP as sent.
  if (savedEmail && savedVerificationId) {
    formData.email = savedEmail
    formData.verification_id = savedVerificationId

    otpSent.value = true
  }
})

// Watch for changes in formData, errors, and message, and handle side effects accordingly.
watchEffect(() => {
  // Store email in session storage whenever it changes.
  if (formData.email) {
    sessionStorage.setItem('otp_email', formData.email)
  }

  // Display error messages in a toast and clear the errors ref afterward.
  if (errors.value) {
    toast.error(errors.value)
    errors.value = null
  }

  // Display informational messages in a toast and clear the message ref afterward.
  if (message.value) {
    toast.info(message.value)
    message.value = null
  }
})

/**
 * Handles the process of resending an OTP (One-Time Password) to the user.
 * This function updates the necessary states to initiate a new OTP request,
 * while ensuring the existing verification ID is preserved for reference.
 *
 * @async
 * @returns {Promise<void>}
 */
const handleResendOtp = async (): Promise<void> => {
  // Set `otpSent` otpSent to false to indicate that the OTP request is creating a new one
  otpSent.value = false

  // Attemp to send a new verification OTP and Set parameter to true to indicate resending OTP
  await requestSendOTP(true)
}

/**
 * Handles the process of sending or verifying an OTP (One-Time Password) for user authentication.
 * This function either initiates an OTP request or verifies an existing OTP based on the current state.
 *
 * @async
 * @returns {Promise<void>}
 */
const handleSendOtp = async (): Promise<void> => {
  // Check if an OTP has not been sent yet
  if (!otpSent.value) {
    // Request to send a new OTP
    const verification_id = await requestSendOTP()

    // If a verification ID is returned, store it in both session storage and form data for later reference
    if (verification_id) {
      sessionStorage.setItem('verification_id', verification_id)
      formData.verification_id = verification_id
    }
  } else {
    // If OTP has been sent, attempt to verify it
    const token = await requestVerifyOTP()

    // If a token is returned, store it, clear session storage, and navigate to the callback route
    if (token) {
      userStore.setToken(token)
      sessionStorage.clear()
      await router.push({ name: 'callback' })
    }
  }
}

// Remove all formdata and clear SessionStorage
const changeEmailOtp = (): void => {
  formData.email = ''
  formData.verification_code = ''
  formData.verification_id = ''

  sessionStorage.clear()
  otpSent.value = false
}
</script>

<template>
  <BaseLayout class="flex pt-[24%] sm:pt-[10%] justify-center">
    <div class="p-5 max-w-[410px] w-full flex flex-col">
      <!-- Title -->
      <div class="mb-10">
        <AppTitle label="Login to continue." />
      </div>

      <!-- Provider buttons -->
      <div class="flex gap-3">
        <Button
          label="Facebook"
          severity="secondary"
          class="w-1/2 !bg-white shadow !text-gray-800"
          :disabled="isLoading"
        />
        <Button
          label="Google"
          severity="secondary"
          class="w-1/2 !bg-white shadow !text-gray-800"
          :disabled="isLoading"
        />
      </div>

      <Divider align="center" type="solid" />

      <!-- OTP form -->
      <form class="flex flex-col gap-3" @submit.prevent="handleSendOtp">
        <!-- Email -->
        <div class="flex flex-col gap-2">
          <label for="email">Email</label>
          <InputText
            id="email"
            type="email"
            v-model="formData.email"
            :disabled="otpSent || isLoading"
            aria-describedby="username-help"
            placeholder="john@example.com"
            required
          />
        </div>

        <!-- Verification code -->
        <div
          v-if="otpSent"
          class="flex flex-col overflow-hidden gap-2 transition-all duration-500 ease-in-out"
        >
          <label for="verification">Verification code</label>
          <InputText
            id="verification"
            aria-describedby="username-help"
            placeholder="code"
            v-model="formData.verification_code"
            :disabled="isLoading"
            required
          />

          <!-- Verification error -->
          <div class="flex justify-between items-center">
            <div>
              <!-- Error -->
              <TextError v-if="false" :label="''" />
            </div>

            <!-- Change email button -->
            <Button
              label="Change email"
              @click="changeEmailOtp"
              class="p-0 h-6"
              :disabled="isLoading"
              type="button"
              link
            />
          </div>
        </div>

        <div>
          <!-- Submit otp button -->
          <Button
            :label="otpSent ? 'Verify' : 'Login with email'"
            type="submit"
            :disabled="isLoading"
            class="w-full"
            raised
          />
          <div>
            <!-- Resend verification button -->
            <Button
              v-if="otpSent"
              label="Resend"
              type="button"
              @click="handleResendOtp"
              :disabled="isLoading"
              class="p-0 h-6 float-right"
              link
            />
          </div>
        </div>
      </form>
    </div>
  </BaseLayout>
</template>
