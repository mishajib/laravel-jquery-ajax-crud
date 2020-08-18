<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

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
    $todos = \App\Todo::latest()->get();
    return view('home', compact('todos'));
});

//--CREATE a link--//
Route::post('/todos', function (Request $request) {
    $todo = \App\Todo::create($request->all());
    return Response::json($todo);
});

//--GET LINK TO EDIT--//
Route::get('/todos/{todo_id?}', function ($todo_id) {
    $todo = \App\Todo::find($todo_id);
    return Response::json($todo);
});

//--UPDATE a link--//
Route::put('/todos/{todo_id?}', function (Request $request, $todo_id = null) {
    $todo        = \App\Todo::find($todo_id);
    $todo->title = $request->title;
    $todo->desc  = $request->desc;
    $todo->save();
    return Response::json($todo);
});

//--DELETE a link--//
Route::delete('/todos/{todo_id?}', function ($todo_id) {
    $todo = \App\Todo::destroy($todo_id);
    return Response::json($todo);
});
