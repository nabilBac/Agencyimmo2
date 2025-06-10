<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    public function login()
    {
        // Supprimez la logique de création automatique d'utilisateur ici.
        // Cette partie est mieux gérée par un "seeder" pour le développement.
        // Laisser cette logique ici pourrait involontairement créer un utilisateur
        // à chaque fois que la page de connexion est visitée et l'utilisateur n'existe pas.
        /*
        $existingUser = User::where('email', 'nabil@bachir.fr')->first();
        if (!$existingUser) {
            User::create([
                'name' => 'Nabil',
                'email' => 'nabil@bachir.fr',
                'password' => Hash::make('0000'),
            ]);
        }
        */

        return view('auth.login');
    }

    public function doLogin(LoginRequest $request)
    {
        $credentials = $request->validated();
    
        if(Auth::attempt($credentials)){
            $request->session()->regenerate();
            // Gardez cette redirection vers l'admin pour le login, car on suppose
            // que si un utilisateur se connecte via cette page, il est censé
            // être un admin ou aura ses droits vérifiés par un middleware.
            return redirect()->intended(route('admin.property.index'));
        }
    
        return back()->withErrors([
            'email'=> 'identifiants incorrect'
        ])->onlyInput('email');
    }

    public function logout(){
        Auth::logout();
        return to_route('login')->with('success', 'Vous êtes maintenant déconnecté');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            // IMPORTANT : Si vous avez une colonne 'is_admin' ou 'role' dans votre table `users`,
            // assurez-vous qu'elle est définie à `false` ou `'user'` par défaut ici
            // pour que les nouveaux inscrits ne soient PAS des administrateurs.
            // Par exemple : 'is_admin' => false,
        ]);

        // Authentifie automatiquement l'utilisateur nouvellement inscrit.
        Auth::login($user);

        // MODIFIÉ : Redirigez l'utilisateur vers votre page d'accueil publique
        // 'home' est un nom de route courant pour la page d'accueil (Route::get('/', ...)->name('home');)
        return redirect()->route('home')->with('success', 'Votre compte a été créé avec succès !');
        // Si vous n'avez pas de route nommée 'home', vous pouvez utiliser :
        // return redirect('/')->with('success', 'Votre compte a été créé avec succès !');
    }
}