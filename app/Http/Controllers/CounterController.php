<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Counter;
use Illuminate\Support\Facades\Storage;

class CounterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return
        Counter::join('users','users.id', '=', 'counter.user_id')
            ->join('roles_v_users','users.id', '=', 'roles_v_users.user_id')
            ->join('roles','roles.id', '=', 'roles_v_users.role_id')
            ->select('counter.*', 'users.username', 'roles.rolename')
            ->simplePaginate(25);

        //return Counter::orderBy('counter.id', 'desc')->simplePaginate(25);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'type' => 'required|string|max:100',
            'customer_name' => 'string',
            'customer_address' => 'string',
            'customer_phone' => 'string',
            'amount' => 'numeric',
            'invoice_photo' => 'string|max:10000000',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $imageUrl = $this->storeImage($request->get('invoice_photo'));

        $counter = new Counter([
            'invoice_id' => uniqid('invoice_'),
            'user_id' => $request->get('user_id'),
            'type' => $request->get('type'),
            'customer_name' => $request->get('customer_name'),
            'customer_address' => $request->get('customer_address'),
            'customer_phone' => $request->get('customer_phone'),
            'amount' => $request->get('amount'),
            'invoice_photo' => $imageUrl,
        ]);


        $counter = $counter->save();

        return response()->json([
            'success' => true,
            'message' => 'Invoice successfully created'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    // stores image
    private function storeImage($encodedImage)
    {
        $image = $encodedImage;
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = uniqid('image_') . '.png';
        Storage::disk('images')->put($imageName, base64_decode($image));
        $imageUrl = asset('images/'.$imageName);

        return $imageUrl;
    }

}
