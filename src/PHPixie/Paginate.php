<?php

namespace PHPixie;

class Paginate
{
    protected $builder;
    
    public function __construct()
    {
        $this->builder = $this->buildBuilder();
    }
    
    public function pager($Loader, $pageSize)
    {
        return $this->builder->pager($Loader, $pageSize);
    }
    
    public function arrayLoader($items)
    {
        return $this->builder->arrayLoader($items);
    }
    
    public function arrayPager($items, $pageSize)
    {
        $Loader = $this->builder->arrayLoader($items);
        return $this->builder->pager($Loader, $pageSize);
    }
    
    public function builder()
    {
        return $this->builder;
    }
    
    protected function buildBuilder()
    {
        return new Paginate\Builder();
    }
}