import type { UserDetailsTypes, UserTypes } from '@/types/usertypes'
import api from '@/lib/axios'
import axios from 'axios'

type GetUserType = {
  payload: UserTypes | null
  error: unknown
}

/**
 * Retrieve user api.
 */
export const getUser = async (): Promise<GetUserType> => {
  try {
    const { data: response } = await api.get('/user')

    // Construct the user payload including optional `details`
    const userPayload: UserTypes = {
      id: response.data.id,
      email: response.data.email,
      is_setup: response.data.is_setup,
      role: response.data.role,
      provider: response.data.provider,
      details: response.data.details ?? null, // ? Set to `null` if `details` is missing
    }

    return {
      payload: userPayload,
      error: null,
    }
  } catch (error) {
    // If error occurred return payload null and error
    return {
      error: error,
      payload: null,
    }
  }
}

/**
 * Setup user api.
 */
export const setupUser = async (formData: UserDetailsTypes) => {
  try {
    const response = await api.patch('/user/setup', formData)

    // If request is success return payload then set error to null
    return {
      payload: {
        message: response.data.message,
        status: response.status,
      },
      error: null,
    }
  } catch (error: unknown) {
    if (axios.isAxiosError(error) && error.response) {
      return {
        error: {
          errors: error.response.data.errors,
          message: error.response.data.message || 'An unexpected error occured',
          status: error.status || 500,
        },
      }
    }

    return {
      error: {
        errors: error,
        message: 'An unexpected error occured',
        status: 500,
      },
    }
  }
}
