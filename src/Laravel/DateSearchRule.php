<?php

namespace Cblink\ModelLibrary\Laravel;

use Illuminate\Contracts\Validation\Rule;

class DateSearchRule extends \Cblink\ModelLibrary\Kernel\DateSearchRule implements Rule
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
        return $this->validate($attribute, $value);
    }
}
