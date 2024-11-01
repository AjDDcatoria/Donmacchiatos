import type { ProductInputsTypes, VerificationTypes } from './inputs'
import type { DetailsErrors, UserDetailsTypes } from './usertypes'
import type { VerificationError } from '@/types/errors'

/*
|--------------------------------------------------------------------------
|  Inputs
|--------------------------------------------------------------------------
|
| Here you can define your default inputs.
*/

export const DefaultVerification: VerificationTypes = {
  email: '',
  verification_code: '',
}

export const DefaultDetails: UserDetailsTypes = {
  firstname: '',
  lastname: '',
  contact_number: '',
  address: '',
}

export const DefaultProducts: ProductInputsTypes = {
  name: '',
  image: null,
  price: 0,
}

/*
|--------------------------------------------------------------------------
|  Errors
|--------------------------------------------------------------------------
|
| Here you can define your default errors to handle
| the error message, status etc.
*/

export const DefaultVerificationError: VerificationError = {
  message: null,
  status: null,
  is_error: false,
}

export const DefaultDetailsErrors: DetailsErrors = {
  key: null,
  message: null,
}
