<?php

namespace myodevops\ALTErnative\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ErrLogsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'datetime' => date('Y-m-d H:i:s', $this->datetime), 
            'message' => $this->message,
            'userid' => $this->userid,
            'actions' => $this->actions,
        ];
    }
}
