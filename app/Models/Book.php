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

    // Check availability
    public function isAvailable()
    {
        return $this->availability_status === 'available';
    }

    // Check if book is reserved
    public function isReserved()
    {
        return $this->availability_status === 'reserved';
    }

    //  Check if book is checked out
    public function isCheckedOut()
    {
        return $this->availability_status === 'checked_out';
    }

    // Current active checkout
    public function currentCheckout()
    {
        return $this->checkouts()
            ->whereNull('return_date')
            ->where('status', 'checked_out')
            ->first();
    }
}
