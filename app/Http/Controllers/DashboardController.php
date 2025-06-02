<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;


class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        
        
        // Try getting user from Auth
        $user = Auth::user();
        // First time: Get from Auth, then store in session
        if ($user && !session()->has('shopDomain') && !session()->has('accessToken')) {
            session([
                'shopDomain' => $user->name,
                'accessToken' => $user->password, // Replace with token column if different
                'user' => $user,
            ]);
        }
        // Fallback to session
        $shopDomain = session('shopDomain');
        $accessToken = session('accessToken');
      
        // If still missing, return error
        if (!$shopDomain || !$accessToken) {
            return response('Shop not authenticated.', 403);
        }
        // Handle pagination cursor
        $afterCursor = $request->query('after');
        // GraphQL query to get orders
        $query = <<<'GRAPHQL'
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
        // Variables for query
        $variables = [
            'first' => 10,
            'after' => $afterCursor,
        ];
        // Send request to Shopify
        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $accessToken,
            'Content-Type' => 'application/json',
        ])->post("https://{$shopDomain}/admin/api/2025-04/graphql.json", [
            'query' => $query,
            'variables' => $variables,
        ]);
      
        // Handle failure
        if (!$response->ok()) {
            return response('Shopify API error: ' . $response->body(), 500);
        }
        // Extract orders and pagination info
        $data = $response->json('data.orders');       
        $orders = $data['edges'];
        $pageInfo = $data['pageInfo'];
        // Pass to view

        // dd($orders);
        // dd($shopDomain);
        return Inertia::render('welcome', [
            'orders' => $orders,
            'hasNextPage' => $pageInfo['hasNextPage'],
            'nextCursor' => $pageInfo['endCursor'],
            'shopDomain' => $shopDomain,
        ]);
        
    }

    public function settings()
    {
        // Get the authenticated user
        $user = Auth::user();
        // dd($user);
        $shopDomain = Auth::user()->name ?? session('shopDomain');
        
        // If user is not authenticated, redirect to login
        // if (!$user) {
            // return redirect()->route('login');
        // }
        
        // Render the settings page with user data
        return Inertia::render('setting', [
            'user' => $user,
            'shopDomain' => $shopDomain,
        ]);
    }
}