<?php

namespace App\Http\Requests;

use App\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->role === UserRole::ADMIN;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $bookId = $this->route('book') ? $this->route('book')->id : null;

        return [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'published_year' => 'nullable|integer|min:1901|max:' . date('Y'),
            'stock_count' => 'required|integer|min:1|max:100',
            'isbn' => 'nullable|string|max:20|unique:books,isbn,' . $bookId,
            'description' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'category_id' => 'category',
            'published_year' => 'published year',
            'stock_count' => 'stock count',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category is invalid.',
            'published_year.min' => 'Published year must be at least 1000.',
            'published_year.max' => 'Published year cannot be in the future.',
            'stock_count.min' => 'Stock count must be at least 1.',
            'stock_count.max' => 'Stock count cannot exceed 100.',
        ];
    }
}
