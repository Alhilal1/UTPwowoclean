<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'container_id_fk',
        'location',
        'description',
        'log_time'
    ];

    public function container()
    {
        return $this->belongsTo(Container::class, 'container_id_fk');
    }
}