<?php

/** @var Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Routing\Router;
use Illuminate\Support\Facades\Hash;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/hello', function () {

    return ['msg' => 'Hello from Lumen!'];
});

$router->get('/java-hello', function () {
    $ch = curl_init('http://javaapp:8081/api/hello');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    return response()->json([
        'from_java' => $response,
    ]);
});


$router->post('register', function (Request $request) {
    DB::table('users')->updateOrInsert([
        'login' => $request->input('login'),
    ], [
        'login'      => $request->input('login'),
        'password'   => Hash::make($request->input('password')),
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ]);

    return response()->json(['status' => 'ok']);
});

$router->post('login', function (Request $request) {
    $user = DB::table('users')->where('login', $request->input('login'))->first();

    if (!$user) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Пользователь не найден',
        ], 404);
    }

    $payload = [
        'sub'   => $user->id,
        'login' => $user->login,
        'iat'   => time(),
        'exp'   => time() + 3600,
    ];

    if (!Hash::check($request->input('password'), $user->password)) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Неверный пароль',
        ], 401);
    }

    $jwt = \Firebase\JWT\JWT::class::encode($payload, env('JWT_SECRET'), 'HS256');

    return response()->json(['status' => 'ok', 'token' => $jwt]);
});


$router->get('/jwt-test', function () {
    $payload = ['foo' => 'bar', 'iat' => time()];
    $token = \Firebase\JWT\JWT::encode($payload, 'secret_key', 'HS256');
    return $token;
});
