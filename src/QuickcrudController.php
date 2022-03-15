<?php
namespace Crankd\Quickcrud;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

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
        // sleep(10);
        $route = $moduleNameLow . '.index';
        while (route($route) == true) {
            return redirect()->route($route)->with('success', $name . ' created successfully!');
        }
    }

}