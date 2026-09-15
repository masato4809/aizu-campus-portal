import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/script/app.tsx'],
            ssr: 'resources/script/ssr.jsx',
            refresh: true,
        }),
    ],
    server: {
        host: true,
        port: 5173,
        hmr: {
            host: 'localhost'
        },
        watch: {
            usePolling: true,
            interval: 1000,
        }
    },
    resolve: {
        alias: {
            '@': '/resources'
        }
    },
    ssr: {
        noExternal: [
            "@apollo/client"
        ]
    },
    build: {
        rollupOptions: {
            onwarn: (warning, defaultHandler) => {
                if (warning.code === 'MODULE_LEVEL_DIRECTIVE') {
                    return;
                }
                defaultHandler(warning);
            },
        }
    }
});
