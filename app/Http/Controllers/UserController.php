<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * UserController: A controller than handles requests related to the user.
 */
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): Collection
    {
        return User::self()->paginate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'unique:users'],
            'name' => ['required', 'string', 'min:3'],
            'password' => ['required',  'confirmed', Password::min(8)],
        ]);

        return $this->register($request);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        // return auth()->user()->is_admin;
        $this->authorize('view', $user);

        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'name' => ['required', 'string', 'min:3'],
        ]);
        $user->fill(['name' => $request->name])->save();

        return $user;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $email = $user->email;
        $user->delete();

        return "User ({$email}) is deleted successfully!";
    }

    /**
     * Login User.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! auth()->attempt($credentials)) {
            abort(401, 'Wrong credentials. Recheck the email and password.');

        }
        $token = $request->user()->createToken('User');

        return response()->json(['token' => $token->plainTextToken]);

    }

    /**
     * Make a user admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function makeAdmin(Request $request)
    {

        $request->validate([
            'user_id' => ['required', 'numeric'],
        ]);
        $user = User::where('id', $request->user_id)->first();
        if (! $user) {
            abort(404, 'User not found!');
        }
        $user->is_admin = 1;
        $user->save();

        return response()->json("User ({$user->email}) is promoted to admin successfully");
    }

    /**
     * Search for a user using an email or name or both.
     * The function requires at least one search parameter
     * If both are added, they will be anded.
     *
     * @return User the registered user.
     */
    public function register(Request $request): User
    {
        $request->validate([
            'email' => ['required', 'unique:users'],
            'name' => ['required', 'string', 'min:3'],
            'password' => ['required',  'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return $user;
    }

    /**
     * Search for a user using an email or name or both.
     *
     * The function requires at least one search parameter
     * If both are added, they will be anded.
     *
     * @return Collection of User.
     */
    public function search(Request $request)
    {
        $request->validate([
            'email' => ['required_without:name'],
            'name' => ['required_without:email'],
        ]);
        $userQuery = User::query();
        if ($request->email) {
            $userQuery->where('email', 'like', '%' . $request->email . '%');
        }
        if ($request->name) {
            $userQuery->where('name', 'like', '%' . $request->name . '%');
        }

        return $userQuery->get();
    }
}
