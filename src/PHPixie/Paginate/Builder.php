<?php

namespace PHPixie\Paginate;

class Builder
{
    public function pager($loader, $pageSize)
    {
        return new Pager($loader, $pageSize);
    }
    
    public function arrayLoader($items)
    {
        return new Loader\ArrayAccess($items);
    }
}