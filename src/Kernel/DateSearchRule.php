<?php

namespace Cblink\ModelLibrary\Kernel;

abstract class DateSearchRule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validate($attribute, $value)
    {
        $date = is_array($value) ? $value : explode("~", $value);

        if (count($date) == 2) {

            if ($date[0] && $date[1]) {
                return strtotime($date[0]) && strtotime($date[1]) && strtotime($date[0]) <= strtotime($date[1]);
            }

            if (empty($date[0])) {
                return (bool) strtotime($date[1]);
            }

            if (empty($date[1])) {
                return (bool) strtotime($date[1]);
            }
        }

        return (bool) strtotime($date[0]);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '日期格式错误.';
    }
}
