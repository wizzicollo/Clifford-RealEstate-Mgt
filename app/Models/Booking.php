<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'tel_number',
        'email',
        'table_id',
        'book_date',
        'rooms_number'
    ];

    protected $dates = [
        'book_date'
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}
