import { Head, Link, usePage } from '@inertiajs/react';
// import { Layout } from '@/layouts/app-layout.jsx';
import Layout from '../layouts/Layout.jsx';

export default function Settings() {
    const { auth,data } = usePage().props;
    console.log('Settings Page Props:', { auth, data });

    return (
        <>
            <h1>Welcome to your Shopify app</h1>
            <h2>Setting Page</h2>

        </>
    );
}

// Welcome.layout = (page) => <Layout title="Welcome">{page}</Layout>;
Settings.layout = (page) => <Layout>{page}</Layout>;
