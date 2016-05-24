<?php

namespace PHPixie\Paginate\Loader;

class ArrayAccess implements \PHPixie\Paginate\Loader
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
            if(!isset($this->items[$i])) {
                break;
            }
            $items[]=$this->items[$i];
        }
        
        return new \ArrayIterator($items);
    }
}
