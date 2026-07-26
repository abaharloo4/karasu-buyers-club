import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';

export default defineConfig({
  plugins: [react()],
  build: {
    outDir: resolve(__dirname, 'assets/dist'),
    emptyOutDir: false,
    rollupOptions: {
      input: resolve(__dirname, 'src/admin/main.jsx'),
      output: {
        entryFileNames: 'admin.js',
        assetFileNames: (assetInfo) => {
          if (assetInfo.name && assetInfo.name.endsWith('.css')) {
            return 'admin.css';
          }
          return '[name].[ext]';
        },
      },
    },
  },
});
