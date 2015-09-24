<?php

namespace PHPixie\Paginate;

class Pager
{
    protected $repository;
    protected $pageSize;
    
    protected $currentPage = 1;
    protected $itemCount;
    protected $pageCount;
    
    
    public function __construct($repository, $pageSize)
    {
        $this->repository = $repository;
        $this->pageSize   = $pageSize;
    }
    
    public function pageSize()
    {
        return $this->pageSize;
    }
    
    public function currentPage()
    {
        return $this->currentPage;
    }
    
    public function setCurrentPage($page)
    {
        if(!$this->pageExists($page)) {
            throw new \PHPixie\Paginate\Exception("Page $page does not exist");
        }
        
        $this->currentPage = $page;
    }
    
    public function pageExists($page)
    {
        $this->requireCount();
        
        return $page <= $this->pageCount && $page > 0;
    }
    
    public function pageOffsetExists($offset)
    {
        return $this->pageExists($this->currentPage + $offset);
    }
    
    public function getPageByOffset($offset)
    {
        $page = $this->currentPage + $offset;
        if($this->pageExists($page)) {
            return $page;
        }
        return null;
    }
    
    public function previous()
    {
        return $this->getPageByOffset(-1);
    }
    
    public function next()
    {
        return $this->getPageByOffset(1);
    }
    
    public function getAdjacentPages($limit)
    {
        $this->requireCount();
        $pageCount = $this->pageCount;
        
        if($limit >= $pageCount) {
            return range(1 , $pageCount);
        }
        
        $start = $this->currentPage - (int) ($limit/2);
        
        if($start < 1) {
            return range(1, $limit);
        }
        
        if($start + $limit - 1 > $pageCount) {
            return range($pageCount - $limit + 1, $pageCount);
        }
        
        $end = $start + $limit - 1;
        
        if($start > $end) {
            return array();
        }
        
        return range($start, $end);    
    }
    
    public function itemCount()
    {
        $this->requireCount();
        return $this->itemCount;
    }
    
    public function pageCount()
    {
        $this->requireCount();
        return $this->pageCount;
    }
    
    protected function requireCount()
    {
        if($this->itemCount === null) {
            $this->itemCount = $this->repository->getCount();
            $this->pageCount = (int) ceil($this->itemCount/$this->pageSize);
            if($this->pageCount === 0) {
                $this->pageCount = 1;
            }
        }
    }
    
    public function getCurrentItems()
    {
        $offset = $this->pageSize * ($this->currentPage - 1);
        return $this->repository->getItems(
            $offset,
            $this->pageSize
        );
    }
}