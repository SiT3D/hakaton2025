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
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
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

    $jwt = JWT::class::encode($payload, env('JWT_SECRET'), 'HS256');

    return response()->json([
        'status' => 'ok', 'token' => $jwt,
        'user'   => ['id' => $user->id, 'login' => $user->login],
    ]);
});


$router->get('/jwt-test', function () {
    $payload = ['foo' => 'bar', 'iat' => time()];
    $token   = JWT::encode($payload, 'secret_key', 'HS256');

    return $token;
});

$router->post('/create-plot', function (Request $request) {
    $coords = json_decode($request->input('coordinates'), true);

    $points = collect($coords)
        ->map(fn($p) => $p[0] . ' ' . $p[1])
        ->join(', ');

    $first = $coords[0];
    $last  = $coords[count($coords) - 1];
    if ($first[0] != $last[0] || $first[1] != $last[1]) {
        $points .= ', ' . $first[0] . ' ' . $first[1];
    }

    $wkt = "POLYGON(($points))";

    $exists = DB::table('plots')->where('cadastral_number', $request->input('cadastral_number'))->first();

    if ($exists) {
        $plotId = $exists->id;
        DB::table('plots')->where('id', $plotId)->update([
            'owner_id'              => $request->input('owner_id'),
            'name'                  => $request->input('name'),
            'sowing_date'           => $request->input('sowing_date'),
            'area'                  => $request->input('area'),
            'land_use'              => $request->input('land_use'),
            'culture'               => $request->input('culture'),
            'culture_description'   => $request->input('culture_description'),
            'livestock'             => $request->input('livestock'),
            'livestock_description' => $request->input('livestock_description'),
            'livestock_count'       => $request->input('livestock_count'),
            'geometry'              => DB::raw("ST_GeomFromText('$wkt', 4326)"),
            'updated_at'            => Carbon::now(),
        ]);
    } else {
        $plotId = DB::table('plots')->insertGetId([
            'owner_id'              => $request->input('owner_id'),
            'name'                  => $request->input('name'),
            'cadastral_number'      => $request->input('cadastral_number'),
            'sowing_date'           => $request->input('sowing_date'),
            'area'                  => $request->input('area'),
            'land_use'              => $request->input('land_use'),
            'culture'               => $request->input('culture'),
            'culture_description'   => $request->input('culture_description'),
            'livestock'             => $request->input('livestock'),
            'livestock_description' => $request->input('livestock_description'),
            'livestock_count'       => $request->input('livestock_count'),
            'geometry'              => DB::raw("ST_GeomFromText('$wkt', 4326)"),
            'created_at'            => Carbon::now(),
            'updated_at'            => Carbon::now(),
        ]);
    }

    if ($request->hasFile('photos')) {
        foreach ($request->file('photos') as $file) {

            $publicPath = base_path('public/uploads/plots');
            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0775, true);
            }

            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move($publicPath, $filename);


            DB::table('plot_photos')->insert([
                'plot_id'    => $plotId,
                'path'       => 'uploads/plots/' . $filename,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }

    return response()->json(['status' => 'ok']);
});


$router->get('/plots', function (Request $request) {
    $owner_id = $request->input('owner_id');

    $plots = DB::table('plots')
        ->where('owner_id', $owner_id)
        ->select(
            'id',
            'name',
            'cadastral_number',
            'sowing_date',
            'area',
            'land_use',
            'culture',
            'culture_description',
            'livestock',
            'livestock_description',
            'livestock_count',
            DB::raw('ST_AsGeoJSON(geometry) as geometry')
        )
        ->get()
        ->map(function ($plot) {
            $plot->geometry = json_decode($plot->geometry, true);

            $plot->photos = DB::table('plot_photos')
                ->where('plot_id', $plot->id)
                ->pluck('path')
                ->map(fn($p) => url($p));

            return $plot;
        });

    return response()->json($plots);
});


$router->get('/plots-all', function (Request $request) {

    $plots = DB::table('plots')
        ->get();

    return response()->json($plots);
});

$router->delete('/plots/{id}', function ($id) {
    $deleted = DB::table('plots')->where('id', $id)->delete();

    return response()->json([
        'status'  => $deleted ? 'ok' : 'error',
        'deleted' => $deleted,
    ]);
});

$router->get('/plots/near/{id}', function ($id, Request $req) {
    $radius = $req->input('radius', 10000); // в метрах

    $plots = DB::select("
        WITH points AS (
          SELECT (dp).geom::geography AS pt
          FROM plots, ST_DumpPoints(geometry::geometry) dp
          WHERE id = ?
        )
        SELECT DISTINCT p.*
        FROM plots p
        JOIN points pt
          ON ST_DWithin(pt, p.geometry, ?)
        WHERE p.id != ?
    ", [$id, $radius, $id]);

    return response()->json($plots);
});


$router->get('/progress/group/{owner_id}', function ($owner_id, Request $req) {
    $radius  = $req->input('radius', 10000); // 10 км
    $maxArea = 10000; // лимит для 100% прогресса

    // все плоты юзера
    $userPlots = DB::table('plots')->where('owner_id', $owner_id)->get();

    $ids    = $userPlots->pluck('id')->all();
    $idList = implode(',', $ids);

    $neighbors = DB::select("
    WITH user_points AS (
      SELECT (dp).geom::geography AS pt
      FROM plots, ST_DumpPoints(geometry::geometry) dp
      WHERE id IN ($idList)
    )
    SELECT DISTINCT p.*
    FROM plots p
    JOIN user_points pt
      ON ST_DWithin(pt, p.geometry, ?)
    WHERE p.id NOT IN ($idList)
", [$radius]);

    // суммируем площадь (у тебя уже хранится в поле area)
    $totalArea = collect($neighbors)->sum('area') + $userPlots->sum('area');

    $progress = min(100, ($totalArea / $maxArea) * 100);

    return response()->json([
        'progress'       => $progress,
        'total_area'     => $totalArea,
        'user_area'      => $userPlots->sum('area'),
        'neighbors_area' => collect($neighbors)->sum('area'),
    ]);
});
