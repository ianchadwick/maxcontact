<?php
/**
 * All rights reserved. No part of this code may be reproduced, modified,
 * amended or retransmitted in any form or by any means for any purpose without
 * prior written consent of Mizmoz Limited.
 * You must ensure that this copyright notice remains intact at all times
 *
 * @package Mizmoz
 * @copyright Copyright (c) Mizmoz Limited 2016. All rights reserved.
 */

namespace MaxContact\Commands;

class ImportCsvFiles extends ImportAbstract
{
    /**
     * @inheritDoc
     */
    protected function getFileExtension()
    {
        return 'csv';
    }

    /**
     * @inheritDoc
     */
    protected function convertToPayload(array $data)
    {
        return $this->createCsv($data);
    }

    /**
     * Create the CSV
     *
     * @param array $data
     * @param string $delimiter
     * @param string $enclosure
     * @return string
     */
    private function createCsv(array $data, $delimiter = ',', $enclosure = '"')
    {
        $csv = [];

        reset($data);
        if (is_string(key(current($data)))) {
            // generate the first row of keys
            $csv[] = $this->createCsv([array_keys(current($data))]);
        }

        foreach ($data as $key => $values) {
            $csv[] = join($delimiter, array_map(function ($value) use ($enclosure) {
                $value = str_replace($enclosure, $enclosure . $enclosure, $value);
                return $enclosure . $value . $enclosure;
            }, $values));
        }

        return join("\n", $csv);
    }
}
