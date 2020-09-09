<?php

namespace Cblink\LaravelModelLibrary\Search;

use Illuminate\Contracts\Validation\Rule;

class DateSearchRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $date = explode("~", $value);

        if (count($date) >= 2) {
            return strtotime($date[0]) && strtotime($date[1]) && strtotime($date[0]) < strtotime($date[1]);
        }

        return strtotime($date[0]);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
