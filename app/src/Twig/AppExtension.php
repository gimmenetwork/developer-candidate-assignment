<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Repository\BookStateRepository;
use App\Entity\Reader;

class AppExtension extends AbstractExtension
{

    public function __construct(private BookStateRepository $bookStateRepository)
    {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('current_leases', [$this, 'current_leases'], [
                'is_safe' => [
                    'html'
                ]
            ]),
        ];
    }

    public function current_leases(Reader $reader)
    {
        return $this->bookStateRepository->getCurrentLeases($reader);
    }

}
