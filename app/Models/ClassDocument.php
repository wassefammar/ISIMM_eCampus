<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassDocument extends Model
{
    use HasFactory;
    protected $table="class_documents";

    protected $fillable = [
        'titre',
        'description',
        'file'
    ];
}
