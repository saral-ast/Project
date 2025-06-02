import '../css/app.css';

import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';
import { initializeTheme } from './hooks/use-appearance';
// import { Provider } from '@shopify/app-bridge-react';
// import { AppBridgeProvider } from './providers/AppBridgeProvider.jsx';
import Layout from './layouts/Layout.jsx';
import { useAppBridge } from '@shopify/app-bridge-react';



const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const appBridge = useAppBridge();

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./pages/${name}.jsx`, import.meta.glob('./pages/**/*.jsx')),
    setup({ el, App, props }) {
        const root = createRoot(el);

        root.render(
            // <AppBridgeProvider>
            // ✅ KEEP ONLY THIS:
            <App {...props} />,

            // </AppBridgeProvider>,

            // </AppBridgeProvider>
        );
    },
    progress: {
        color: '#4B5563',
    },
});

initializeTheme();
