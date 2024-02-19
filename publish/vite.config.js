import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';

import vue from '@vitejs/plugin-vue';

export default defineConfig({
	plugins: [
		laravel({
			input: [
				'resources/css/app.css',
				'resources/js/app.js',

				'resources/js/public.js',
				'resources/js/backend.js',
				'resources/js/add-users-to-group.js',

				'resources/sass/oxygen/bootstrap.scss',
				'resources/sass/public.scss',
				'resources/sass/backend.scss',
				'resources/sass/auth.scss',
			],
			publicDirectory: '/public/',
			refresh: true,
		}),

		{
			// hot-reload Blade files
			// @link https://freek.dev/2277-using-laravel-vite-to-automatically-refresh-your-browser-when-changing-a-blade-file
			name: 'blade',
			handleHotUpdate({ file, server }) {
				if (file.endsWith('.blade.php')) {
					server.ws.send({
						type: 'full-reload',
						path: '*',
					});
				}
			},
		},

		vue({
			template: {
				transformAssetUrls: {
					// The Vue plugin will re-write asset URLs, when referenced
					// in Single File Components, to point to the Laravel web
					// server. Setting this to `null` allows the Laravel plugin
					// to instead re-write asset URLs to point to the Vite
					// server instead.
					base: null,

					// The Vue plugin will parse absolute URLs and treat them
					// as absolute paths to files on disk. Setting this to
					// `false` will leave absolute URLs un-touched so they can
					// reference assets in the public directly as expected.
					includeAbsolute: false,
				},
			},
		}),
	],

	server: {
		// https: true,
		host: 'localhost.test',
	},
});
