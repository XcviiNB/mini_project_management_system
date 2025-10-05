<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $project = $this->route('project');

        return in_array(auth()->user()->role, ['admin', 'manager']) && $project->user_id === auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'         =>  'required|string|max:255',
            'assigned_to'   =>  'required|exists:users,id',
            'status'        =>  'required|in:pending, in-progress, completed',
            'due_date'      =>  'nullable|date|after_or_equal:now'
        ];
    }
}
