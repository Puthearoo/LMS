<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use App\Models\Checkout;
use Illuminate\Http\Request; // Add this line

class WelcomeController extends Controller
{
    public function index()
    {
        // Get ALL books including unavailable ones (remove the availability filter)
        $featuredBooks = Book::orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // Get categories from books table (since you don't have categories table)
        $categories = Book::selectRaw('category, COUNT(*) as book_count')
            ->groupBy('category')
            ->orderBy('book_count', 'desc')
            ->take(6)
            ->get();

        // Stats for the welcome page
        $totalBooks = Book::count();
        $totalUsers = User::whereIn('role', ['student', 'librarian'])->count();
        $totalCategories = $categories->count(); // Count the unique categories
        $totalCheckouts = Checkout::where('status', 'checked_out')->count();

        return view('welcome', [
            'featuredBooks' => $featuredBooks,
            'categories' => $categories,
            'totalBooks' => $totalBooks,
            'totalUsers' => $totalUsers,
            'totalCategories' => $totalCategories,
            'totalCheckouts' => $totalCheckouts
        ]);
    }

    public function category($category)
    {
        // Show ALL books in category (remove availability filter)
        $books = Book::where('category', $category)
            ->orderBy('title')
            ->paginate(12);

        return view('books-by-category', [
            'books' => $books,
            'category' => $category
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        // Search ALL books (remove availability filter)
        $books = Book::where(function ($q) use ($query) {
            $q->where('title', 'like', "%$query%")
                ->orWhere('author', 'like', "%$query%")
                ->orWhere('category', 'like', "%$query%")
                ->orWhere('genre', 'like', "%$query%");
        })
            ->orderBy('title')
            ->paginate(12);

        return view('search-results', [
            'books' => $books,
            'query' => $query
        ]);
    }
}