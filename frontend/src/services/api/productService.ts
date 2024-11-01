import api from '@/lib/axios'
import type { FormOrderTypes, ProductInputsTypes } from '@/types/inputs'

export const getAllProducts = async () => {
  try {
    const { data: response } = await api.get('products/getAll')

    return {
      error: null,
      payload: response.data,
    }
  } catch (error) {
    return {
      error: error,
      payload: null,
    }
  }
}

export const addProducts = async (product: ProductInputsTypes) => {
  try {
    const response = await api.post('/products/add', product, {
      headers: { 'Content-type': 'multipart/form-data' },
    })

    const productPayload = {
      data: {
        id: response.data.data.id,
        name: response.data.data.name,
        price: response.data.data.price,
        image_url: response.data.data.image_url,
      },
      message: response.data.message,
    }

    return {
      payload: productPayload,
      error: null,
    }
  } catch (error) {
    return {
      payload: null,
      error: error,
    }
  }
}

export const createOrder = async (formData: FormOrderTypes) => {
  try {
    const response = await api.post('order/create', formData)
    return {
      error: null,
      payload: response.data,
    }
  } catch (error) {
    return {
      error: error,
      payload: null,
    }
  }
}
