<?php

namespace App\Http\Controllers;

use App\Http\Resources\TodolistResource;
use App\Models\Todolist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TodolistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Todolist::query();

        if (! $request->boolean('include_done', false)) {
            $query->where('is_done', false);
        }

        if ($request->filled('name')) {
            $name = (string) $request->query('name');
            $query->where('name', 'like', "%{$name}%");
        }

        $todolists = $query->get();

        return response()->json(TodolistResource::collection($todolists)->resolve(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:1', 'max:100'],
            'priority' => ['required', 'integer'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string'],
        ]);

        $todolist = Todolist::create([
            'name' => $validated['name'],
            'priority' => $validated['priority'],
            'tags' => $validated['tags'] ?? [],
            'is_done' => false,
        ]);

        return response()->json((new TodolistResource($todolist))->resolve(), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $todolist = Todolist::find($id);

        if (! $todolist) {
            return response()->json([
                'message' => 'Todolist not found',
            ], 404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:1', 'max:100'],
            'priority' => ['required', 'integer'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string'],
        ]);

        $todolist->update([
            'name' => $validated['name'],
            'priority' => $validated['priority'],
            'tags' => $validated['tags'] ?? [],
        ]);

        return response()->json((new TodolistResource($todolist))->resolve(), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $todolist = Todolist::find($id);

        if (! $todolist) {
            return response()->json([
                'success' => false,
            ], 404);
        }

        $todolist->delete();

        return response()->json([
            'success' => true,
        ], 200);
    }
}
