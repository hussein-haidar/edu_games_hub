<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\Payment\PaymentManager;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::where('aktif', true)->orderBy('urutan')->get();

        return view('subscription', compact('plans'));
    }

    public function checkout(Request $request, SubscriptionPlan $plan, PaymentService $payment)
    {
        abort_unless($plan->aktif, 404);

        try {
            $result = $payment->createOrder(auth()->user(), $plan);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        $subscription = Subscription::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->latest('id')
            ->first();

        if (! empty($result['redirect_url'])) {
            return redirect($result['redirect_url']);
        }

        return redirect()
            ->route('subscription.pay', $subscription)
            ->with('success', 'Pesanan dibuat. Silakan selesaikan pembayaran.');
    }

    public function pay(Subscription $subscription, PaymentManager $manager)
    {
        abort_unless($subscription->user_id === auth()->id(), 403);

        // Provider mock: kunjungi halaman ini = konfirmasi pembayaran (simulasi).
        $result = $manager->provider()->handleCallback(request()->all(), $subscription);

        if ($result->paid) {
            app(PaymentService::class)->activate($subscription);

            return redirect()->route('subscription.result', $subscription)
                ->with('success', 'Pembayaran berhasil! Langgananmu aktif.');
        }

        return redirect()->route('subscription')->with('error', $result->message ?? 'Pembayaran belum selesai.');
    }

    public function callback(Request $request, Subscription $subscription, PaymentManager $manager)
    {
        $result = $manager->provider()->handleCallback($request->all(), $subscription);

        if ($result->paid) {
            app(PaymentService::class)->activate($subscription);
        }

        // Untuk notifikasi server-to-server, balas 200 agar provider tidak mengulang.
        if ($request->isJson() || $request->expectsJson()) {
            return response()->json(['success' => $result->paid]);
        }

        return redirect()->route('subscription.result', $subscription);
    }

    public function result(Subscription $subscription)
    {
        abort_unless($subscription->user_id === auth()->id(), 403);

        return view('subscription_result', compact('subscription'));
    }
}
