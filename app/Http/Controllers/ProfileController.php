<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Ynotz\EasyAdmin\Traits\HasMVConnector;
use App\Http\Requests\ProfileUpdateRequest;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class ProfileController extends SmartController
{
    use HasMVConnector;

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request)
    {
        return $this->buildResponse('pages.profile', [
            'user' => $request->user(),
        ]);
    }

    public function save(Request $request){
        $request->validate([
            'name'=>'required|min:4'
        ]);
        /**
         * @var User
         */
        $user = User::find(auth()->user()->id);
        $user->name = $request->name;
        $user->save();
        $user->addOneMediaFromEAInput('user_picture', $request->input('user_picture'));
        return response()->json(['success'=>true, 'message'=>'Profile updated Successfully', 200]);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
