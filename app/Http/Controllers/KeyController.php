<?php

namespace App\Http\Controllers;

use App\Models\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeyController extends Controller
{
    public function index(Request $request)
    {
        $query = Key::with('creator')->latest();

        if ($request->filled('search')) {
            $query->where('key', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $keys = $query->paginate(20)->withQueryString();

        return view('keys.index', compact('keys'));
    }

    public function create()
    {
        return view('keys.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'quantity'   => 'required|integer|min:1|max:100',
            'duration'   => 'nullable|integer|min:1',
            'max_uses'   => 'required|integer|min:1|max:100',
            'note'       => 'nullable|string|max:500',
        ]);

        $quantity = (int) $request->quantity;
        $duration = $request->duration ? (int) $request->duration : null;
        $maxUses  = (int) $request->max_uses;
        $note     = $request->note;

        for ($i = 0; $i < $quantity; $i++) {
            $keyValue = Key::generateKey();

            while (Key::where('key', $keyValue)->exists()) {
                $keyValue = Key::generateKey();
            }

            $expiresAt = $duration ? now()->addDays($duration) : null;

            Key::create([
                'key'        => $keyValue,
                'status'     => 'active',
                'duration'   => $duration,
                'expires_at' => $expiresAt,
                'max_uses'   => $maxUses,
                'used_count' => 0,
                'note'       => $note,
                'created_by' => Auth::id(),
            ]);
        }

        return redirect()
            ->route('keys.index')
            ->with('success', $quantity . ' key(s) generated successfully!');
    }

    public function ban(Key $key)
    {
        $key->update(['status' => 'banned']);
        return back()->with('success', 'Key banned successfully.');
    }

    public function destroy(Key $key)
    {
        $key->delete();
        return back()->with('success', 'Key deleted successfully.');
    }
}
