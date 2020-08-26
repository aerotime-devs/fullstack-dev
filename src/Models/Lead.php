<?php


namespace MekDrop\FlatLeadsDB\Models;

/**
 * Defines lead
 *
 * @package MekDrop\FlatLeadsDB\Models
 *
 * @property string $firstName First name
 * @property string $lastName Last name
 * @property string $email Email
 * @property string $phone1 Phone #1
 * @property string $phone2 Phone #2
 * @property string $comment Comment
 */
class Lead extends AbstractModel
{

    /**
     * @inheritDoc
     */
    public function getValidationRules(): array
    {
        return [
            'firstName' => 'required | MinLength(min=1)',
            'lastName' => 'required | MinLength(min=1)',
            'email' => 'required | email | EmailDomain',
            'phone1' => 'required',
            'phone2' => null,
            'comment' => null
        ];
    }

    /**
     * @inheritDoc
     */
    public function getIdName(): string
    {
        return 'email';
    }
}