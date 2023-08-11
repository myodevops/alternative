<?php

namespace myodevops\ALTErnative\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use myodevops\ALTErnative\Views\Components\Form\Traits\AdminLteDataTableManageable;
use myodevops\ALTErnative\Views\Components\Form\Traits\AdminLteDataTableManage;

class LaravelLog extends Model implements AdminLteDataTableManageable
{
    use HasFactory;
    use AdminLteDataTableManage;

    protected $connection = 'altesqlite';
    public $table = 'laravellogs';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
		'datetime',
        'message',
        'stacktrace',
    ];
}