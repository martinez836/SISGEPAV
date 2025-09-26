<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBatchClassificationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'batch_id' => ['required','exists:batches,id'],
            'details'  => ['required','array','min:1'],
            'details.*.category_id'        => ['required','exists:categories,id'],
            'details.*.totalClassification'=> ['required','integer','min:0'],
            'notes'    => ['nullable','string','max:2000'], // si decides guardar notas en otra parte
        ];
    }
}
