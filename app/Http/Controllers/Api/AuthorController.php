<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;
use Validator;

class AuthorController extends Controller
{
    public function index(){
        $authors= Author::all();
        return response()->json(['status'=>405,'success'=>true,'data'=>$authors]);
    }
    public function store(Request $request){
        $validator = Validator::make(
            $request->all(),
            ['name'=>['required','max:255'],
            'surname'=>'',
            'email'=>['required','unique:Authors']
            ]
        );
        $data= $validator->validated();
        $authors=Author::create($data);
        return response()->json(['status'=>405,'succes'=>true,'data'=>$authors]);

        /*
        $author = new Author();
        $author->name = $request->name;
        $author->surname = $request->surname;
        $author->email = $request->email;
        $author->save();
        return $request->all();
        return response()->json(['status'=>405,'succes'=>true,'data'=>$author]); */
    }
    public function destroy($author){
        $author->delete();
        return response()->json(['status'=>405,'succes'=>true,'data'=>'']);


    }
    public function show($id){
        $author = Author::find($id);
        if (!$author) {
            return response()->json(['status' => 404, 'success' => false, 'message' => 'Author not found']);
        }
        return response()->json(['status' => 200, 'success' => true, 'data' => $author]);
    }

    public function update(Request $request, $id){
        $author = Author::find($id);
        if (!$author) {
            return response()->json(['status' => 404, 'success' => false, 'message' => 'Author not found']);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'sometimes|required|max:255',
                'surname' => 'sometimes|max:255',
                'email' => 'sometimes|required|email|unique:Authors,email,' . $id
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'success' => false, 'errors' => $validator->errors()]);
        }

        $author->update($validator->validated());
        return response()->json(['status' => 200, 'success' => true, 'data' => $author]);
    }
}
