<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PropertyFormRequest;
use App\Models\Option;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // <-- NOUVEAU : Ajouté ceci pour utiliser Auth::id()

class PropertyController extends Controller
{
    
    public function index()
    { 
        
        return view('admin.properties.index', [
            'properties'=> Property::orderBy('created_at','desc')->paginate(25)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $property = new Property();

        $property->fill([
            'surface'=>40,
            'rooms'=>3,
            'bedrooms'=>1,
            'floor'=>0,
            'city'=> 'Toulon',
            'postal_code'=> '83000',
            'sold'=> false,
        ]);
        
        return view('admin.properties.form',[
            'property'=> $property,
            'options'=> Option::pluck('name', 'id'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PropertyFormRequest $request)
    { 
        // 1. Extrayez les données validées, y compris celles du formulaire
        $data = $this->extractData(new Property(), $request); 

        // 2. Assurez-vous que le user_id de l'utilisateur actuellement connecté est ajouté aux données AVANT la création.
        // C'est crucial car la colonne 'user_id' ne peut pas être nulle.
        $data['user_id'] = Auth::id(); 

        // 3. Créez la propriété en une seule fois dans la base de données avec toutes les données, y compris le user_id.
        $property = Property::create($data);

        // 4. Synchronisez les options associées à la propriété.
        // On vérifie si des options ont été soumises pour éviter une erreur si le champ est absent.
        if ($request->has('options')) {
            $property->options()->sync($request->validated('options'));
        } else {
            // Si aucune option n'est sélectionnée, on désynchronise toutes les options précédentes.
            $property->options()->sync([]); 
        }

        // 5. Redirigez l'utilisateur vers la liste des propriétés avec un message de succès.
        return redirect()->route('admin.property.index')->with('success', 'Le bien a bien été enregistré');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Property $property)
    {
        return view('admin.properties.form', [
            'property'=> $property,
            'options'=> Option::pluck('name', 'id'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PropertyFormRequest $request, Property $property)
    { 
        // Met à jour la propriété avec les données validées et la gestion de l'image.
        $property->update($this->extractData($property, $request));
        // Synchronise les options pour la propriété mise à jour.
        $property->options()->sync($request->validated('options'));
        return redirect()->route('admin.property.index')->with('success', 'Le bien a bien été modifié');
    }

    /**
     * Méthode privée pour extraire et préparer les données du formulaire, y compris l'upload d'image.
     */
    private function extractData (Property $property, PropertyFormRequest $request): array
    {
        $data = $request->validated();
        /** @var UploadedFile|null $image */
        $image = $request->validated('image');
        
        // Si aucune nouvelle image n'est téléchargée ou s'il y a une erreur
        if($image === null || $image->getError()){
            // On supprime l'entrée 'image' des données pour ne pas écraser l'ancienne si elle n'est pas modifiée.
            // Si vous voulez conserver l'ancienne image si le champ est vide, il faut l'enlever de $data.
            unset($data['image']); 
            return $data;
        }

        // Si une ancienne image existe, la supprimer du stockage public
        if($property->image){
            Storage::disk('public')->delete($property->image);
        }
        
        // Stocke la nouvelle image dans le dossier 'blog' du disque public
        $data['image'] = $image->store('blog', 'public');
        return $data;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property)
    {   
        // Supprime l'image associée si elle existe avant de supprimer la propriété
        if ($property->image) {
            Storage::disk('public')->delete($property->image);
        }
        $property->delete();
        return redirect()->route('admin.property.index')->with('success', 'Le bien a bien été supprimé');
    }
    
    // Note: La méthode show a été ajoutée pour la partie admin, mais elle n'est pas "except(['show'])" dans les routes.
    // Cela signifie que 'admin/property/{id}' est accessible, si c'est ce que vous voulez.
    public function show(Property $property)
    {
        return view('admin.properties.show', compact('property'));
    }
}