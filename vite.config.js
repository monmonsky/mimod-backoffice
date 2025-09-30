import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { globSync } from 'glob';
import { readFileSync } from 'node:fs';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                ...globSync('resources/css/**/*.css'),
                ...globSync('resources/js/**/*.js')
            ],
            refresh: true,
        }),
    ],
    server: {
        host: 'mimod-bo.aksarahati.space',
        https: {
            key: readFileSync('/home/vlasusu/ssl/mimod-bo/privkey.pem'),
            cert: readFileSync('/home/vlasusu/ssl/mimod-bo/fullchain.pem'),
        },
        cors: true,
    }
});
