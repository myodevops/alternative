<?php

namespace myodevops\ALTErnative\Controllers;

use App\Http\Controllers\Controller;
use myodevops\ALTErnative\Models\Errorlog;
use myodevops\ALTErnative\Resources\ErrLogsResource;

class ErrLogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    static public function index()
    {
        $errlog = new Errorlog();

        return $errlog->getData ($_GET, $errlog, "\myodevops\ALTErnative\Resources\ErrLogsResource");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    static public function show($id)
    {
        return Errorlog::findOrFail($id);
    }
}