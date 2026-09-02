<?php

namespace App\Http\Controllers;

use App\Models\Progres;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $user->load('progres.game.mapel');

        $subscriptions = Subscription::where('user_id', $user->id)
            ->with('plan')
            ->latest()
            ->limit(10)
            ->get();

        $totalBenar = Progres::where('user_id', $user->id)->sum('benar');
        $totalSalah = Progres::where('user_id', $user->id)->sum('salah');
        $totalGame = Progres::where('user_id', $user->id)->count();

        return view('profile', compact('user', 'subscriptions', 'totalBenar', 'totalSalah', 'totalGame'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $update = ['name' => $data['name'], 'email' => $data['email']];
        if (! empty($data['password'])) {
            $update['password'] = Hash::make($data['password']);
        }

        $user->update($update);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
