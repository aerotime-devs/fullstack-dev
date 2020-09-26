<?php

namespace App\Serializer;

use PHPUnit\Framework\TestCase;

class LeadListEncodeTest extends TestCase
{
    public function testIfDecodeFromFileCsvToArray(): void
    {
        $leadListEncode = new LeadListEncode();
        $leadListEncodeToJsonFormat = $leadListEncode->leadListEncodeToJsonFormat([], [
            'firstname' => 'Test'
        ]);

        $data = json_decode($leadListEncodeToJsonFormat, true);

        $this->assertJson($leadListEncodeToJsonFormat);
        $this->assertArrayHasKey('firstname', $data[0]);
    }
}