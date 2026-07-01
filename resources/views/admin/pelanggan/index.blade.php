@extends('layouts.admin')

@section('title', 'Pelanggan')
@section('page-title', 'Data Pelanggan')

@section('content')

    @if(session('success'))
        <div class="mb-6 bg-[#e8f5f0] text-[#085041] text-sm font-medium px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-[#00372c] text-base">Semua Pelanggan</h2>
            <span class="text-xs text-gray-400 font-semibold">{{ $pelanggan->count() }} pelanggan terdaftar</span>
        </div>

        @if($pelanggan->isEmpty())
            <div class="px-6 py-16 text-center text-gray-400 text-sm">
                <span class="material-symbols-outlined text-4xl block mb-3 opacity-30">group</span>
                Belum ada pelanggan terdaftar.
            </div>
        @else
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider">Pelanggan</th>
                        <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider">Kontak</th>
                        <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider">Metode Daftar</th>
                        <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider">Terdaftar</th>
                        <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($pelanggan as $p)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $p->avatar_url }}" alt="{{ $p->nama_lengkap }}" class="w-9 h-9 rounded-full object-cover border border-gray-100">
                                <div>
                                    <p class="font-semibold text-[#00372c]">{{ $p->nama_lengkap }}</p>
                                    <p class="text-gray-400 text-xs">{{ $p->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $p->no_telp ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @if($p->google_id)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-50 text-blue-600">
                                    <span class="material-symbols-outlined text-sm">account_circle</span>
                                    Google
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-500">
                                    <span class="material-symbols-outlined text-sm">email</span>
                                    Email
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-400 text-xs">{{ $p->created_at->translatedFormat('d M Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.pelanggan.show', $p->id) }}"
                                    class="p-1.5 text-gray-400 hover:text-[#085041] hover:bg-[#e8f5f0] rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-base">visibility</span>
                                </a>
                                <form method="POST" action="{{ route('admin.pelanggan.destroy', $p->id) }}" onsubmit="return confirm('Yakin hapus pelanggan {{ $p->nama_lengkap }}? Semua data terkait akan ikut terhapus.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-base">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

@endsection