<?php

namespace PHPixie\Paginate;

class Builder
{
    public function pager($repository, $pageSize)
    {
        return new Pager($repository, $pageSize);
    }
    
    public function arrayRepository($items)
    {
        return new Repository\ArrayAccess($items);
    }
}