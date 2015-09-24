<?php

namespace PHPixie\Tests\Paginate;

/**
 * @coversDefaultClass PHPixie\Paginate\Builder
 */
class BuilderTest extends \PHPixie\Test\Testcase
{
    protected $builder;
    
    public function setUp()
    {
        $this->builder = new \PHPixie\Paginate\Builder();
    }
    
    /**
     * @covers ::pager
     * @covers ::<protected>
     */
    public function testPager()
    {
        $repository = $this->quickMock('\PHPixie\Paginate\Repository');
        $pager = $this->builder->pager($repository, 15);
        
        $this->assertInstance($pager, '\PHPixie\Paginate\Pager', array(
            'repository' => $repository,
            'pageSize'   => 15
        ));
    }
    
    /**
     * @covers ::arrayRepository
     * @covers ::<protected>
     */
    public function testArrayRepository()
    {
        $items = array('test');
        $repository = $this->builder->arrayRepository($items);
        
        $this->assertInstance($repository, '\PHPixie\Paginate\Repository\ArrayAccess', array(
            'items' => $items
        ));
    }
}