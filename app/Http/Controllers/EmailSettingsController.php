<?php

namespace App\Http\Controllers;

use App\Models\Email_settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class EmailSettingsController extends Controller
{
    public function index()
    {
        $settings=Email_settings::first();
        if($settings && $settings->password){
            try {
            $settings->password=Crypt::decryptString($settings->password);
            }catch (\Exception $e) {
            $settings->password = '[Mot de passe corrompu]';
            }
        }
        return view('admin.email_settings.index',compact('settings'));
    }

    
//Dans ton contrôleur, tu as déjà la logique pour :
//Créer si aucun enregistrement n’existe
//Modifier sinon
    public function update(Request $request)
    {
       $rules = [
            'protocole'    => 'required',
            'host'         => 'required',
            'port'         => 'required|integer',
            'username'     => 'required',
            'encryption'   => 'nullable',
            'from_address' => 'required|email',
            'from_name'    => 'required',
        ];

        $settings = Email_settings::first();
        if (!$settings || $request->filled('password')) {
            $rules['password'] = 'required';
        }

        $data = $request->validate($rules);

        if ($request->filled('password')) {
            $data['password'] = Crypt::encryptString($data['password']);
        } elseif ($settings) {
            $data['password'] = $settings->password;
        }

        if ($settings) {
            $settings->update($data);
        } else {
            Email_settings::create($data);
        }

        return back()->with('success', 'Paramètres email mis à jour.');
    }
}
