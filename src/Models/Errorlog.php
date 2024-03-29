<?php

namespace myodevops\ALTErnative\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use myodevops\ALTErnative\Views\Components\Form\Traits\AdminLteDataTableManageable;
use myodevops\ALTErnative\Views\Components\Form\Traits\AdminLteDataTableManage;

class Errorlog extends Model implements AdminLteDataTableManageable
{
    use HasFactory;
    use AdminLteDataTableManage;

    protected $connection = 'altesqlite';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'datetime',
        'message',
        'userid',
    ];
}