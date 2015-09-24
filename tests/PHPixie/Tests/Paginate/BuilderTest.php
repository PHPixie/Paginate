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
        $Loader = $this->quickMock('\PHPixie\Paginate\Loader');
        $pager = $this->builder->pager($Loader, 15);
        
        $this->assertInstance($pager, '\PHPixie\Paginate\Pager', array(
            'Loader' => $Loader,
            'pageSize'   => 15
        ));
    }
    
    /**
     * @covers ::arrayLoader
     * @covers ::<protected>
     */
    public function testArrayLoader()
    {
        $items = array('test');
        $Loader = $this->builder->arrayLoader($items);
        
        $this->assertInstance($Loader, '\PHPixie\Paginate\Loader\ArrayAccess', array(
            'items' => $items
        ));
    }
}