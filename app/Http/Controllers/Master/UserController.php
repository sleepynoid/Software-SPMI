<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Master/Users/Index', [
            'users' => User::with('role', 'unitKerja')->get(),
            'roles' => Role::all(),
            'units' => UnitKerja::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id',
            'unit_kerja_id' => 'nullable|exists:unit_kerja,id',
            'jenis_user' => 'required|in:Dosen,Tenaga Kependidikan',
            'nidn' => 'nullable|string',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        User::create($validated);

        return redirect()->back()->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'unit_kerja_id' => 'nullable|exists:unit_kerja,id',
            'jenis_user' => 'required|in:Dosen,Tenaga Kependidikan',
            'nidn' => 'nullable|string',
        ]);

        $user->update($validated);

        return redirect()->back()->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }
}
