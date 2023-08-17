<?php

namespace myodevops\ALTErnative\Controllers;

use App\Http\Controllers\Controller;
use myodevops\ALTErnative\Models\LaravelLog;
use myodevops\ALTErnative\Tools\ManageLaravelLog;
use myodevops\ALTErnative\Tools\Log;

class LaravelLogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    static public function index()
    {
        $method = __METHOD__;

        $managelaravellog = new ManageLaravelLog();
        $result = $managelaravellog->processLog();
        if (!$result) {
            Log::Fatal("Error in $method.", "Error processing the larevel.log file.");
        }

        $laravellog = new LaravelLog();

        return $laravellog->getData ($_GET, $laravellog->query(), "\myodevops\ALTErnative\Resources\LaravelLogsResource", false);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    static public function show($id)
    {
        return LaravelLog::findOrFail($id);
    }
}