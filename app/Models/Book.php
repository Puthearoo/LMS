<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title',
        'author',
        'isbn',
        'category',
        'genre',
        'price',
        'availability_status',
        'image'
    ];
    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function checkouts()
    {
        return $this->hasMany(Checkout::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function fines()
    {
        return $this->hasManyThrough(Fine::class, Checkout::class);
    }

    // Helper method to check availabilit
    public function isAvailable()
    {
        return $this->availability_status === 'available';
    }

    // Helper method to check if reserved
    public function isReserved()
    {
        return $this->availability_status === 'reserved';
    }

    // ADD THIS METHOD - Check if book is checked out
    public function isCheckedOut()
    {
        return $this->availability_status === 'checked_out';
    }

    // You might also want this method to get current active checkout
    public function currentCheckout()
    {
        return $this->checkouts()
            ->whereNull('return_date')
            ->where('status', 'checked_out')
            ->first();
    }
}
