<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model; // Il est déjà là, c'est bien.
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\HasMany; // Pas nécessaire pour cette correction, mais ok si vous l'utilisez ailleurs.

class Property extends Model
{
    use HasFactory;

    protected $fillable =[
        'title',
        'description',
        'surface',
        'rooms',
        'bedrooms',
        'floor',
        'city',
        'price',
        'address',
        'postal_code',
        'sold',
        'image',
        'user_id' // <--- AJOUTEZ CETTE LIGNE ! C'est la clé.
    ];

    // Vous pourriez aussi vouloir ajouter un 'casts' pour 'sold' pour qu'il soit toujours un booléen
    protected $casts = [
        'sold' => 'boolean',
    ];

    public function options(): BelongsToMany
    {
        return $this->belongsToMany(Option::class);
    }

    public function getSlug(): string
    {
        return Str::slug($this->title);
    }

    public function imageUrl (): string
    {
        // Assurez-vous que $this->image n'est pas null avant d'appeler Storage::url
        return $this->image ? Storage::url($this->image) : '/path/to/default/image.jpg'; // Ou un chemin d'image par défaut
    }

    // NOUVEAU : Ajoutez la relation avec le modèle User
    // Cela permet à une propriété de "appartenir à" un utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}