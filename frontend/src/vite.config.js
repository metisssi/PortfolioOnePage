import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
  appType: 'mpa',
  build: {
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'index.html'),
        // Update this line to match your folder structure:
        admin: resolve(__dirname, 'src/admin/admin.html')
      }
    }
  }
});