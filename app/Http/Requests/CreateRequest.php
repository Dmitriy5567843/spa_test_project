<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use PhpParser\Node\Stmt\DeclareDeclare;

class CreateRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'=>['required','min:1','max:255',],
            'email'=>['required', 'email', 'min:3','max:255',],
            'content'=>['required','string','max:255',],
            'files.*' => ['nullable', 'file','mimes:txt,jpg,gif,png', 'max:200'],
        ];
    }
}
