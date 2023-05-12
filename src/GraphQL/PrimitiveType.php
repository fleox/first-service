<?php

namespace App\GraphQL;

enum PrimitiveType: string
{
    public const STRING = 'String';
    public const STRING_REQUIRED = self::STRING . '!';

    public const INT = 'Int';
    public const INT_REQUIRED = self::INT . '!';

    public const BOOLEAN = 'Boolean';
    public const BOOLEAN_REQUIRED = self::BOOLEAN . '!';

    public const ARRAY_INT = '[Int]';
    public const ARRAY_INT_REQUIRED = '[Int!]';
}
