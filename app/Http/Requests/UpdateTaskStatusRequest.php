<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $task = $this->route('task');
        $newStatus = $this->status;
        $currentStatus = $task->status;
        $allowedTransitions = [
            'pending' => ['pending', 'in-progress'],
            'in-progress' => ['in-progress', 'completed'],
            'completed' => ['completed'],
        ];
        return in_array(auth()->user()->role, ['admin', 'manager', 'developer'])
            && ($task->assigned_to === auth()->id() || auth()->user()->role === 'admin')
            && in_array($newStatus, $allowedTransitions[$currentStatus] ?? []);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status'    =>  'required|in:pending, in-progress, completed',
        ];
    }
}
