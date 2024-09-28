<?php

namespace App\Http\Controllers;

use App\Models\Listshes; // Make sure the model name is capitalized
use Illuminate\Http\Request;

class ListshesController extends Controller
{
    // Retrieve all users
    public function list(Request $request) {
        $items = Listshes::all();
        return response()->json($items);
    }

    // Create a new user
   public function create(Request $request) {
        // Validate the request data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        // Create and return the new user
        $newUser = Listshes::create($validatedData);
        return response()->json($newUser, 201);
    }


    // Delete a user by ID
    public function delete($id) {
        $item = Listshes::find($id);

        if (!$item) {
            return response()->json([
                'message' => 'Item not found',
                'status' => 404,
            ], 404);
        }

        $item->delete();

        return response()->json([
            'message' => 'Deleted successfully',
            'status' => 200,
            'data' => $item,
        ], 200);
    }
    public function update(Request $request, $id) {
        // Validate the request data
        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
        ]);
    
        $item = Listshes::find($id);
    
        if (!$item) {
            return response()->json([
                'message' => 'Item not found',
                'status' => 404,
            ], 404);
        }
    
        // Update the item with the validated data
        $item->update($validatedData);
    
        return response()->json($item, 200);
    }
}