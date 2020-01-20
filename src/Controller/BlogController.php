<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class BlogController extends AbstractController
{
    /**
     * @Route({
     *  "fr": "/",
     *  "en": "/"
     * }, name="home")
     */
    public function index()
    {
        return $this->render('base.html.twig');
    }
}