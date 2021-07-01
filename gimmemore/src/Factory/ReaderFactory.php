<?php


namespace App\Factory;


use App\Entity\Reader;

class ReaderFactory
{
    private const WHITELISTED_PARAMETERS = [
        'name',
    ];

    public static function build(array $params): Reader
    {
        $reader = new Reader();

        foreach ($params as $key => $value) {
            if (in_array($key, self::WHITELISTED_PARAMETERS)) {
                $setter = sprintf('set%s', ucfirst($key));
                $reader->$setter($value);
            }
        }

        return $reader;
    }
}
