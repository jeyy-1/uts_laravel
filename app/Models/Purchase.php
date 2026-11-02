<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'supplier_id', 'user_id', 'purchase_number',
        'total', 'status', 'notes', 'cancellation_reason', 'cancelled_by', 'cancelled_at', 'purchased_at',
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

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
        return $this->hasMany(PurchaseItem::class);
    }
}
