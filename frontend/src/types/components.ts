export interface DefaultProps {
  class?: string
  id?: string
  name?: string
}

export interface TextProps extends DefaultProps {
  label?: string | null
}

export interface NavigationTypes {
  icon: string
  label: string
  path: string
  badge?: number | null
  severity?:
    | 'danger'
    | 'secondary'
    | 'info'
    | 'success'
    | 'warn'
    | 'contrast'
    | null
    | undefined
}
