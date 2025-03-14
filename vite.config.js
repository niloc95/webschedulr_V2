import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
  build: {
    // Change output directory to be outside of public
    outDir: 'assets',
    emptyOutDir: true,
    rollupOptions: {
      input: {
        app: resolve(__dirname, 'src/js/app.js')
      },
      output: {
        entryFileNames: 'js/[name].js',
        chunkFileNames: 'js/[name]-[hash].js',
        assetFileNames: ({name}) => {
          if (/\.(gif|jpe?g|png|svg)$/.test(name ?? '')) {
            return 'images/[name]-[hash][extname]';
          }
          
          if (/\.css$/.test(name ?? '')) {
            return 'css/[name][extname]';
          }
          
          return 'assets/[name]-[hash][extname]';
        }
      }
    }
  }
});
