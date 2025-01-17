<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Note;
use Symfony\Component\HttpFoundation\JsonResponse;

class NoteController extends Controller
{
    public function index(): JsonResponse{
        $notes=Note::all();
        return response()->json($notes,200);
    }
    public function store(Request $request):JsonResponse{
        $note=Note::create($request->all());
        return response()->json([
            'succes'=>true,
            'data'=>$note
        ],201);
    }
    public function show($id):JsonResponse{
        $note = Note::find($id);
        return response()->json($note,200);
    }
    public function update(Request $request,$id):JsonResponse{
        $note = Note::find($id);
        $note->update($request->all());
        return response()->json($note,200);
    }
    public function destroy($id): JsonResponse{
        $note= Note::find($id);
        $note->delete();
        return response()->json([
            'succes'=>true
        ],200);
    }
}
