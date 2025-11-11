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
            'activePromotions' => $activePromotions,
            'services' => $services,
            'topCleaners' => $topCleaners,
        ]);
    }
}
