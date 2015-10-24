<?php
namespace PHPVCSControl\ValueObjects\Web;

use PHPVCSControl\ValueObjects\RepositorySourceInterface;
use PHPVCSControl\ValueObjects\StringLiteral\StringLiteral;

class Url extends StringLiteral implements RepositorySourceInterface
{
    public function __construct($value)
    {
        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
            throw new \InvalidArgumentException('Invalid url format.');
        }

        parent::__construct($value);
    }
}
