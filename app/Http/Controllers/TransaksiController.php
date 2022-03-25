<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestStoreTransaksi;
use App\Models\Aktifitas;
use App\Models\Menu;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transaksis = Transaksi::latest()->paginate(10);
        return view('dashboard.transaksis.index', compact('transaksis'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $menus = Menu::where('stok', '>', '0')->get();
        return view('dashboard.transaksis.create', compact('menus'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RequestStoreTransaksi $request)
    {
        $validated = $request->validated();
        $menu = Menu::findOrFail($validated['menu']);
        if ($menu->stok < (int) $validated['jumlah']) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi');
        }
        $transaksi = Transaksi::create([
            'nama_pelanggan' => $validated['nama_pelanggan'],
            'menu_id' => $menu->id,
            'jumlah' => $validated['jumlah'],
            'total_harga' => $menu->harga * (int) $validated['jumlah'],
            'kasir_id' => auth()->user()->id,
            'tanggal' => date('Y-m-d'),
        ]);
        $menu->update([
            'stok' => $menu->stok - (int) $validated['jumlah'],
        ]);

        Aktifitas::insert([
            'deskripsi' => 'Melakukan transaksi dengan nama pelanggan ' . Str::title($transaksi->nama_pelanggan),
            'pegawai_id' => auth()->user()->id,
            'created_at' => now(),
        ]);
        return redirect()->route('transaksis.index')->with('success', 'Transaksi berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function laporan()
    {
        return view('dashboard.transaksis.laporan');

        // klo ada garis bawah gitu, tgl klik ctrl + klik. tar dia ngarah ke view nya
    }

    public function dataLaporan(Request $request)
    {
        if (isset($request->tanggal_awal) && isset($request->tanggal_akhir)) {
            $transaksis = Transaksi::whereBetween('tanggal', [$request->tanggal_awal, date($request->tanggal_akhir . ' 23:59:59')])
                ->get()->sortByDesc('id');
        } else {
            $transaksis = Transaksi::latest()->get();
        }
        return view('dashboard.transaksis.data-laporan', compact('transaksis'))->render();
    }
}
