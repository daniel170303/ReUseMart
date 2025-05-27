@extends('layouts.penitip')

@section('content')
    <h2 class="text-xl font-semibold mb-4">Barang Titipan Anda</h2>

    @if ($barangTitipan->isEmpty())
        <div class="p-4 bg-yellow-100 text-yellow-800 rounded">
            Belum ada barang yang dititipkan.
        </div>
    @else
        <table class="w-full table-auto bg-white shadow rounded-lg overflow-hidden">
            <thead class="bg-gray-200 text-gray-700">
                <tr>
                    <th class="px-4 py-2 text-left">No</th>
                    <th class="px-4 py-2 text-left">Nama Barang</th>
                    <th class="px-4 py-2 text-left">Kategori</th>
                    <th class="px-4 py-2 text-left">Jumlah</th>
                    <th class="px-4 py-2 text-left">Tanggal Titip</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($barangTitipan as $index => $barang)
                    <tr class="border-b hover:bg-gray-100">
                        <td class="px-4 py-2">{{ $index + 1 }}</td>
                        <td class="px-4 py-2">{{ $barang->nama_barang }}</td>
                        <td class="px-4 py-2">{{ $barang->kategori }}</td>
                        <td class="px-4 py-2">{{ $barang->jumlah }}</td>
                        <td class="px-4 py-2">{{ $barang->created_at->format('d-m-Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
