import { defineConfig, loadEnv } from 'vite'
import react from '@vitejs/plugin-react'

// https://vite.dev/config/
export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '')
  const backendOrigin = env.VITE_BACKEND_ORIGIN || 'http://localhost'
  const backendBasePath = env.VITE_BACKEND_BASE_PATH || '/public_html'
  const publicPath = env.VITE_PUBLIC_PATH || `${backendBasePath.replace(/\/+$/, '')}/react-app/dist/`
  const outDir = env.VITE_BUILD_OUT_DIR || 'dist'

  return {
    plugins: [react()],
    // In dev we want a normal root-served app (/) so direct routes like
    // http://localhost:5173/admin/login work.
    // In production we keep building/serving under /public_html/react-app/dist/.
    base: mode === 'development' ? '/' : publicPath,
    optimizeDeps: {
      include: ['react', 'react-dom', 'react-router-dom'],
    },
    build: {
      outDir,
      emptyOutDir: true,
    },
    server: {
      proxy: {
        '/api': {
          target: backendOrigin,
          changeOrigin: true,
          rewrite: (path) => `${backendBasePath}${path}`,
        },
        '/sendmail': {
          target: backendOrigin,
          changeOrigin: true,
          rewrite: (path) => `${backendBasePath}${path}`,
        },
        '/uploads': {
          target: backendOrigin,
          changeOrigin: true,
          rewrite: (path) => `${backendBasePath}${path}`,
        },
      },
    },
  }
})
