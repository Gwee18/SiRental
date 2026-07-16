<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransaksiController extends Controller
{
    public function index(Request $request): View
    {
        $transaksi = $this->customer($request)
            ->transaksi()
            ->with('detailTransaksi.alat')
            ->latest()
            ->get();

        return view('customer.transaksi.index', compact('transaksi'));
    }

    public function show(Request $request, int $id): View
    {
        $transaksi = $this->customer($request)
            ->transaksi()
            ->with(['detailTransaksi.alat', 'denda'])
            ->findOrFail($id);

        return view('customer.transaksi.show', compact('transaksi'));
    }

    private function customer(Request $request): Customer
    {
        return $request->user('web');
    }
}
