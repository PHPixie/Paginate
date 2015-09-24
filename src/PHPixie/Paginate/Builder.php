<?php

namespace PHPixie\Paginate;

class Builder
{
    public function pager($Loader, $pageSize)
    {
        return new Pager($Loader, $pageSize);
    }
    
    public function arrayLoader($items)
    {
        return new Loader\ArrayAccess($items);
    }
}