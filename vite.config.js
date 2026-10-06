import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

const inDocker = process.env.DOCKER === 'true';

export default defineConfig(({ command }) => {
    // No Docker o dev server fica atrás do Traefik, em http://localhost/vite.
    const behindProxy = inDocker && command === 'serve';

    return {
        ...(behindProxy && { base: '/vite/' }),
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
                fonts: [
                    bunny('Instrument Sans', {
                        weights: [400, 500, 600],
                    }),
                ],
            }),
            tailwindcss(),
        ],
        server: {
            ...(behindProxy && {
                host: '0.0.0.0',
                port: 5173,
                strictPort: true,
                origin: 'http://localhost',
                hmr: { host: 'localhost', clientPort: 80 },
            }),
            watch: {
                ignored: ['**/storage/framework/views/**'],
                usePolling: inDocker,
            },
        },
    };
});
