<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;

class PelangganController extends Controller
{
    public function index()
    {
        $pelanggan = Customer::orderBy('created_at', 'desc')->get();
        return view('admin.pelanggan.index', compact('pelanggan'));
    }

    public function show($id)
    {
        $pelanggan = Customer::with('transaksi')->findOrFail($id);
        return view('admin.pelanggan.show', compact('pelanggan'));
    }

    public function destroy($id)
    {
        $pelanggan = Customer::findOrFail($id);
        $pelanggan->delete();

        return redirect()->route('admin.pelanggan.index')
            ->with('success', 'Data pelanggan berhasil dihapus!');
    }
}