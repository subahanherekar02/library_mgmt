<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Book;
use App\RentedBook;
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

    public function rentABook(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'users_id' => 'required|numeric|exists:users,id',
            'books_id' => 'required|numeric|exists:books,id',
            'books_issued_date' => 'required|date_format:Y-m-d H:i:s|before_or_equal:' . date('Y-m-d H:i:s')
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(),'success'=>false], 400);
        }

        $already_issued = RentedBook::where('id', $request->get('users_id'))
            ->where('id', $request->get('books_id'))
            ->whereNull('books_returned_date')
            ->first();

        if (!empty($already_issued)) {
            return response()->json(['suceess'=>false,'message'=>"Book already issue to the user!!!!"], 400);
        }

        $book_rented_data = RentedBook::create([
            'users_id' => $request->get('users_id'),
            'books_id' => $request->get('books_id'),
            'books_issued_date' => $request->get('books_issued_date'),
        ]);
        
        return response()->json(['book_data' => $book_rented_data,'message' => "Book issued to user successfully!!!!!!."], 200);
    }
     public function returnABook(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'books_returned_date' => 'required|date_format:Y-m-d H:i:s|before_or_equal:' . date('Y-m-d H:i:s'),
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message'=>$validator->errors()->first()], 400);
        }

        $rentedBook = RentedBook::whereNull('books_returned_date')
            ->find($id);

        if (empty($rentedBook)) {
            return response()->json(['success'=>false,'message'=>'Book details your looking for are not found!!!!!'], 400);
        }

        if ($request->get('books_returned_date') < $rentedBook->issued_on) {
            return response()->json(['success'=>false,'message' => "Returned date must be greater than the issued date!!"
           ], 400);
        }

        $rentedBook->books_returned_date = $request->get('books_returned_date');
        $rentedBook->save();

        return response()->json(['suceess'=>true,'message'=>'Book returned successfully!!!','data' => $rentedBook], 200);
    }
    public function rentedBooksData($user_id)
    {
        $data = RentedBook::select('users.id', 'users.firstname', 'users.lastname', 'users.email', 'books.id', 'books.book_name', 'books.author', 'rented_books.books_issued_date', 'rented_books.books_returned_date')
            ->from('rented_books')
            ->join('books', 'books.id', 'rented_books.books_id')
            ->join('users', 'users.id', 'rented_books.users_id')
            ->where('rented_books.id', $user_id)
            ->get();

        $response['status'] = 'success';
        $response['data'] = $data;
        return response()->json($response, 200);
    }
}
