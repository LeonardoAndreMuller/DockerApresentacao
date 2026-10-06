<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProdutoRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:120'],
            'descricao' => ['nullable', 'string'],
            'preco' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'disponivel' => ['boolean'],
            'atributos' => ['nullable', 'json'],
        ];
    }

    /**
     * Normalize the checkbox before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'disponivel' => $this->boolean('disponivel'),
        ]);
    }

    /**
     * Validated data with the JSON attributes decoded.
     *
     * @return array{nome: string, descricao: ?string, preco: string, disponivel: bool, atributos: array<string, mixed>}
     */
    public function produtoData(): array
    {
        $data = $this->validated();
        $data['atributos'] = json_decode($data['atributos'] ?? '', true) ?: [];

        return $data;
    }
}
