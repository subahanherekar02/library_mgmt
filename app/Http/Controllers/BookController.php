<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Book;
use Validator;
class BookController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $book_data = Book::all();
        return response()->json([
            'success'=>true,
            'book_details'=>$book_data
        ],200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
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
            'book_name' => 'required|string|max:255|unique:books',
            'author' => 'required|string|max:255',
            'cover_image' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {;
            return response()->json(['message'=>$validator->errors()->first(),'status'=>false], 400);
        }
        $book = Book::create([
            'book_name' => $request->get('book_name'),
            'author' => $request->get('author'),
            'cover_image' => $request->get('cover_image'),
        ]);
        return response()->json(['success'=>true,'book'=>$book,'message'=>'Book added successfully!!!'], 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $book = Book::find($id);
        if (!empty($book)) {
            return response()->json(['success'=>true,'book_detail'=>$book], 200);
        } else {
            return response()->json(['success'=>false,'message'=>'Book details not found'], 404);
        };
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
        $validator = Validator::make($request->all(), [
            'book_name' => 'required|string|max:255|unique:books,book_name,' . $id . ',id',
            'author' => 'required|string|max:255',
            'cover_image' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message'=>$validator->errors()->first()], 400);
        }

        $book = Book::find($id);
        if (empty($book)) {
            return response()->json(['success'=>false,'message'=>"Book not found in our system"], 404);
        }

        $book->fill($request->all());
        $book->save();

        return response()->json(['success'=>true,'message'=>"Book updated successfully."], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $book = Book::find($id);
        if(empty($book)) {
            return response()->json([
                'success'=>false,
                'message'=>"Book Not found in our system"
            ], 404);
        }

        Book::destroy($id);
        return response()->json([
                'success'=>true,
                'message'=>"Book has been deleted successfully"
            ], 200);
    }
}
