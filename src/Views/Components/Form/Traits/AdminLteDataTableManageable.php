<?php
namespace myodevops\ALTErnative\Views\Components\Form\Traits;

interface AdminLteDataTableManageable 
{
    public function getData ($get, $model, $jsonRes, $requerable);
}