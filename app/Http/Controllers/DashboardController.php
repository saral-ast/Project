<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Ensure session has current user data
     */
    private function syncSessionData()
    {
        $user = Auth::user();
        
        if ($user) {
            // Always sync session with current authenticated user data
            session([
                'shopDomain' => $user->name,
                'accessToken' => $user->password, // Replace with actual token column
                'user' => $user,
            ]);
            
            Log::info('Session synced for user: ' . $user->name);
        }
        
        return $user;
    }

    /**
     * Get shop credentials from session or user
     */
    private function getShopCredentials()
    {
        return [
            'shopDomain' => session('shopDomain') ?? Auth::user()?->name,
            'accessToken' => session('accessToken') ?? Auth::user()?->password,
            'user' => session('user') ?? Auth::user(),
        ];
    }

    public function dashboard(Request $request)
    {
        // Log session info for debugging
        Log::info('Dashboard - Session ID: ' . session()->getId());
        Log::info('Dashboard - Auth check: ' . (Auth::check() ? 'Yes' : 'No'));
        
        // Sync session data with current user
        $user = $this->syncSessionData();
        
        // Get shop credentials
        $credentials = $this->getShopCredentials();
        extract($credentials); // $shopDomain, $accessToken, $user
        
        // Validate required data
        if (!$shopDomain || !$accessToken) {
            Log::error('Dashboard - Missing shop credentials', [
                'shopDomain' => $shopDomain,
                'hasAccessToken' => !empty($accessToken),
                'userId' => $user?->id,
            ]);
            
            return response('Shop not authenticated. Please reinstall the app.', 403);
        }

        // Handle pagination cursor
        $afterCursor = $request->query('after');
        
        // GraphQL query to get orders
        $query = $this->getOrdersQuery();
        
        // Variables for query
        $variables = [
            'first' => 10,
            'after' => $afterCursor,
        ];

        try {
            // Send request to Shopify
            $response = Http::withHeaders([
                'X-Shopify-Access-Token' => $accessToken,
                'Content-Type' => 'application/json',
            ])->post("https://{$shopDomain}/admin/api/2025-04/graphql.json", [
                'query' => $query,
                'variables' => $variables,
            ]);

            // Handle API failure
            if (!$response->ok()) {
                Log::error('Shopify API error in dashboard', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'shopDomain' => $shopDomain,
                ]);
                
                return response('Shopify API error: ' . $response->body(), 500);
            }

            // Extract orders and pagination info
            $data = $response->json('data.orders');
            $orders = $data['edges'] ?? [];
            $pageInfo = $data['pageInfo'] ?? ['hasNextPage' => false, 'endCursor' => null];

            Log::info('Dashboard - Successfully fetched ' . count($orders) . ' orders');

        } catch (\Exception $e) {
            Log::error('Dashboard - Exception occurred', [
                'message' => $e->getMessage(),
                'shopDomain' => $shopDomain,
            ]);
            
            return response('Error fetching orders: ' . $e->getMessage(), 500);
        }

        // Get session data for debugging
        $sessionData = session()->all();
        
        // Pass to view
        return Inertia::render('welcome', [
            'orders' => $orders,
            'hasNextPage' => $pageInfo['hasNextPage'],
            'nextCursor' => $pageInfo['endCursor'],
            'shopDomain' => $shopDomain,
            'session' => $sessionData,
            'debug' => [
                'sessionId' => session()->getId(),
                'userId' => $user?->id,
                'authenticated' => Auth::check(),
            ],
        ]);
    }

    public function settings()
    {
        // Log session info for debugging
        Log::info('Settings - Session ID: ' . session()->getId());
        Log::info('Settings - Auth check: ' . (Auth::check() ? 'Yes' : 'No'));
        
        // Sync session data with current user
        $user = $this->syncSessionData();
        
        // Get shop credentials
        $credentials = $this->getShopCredentials();
        
        // Validate user authentication
        if (!$user) {
            Log::warning('Settings - No authenticated user found');
            return response('User not authenticated.', 401);
        }

        // Get session data for debugging
        $sessionData = session()->all();
        
        Log::info('Settings - Successfully loaded for user: ' . $user->name);

        // Render the settings page with user data
        return Inertia::render('setting', [
            'user' => $user,
            'shopDomain' => $credentials['shopDomain'],
            'session' => $sessionData,
            'debug' => [
                'sessionId' => session()->getId(),
                'userId' => $user->id,
                'authenticated' => Auth::check(),
            ],
        ]);
    }

    /**
     * Get the GraphQL query for fetching orders
     */
    private function getOrdersQuery(): string
    {
        return <<<'GRAPHQL'
        query GetOrders($first: Int!, $after: String) {
            orders(first: $first, after: $after, sortKey: CREATED_AT, reverse: true) {
                pageInfo {
                    hasNextPage
                    endCursor
                }
                edges {
                    node {
                        id
                        name
                        email
                        createdAt
                        totalPriceSet {
                            presentmentMoney {
                                amount
                                currencyCode
                            }
                        }
                    }
                }
            }
        }
        GRAPHQL;
    }
}