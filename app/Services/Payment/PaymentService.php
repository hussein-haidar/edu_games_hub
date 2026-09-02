<?php

namespace App\Services\Payment;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PaymentService
{
    public function __construct(private PaymentManager $manager)
    {
    }

    /**
     * Buat pesanan langganan & inisiasi pembayaran.
     * Mengembalikan arahan lanjutan dari provider (redirect_url / instruksi).
     *
     * @return array<string, mixed>
     */
    public function createOrder(User $user, SubscriptionPlan $plan): array
    {
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'order_id' => 'EDU-'.date('YmdHis').'-'.Str::upper(Str::random(4)),
            'amount' => $plan->harga,
            'status' => 'pending',
        ]);

        try {
            $result = $this->manager->provider()->createPayment($subscription);
        } catch (\Throwable $e) {
            $subscription->update(['status' => 'failed']);
            throw $e;
        }

        return $result;
    }

    /**
     * Proses hasil pembayaran dari provider dan aktifkan langganan user bila sukses.
     */
    public function process(PaymentResult $result, Subscription $subscription): bool
    {
        if ($result->paid) {
            return $this->activate($subscription);
        }

        if (! $result->success) {
            $subscription->update(['status' => 'failed']);
        }

        return false;
    }

    /**
     * Tandai pembayaran lunas dan perpanjang masa berlaku user.
     */
    public function activate(Subscription $subscription): bool
    {
        $user = $subscription->user;
        $months = $subscription->plan?->durasi_bulan ?? 1;

        // Jika user masih berlangganan, perpanjang dari tanggal habis berlaku.
        $base = ($user->isSubscribed() && $user->subscribed_until)
            ? $user->subscribed_until
            : Carbon::now();

        $endsAt = $base->copy()->addMonths($months);

        $subscription->update([
            'status' => 'paid',
            'paid_at' => Carbon::now(),
            'starts_at' => $base,
            'ends_at' => $endsAt,
        ]);

        $user->update(['subscribed_until' => $endsAt]);

        return true;
    }
}
