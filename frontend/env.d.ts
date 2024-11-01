/// <reference types="vite/client" />

interface ImportMetaEnv {
  readonly VITE_APP_BASE_URL: string

  readonly VITE_APP_USER_SECRTE: string
  readonly VITE_APP_TOKEN_SECRTE: string
}

interface ImportMeta {
  readonly env: ImportMetaEnv
}
