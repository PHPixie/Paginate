<?php

namespace PHPixie\Paginate;

interface Repository
{
    public function getCount();
    public function getItems($offset, $limit);
}