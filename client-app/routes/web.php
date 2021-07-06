<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});



// To use PKCE Way
Route::get('/redirect', function (Request $request) {
    $request->session()->put('state', $state = Str::random(40));

    $request->session()->put(
        'code_verifier',
        $code_verifier = Str::random(128)
    );

    $codeChallenge = strtr(rtrim(
        base64_encode(hash('sha256', $code_verifier, true)),
        '='
    ), '+/', '-_');

    $query = http_build_query([
        'client_id' => '4',
        'redirect_uri' => 'http://localhost:8001/callback',
        'response_type' => 'code',
        'scope' => '',
        'state' => $state,
        'code_challenge' => $codeChallenge,
        'code_challenge_method' => 'S256',
    ]);

    return redirect('http://localhost:8000/oauth/authorize?' . $query);
});

// To use PKCE Way
Route::get('/callback', function (Request $request) {
    $state = $request->session()->get('state');

    $codeVerifier = $request->session()->get('code_verifier');

    throw_unless(
        strlen($state) > 0 && $state === $request->get('state'),
        InvalidArgumentException::class
    );

    $response = Http::asForm()->post('http://localhost:8000/oauth/token', [
        'grant_type' => 'authorization_code',
        'client_id' => '4',
        'redirect_uri' => 'http://localhost:8001/callback',
        'code_verifier' => $codeVerifier,
        'code' => $request->code,
    ]);

    return view('success', ['token' => $response->json()]);
});
