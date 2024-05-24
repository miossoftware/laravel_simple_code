<?php

namespace App\Http\Controllers\Api;

use App\Accounts;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AccountResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;

class AccountController extends Controller
{

    public function index()
    {
        $accounts = Accounts::all()->sortBy('account_id');
        return AccountResource::collection($accounts);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = $this->validator_create($input);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Could not create new account.',
                'errors' => $validator->errors(),
                'code' => 400
            ], 400);
        }

        if (Accounts::create($input)) {
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
        $account = Accounts::find($id);
        
        if (!$account) {
            return response()->json([
                'message' => 'Could not find the account',
                'code' => 404
            ],404);
        }
        return new AccountResource($account);
    }

    public function update(Request $request, $id)
    {   
        $input = $request->all();
        $account = Accounts::find($id);

        if (!$account) {
            return response()->json([
                'message' => 'Could not find the account',
                'code' => 404
            ],404);
        }

        $validator = $this->validator_update($input);
        if ($validator->fails()) {
            return response()->json([
                'message'   =>  'Could not create new account.',
                'errors'   =>  $validator->errors(),
                'code'   =>  400
            ], 400);
        }

        if ($account->update($input)) {
            return response()->json([
                'message' => 'OK',
                'code' => 200,
            ],200);
        } else {
            return response()->json([
                'message' => 'Internal Error',
                'code' => 500,
            ],500);
        }
    }

    public function destroy($id)
    {
        $account = Accounts::find($id);

        if (!$account) {
            return response()->json([
                'message' => 'Could not find the account',
                'code' => 404
            ],404);
        }

        if ($account->delete()) {
            return response()->json(null, 204);
        } else {
            return response()->json([
                'message' => 'Internal Error',
                'code' => 500,
            ],500);
        }
    }

    private function validator_create($data){
        return Validator::make($data, [
            'name' => 'required|string',
            'balance' => 'required|integer'
        ]);
    }

    private function validator_update($data){
        $rules = array();
        if (array_key_exists('name', $data)){
            $rules['name'] = 'required|string';
        }
        if (array_key_exists('balance', $data)){
            $rules['balance'] = 'required|integer';
        }
        return Validator::make($data,
            $rules
        );    
    }
    
}
