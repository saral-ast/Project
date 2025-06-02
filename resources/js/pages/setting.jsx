import { Head, Link, usePage } from '@inertiajs/react';
// import { Layout } from '@/layouts/app-layout.jsx';
import Layout from '../layouts/Layout.jsx';

export default function Settings() {
    const { auth,user,shopDomain } = usePage().props;
    console.log('Settings Page Props:', { auth, user, shopDomain });

    return (
        <>
            <h1>Welcome to your Shopify app</h1>
            <h2>Setting Page : {shopDomain}</h2>

        </>
    );
}

// Welcome.layout = (page) => <Layout title="Welcome">{page}</Layout>;
Settings.layout = (page) => <Layout>{page}</Layout>;
