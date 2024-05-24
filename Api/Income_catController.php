<?php

namespace App\Http\Controllers\Api;

use App\Income_cat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Income_catResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;

class Income_catController extends Controller
{
    public function index()
    {
        $incomecat = Income_cat::all()->sortBy('category_id');
        return Income_catResource::collection($incomecat);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = $this->validator_create($input);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Could not create new income_cat.',
                'errors' => $validator->errors(),
                'code' => 400
            ], 400);
        }

        if (Income_cat::create($input)) {
            return response()->json([
                'message' => 'The resource is created successfully',
                'code' => 201
            ],201);
        } else {
            return response()->json([
                'message' => 'Internal Error',
                'code' => 500
            ],500);
        }
    }

    public function show($id)
    {
        $income_cat = Income_cat::find($id);

        if (!$income_cat) {
            return response()->json([
                'message' => 'Could not find the income_cat',
                'code' => 404
            ],404);
        }
        return new Income_catResource($income_cat);
    }

    public function update(Request $request, Income_cat $income_cat, $id)
    {
        $input = $request->all();
        $income_cat = Income_cat::find($id);
        $validator = $this->validator_update($input);

        if (!$income_cat) {
            return response()->json([
                'message' => 'Could not find the income_cat',
                'code' => 404
            ],404);
        } 

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Could not update income_cat.',
                'errors' => $validator->errors(),
                'code' => 400
            ],400);
        }

        if (!$income_cat->update($input)) {
            return response()->json([
                'message' => 'Bad Request',
                'code' => 400
            ],400);
        } else {
            return response()->json([
                'message' => 'OK',
                'code' => 200
            ],200);
        }
    }

    public function destroy($id)
    {
        $income_cat = Income_cat::find($id);

        if (!$income_cat) {
            return response()->json([
                'message' => 'Could not find the income_cat',
                'code' => 404
            ],404);
        }

        if ($income_cat->delete()) {
            return response()->json(null, 204);
        } else {
            return response()->json([
                'message' => 'Internal Error',
                'code' => 500
            ],500);
        }
    }

    private function validator_create($data){
        return Validator::make($data, [
            'name' => 'required|string'
        ]);
    }

    private function validator_update($data){
        $rules = array();
        if (array_key_exists('name', $data)){
            $rules['name'] = 'required|string';
        }
        return Validator::make($data,
            $rules
        );    
    }
}
