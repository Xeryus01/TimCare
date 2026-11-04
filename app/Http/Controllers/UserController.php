<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:Admin');
    }

    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50]) ? (int)$perPage : 10;

        $search = trim((string) $request->input('search', ''));

        $users = User::with('roles', 'teams')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%");
                });
            });

        $sortable = [
            'name' => 'name',
            'email' => 'email',
            'phone_number' => 'phone_number',
            'created_at' => 'created_at',
        ];
        $sort = $request->input('sort');
        $direction = $request->input('direction', 'asc') === 'desc' ? 'desc' : 'asc';

        if (array_key_exists($sort, $sortable)) {
            $users->orderBy($sortable[$sort], $direction);
        } else {
            $users->orderBy('name');
        }

        $users = $users->paginate($perPage)->appends(request()->query());

        return view('users.index', compact('users', 'search', 'sort', 'direction'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::all();

        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name, 'email' => $request->email,
                'phone_number' => $request->phone_number, 'password' => Hash::make($request->password),
            ]);
            $user->assignRole($request->role);
        });

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load('roles', 'teams.leader');
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $roles = Role::all();

        $user->load('teams');
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone_number' => 'nullable|string|max:20',
            'role' => 'required|string|exists:roles,name',
        ]);

        DB::transaction(function () use ($request, $user) {
            $user->update(['name'=>$request->name, 'email'=>$request->email, 'phone_number'=>$request->phone_number]);
            $user->syncRoles([$request->role]);
        });

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }


        // User memakai soft delete. Pertahankan tiket, reservasi, lampiran, dan
        // audit trail agar riwayat layanan tidak hilang.
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Show the form for editing user password.
     */
    public function editPassword(User $user)
    {
        return view('users.change-password', compact('user'));
    }

    /**
     * Update the specified user password in storage.
     */
    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.show', $user)
            ->with('success', 'Password user berhasil diperbarui.');
    }
}