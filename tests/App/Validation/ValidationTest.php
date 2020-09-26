<?php

namespace App\Validation;

use PHPUnit\Framework\TestCase;

class ValidationTest extends TestCase
{
    public function testIfEmailsValid(): void
    {
        $validation = new Validation();

        $isEmailValidTrue = $validation->isEmailValid('test@test.test');
        $this->assertTrue($isEmailValidTrue);

        $isEmailValidFalse = $validation->isEmailValid('test@testtest');
        $this->assertFalse($isEmailValidFalse);
    }

    public function testIfEmailIsInLeadListDataFile(): void
    {
        $paramArray = [['email' => 'test@test.test']];

        $validation = new Validation();
        $isEmailInLeadListTrue = $validation->isEmailInLeadList('test@test.test', $paramArray);

        $this->assertTrue($isEmailInLeadListTrue);
        $this->assertIsBool($isEmailInLeadListTrue);

        $isEmailInLeadListFalse =$validation->isEmailInLeadList('testtest.test', $paramArray);

        $this->assertFalse($isEmailInLeadListFalse);
        $this->assertIsBool($isEmailInLeadListFalse);
    }
}