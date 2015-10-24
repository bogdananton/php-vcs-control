<?php
namespace PHPVCSControl\ValueObjects;

use PHPVCSControl\ValueObjects\Exceptions\InvalidNativeArgumentException;
use PHPVCSControl\ValueObjects\Filesystem\Filepath;
use PHPVCSControl\ValueObjects\StringLiteral\StringLiteral;
use PHPVCSControl\ValueObjects\Web\Url;

class ValueObjectFactory
{
    const FILEPATH = 'FILEPATH';
    const STRING = 'STRING';
    const URL = 'URL';

    public static function get($native, $type = self::STRING)
    {
        switch ($type) {
            case self::FILEPATH:
                return Filepath::fromNative($native);
                break;

            case self::STRING:
                return StringLiteral::fromNative($native);
                break;

            case self::URL:
                return Url::fromNative($native);
                break;

            default:
                $oReflection = new \ReflectionObject(new self);
                $allowedTypes = array_keys($oReflection->getConstants());

                throw new InvalidNativeArgumentException($type, $allowedTypes);
                break;
        }
    }
}
