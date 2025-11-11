<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Cleaner;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
        // Optional permissions
        // $this->middleware('can:reviews.view')->only(['index']);
        // $this->middleware('can:reviews.manage')->only(['approve','reject']);
    }

    public function index(Request $request)
    {
        $status = $request->query('status');

        $query = Review::query()->with(['customer', 'booking.cleaner', 'booking.service']);
        if ($status) {
            $query->where('status', $status);
        }
        $reviews = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        // Summary by service
        $serviceSummary = Review::selectRaw('bookings.service_id, AVG(reviews.rating) as avg_rating, COUNT(reviews.id) as total')
            ->join('bookings', 'reviews.booking_id', '=', 'bookings.id')
            ->where('reviews.status', 'approved')
            ->groupBy('bookings.service_id')
            ->get()
            ->map(function($row){
                return [
                    'service' => Service::find($row->service_id),
                    'avg_rating' => round($row->avg_rating, 2),
                    'total' => $row->total,
                ];
            });

        // Summary by cleaner
        $cleanerSummary = Review::selectRaw('bookings.cleaner_id, AVG(reviews.rating) as avg_rating, COUNT(reviews.id) as total')
            ->join('bookings', 'reviews.booking_id', '=', 'bookings.id')
            ->whereNotNull('bookings.cleaner_id')
            ->where('reviews.status', 'approved')
            ->groupBy('bookings.cleaner_id')
            ->get()
            ->map(function($row){
                return [
                    'cleaner' => Cleaner::find($row->cleaner_id),
                    'avg_rating' => round($row->avg_rating, 2),
                    'total' => $row->total,
                ];
            });

        return view('reviews.index', compact('reviews', 'status', 'serviceSummary', 'cleanerSummary'));
    }

    public function approve(Review $review)
    {
        $review->update(['status' => 'approved']);
        return redirect()->route('reviews.index', ['status' => 'pending'])->with('success', 'Ulasan disetujui.');
    }

    public function reject(Review $review)
    {
        $review->update(['status' => 'rejected']);
        return redirect()->route('reviews.index', ['status' => 'pending'])->with('success', 'Ulasan ditolak.');
    }
}

