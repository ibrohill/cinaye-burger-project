<?php
namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Statistique;
use Illuminate\Http\Request;

class StatistiqueController extends Controller
{
    public function getCommandesEnCoursDuJour()
    {
        $commandesEnCours = Commande::whereDate('date_commande', today())
                                    ->where('etat', 'En cours')
                                    ->count();
        return response()->json(['commandesEnCours' => $commandesEnCours]);
    }
    
    public function getCommandesValideesDuJour()
    {
        $commandesValidees = Commande::whereDate('date_commande', today())
                                     ->where('etat', 'Terminé')
                                     ->get(); // Utiliser get() pour renvoyer les détails des commandes
        return response()->json(['commandesValidees' => $commandesValidees]);
    }

    public function getRecettesJournalieres()
    {
        $recettesJournalieres = Commande::whereDate('date_commande', today())
                                       ->where('etat', 'Terminé')
                                       ->sum('montant');
        return response()->json(['recettesJournalieres' => $recettesJournalieres]);
    }

    public function getCommandesAnnuleesDuJour()
    {
        $commandesAnnulees = Commande::whereDate('date_commande', today())
                                     ->where('etat', 'Annulé')
                                     ->get(); // Utiliser get() pour renvoyer les détails des commandes
        return response()->json(['commandesAnnulees' => $commandesAnnulees]);
    }

    public function getTotaux()
    {
        $totalCommandes = Commande::count();
        $totalCommandesEnCours = Commande::where('etat', 'En cours')->count();
        $totalCommandesValidees = Commande::where('etat', 'Terminé')->count();
        $totalCommandesAnnulees = Commande::where('etat', 'Annulé')->count();
        
        return response()->json([
            'totalCommandes' => $totalCommandes,
            'totalCommandesEnCours' => $totalCommandesEnCours,
            'totalCommandesValidees' => $totalCommandesValidees,
            'totalCommandesAnnulees' => $totalCommandesAnnulees
        ]);
    }

    public function updateStatistiques()
    {
        $date = today();

        Statistique::updateOrCreate(
            ['date' => $date],
            [
                'total_commandes' => Commande::whereDate('date_commande', $date)->count(),
                'total_commandes_en_cours' => Commande::whereDate('date_commande', $date)->where('etat', 'En cours')->count(),
                'total_commandes_validees' => Commande::whereDate('date_commande', $date)->where('etat', 'Terminé')->count(),
                'total_commandes_annulees' => Commande::whereDate('date_commande', $date)->where('etat', 'Annulé')->count(),
                'recettes_journalieres' => Commande::whereDate('date_commande', $date)->where('etat', 'Terminé')->sum('montant')
            ]
        );
    }
}
