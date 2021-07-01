<?php


namespace App\Factory;


use App\Entity\Author;

class AuthorFactory
{
    private const WHITELISTED_PARAMETERS = [
        'name',
    ];

    public static function build(array $params): Author
    {
        $author = new Author();

        foreach ($params as $key => $value) {
            if (in_array($key, self::WHITELISTED_PARAMETERS)) {
                $setter = sprintf('set%s', ucfirst($key));
                $author->$setter($value);
            }
        }

        return $author;
    }
}
