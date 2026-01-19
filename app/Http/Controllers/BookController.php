<?php
// app/Http/Controllers/BookController.php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Book::latest()->paginate(10);

        // Get statistics
        $availableCount = Book::where('availability_status', 'available')->count();
        $reservedCount = Book::where('availability_status', 'reserved')->count();
        $checkedOutCount = Book::where('availability_status', 'checked_out')->count();

        return view('books.index', compact(
            'books',
            'availableCount',
            'reservedCount',
            'checkedOutCount'
        ));
    }
    public function updateStatus(Request $request, Book $book)
    {
        $validated = $request->validate([
            'availability_status' => 'required|in:available,checked_out,reserved'
        ]);

        $book->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Book status updated successfully.',
                'status' => $book->availability_status
            ]);
        }

        return redirect()->route('librarian.books.index')
            ->with('success', 'Book status updated successfully.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('books.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|unique:books,isbn',
            'category' => 'nullable|string|max:255',
            'genre' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'availability_status' => 'required|in:available,reserved,checked_out',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $image->storeAs('book-covers', $filename, 'public');
            $validated['image'] = 'book-covers/' . $filename;
        }

        Book::create($validated);

        return redirect()
            ->route('librarian.books.index')
            ->with('success', 'Book added successfully!');
    }


    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        return view('books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        return view('books.edit', compact('book'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|unique:books,isbn,' . $book->id,
            'category' => 'nullable|string|max:255',
            'genre' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'availability_status' => 'required|in:available,reserved,checked_out',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {

            // Delete old image
            if ($book->image && Storage::disk('public')->exists($book->image)) {
                Storage::disk('public')->delete($book->image);
            }

            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $image->storeAs('book-covers', $filename, 'public');
            $validated['image'] = 'book-covers/' . $filename;
        }

        $book->update($validated);

        return redirect()
            ->route('librarian.books.index')
            ->with('success', 'Book updated successfully!');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        if ($book->image && Storage::disk('public')->exists($book->image)) {
            Storage::disk('public')->delete($book->image);
        }

        $book->delete();

        return redirect()
            ->route('librarian.books.index')
            ->with('success', 'Book deleted successfully!');
    }


    // Additional methods from your routes
    public function checkout(Book $book)
    {
        // Checkout logic will be implemented later
    }

    public function reserve(Book $book)
    {
        // Reservation logic will be implemented later
    }

    public function checkAvailability(Book $book)
    {
        // Availability check logic
        return response()->json([
            'available' => $book->isAvailable(),
            'status' => $book->availability_status
        ]);
    }
}