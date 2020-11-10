<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Base64Rule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
//        return preg_match("~data:image/[a-zA-Z]*;base64,[a-zA-Z0-9+/\\=]*=~", $value);
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'The string should be in Base64 format (data:image/jpg;base64,base_64_string=)';
    }
}
