<?php

namespace PHPixie\Paginate;

interface Loader
{
    public function getCount();
    public function getItems($offset, $limit);
}