<?php
/**
 * SearchControllerTest.php
 * Copyright (C) 2016 Sander Dorigo
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */
use Illuminate\Support\Collection;


/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-01-19 at 15:39:29.
 */
class SearchControllerTest extends TestCase
{
    /**
     * @covers FireflyIII\Http\Controllers\SearchController::__construct
     * @covers FireflyIII\Http\Controllers\SearchController::index
     */
    public function testIndex()
    {
        $searcher = $this->mock('FireflyIII\Support\Search\SearchInterface');
        $searcher->shouldReceive('searchTransactions')->once()->with(['test'])->andReturn(new Collection);
        $searcher->shouldReceive('searchAccounts')->once()->with(['test'])->andReturn(new Collection);
        $searcher->shouldReceive('searchCategories')->once()->with(['test'])->andReturn(new Collection);
        $searcher->shouldReceive('searchBudgets')->once()->with(['test'])->andReturn(new Collection);
        $searcher->shouldReceive('searchTags')->once()->with(['test'])->andReturn(new Collection);

        $this->be($this->user());
        $this->call('GET', '/search?q=test&search=');
        $this->assertResponseStatus(200);
    }
}
