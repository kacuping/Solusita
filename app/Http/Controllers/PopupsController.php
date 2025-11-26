<?php

namespace App\Http\Controllers;

use App\Models\Popup;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PopupsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    public function index()
    {
        $popups = Popup::orderByDesc('updated_at')->paginate(15);
        return view('popups.index', compact('popups'));
    }

    public function create()
    {
        return view('popups.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        if ($request->hasFile('image')) {
            $dest = public_path('uploads/popups');
            if (! is_dir($dest)) { @mkdir($dest, 0775, true); }
            $fn = Str::random(12).'_'.$request->file('image')->getClientOriginalName();
            $request->file('image')->move($dest, $fn);
            $data['image_path'] = 'uploads/popups/'.$fn;
        }
        Popup::create($data);
        return redirect()->route('popups.index')->with('success', 'Popup berhasil dibuat.');
    }

    public function edit(Popup $popup)
    {
        return view('popups.edit', compact('popup'));
    }

    public function update(Request $request, Popup $popup)
    {
        $data = $this->validateData($request);
        if ($request->hasFile('image')) {
            $dest = public_path('uploads/popups');
            if (! is_dir($dest)) { @mkdir($dest, 0775, true); }
            $fn = Str::random(12).'_'.$request->file('image')->getClientOriginalName();
            $request->file('image')->move($dest, $fn);
            $data['image_path'] = 'uploads/popups/'.$fn;
        }
        $popup->update($data);
        return redirect()->route('popups.index')->with('success', 'Popup berhasil diperbarui.');
    }

    public function destroy(Popup $popup)
    {
        $popup->delete();
        return redirect()->route('popups.index')->with('success', 'Popup berhasil dihapus.');
    }

    public function force(Popup $popup)
    {
        $payload = [
            'popup_id' => (int) $popup->id,
            'updated_at' => now()->timestamp,
            'expires_at' => now()->addMinutes(10)->timestamp,
        ];
        $file = storage_path('app/popup_force.json');
        file_put_contents($file, json_encode($payload, JSON_PRETTY_PRINT));
        return redirect()->route('popups.index')->with('success', 'Popup akan ditampilkan sekarang (mengabaikan aturan) selama 10 menit.');
    }

    protected function validateData(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required','string','max:100'],
            'enabled' => ['nullable'],
            'max_per_day' => ['nullable','integer','min:1'],
            'hours' => ['nullable','string'],
            'starts_at' => ['nullable','date'],
            'ends_at' => ['nullable','date','after_or_equal:starts_at'],
            'active' => ['nullable'],
            'image' => ['nullable','image','max:4096'],
        ]);
        $data = [
            'title' => (string) ($validated['title'] ?? ''),
            'enabled' => $request->boolean('enabled'),
            'max_per_day' => (int) ($validated['max_per_day'] ?? 1),
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
            'active' => $request->boolean('active'),
        ];
        $hours = trim((string) ($validated['hours'] ?? ''));
        $hoursArr = [];
        if ($hours !== '') {
            foreach (explode(',', $hours) as $h) {
                $hh = trim($h);
                if ($hh !== '') { $hoursArr[] = $hh; }
            }
        }
        $data['hours'] = $hoursArr;
        return $data;
    }
}
