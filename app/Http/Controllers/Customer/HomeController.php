<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Customer;

class HomeController extends Controller
{
    public function index()
    {
        $alat = Alat::tersedia()
            ->latest()
            ->take(4)
            ->get();

        $alatByKategori = Alat::aktif()
            ->latest()
            ->get()
            ->groupBy('kategori');

        $totalAlat = Alat::tersedia()->count();
        $totalCustomer = Customer::count();

        return view('customer.home', compact(
            'alat',
            'alatByKategori',
            'totalAlat',
            'totalCustomer'
        ));
    }

    public function katalog()
    {
        $alat = Alat::tersedia()
            ->latest()
            ->paginate(12);

        return view('customer.katalog.index', compact('alat'));
    }
}
