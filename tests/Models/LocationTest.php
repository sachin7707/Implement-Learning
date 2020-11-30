<?php

use App\Location;

/**
 * @author jimmiw
 * @since 2019-01-30
 */
class LocationTest extends TestCase
{
    public function testAlive()
    {
        $location = Location::where('externalId', '123')->first();
        $this->assertNull($location);
    }
}