<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    protected $guarded = [];

    // Relations
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function resource() {
        return $this->belongsTo(Resource::class);
    }
}