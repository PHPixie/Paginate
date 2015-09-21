<?php

namespace PHPixie\Split;

class Pager
{
    protected $repository;
    
    protected $itemCount;
    protected $pageCount;
    
    public function __construct($repository)
    {
        $this->repository = $repository;
    }
    
    public function items()
    {
        $offset = $this->pageSize * ($this->currentPage() - 1);
        $this->repository->getItems($offset);
    }
    
    public function calculateSize()
    {
        $this->itemCount = $this->repository->getItemCount();
        $this->pageCount = ceil($this->itemCount()/$this->pageSize);
    }
    
    public function pageCount()
    {
        return $this->pageCount;
    }
    
    public function itemCount()
    {
        return $this->itemCount;
    }
    
    public function next()
    {
        return $this->pageByOffset(1);
    }
    
    public function previous()
    {
        return $this->pageByOffset(-1);
    }
    
    public function exists($page)
    {
        return $page < $this->pageCount && $page > 0;
    }
}