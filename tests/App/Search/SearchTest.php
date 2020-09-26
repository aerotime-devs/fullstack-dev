<?php

namespace App\Search;

use PHPUnit\Framework\TestCase;

class SearchTest extends TestCase
{
    public function testSearchByUserFirstNameAndLastName(): void
    {
        $search = new Search();
        $fakeData = $this->fakeData();

        $searchByNameAndLastNameTrue = $search->search($fakeData['data'], [
            'firstname' => 'TestName',
            'lastname'  => 'TestLastName'

        ]);

        $this->assertIsArray($searchByNameAndLastNameTrue);
        $this->assertNotEmpty($searchByNameAndLastNameTrue);
        $this->assertArrayHasKey('firstname', $searchByNameAndLastNameTrue[0]);
        $this->assertArrayHasKey('lastname', $searchByNameAndLastNameTrue[0]);
        $searchByNameAndLastNameFalse = $search->search($fakeData['data'], [
            'firstname' => 'TestNameF',
            'lastname'  => 'TestLastNameF'

        ]);

        $this->assertIsArray($searchByNameAndLastNameFalse);
        $this->assertEmpty($searchByNameAndLastNameFalse);
    }

    public function testSearchPersonByEmailFromArrayAndCheckReturnArrayOrFalse(): void
    {
        $search = new Search();
        $fakeData = $this->fakeData();

        $searchPersonByEmailFromArray = $search->searchPersonByEmailFromArray(
            $fakeData['data'],
            'test@meil.com'
        );

        $this->assertIsInt($searchPersonByEmailFromArray);
        $this->assertIsNotBool($searchPersonByEmailFromArray);

        $searchPersonByEmailFromArrayReturnFalse = $search->searchPersonByEmailFromArray(
            $fakeData['data'], '
            test@mexil.com'
        );

        $this->assertIsBool($searchPersonByEmailFromArrayReturnFalse);
        $this->assertIsNotInt($searchPersonByEmailFromArrayReturnFalse);
    }

    /**
     * @return array
     */
    public function fakeData(): array
    {
        return [
            'data' => [
                [
                    'firstname' => 'TestName',
                    'lastname'  => 'TestLastName',
                    'email'     => 'test@meil.com',
                ],
                [
                    'firstname' => 'TestName',
                    'lastname'  => 'TestLastNameFalse',
                    'email'     => 'test@meil.com',
                ],
                [
                    'firstname' => 'TestName',
                    'lastname'  => 'TestLastName',
                    'email'     => 'test@meil.com',
                ]
            ],
        ];
    }
}