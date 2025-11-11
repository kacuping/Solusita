<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Promotion;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class CustomerHomeController extends Controller
{
    /**
     * Determine if a promotion is eligible for the given customer based on segment_rules.
     */
    private function isPromotionEligible(Promotion $promotion, ?Customer $customer, int $totalPastBookings): bool
    {
        $rules = $promotion->segment_rules ?? [];

        // If no rules, eligible for everyone
        if (empty($rules)) {
            return true;
        }

        // new_customer: true -> Only customers with 0 past bookings
        if (array_key_exists('new_customer', $rules)) {
            $required = (bool) $rules['new_customer'];
            if ($required) {
                if (!$customer) return false; // must have a customer profile
                if ($totalPastBookings > 0) return false; // not new anymore
            }
        }

        // min_days_since_registration: N -> require account age >= N days
        if (array_key_exists('min_days_since_registration', $rules)) {
            $days = (int) $rules['min_days_since_registration'];
            if ($days > 0) {
                if (!$customer || !$customer->created_at) return false;
                $ageDays = now()->diffInDays($customer->created_at);
                if ($ageDays < $days) return false;
            }
        }

        // min_past_bookings: N -> require at least N completed bookings in the past
        if (array_key_exists('min_past_bookings', $rules)) {
            $min = (int) $rules['min_past_bookings'];
            if ($min > 0 && $totalPastBookings < $min) {
                return false;
            }
        }

        return true;
    }
    public function index()
    {
        $user = Auth::user();

        // Temukan entitas Customer berdasarkan relasi user_id (lebih kokoh daripada kecocokan email)
        $customer = Customer::where('user_id', $user->id)->first();

        $nextBooking = null;
        $upcomingBookings = collect();
        $totalPastBookings = 0;

        if ($customer) {
            $upcomingBookings = Booking::with(['service', 'cleaner'])
                ->where('customer_id', $customer->id)
                ->where('scheduled_at', '>=', now())
                ->orderBy('scheduled_at')
                ->limit(5)
                ->get();

            $nextBooking = $upcomingBookings->first();

            $totalPastBookings = Booking::where('customer_id', $customer->id)
                ->where('scheduled_at', '<', now())
                ->count();
        }

        // Promo aktif saat ini
        $activePromotions = Promotion::query()
            ->where('active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->orderBy('starts_at', 'desc')
            ->limit(5)
            ->get();

        // Filter berdasarkan eligibility (segment_rules)
        $eligiblePromotions = $activePromotions->filter(function ($promo) use ($customer, $totalPastBookings) {
            return $this->isPromotionEligible($promo, $customer, $totalPastBookings);
        })->values();

        // Layanan populer/terdaftar untuk ditampilkan sebagai "Spesialisasi/Layanan"
        $services = Service::query()
            ->orderBy('name')
            ->limit(6)
            ->get();

        // Top rated cleaners berdasarkan rata-rata rating dari review via booking
        $topCleaners = DB::table('cleaners')
            ->leftJoin('bookings', 'bookings.cleaner_id', '=', 'cleaners.id')
            ->leftJoin('reviews', 'reviews.booking_id', '=', 'bookings.id')
            ->select('cleaners.id', 'cleaners.name', 'cleaners.phone', 'cleaners.address', DB::raw('AVG(reviews.rating) as avg_rating'), DB::raw('COUNT(reviews.id) as review_count'))
            ->groupBy('cleaners.id', 'cleaners.name', 'cleaners.phone', 'cleaners.address')
            ->orderByDesc(DB::raw('AVG(reviews.rating)'))
            ->orderByDesc(DB::raw('COUNT(reviews.id)'))
            ->limit(5)
            ->get();

        return view('customer.home', [
            'customer' => $customer,
            'nextBooking' => $nextBooking,
            'upcomingBookings' => $upcomingBookings,
            'totalPastBookings' => $totalPastBookings,
            'activePromotions' => $eligiblePromotions,
            'services' => $services,
            'topCleaners' => $topCleaners,
        ]);
    }
}
