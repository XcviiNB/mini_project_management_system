<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $task = $this->route('task');

        return auth()->check()
            && in_array(auth()->user()->role, ['admin', 'manager', 'developer'])
            && ($task->assigned_to === auth()->id() || $task->project->user_id === auth()->id() || auth()->user()->role === 'admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'comment'   =>  'required|string|max:1000'
        ];
    }
}
