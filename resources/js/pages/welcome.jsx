import { usePage } from '@inertiajs/react';
// import { Layout } from '@/layouts/app-layout.jsx';
import Layout from '../layouts/Layout.jsx';

export default function Welcome() {
    const { auth, orders, hasNextPage, nextCursor, shopDomain,session } = usePage().props;
    console.log(session)

    return (
        <>
            <div className="min-h-screen bg-gray-50 p-8">
                <div className="mx-auto max-w-4xl space-y-6 rounded-2xl bg-white p-6 shadow-md">
                    <h1 className="text-3xl font-bold text-gray-800">Welcome to your Shopify app</h1>
                    <h2 className="text-lg text-gray-600">
                        Shop Domain: <span className="font-semibold">{shopDomain ?? 'No shop found'}</span>
                    </h2>

                    <div>
                        <h3 className="mb-4 text-xl font-semibold text-gray-700">Orders:</h3>

                        {orders.length > 0 ? (
                            <ul className="space-y-2">
                                {orders.map((order) => (
                                    <li
                                        key={order.node.id}
                                        className="flex flex-col rounded-lg border border-gray-200 p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between"
                                    >
                                        <div>
                                            <p className="font-medium text-gray-800">{order.node.name}</p>
                                            <p className="text-sm text-gray-600">{order.node.email}</p>
                                        </div>
                                        <p className="text-sm text-gray-700">
                                            {order.node.totalPriceSet.presentmentMoney.amount}{' '}
                                            {order.node.totalPriceSet.presentmentMoney.currencyCode}
                                        </p>
                                    </li>
                                ))}
                            </ul>
                        ) : (
                            <p className="text-gray-500 italic">No orders yet.</p>
                        )}
                    </div>

                    {hasNextPage && (
                        <div className="rounded-lg border border-yellow-300 bg-yellow-100 p-4 text-yellow-800">
                            More orders available! <span className="font-mono text-sm">Cursor: {nextCursor}</span>
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}

// Welcome.layout = (page) => <Layout title="Welcome">{page}</Layout>;
Welcome.layout = (page) => <Layout>{page}</Layout>;
