<?php
namespace Crankd\Quickcrud;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

class QuickcrudController extends Controller
{
    //
    public function create()
    {
        $compact = [];
        return view('quickcrud::create', compact($compact));

    }

    public function store(Request $request)
    {
        $name = $request->name;
        $moduleNameLow = strtolower($name);
        Artisan::call('make:module ' . $name);
        $route = $moduleNameLow . '.index';
        sleep(3);
        return redirect()->route('quickcrud.redirect', $route);
    }

    public function redirect($route)
    {
        if (Route::has($route)) {
            return redirect()->route($route)->with('success', 'Created successfully!');
        }
        header("Refresh:0");
    }

}