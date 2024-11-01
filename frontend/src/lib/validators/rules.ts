/*
|--------------------------------------------------------------------------
| Validation Rules
|--------------------------------------------------------------------------
| This section defines the validation rules for form fields used in
| the application. These rules specify the constraints and requirements
| for each field, ensuring that user input meets the necessary criteria.
|
*/

import { helpers, maxLength, minLength, required } from '@vuelidate/validators'

const startsWith09 = helpers.withMessage('Number starts 09', (value: string) =>
  value.startsWith('09'),
)

export const userDetails = {
  firstname: {
    required: helpers.withMessage('Given name is required', required),
    minLength: helpers.withMessage(
      'Given name must be least 2 characters',
      minLength(2),
    ),
  },
  lastname: {
    required: helpers.withMessage('Family name is required', required),
    minLength: helpers.withMessage(
      'Family name must be least 2 characters',
      minLength(2),
    ),
  },
  contact_number: {
    required: helpers.withMessage('Phone number is required', required),
    minLength: helpers.withMessage('Phone number must valid', minLength(11)),
    maxLength: helpers.withMessage('Phone number must valid', maxLength(11)),
    startsWith09,
  },
  address: {
    required: helpers.withMessage('Address is required', required),
    minLength: helpers.withMessage(
      'Address be least 2 characters',
      minLength(2),
    ),
  },
}
