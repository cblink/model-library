<?php

namespace Cblink\ModelLibrary\Kernel;

use Carbon\Carbon;

trait CreatedAt
{

    /**
     * @return mixed
     */
    public function createAt()
    {
        return $this->created_at instanceof Carbon ?
            $this->create_at->toDateTimeString() :
            $this->created_at;
    }

    /**
     * @return mixed
     */
    public function updateAt()
    {
        return $this->updated_at instanceof Carbon ?
            $this->updated_at->toDateTimeString() :
            $this->updated_at;
    }
}