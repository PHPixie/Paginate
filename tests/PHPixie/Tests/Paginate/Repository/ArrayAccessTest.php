<?php

namespace PHPixie\Tests\Paginate\Repository;

/**
 * @coversDefaultClass PHPixie\Paginate\Repository\ArrayAccess
 */
class ArrayAccessTest extends \PHPixie\Test\Testcase
{
    protected $items = array();
    
    protected $repository;
    
    public function setUp()
    {
        for($i=0; $i<20; $i++) {
            $this->items[]= 'item'.$i;
        }
        
        $this->repository = new \PHPixie\Paginate\Repository\ArrayAccess(
            $this->items
        );
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
    
    }
    
    /**
     * @covers ::getCount
     * @covers ::<protected>
     */
    public function testGetCount()
    {
        $count = count($this->items);
        $this->assertSame($count, $this->repository->getCount());
    }
    
    /**
     * @covers ::getItems
     * @covers ::<protected>
     */
    public function testGetItems()
    {
        $expect = array_slice($this->items, 5, 10);
        
        $items = $this->repository->getItems(5, 10);
        $this->assertSame($expect, iterator_to_array($items));
    }
}