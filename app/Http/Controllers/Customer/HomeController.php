<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Customer;

class HomeController extends Controller
{
    public function index()
    {
        $alat            = Alat::where('stok_tersedia', '>', 0)->latest()->take(8)->get();
        $alatByKategori  = Alat::all()->groupBy('kategori');
        $totalAlat       = Alat::where('stok_tersedia', '>', 0)->count();
        $totalCustomer   = Customer::count();

        return view('customer.home', compact(
            'alat',
            'alatByKategori',
            'totalAlat',
            'totalCustomer'
        ));
    }
}