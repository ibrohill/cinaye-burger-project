<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Statistique; // Ajouté pour les statistiques
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Mail\CommandeMailable;
use App\Mail\CommandeStatusChanged;

class CommandeController extends Controller
{
    public function index()
    {
        $commandes = Commande::with('burger')->get();
        return response()->json($commandes, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_nom' => 'required|string|max:255',
            'client_prenom' => 'required|string|max:255',
            'client_email' => 'required|email',
            'client_telephone' => 'required|string|max:20',
            'burger_id' => 'required|integer|exists:burgers,id',
            'montant' => 'required|numeric',
            'etat' => 'required|string',
            'date_commande' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->all();
        $data['date_commande'] = Carbon::parse($data['date_commande'])->format('Y-m-d H:i:s');

        $commande = Commande::create($data);

        // Mettre à jour les statistiques après la création de la commande
        $this->updateStatistiques();

        Mail::to('ibrohillb@gmail.com')->send(new CommandeMailable($commande));

        return response()->json($commande, 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'client_nom' => 'sometimes|string|max:255',
            'client_prenom' => 'sometimes|string|max:255',
            'client_telephone' => 'sometimes|string|max:20',
            'burger_id' => 'sometimes|exists:burgers,id',
            'montant' => 'sometimes|numeric',
            'etat' => 'sometimes|in:En cours,Terminé,Annulé,Payé',
            'date_commande' => 'sometimes|date',
            'date_paiement' => 'sometimes|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $commande = Commande::find($id);

        if ($commande) {
            $commande->update($request->all());
            
            // Mettre à jour les statistiques après la mise à jour de la commande
            $this->updateStatistiques();

            return response()->json($commande, 200);
        }

        return response()->json(['error' => 'Commande non trouvée'], 404);
    }

    private function updateStatistiques()
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

    public function show($id)
    {
        $commande = Commande::with('burger')->find($id);

        if ($commande) {
            return response()->json($commande, 200);
        }

        return response()->json(['error' => 'Commande non trouvée'], 404);
    }

    public function destroy($id)
    {
        $commande = Commande::find($id);

        if ($commande) {
            $commande->delete();

            // Mettre à jour les statistiques après la suppression de la commande
            $this->updateStatistiques();

            return response()->json(['message' => 'Commande annulée'], 200);
        }

        return response()->json(['error' => 'Commande non trouvée'], 404);
    }

    public function payer($id)
    {
        $commande = Commande::find($id);
        
        if ($commande) {
            $commande->etat = 'Payée';
            $commande->save();

            // Définir le chemin du répertoire des factures
            $invoiceDir = storage_path('app/public/invoices');

            // Vérifier si le répertoire existe, sinon le créer
            if (!file_exists($invoiceDir)) {
                mkdir($invoiceDir, 0755, true);
            }

            // Définir le chemin du fichier de la facture
            $invoicePath = $invoiceDir . "/invoice_{$id}.pdf";

            // Générer le contenu du PDF (remplacez cette ligne par votre code de génération de PDF)
            $pdfContent = 'Contenu du PDF de la facture';

            // Sauvegarder le PDF de la facture
            file_put_contents($invoicePath, $pdfContent);

            // Assurez-vous que l'adresse email du client est valide avant d'envoyer l'email
            if (filter_var($commande->client_email, FILTER_VALIDATE_EMAIL)) {
                // Envoyer l'email avec le changement de statut de la commande
                Mail::to($commande->client_email)->send(new CommandeStatusChanged($commande, 'Payée'));
                return response()->json(['message' => 'Commande marquée comme payée et email envoyé']);
            } else {
                return response()->json(['message' => 'Adresse email du client invalide'], 400);
            }
        }
        
        return response()->json(['message' => 'Commande non trouvée'], 404);
    }

    public function filter(Request $request)
    {
        $query = Commande::query();

        if ($request->has('burger_id')) {
            $query->where('burger_id', $request->input('burger_id'));
        }

        if ($request->has('date_commande')) {
            $query->whereDate('date_commande', $request->input('date_commande'));
        }

        if ($request->has('etat')) {
            $query->where('etat', $request->input('etat'));
        }

        if ($request->has('client_nom')) {
            $query->where('client_nom', 'like', '%' . $request->input('client_nom') . '%');
        }

        return response()->json($query->get());
    }

    public function sendEmail($id, Request $request)
    {
        $commande = Commande::find($id);
        if ($commande) {
            $status = $request->input('status');
            Mail::to($commande->client_email)->send(new CommandeStatusChanged($commande, $status));
            return response()->json(['message' => 'Email sent successfully']);
        }
        return response()->json(['message' => 'Commande not found'], 404);
    }

    public function terminerCommande($id)
    {
        $commande = Commande::findOrFail($id);
        $commande->etat = 'Terminée';
        $commande->save();

        Mail::to($commande->client_email)->send(new CommandeStatusChanged($commande, 'Terminée'));

        // Mettre à jour les statistiques après la mise à jour de la commande
        $this->updateStatistiques();

        return response()->json(['message' => 'Commande marquée comme terminée']);
    }

    public function annulerCommande($id)
    {
        $commande = Commande::findOrFail($id);
        $commande->etat = 'Annulée';
        $commande->save();
        Mail::to($commande->client_email)->send(new CommandeStatusChanged($commande, 'Annulée'));

        // Mettre à jour les statistiques après la mise à jour de la commande
        $this->updateStatistiques();

        return response()->json(['message' => 'Commande annulée']);
    }

    public function getEmailAddress($commandeId)
    {
        $commande = Commande::find($commandeId);

        if ($commande) {
            return $commande->client_email;
        } else {
            return response()->json(['message' => 'Commande not found'], 404);
        }
    }

    public function updateCommandeToCompleted($id)
    {
        $commande = Commande::find($id);
        if ($commande) {
            $commande->etat = 'Terminée';
            $commande->save();
            
            // Send email
            Mail::to($commande->client_email)->send(new CommandeStatusChanged($commande, 'Terminée'));
            
            // Mettre à jour les statistiques après la mise à jour de la commande
            $this->updateStatistiques();
            
            return response()->json(['message' => 'Commande mise à jour en Terminée']);
        }
        return response()->json(['message' => 'Commande non trouvée'], 404);
    }
}
