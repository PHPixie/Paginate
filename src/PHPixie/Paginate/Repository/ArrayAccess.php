<?php

namespace PHPixie\Paginate\Repository;

class ArrayAccess implements \PHPixie\Paginate\Repository
{
    protected $items;
    
    public function __construct($items)
    {
        $this->items = $items;
    }
    
    public function getCount()
    {
        return count($this->items);
    }
    
    public function getItems($offset, $limit)
    {
        $items = array();
        $end = $offset+$limit;
        
        for($i=$offset; $i<$end; $i++) {
            $items[]=$this->items[$i];
        }
        
        return new \ArrayIterator($items);
    }
}