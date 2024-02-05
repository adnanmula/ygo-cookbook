<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Ygo;

use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Response;

final class HomeController extends Controller
{
    public function __invoke(): Response
    {
        return $this->render('Ygo/home.html.twig');
    }
}
