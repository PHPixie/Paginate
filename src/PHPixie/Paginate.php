<?php

namespace PHPixie;

class Paginate
{
    protected $builder;
    
    public function __construct()
    {
        $this->builder = $this->buildBuilder();
    }
    
    public function pager($repository, $pageSize)
    {
        return $this->builder->pager($repository, $pageSize);
    }
    
    public function arrayRepository($items)
    {
        return $this->builder->arrayRepository($items);
    }
    
    public function arrayPager($items, $pageSize)
    {
        $repository = $this->builder->arrayRepository($items);
        return $this->builder->pager($repository, $pageSize);
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