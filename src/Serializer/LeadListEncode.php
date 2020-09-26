<?php

namespace App\Serializer;

class LeadListEncode
{
    /**
     * @param $leadListData
     * @param array $fields
     * @return false|string
     */
    public function leadListEncodeToJsonFormat($leadListData, array $fields)
    {
        $leadListData[] = $fields;

        return $this->jsonEncode($leadListData);
    }

    /**
     * @param array $decodedToArrayData
     * @return false|string
     */
    public function removeArrayKeys(array $decodedToArrayData)
    {
        $formattedArray = array_values($decodedToArrayData);

        return  $this->jsonEncode($formattedArray);
    }

    /**
     * @param $encodeData
     * @return false|string
     */
    private function jsonEncode(array $encodeData)
    {
        return json_encode($encodeData);
    }
}