import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import { globSync } from "glob";
import { readFileSync, existsSync } from "node:fs";

const sslKeyPath = "/home/vlasusu/ssl/mimod-bo/privkey.pem";
const sslCertPath = "/home/vlasusu/ssl/mimod-bo/fullchain.pem";
const hasSSL = existsSync(sslKeyPath) && existsSync(sslCertPath);

export default defineConfig({
    plugins: [
        laravel({
            input: [
                ...globSync("resources/css/**/*.css"),
                ...globSync("resources/js/**/*.js"),
            ],
            refresh: true,
        }),
    ],

    ...(hasSSL && {
        server: {
            host: "mimod-bo.aksarahati.space",
            https: {
                key: readFileSync("/home/vlasusu/ssl/mimod-bo/privkey.pem"),
                cert: readFileSync("/home/vlasusu/ssl/mimod-bo/fullchain.pem"),
            },
            cors: true,
        },
    }),
});
