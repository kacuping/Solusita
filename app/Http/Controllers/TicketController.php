<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Booking;
use App\Models\Cleaner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
        // Optional permissions
        // $this->middleware('can:support.view')->only(['index','show']);
        // $this->middleware('can:support.manage')->except(['index','show']);
    }

    public function index(Request $request)
    {
        $status = $request->query('status');
        $query = Ticket::query()->with(['customer', 'booking', 'cleaner']);
        if ($status) {
            $query->where('status', $status);
        }
        $tickets = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        return view('support.index', compact('tickets', 'status'));
    }

    public function create()
    {
        $cleaners = Cleaner::where('active', true)->orderBy('full_name')->get();
        return view('support.create', compact('cleaners'));
    }

    public function store(Request $request)
    {
        // Admin membuat tiket: hanya membutuhkan booking_id, tanpa mengisi customer_id secara manual.
        $data = $request->validate([
            'booking_id' => ['required','exists:bookings,id'],
            'cleaner_id' => ['nullable','exists:cleaners,id'],
            'subject' => ['required','string','max:150'],
            'message' => ['required','string'],
            'priority' => ['required','in:low,medium,high'],
        ]);

        $booking = Booking::findOrFail($data['booking_id']);

        $ticket = Ticket::create([
            'customer_id' => $booking->customer_id,
            'booking_id' => $booking->id,
            'cleaner_id' => $data['cleaner_id'] ?? null,
            'subject' => $data['subject'],
            'message' => $data['message'],
            'priority' => $data['priority'],
            'status' => 'open',
        ]);

        return redirect()->route('support.show', $ticket)->with('success', 'Tiket dibuat.');
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['customer', 'booking', 'cleaner', 'attachments']);
        $cleaners = Cleaner::where('active', true)->orderBy('full_name')->get();
        return view('support.show', compact('ticket', 'cleaners'));
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'status' => ['required','in:open,pending,resolved,closed'],
        ]);
        $ticket->status = $data['status'];
        $ticket->closed_at = in_array($ticket->status, ['resolved','closed']) ? now() : null;
        $ticket->save();
        return redirect()->route('support.show', $ticket)->with('success', 'Status tiket diperbarui.');
    }

    public function assign(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'cleaner_id' => ['nullable','exists:cleaners,id'],
        ]);
        $ticket->update(['cleaner_id' => $data['cleaner_id']]);
        // Sinkronkan ke booking terkait (jika ada)
        if ($ticket->booking_id && $data['cleaner_id']) {
            $booking = $ticket->booking; // eager loaded di show/index, atau akan lazy load di sini
            if ($booking) {
                $booking->cleaner_id = $data['cleaner_id'];
                // Jika status booking masih pending, ubah ke scheduled
                if ($booking->status === 'pending') {
                    $booking->status = 'scheduled';
                }
                $booking->save();
            }
        }
        return redirect()->route('support.show', $ticket)->with('success', 'Tiket telah di-assign.');
    }

    public function addAttachment(Request $request, Ticket $ticket)
    {
        $request->validate([
            'file' => ['required','file','max:5120'], // 5MB
        ]);
        $path = $request->file('file')->store('tickets/'.$ticket->id, 'public');
        $ticket->attachments()->create([
            'uploaded_by' => auth()->id(),
            'original_name' => $request->file('file')->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $request->file('file')->getClientMimeType(),
            'size' => $request->file('file')->getSize(),
        ]);

        return redirect()->route('support.show', $ticket)->with('success', 'Lampiran ditambahkan.');
    }

    public function destroyAttachment(Ticket $ticket, $attachmentId)
    {
        $attachment = $ticket->attachments()->where('id', $attachmentId)->firstOrFail();
        Storage::disk('public')->delete($attachment->path);
        $attachment->delete();
        return redirect()->route('support.show', $ticket)->with('success', 'Lampiran dihapus.');
    }
}
