<?php

namespace App\Http\Controllers;

use App\Models\Email_template;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $templates= Email_template::all();
        return view('admin.email_templates.index',compact('templates'));
    }

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $request->validate([
            'type' => 'required|unique:email_templates,type',
            'subject' => 'required',
            'content' => 'required',
            'description' => 'nullable'
        ]);
        Email_template::create($request->all());
        return redirect()->route('admin.email_templates.index')->with('success', 'Modèle créé avec succès.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Email_template $email_template)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
   

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $template = Email_template::findOrFail($id);
        $request->validate([
            'type' => 'required|unique:email_templates,type,' . $id,
            'subject' => 'required',
            'content' => 'required',
            'description' => 'nullable'
        ]);
        $template->update($request->all());
        return redirect()->route('admin.email_templates.index')->with('success', 'Modèle mis à jour.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Email_template $email_template)
    {
        //
    }
}
