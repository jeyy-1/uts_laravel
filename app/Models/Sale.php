<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'user_id', 'sale_number', 'customer_name', 'payment_method',
        'total', 'status', 'cancellation_reason', 'cancelled_by', 'cancelled_at', 'sold_at'
    ];

    protected $casts = [
        'sold_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}
