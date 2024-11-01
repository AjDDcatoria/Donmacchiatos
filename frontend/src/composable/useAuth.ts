import { sendOTP, verifyOTP } from '@/services/api/otpService'
import { DefaultVerification } from '@/types/defaults'
import type { VerificationTypes } from '@/types/inputs'
import { AxiosError } from 'axios'
import { reactive, ref, type Ref } from 'vue'

type UseAuthTypes = {
  otpSent: Ref<boolean>
  isLoading: Ref<boolean>
  formData: VerificationTypes
  message: Ref<string | null>
  errors: Ref<string | null>
  requestSendOTP: (resend?: boolean) => Promise<string | undefined>
  requestVerifyOTP: () => Promise<string | undefined>
}

/**
 * `useAuth` handling OTP (One-Time Password) verification in a user authentication process.
 *
 * @returns {UseAuthTypes} An object containing:
 * - `otpSent` {Ref<boolean>}: Tracks whether an OTP has been successfully sent.
 * - `isLoading` {Ref<boolean>}: Indicates if an OTP request is currently in progress.
 * - `formData` {VerificationTypes}: Holds the user input data required for OTP verification.
 * - `message` {Ref<string | null>}: Stores any success messages returned from the OTP requests.
 * - `errors` {Ref<string | null>}: Stores error messages, if any, from the OTP requests.
 * - `requestSendOTP` {(resend?: boolean) => Promise<string | undefined>}: Sends an OTP request, either creating a new OTP or resending an existing one.
 * - `requestVerifyOTP` {() => Promise<string | undefined>}: Verifies the OTP based on the user’s input data.
 */
export function useAuth(): UseAuthTypes {
  const otpSent = ref<boolean>(false)
  const isLoading = ref<boolean>(false)
  const message = ref<string | null>(null)
  const errors = ref<string | null>(null)
  const formData = reactive<VerificationTypes>({ ...DefaultVerification })

  /**
   * Asynchronously handles the request to send an OTP (One-Time Password) for user verification.
   * This function manages two actions: creating a new OTP or resending an existing one.
   *
   * @async
   * @param {boolean} resend - Determines the type of request:
   *      - `true`: Resends an existing OTP if the previous one has expired or was not received.
   *      - `false`: Resends an existing OTP if the previous one has expired or was not received.
   *
   * @returns {Promise<string | undefined>} - Returns the `verification_id` as a string if the OTP request is successful.
   *      - If the OTP is sent successfully, the function returns the unique `verification_id`.
   *      - If there is an error, the function returns `undefined`.
   */
  const requestSendOTP = async (
    resend: boolean = false,
  ): Promise<string | undefined> => {
    // Set `isLoading` to true to indicate that the OTP request process has started.
    isLoading.value = true

    // Attempt to send the OTP request with `sendOTP`, passing in `formData` and the `resend` flag.
    // The response includes either a `payload` on success or an `error` on failure.
    const { payload, error } = await sendOTP(formData, resend)

    // Set `isLoading` to false to indicate that the OTP request process has completed.
    isLoading.value = false

    // If an error occurred, display a user-friendly error message and exit the function early.
    if (error) {
      errors.value = 'Something went wrong please try again!'
      return
    }

    // If a `payload` is received, the OTP request was successful.
    // Update relevant states, including `message` and `otpSent`, and return the verification ID
    // to allow further handling in the verification process.
    if (payload) {
      message.value = payload.message
      otpSent.value = true

      return payload.verification_id
    }
  }

  /**
   * Asynchronously handles OTP (One-Time Password) verification for user authentication.
   * This function sends the entered OTP data to the server for validation, managing
   * both success and error responses effectively.
   *
   * @async
   * @returns {Promise<string | undefined>} - Resolves with the user authentication `token` if verification is successful.
   *      - On success, returns the token as a string.
   *      - On failure, returns `undefined` and updates the `errors` ref with an appropriate error message.
   *
   * Error Handling:
   *      - If an error occurs during verification, displays a specific error message if available,
   *        or a general error message for any unexpected issues.
   */
  const requestVerifyOTP = async (): Promise<string | undefined> => {
    // Set `isLoading` to true to indicate that the OTP request process has started.
    isLoading.value = true

    // Attempt to verify the OTP by sending `formData` to `verifyOTP`.
    // The response includes either a `payload` on success or an `error` on failure.
    const { payload, error } = await verifyOTP(formData)

    // Set `isLoading` to false to indicate that the OTP request process has completed.
    isLoading.value = false

    // Handle any errors that occur during OTP verification.
    if (error) {
      // If the error is an AxiosError, extract and display the specific error message from the response.
      if (error instanceof AxiosError) {
        errors.value = error.response?.data.message
      } else {
        // For any other error type, display a general error message.
        errors.value = 'Something went wrong please try again!'
      }
      // Exit the function early due to error.
      return
    }

    // If a `payload` is received, the OTP verification was successful.
    // Update the `message` with the response message and return the `token`
    // for further handling, such as authentication or user session setup.
    if (payload) {
      message.value = payload.message
      return payload.token
    }
  }

  return {
    requestSendOTP,
    requestVerifyOTP,
    otpSent,
    isLoading,
    formData,
    message,
    errors,
  }
}
