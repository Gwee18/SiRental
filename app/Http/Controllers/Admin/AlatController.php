<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use Illuminate\Http\Request;

class AlatController extends Controller
{
    public function index()
    {
        $alat = Alat::orderBy('created_at', 'desc')->get();
        return view('admin.alat.index', compact('alat'));
    }

    public function create()
    {
        return view('admin.alat.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_alat'    => 'required|string|max:255',
            'kategori'     => 'required|string|max:255',
            'stok_total'   => 'required|integer|min:1',
            'harga_per_hari' => 'required|numeric|min:0',
            'kondisi'      => 'required|string',
            'deskripsi'    => 'nullable|string',
            'foto_alat'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data                  = $request->all();
        $data['stok_tersedia'] = $request->stok_total;

        if ($request->hasFile('foto_alat')) {
            $data['foto_alat'] = $request->file('foto_alat')->store('foto-alat', 'public');
        }

        Alat::create($data);

        return redirect()->route('admin.alat.index')
            ->with('success', 'Alat berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $alat = Alat::findOrFail($id);
        return view('admin.alat.form', compact('alat'));
    }

    public function update(Request $request, $id)
    {
        $alat = Alat::findOrFail($id);

        $request->validate([
            'nama_alat'      => 'required|string|max:255',
            'kategori'       => 'required|string|max:255',
            'stok_total'     => 'required|integer|min:1',
            'harga_per_hari' => 'required|numeric|min:0',
            'kondisi'        => 'required|string',
            'deskripsi'      => 'nullable|string',
            'foto_alat'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto_alat')) {
            $data['foto_alat'] = $request->file('foto_alat')->store('foto-alat', 'public');
        }

        $alat->update($data);

        return redirect()->route('admin.alat.index')
            ->with('success', 'Alat berhasil diupdate!');
    }

    public function destroy($id)
    {
        $alat = Alat::findOrFail($id);
        $alat->delete();

        return redirect()->route('admin.alat.index')
            ->with('success', 'Alat berhasil dihapus!');
    }
}