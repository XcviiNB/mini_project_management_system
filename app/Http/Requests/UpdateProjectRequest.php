<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $project = Project::findOrFail($this->route('project'));

        return $project->owner_id === auth()->id() || in_array(auth()->user()->role, ['admin', 'manager']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'          =>  'required|string|max:255',
            'description'   =>  'nullable|string',
            'start_date'    =>  'required|date',
            'end_date'      =>  'nullable|date|after_or_equal:start_date'
        ];
    }
}
