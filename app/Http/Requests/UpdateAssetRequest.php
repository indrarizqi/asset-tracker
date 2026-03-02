<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category' => 'required|in:mobile,semi-mobile,fixed',
            'description' => 'required|string',
            'status' => 'required|in:in_use,maintenance,broken,available',
            'purchase_date' => 'required|date',
            'condition' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'person_in_charge' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'vendor' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'warranty_expiry_date' => 'nullable|date',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:5120',
        ];
    }
}
