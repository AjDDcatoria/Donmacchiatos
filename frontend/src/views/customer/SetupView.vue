<script setup lang="ts">
import validate from '@/lib/validators';
import { userDetails } from '@/lib/validators/rules';
import { DefaultDetails, DefaultDetailsErrors } from '@/types/defaults';
import type { DetailsErrors, UserDetailsTypes } from '@/types/usertypes';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import { reactive, ref, unref } from 'vue';
import { setupUser } from '@/services/api/userService';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import BaseLayout from '@/components/partials/BaseLayout.vue';
import TextError from '@/components/ui/TextError.vue';
import AppTitle from '@/components/ui/AppTitle.vue';

const router = useRouter();
const toast = useToast();
const isLoading = ref<boolean>(false);
const formData = reactive<UserDetailsTypes>(DefaultDetails);
const validated = validate(formData, userDetails);
const detailsErrors = reactive<DetailsErrors>(DefaultDetailsErrors);

/*
 * Handles errors and setup account to user.
 */
const handleSetup = async () => {
  // Touch all form fields to apply validation rules
  validated.value.$touch();

  // Check if the form is invalid
  if (validated.value.$invalid) {
    // If invalid, capture the first validation error
    detailsErrors.key = validated.value.$errors[0].$propertyPath;
    detailsErrors.message = unref(validated.value.$errors[0].$message);
  } else {
    // Attempt to set up the user with the provided form data
    const { error } = await setupUser(formData);

    // Handle error case
    if (error) {
      toast.error('Something went wrong please try again!');
    } else {
      await router.push({ name: 'callback' });
    }
  }
}
</script>

<template>
  <BaseLayout class="h-dvh flex items-center justify-center">
    <form
      @submit.prevent="handleSetup"
      class="flex flex-col gap-4 max-w-[460px] w-full p-4"
    >
      <div>
        <!-- Title -->
        <div class="mb-2">
          <AppTitle label="Setup account." />
        </div>

        <!-- Display error firstname, lastname -->
        <div class="h-6 text-red-500 font-semibold">
          <TextError
            v-if="
              detailsErrors.key == 'firstname' ||
              detailsErrors.key == 'lastname'
            "
            :label="detailsErrors.message"
          />
        </div>

        <div class="flex gap-2 flex-wrap sm:flex-nowrap">
          <!-- Given name -->
          <div class="flex flex-col gap-2 w-full sm:w-1/2">
            <label for="given_name"> Given name </label>
            <InputText
              id="given_name"
              aria-describedby="username-help"
              placeholder="John"
              v-model="formData.firstname"
              :disabled="isLoading"
              required
            />
          </div>

          <!-- Family name -->
          <div class="flex flex-col gap-2 w-full sm:w-1/2">
            <label for="family_name">Family name</label>
            <InputText
              id="family_name"
              aria-describedby="username-help"
              placeholder="Doe"
              v-model="formData.lastname"
              :disabled="isLoading"
              required
            />
          </div>
        </div>
      </div>

      <!-- Contact number -->
      <div class="flex flex-col gap-3">
        <div class="flex flex-col gap-2">
          <label for="contact_number">
            Contact number

            <!-- Contact number error -->
            <TextError
              v-if="detailsErrors.key == 'contact_number'"
              :label="detailsErrors.message"
            />
          </label>
          <InputText
            id="contact_number"
            aria-describedby="username-help"
            placeholder="09123456789"
            v-model="formData.contact_number"
            :disabled="isLoading"
            required
          />
        </div>

        <!-- Address -->
        <div class="flex flex-col gap-2">
          <label for="address">
            Address

            <!-- Address error -->
            <TextError
              v-if="detailsErrors.key == 'address'"
              :label="detailsErrors?.message"
            />
          </label>
          <InputText
            id="address"
            aria-describedby="username-help"
            placeholder="Tandag city"
            v-model="formData.address"
            :disabled="isLoading"
            required
          />
        </div>
      </div>

      <!-- Submit button -->
      <Button
        label="Submit"
        type="submit"
        severity="primary"
        :disabled="isLoading"
        class="mt-4"
        raised
      />
    </form>
  </BaseLayout>
</template>
