<?php

namespace App\Http\Controllers;

use App\Models\Burger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BurgerController extends Controller
{
    public function index()
    {
        $burgers = Burger::all()->map(function ($burger) {
            $burger->image = $burger->image ? asset('storage/' . $burger->image) : null;
            return $burger;
        });
        return response()->json($burgers, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prix' => 'required|numeric',
            'description' => 'required|string',
            'image' => 'required|image',
        ]);

        $imagePath = $request->file('image')->store('images', 'public');

        $burger = new Burger([
            'nom' => $request->get('nom'),
            'prix' => $request->get('prix'),
            'description' => $request->get('description'),
            'image' => $imagePath,
        ]);
        $burger->save();

        return response()->json($burger, 201);
    }

    public function show($id)
    {
        $burger = Burger::find($id);

        if ($burger) {
            $burger->image = $burger->image ? asset('storage/' . $burger->image) : null;
            return response()->json($burger, 200);
        }

        return response()->json(['error' => 'Burger not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'sometimes|string|max:255',
            'prix' => 'sometimes|numeric',
            'description' => 'sometimes|string',
            'image' => 'sometimes|image',
        ]);

        $burger = Burger::find($id);

        if ($burger) {
            $burger->nom = $request->nom ?? $burger->nom;
            $burger->prix = $request->prix ?? $burger->prix;
            $burger->description = $request->description ?? $burger->description;

            if ($request->hasFile('image')) {
                Storage::disk('public')->delete($burger->image);
                $burger->image = $request->file('image')->store('images', 'public');
            }

            $burger->save();

            return response()->json($burger, 200);
        }

        return response()->json(['error' => 'Burger not found'], 404);
    }


    public function destroy($id)
    {
        $burger = Burger::find($id);

        if ($burger) {
            if ($burger->image) {
                Storage::disk('public')->delete($burger->image);
            }
            $burger->delete();
            return response()->json(['message' => 'Burger deleted'], 200);
        }

        return response()->json(['error' => 'Burger not found'], 404);
    }
}
