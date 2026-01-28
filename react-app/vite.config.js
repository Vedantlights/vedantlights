import { defineConfig, loadEnv } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '')
  const backendOrigin = env.VITE_BACKEND_ORIGIN || 'http://localhost'

  return {
    plugins: [react()],

    // ✅ Correct base
    base: mode === 'development' ? '/' : '/react-app/',

    optimizeDeps: {
      include: ['react', 'react-dom', 'react-router-dom'],
    },

    build: {
      outDir: 'dist',
      emptyOutDir: true,
    },

    server: {
      proxy: {
        '/api': {
          target: backendOrigin,
          changeOrigin: true,
        },
        '/sendmail': {
          target: backendOrigin,
          changeOrigin: true,
        },
        '/uploads': {
          target: backendOrigin,
          changeOrigin: true,
        },
      },
    },
  }
})
