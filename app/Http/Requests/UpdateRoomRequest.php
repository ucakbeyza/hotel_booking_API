<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:rooms,id',
            'hotel_id' => 'sometimes|required|integer|exists:hotels,id',
            'room_number' => 'sometimes|required|string|max:50',
            'type' => 'sometimes|required|string|max:100',
            'price' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|required|string|in:available,booked',
        ];
    }
}
