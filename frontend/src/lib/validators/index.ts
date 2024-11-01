/* eslint-disable @typescript-eslint/no-explicit-any */
import { computed } from 'vue'
import { useVuelidate } from '@vuelidate/core'

type ValidationRules = { [key: string]: any }
type FormData = { [key: string]: any }

const createValidationRules = (rules: ValidationRules) => {
  return computed(() => rules)
}

const validate = (formData: FormData, rules: ValidationRules) => {
  const validationRules = createValidationRules(rules)
  return useVuelidate(validationRules, formData)
}

export default validate
