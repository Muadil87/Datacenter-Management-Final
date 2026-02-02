<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
    'name',
    'category_id',
    'state',
    'cpu_cores',
    'ram_gb',
    'storage_gb',
    'storage_type',
    'os',
    'bandwidth_mbps',
    'responsible_id',
    'location',
    'description',
];



    public function category()
    {
        return $this->belongsTo(ResourceCategory::class, 'category_id');
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }
}
