<?php

namespace App\Models\Warrant;

use App\Models\LocationWork\LocationWork;
use App\Models\Presence\Presence;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warrant extends Model
{
    use HasFactory;

    protected $table = 'warrant';
    protected $guarded = [];

    public function locationWork()
    {
        return $this->hasOne(LocationWork::class, 'id', 'location_work_id');
    }

    public function warrantUser()
    {
        return $this->hasMany(WarrantUser::class, 'warrant_id', 'id')->whereNull('deleted_at');
    }

    public function presence()
    {
        return $this->hasMany(Presence::class, 'warrant_id', 'id');
    }

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function updatedBy()
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    public function deletedBy()
    {
        return $this->hasOne(User::class, 'id', 'deleted_by');
    }
}
