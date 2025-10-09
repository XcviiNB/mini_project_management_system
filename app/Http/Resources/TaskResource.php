<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'project_id'    => $this->project_id,
            'assigned_to'   => $this->assigned_to,
            'title'         => $this->title,
            'status'        => $this->status,
            'due_date'      => $this->due_date
        ];
    }
}
