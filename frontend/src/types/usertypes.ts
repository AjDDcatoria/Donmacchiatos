export interface UserDetailsTypes {
  fullname?: string
  firstname: string
  lastname: string
  contact_number: string
  address: string
  avatar?:string | null
}

export interface UserTypes {
  id:string
  email:string
  role: 'customer' | 'admin'
  is_setup: 1 | 0
  provider: 'email' | 'google' | 'facebook'
  details?: UserDetailsTypes | null
}

export interface DetailsErrors {
  key: string | null
  message: string | null
}

