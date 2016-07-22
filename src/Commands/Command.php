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


abstract class Command
{
    /**
     * Get the http method
     *
     * @return string
     */
    abstract public function getMethod();

    /**
     * Get the url for the command
     *
     * @param string $endpoint
     * @return string
     */
    public function getUrl($endpoint)
    {
        $name = explode('\\', get_called_class());
        $method = array_pop($name);
        return $endpoint . '/services/leadmanagement/' . $method;
    }

    /**
     * Get the payload for the request
     *
     * @param $payload
     * @return mixed
     */
    public function getPayload(array $payload)
    {
        return $payload;
    }
}
