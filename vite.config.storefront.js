import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';

export default defineConfig({
  plugins: [react()],
  build: {
    outDir: resolve(__dirname, 'assets/dist'),
    emptyOutDir: false,
    rollupOptions: {
      input: resolve(__dirname, 'src/storefront/main.jsx'),
      output: {
        entryFileNames: 'storefront.js',
        assetFileNames: (assetInfo) => {
          if (assetInfo.name && assetInfo.name.endsWith('.css')) {
            return 'storefront.css';
          }
          return '[name].[ext]';
        },
      },
    },
  },
});
