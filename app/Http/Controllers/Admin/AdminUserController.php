<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'user');

        if ($request->filled('cari')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->cari.'%')
                    ->orWhere('email', 'like', '%'.$request->cari.'%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function subscription(Request $request, User $user)
    {
        $data = $request->validate([
            'durasi' => ['required', 'integer', 'min:1', 'max:36'],
        ]);

        $user->update([
            'subscribed_until' => Carbon::now()->addMonths((int) $data['durasi']),
        ]);

        return back()->with('success', "Langganan {$user->name} diaktifkan selama {$data['durasi']} bulan.");
    }

    public function cancelSubscription(User $user)
    {
        $user->update(['subscribed_until' => null]);

        return back()->with('success', 'Langganan dibatalkan.');
    }
}
