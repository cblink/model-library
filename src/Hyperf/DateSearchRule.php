<?php

namespace Cblink\ModelLibrary\Hyperf;

use Hyperf\Validation\Contract\Rule;

class DateSearchRule extends \Cblink\ModelLibrary\Kernel\DateSearchRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes(string $attribute, $value) :bool
    {
       return $this->validate($attribute, $value);
    }
}
