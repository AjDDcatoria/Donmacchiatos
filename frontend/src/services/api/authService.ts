/* eslint-disable @typescript-eslint/no-explicit-any */
import api from '@/lib/axios'
import { auth } from '@/lib/constans/routes'
import type { VerificationTypes } from '@/types/inputs'

type OtpTypes = {
  payload: PayloadResponse | Record<string ,any> | null
  error: unknown
}

type PayloadResponse = {
  message: string
  verification_id?: string
  token?: string
}

/**
 * Handle OTP included resend and create new otp.
 */
export const sendOTP = async (
  formData: VerificationTypes,
  resend: boolean,
): Promise<OtpTypes> => {
  try {
    const { data: response } = await api.post(
      `${auth.sendOTP}${resend}`,
      formData,
    )

    return {
      payload: response.data,
      error: null,
    }
  } catch (error) {
    return {
      payload: null,
      error,
    }
  }
}

/**
 * Handle verify OTP api.
 */
export const verifyOTP = async (
  formData: VerificationTypes,
): Promise<OtpTypes> => {
  try {
    const { data: response } = await api.post(auth.verifyOTP, formData)

    return {
      payload: response.data,
      error: null,
    }
  } catch (error) {
    return {
      payload: null,
      error,
    }
  }
}

/**
 * Handle logout api.
 */
export const logout = async ():Promise<OtpTypes> => {
  try {
    const response = await api.post('/authenticate/logout')
    return {
      payload: response.data,
      error: null,
    }
  } catch (error) {
    return {
      payload: null,
      error,
    }
  }
}
