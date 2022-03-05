<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vote extends Model
{
    protected $primaryKey = "id";
    protected $keyType = "string";
    public $incremanting = false;

    use HasFactory;
    public function voteable() {
        return $this->morphTo();
    }
}
