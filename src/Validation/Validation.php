<?php

namespace App\Validation;

class Validation
{
    public const PERSON_NOT_ADDED_IN_LEAD_LIST = 'Person not added in lead list.';
    public const EMAIL_NOT_VALID = 'Email not valid!';
    public const PERSON_EMAIL_ALREADY_IN_LIST = 'Person with this email already is in Lead list';
    public const PERSON_ADDED_SUCCESS = 'Person added Successfully';
    public const PERSON_NOT_FOUND = 'Person not find';
    public const PERSON_DELETED_FROM_FILE = 'Person deleted from file.';
    public const CSV_FILE_IMPORTED_SUCCESS = 'Csv file imported Successfully';
    public const CSV_FILE_HEADER_IS_EMPTY = 'Csv file header is empty!';
    public const FILE_HEADER_AND_ROW_DATA_ERROR = 'Csv file header number and data row number not match!';
    public const IMPORTED_IN_FILE = 'Imported in file: ';
    public const DUPLICATE_ROWS = 'Duplicate rows not imported: ';

    /**
     * @param string $email
     * @return bool
     */
    public function isEmailValid(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * @param string $email
     * @param $leadListData
     * @return bool
     */
    public function isEmailInLeadList(string $email, array $leadListData): bool
    {
        $emailListArray = array_column($leadListData, 'email');

        return in_array($email, $emailListArray, true);
    }

    /**
     * @param array $dataArray
     * @return array
     */
    public function removeDuplicatedRowsInArray(array $dataArray): array
    {
        return array_values(
            array_map('unserialize',
                array_unique(
                    array_map('serialize', $dataArray)
                )
            )
        );
    }
}