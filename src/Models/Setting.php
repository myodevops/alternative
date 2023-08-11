<?php

namespace myodevops\ALTErnative\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use myodevops\ALTErnative\Tools\Log;

class Setting extends Model
{
    use HasFactory;

    protected $connection = 'altesqlite';
    public $table = 'settings';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'keyname',
        'value',
    ];

    /**
     * Get the value of a setting
     *
     * @param string $keyname The key name of the setting
     * @return void
     */
    static function GetValue ($keyname) {
        $setting = Setting::where('keyname', $keyname)->first();
        if ($setting) {
            return $setting->value;
        }

        return FALSE;
    }

    /**
     * Update or create a setting in the table
     *
     * @param String $keyname The key name of the setting
     * @param String $value The value of the setting
     * @return void
     */
    static function SetValue ($keyname, $value) {
        $method = __METHOD__;
        try {
            Setting::updateOrCreate (
                ['keyname' => $keyname],
                ['value' => $value]
            );
        } catch (\Exception $e) {
            Log::Fatal ("Error saving the Laravel log records in $method.", $e->getMessage());
        }
        
    }
}