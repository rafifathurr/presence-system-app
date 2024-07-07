<?php

namespace App\Models\Division;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $table = 'division';
    protected $guarded = [];

    public function user()
    {
        return $this->hasMany(User::class, 'division_id', 'id');
    }
}
