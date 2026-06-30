<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksi = Transaksi::where('customer_id', Auth::guard('web')->id())
            ->with('detailTransaksi.alat')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('customer.transaksi.index', compact('transaksi'));
    }

    public function show($id)
    {
        $transaksi = Transaksi::where('customer_id', Auth::guard('web')->id())
            ->with('detailTransaksi.alat', 'denda')
            ->findOrFail($id);

        return view('customer.transaksi.show', compact('transaksi'));
    }
}