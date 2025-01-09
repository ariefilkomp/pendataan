<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function ssoCallback(Request $request)
    {
        if ($request->input('code')) {

            if (empty($request->input('state')) || $request->input('state') != session('state')) {
                return view('auth.sso-state-error');
            }

            $params = array(
                'grant_type' => 'authorization_code',
                'client_id' => env('SSO_CLIENT_ID', 'paklay'),
                'client_secret' => env('SSO_CLIENT_SECRET', 'password'),
                'redirect_uri' => url('/'),
                'code' => $request->input('code')
            );

            $response = Http::accept('application/json')
                ->withOptions(["verify"=>false])
                ->post(env('SSO_TOKEN_URL', 'http://sso.test/api/oauth/access_token'), $params);

            if ($response->successful()) {
                $access_token = $response->json()['access_token'];
                $res = Http::accept('application/json')->withOptions(["verify"=>false])->withToken($access_token)->get(env('SSO_USER_URL', 'https://sso.karanganyarkab.go.id/api/user'));

                if ($res->successful()) {
                    $user = User::where('email', $res->json()['email'])->first();
                    if ($user) {
                        Auth::login($user);
                        return redirect()->intended(route('dashboard', absolute: false));
                    } else {
                        $user = User::create([
                            'name' => $res->json()['nama'],
                            'email' => $res->json()['email'],
                            'email_verified_at' => now(),
                            'password' => bcrypt(env('DEFAULT_PASSWORD', 'ssodebugKMZWAY87AA')),
                        ]);

                        $user->assignRole('umum');

                        Auth::login($user);
                        return redirect()->intended(route('dashboard', absolute: false));
                    }
                } else {
                    return view('auth.sso-state-error');
                }
            } else {
                return view('auth.sso-state-error');
            }
        }
    }

    public function ssoLogin(Request $request) {
        $state = bin2hex(random_bytes(16));
        session(['state' => $state]);
        $params = array(
            'response_type' => 'code',
            'client_id' => env('SSO_CLIENT_ID','paklay'),
            'redirect_uri' => env('SSO_CALLBACK_URI', 'https://newpaklay.test/sso/callback'),
            'scopes' => 'user',
            'state' => $state
        );
        
        $authorizeUrl = env('SSO_AUTHORIZE_URL', 'https://sso.karanganyarkab.go.id/login/oauth/authorize').'?'.http_build_query($params);
        return redirect($authorizeUrl);
    }
}
