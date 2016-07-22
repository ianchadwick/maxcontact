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

use GuzzleHttp\Post\MultipartBody;
use GuzzleHttp\Post\PostFile;

abstract class ImportAbstract extends Command
{
    /**
     * @var int
     */
    private $listId;

    /**
     * @var string
     */
    private $mapName;

    /**
     * @var string
     */
    private $data;

    /**
     * @var string
     */
    private $fileName;

    /**
     * Get the file extension for the default import filename
     *
     * @return string
     */
    abstract protected function getFileExtension();

    /**
     * Convert the data to the format for the web request
     *
     * @param array $data
     * @return string
     */
    abstract protected function convertToPayload(array $data);

    /**
     * ImportAbstract constructor.
     *
     * @param int $listId
     * @param string $mapName
     * @param array $data
     * @param string $fileName
     */
    public function __construct($listId, $mapName, array $data, $fileName = null)
    {
        $this->listId = $listId;
        $this->mapName = $mapName;
        $this->fileName = ($fileName
            ? $fileName : (new \DateTime())->format('Y-m-d-H-i-s') . '.' . $this->getFileExtension());
        $this->data = $this->convertToPayload($data);
    }

    /**
     * @inheritDoc
     */
    public function getMethod()
    {
        return 'post';
    }

    /**
     * @inheritdoc
     */
    public function getPayload(array $payload)
    {
        $boundary = uniqid();

        $fields = [
            'mapname' => $this->mapName,
            'options' => json_encode([
                'ListId' => $this->listId,
            ])
        ];

        $files = [
            new PostFile('file', $this->data, $this->fileName, [
                'Content-Type' => 'application/octet-stream'
            ])
        ];

        return array_merge($payload, [
            'headers' => [
                'Content-Type' => 'multipart/form-data; boundary="' . $boundary . '"'
            ],
            'body' => new MultipartBody($fields, $files, $boundary)
        ]);
    }
}
