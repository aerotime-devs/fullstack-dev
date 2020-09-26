<?php

namespace App\Search;

class Search
{
    /**
     * @param $arrayData
     * @param $searchList
     * @return array
     */
    public function search($arrayData, $searchList): array
    {
        $result = [];

        foreach ($arrayData as $key => $value) {
            foreach ($searchList as $k => $v) {
                if (!isset($value[$k]) || $value[$k] !== $v) {
                    continue 2;
                }
            }

            $result[] = $value;
        }

        return $result;
    }

    /**
     * @param $arrayData
     * @param $searchEmail
     * @return false|int
     */
    public function searchPersonByEmailFromArray($arrayData, $searchEmail)
    {
        foreach ($arrayData as $key => $value) {
            if (!empty($value['email']) && $value['email'] === $searchEmail) {
                return $key;
            }
        }

        return false;
    }
}