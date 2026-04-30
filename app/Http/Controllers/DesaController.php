<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use Illuminate\Http\Request;

class DesaController extends Controller
{
    public function index()
    {
        $desas = Desa::latest()->paginate(10);
        return view('desa.index', compact('desas'));
    }

    public function create()
    {
        return view('desa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
        ]);

        Desa::create($request->only(['name', 'kecamatan']));

        return redirect()->route('desa.index')->with('success', 'Data Desa berhasil ditambahkan.');
    }

    public function edit(Desa $desa)
    {
        return view('desa.edit', compact('desa'));
    }

    public function update(Request $request, Desa $desa)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
        ]);

        $desa->update($request->only(['name', 'kecamatan']));

        return redirect()->route('desa.index')->with('success', 'Data Desa berhasil diperbarui.');
    }

    public function destroy(Desa $desa)
    {
        $desa->delete();
        return redirect()->route('desa.index')->with('success', 'Data Desa berhasil dihapus.');
    }
}


