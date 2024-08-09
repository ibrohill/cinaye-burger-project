<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commande extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_nom', 'client_prenom', 'client_telephone', 
        'burger_id', 'montant', 'etat', 'date_commande', 'date_paiement'
    ];

    protected $casts = [
        'date_commande' => 'datetime',
        'date_paiement' => 'datetime',
    ];

    public function burger()
    {
        return $this->belongsTo(Burger::class);
    }

    public function getFormattedDateCommandeAttribute()
    {
        return $this->date_commande->format('d-m-Y');
    }

    public function setClientTelephoneAttribute($value)
    {
        $this->attributes['client_telephone'] = preg_replace('/\D/', '', $value);
    }

    public function markAsPaid()
    {
        $this->etat = 'Payé';
        $this->date_paiement = now();
        $this->save();
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($commande) {
            app('App\Http\Controllers\StatistiqueController')->updateStatistiques();
        });

        static::updated(function ($commande) {
            // Appel à la mise à jour des statistiques après la mise à jour d'une commande
            app('App\Http\Controllers\StatistiqueController')->updateStatistiques();
        });

        static::deleted(function ($commande) {
            // Appel à la mise à jour des statistiques après la suppression d'une commande
            app('App\Http\Controllers\StatistiqueController')->updateStatistiques();
        });
    }
}
