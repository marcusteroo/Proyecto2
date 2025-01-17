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
        $author=Author::create($data);
        return response()->json(['status'=>405,'succes'=>true,'data'=>$author]);

        /*
        $author = new Author();
        $author->name = $request->name;
        $author->surname = $request->surname;
        $author->email = $request->email;
        $author->save();
        return $request->all();
        return response()->json(['status'=>405,'succes'=>true,'data'=>$author]); */
    }
}
