<?php

/**
 * @author jimmiw
 * @since 2019-02-07
 */
class SignupTest extends TestCase
{
    public function testSignup()
    {
        $service = new \App\Newsletter\SignupService('https://analytics-eu.clickdimensions.com/forms/h/aVwnRDQEYkCW2dV6vA759A');
        $response = $service->signup('Jimmi', 'unitTest', 'jw@konform.com');
        $this->assertNotEmpty($response);
        $this->assertEquals('1', $response->success);
    }
}