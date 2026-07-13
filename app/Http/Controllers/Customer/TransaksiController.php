<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    public function index()
    {
        $customerId = Auth::guard('web')->id();

        $transaksi = Transaksi::where('customer_id', $customerId)
            ->with('detailTransaksi.alat')
            ->latest()
            ->get();

        return view('customer.transaksi.index', compact('transaksi'));
    }

    public function show($id)
    {
        $customerId = Auth::guard('web')->id();

        $transaksi = Transaksi::where('customer_id', $customerId)
            ->with([
                'detailTransaksi.alat',
                'denda',
            ])
            ->findOrFail($id);

        return view('customer.transaksi.show', compact('transaksi'));
    }
}
