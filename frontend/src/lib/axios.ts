import axios from 'axios'
import { useUserStore } from '@/stores/userStore'

const baseUrl = (() => {
  const hostname = window.location.hostname
  const backendUrl = import.meta.env.VITE_APP_BASE_URL
  if (hostname === 'localhost') {
    return 'http://127.0.0.1:8000/api'
  } else {
    return backendUrl
  }
})()

const api = axios.create({
  baseURL: baseUrl,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
})

api.interceptors.request.use(
  config => {
    const userStore = useUserStore()
    const token = userStore.getToken()?.toString()
    if (token) {
      config.headers.Authorization = `Bearer ${token}` // Set the Authorization header if the token exists
    }

    return config
  },
  error => {
    return Promise.reject(error)
  },
)

api.interceptors.response.use(
  response => response,
  error => {
    if (error.response) {
      // Server responded with a status other than 2xx
      switch (error.response.status) {
        case 401:
          // Unauthorized - Handle token refresh or redirection to login
          console.error('Unauthorized access - please log in again.')
          // e.g., redirect to login or handle token refresh
          break
        case 403:
          // Forbidden - user might not have access
          console.error('Forbidden - you do not have access.')
          break
        case 404:
          console.error('Resource not found.')
          break
        case 500:
          console.error('Server error - try again later.')
          break
        default:
          console.error('An error occurred:', error.response.statusText)
      }
    } else if (error.request) {
      // Request was made but no response received
      console.error('Network error or no response received.')
    } else {
      // Other errors
      console.error('Error:', error.message)
    }

    return Promise.reject(error)
  },
)

export default api
