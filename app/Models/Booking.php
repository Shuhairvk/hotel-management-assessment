<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = ['guest_id', 'room_id', 'check_in', 'check_out', 'status', 'total_amount'];
    protected $casts = ['check_in' => 'date:Y-m-d', 'check_out' => 'date:Y-m-d'];

    public function guest() { return $this->belongsTo(Guest::class); }
    public function room() { return $this->belongsTo(Room::class); }
}
