import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueJsx from '@vitejs/plugin-vue-jsx'

import Components from 'unplugin-vue-components/vite';
import { VantResolver } from '@vant/auto-import-resolver';

// https://vitejs.dev/config/
export default defineConfig({
    base: "./",
    plugins: [
        vue({
            template: {
            compilerOptions: {
                // 所有以 mdui- 开头的标签名都是 mdui 组件
                isCustomElement: (tag) => tag.startsWith('mdui-')
            }
            }
        }),
        vueJsx(),
        Components({
            resolvers: [VantResolver()],
        }),
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./src', import.meta.url)),
            "vue": "vue/dist/vue.esm-bundler.js"
        }
    }
})
