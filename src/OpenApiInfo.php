<?php

namespace BYanelli\OpenApiLaravel;

use Spatie\DataTransferObject\DataTransferObject;

class OpenApiInfo extends DataTransferObject
{
    use DtoSerializationOptions;

    /** @var string */
    public $title;

    /** @var string */
    public $version;

    /** @var string */
    public $description = '';

    /** @var string */
    public $termsOfService = '';

    /** @var string */
    public $contactName = '';

    /** @var string */
    public $contactEmail = '';

    /** @var string */
    public $contactUrl = '';

    /** @var string */
    public $licenseName = '';

    /** @var string */
    public $licenseUrl = '';

    /** @var \BYanelli\OpenApiLaravel\OpenApiExternalDocs|null */
    public $externalDocs;

    protected $applyKeys = [
        'licenseName'               => 'license.name',
        'licenseUrl'                => 'license.url',
        'contactName'               => 'contact.name',
        'contactEmail'              => 'contact.email',
        'contactUrl'                => 'contact.url',
    ];

    public $ignoreKeysIfEmpty = [
        'description',
        'termsOfService',
        'contactName',
        'contactEmail',
        'contactUrl',
        'licenseName',
        'licenseUrl',
        'externalDocs',
    ];
}