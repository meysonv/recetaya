import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { renderToString } from '@vue/server-renderer';
import { createSSRApp, h, DefineComponent } from 'vue';
import route, { ziggyRoute } from 'ziggy-js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createServer((page) =>
    createInertiaApp({
        page,
        render: renderToString,
        title: (title) => `${title} - ${appName}`,
        resolve: async (name) => {
            const pages = import.meta.glob('./pages/**/*.vue');
            const importPage = pages[`./pages/${name}.vue`];
            if (!importPage) throw new Error(`Page not found: ${name}`);
            const module = await importPage();
            return (module as { default: DefineComponent }).default;
        },
        setup({ App, props, plugin }) {
            const app = createSSRApp({ render: () => h(App, props) });
            app.use(plugin);
            (globalThis as any).route = route;
            return app;
        },
    }),
);
