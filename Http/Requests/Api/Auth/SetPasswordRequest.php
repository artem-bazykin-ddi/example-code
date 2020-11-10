<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\ApiRequest;

class SetPasswordRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'password' => 'min:6|confirmed',
        ];
    }
}
