<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerHelpController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified','customer']);
    }

    /**
     * Tampilkan form bantuan (help) khusus pelanggan.
     */
    public function create()
    {
        $user = Auth::user();
        $customer = Customer::where('user_id', $user->id)->first();
        $bookings = collect();

        if ($customer) {
            $bookings = Booking::with('service')
                ->where('customer_id', $customer->id)
                ->orderByDesc('scheduled_at')
                ->limit(20)
                ->get();
        }

        return view('customer.help', compact('customer', 'bookings'));
    }

    /**
     * Simpan tiket bantuan dari pelanggan tanpa melalui halaman admin support.create.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $customer = Customer::where('user_id', $user->id)->firstOrFail();

        $data = $request->validate([
            'booking_id' => ['required','exists:bookings,id'],
            'subject' => ['required','string','max:150'],
            'message' => ['required','string'],
        ]);

        // Pastikan booking yang dipilih adalah milik customer ini
        $booking = Booking::where('id', $data['booking_id'])
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        $ticket = Ticket::create([
            'customer_id' => $customer->id,
            'booking_id' => $booking->id,
            'subject' => $data['subject'],
            'message' => $data['message'],
            'priority' => 'medium', // default priority untuk tiket pelanggan
            'status' => 'open',
        ]);

        return redirect()->route('customer.schedule')->with('status', 'Permintaan bantuan dikirim. Kami akan menghubungi Anda.');
    }
}
