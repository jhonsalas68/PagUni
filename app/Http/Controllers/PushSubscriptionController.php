<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class PushSubscriptionController extends Controller
{
    /**
     * Get the VAPID public key.
     */
    public function publicKey()
    {
        return response()->json(['publicKey' => config('services.vapid.public_key')]);
    }

    /**
     * Store a new push subscription.
     */
    public function store(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        $user = Auth::user();
        $userId = $user ? $user->id : null;

        // Check if subscription already exists
        $exists = \DB::table('push_subscriptions')
            ->where('endpoint', $request->endpoint)
            ->exists();

        if ($exists) {
            // Update user_id if changed
            if ($userId) {
                \DB::table('push_subscriptions')
                    ->where('endpoint', $request->endpoint)
                    ->update(['user_id' => $userId, 'updated_at' => now()]);
            }
            return response()->json(['success' => true, 'message' => 'Subscription updated.']);
        }

        \DB::table('push_subscriptions')->insert([
            'endpoint' => $request->endpoint,
            'p256dh' => $request->keys['p256dh'],
            'auth' => $request->keys['auth'],
            'user_id' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Subscription stored.']);
    }

    /**
     * Remove a subscription.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
        ]);

        \DB::table('push_subscriptions')
            ->where('endpoint', $request->endpoint)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Subscription deleted.']);
    }

    /**
     * Send a test notification (For verification purposes).
     */
    public function testNotification(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $subscriptions = \DB::table('push_subscriptions')
            ->where('user_id', $user->id)
            ->get();

        if ($subscriptions->isEmpty()) {
            return response()->json(['error' => 'No subscriptions found for this user.'], 404);
        }

        $auth = [
            'VAPID' => [
                'subject' => config('services.vapid.subject'),
                'publicKey' => config('services.vapid.public_key'),
                'privateKey' => config('services.vapid.private_key'),
            ],
        ];

        try {
            $webPush = new WebPush($auth);

            foreach ($subscriptions as $sub) {
                $subscription = Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'publicKey' => $sub->p256dh,
                    'authToken' => $sub->auth,
                ]);

                $webPush->sendOneNotification(
                    $subscription,
                    json_encode([
                        'title' => 'Test Notification',
                        'body' => 'This is a test notification from SGU.',
                        'url' => '/profesor/dashboard'
                    ])
                );
            }

            return response()->json(['success' => true, 'message' => 'Notification sent.']);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
