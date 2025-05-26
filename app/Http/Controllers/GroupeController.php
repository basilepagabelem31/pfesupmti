<?php

namespace App\Http\Controllers;

use App\Models\Groupe;
use Illuminate\Http\Request;

class GroupeController extends Controller
{
    public function index()
    {
        $groupes = Groupe::all();
        return view('groupes.index', compact('groupes'));
    }

    public function create()
    {
        return view('groupes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:groupes',
            'nom' => 'required',
            'date' => 'required|date'
        ]);

        Groupe::create($request->all());
        return redirect()->route('groupes.index')->with('success', 'Groupe ajouté.');
    }

    public function edit(Groupe $groupe)
    {
        return view('groupes.edit', compact('groupe'));
    }

    public function update(Request $request, Groupe $groupe)
    {
        $request->validate([
            'code' => 'required|unique:groupes,code,' . $groupe->id,
            'nom' => 'required',
            'date' => 'required|date'
        ]);

        $groupe->update($request->all());
        return redirect()->route('groupes.index')->with('success', 'Groupe mis à jour.');
    }

    public function destroy(Groupe $groupe)
    {
        $groupe->delete();
        return redirect()->route('groupes.index')->with('success', 'Groupe supprimé.');
    }
}

