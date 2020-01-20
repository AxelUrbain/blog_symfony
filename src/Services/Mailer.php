<?php


namespace App\Services;


use App\Entity\Article;
use Symfony\Contracts\Translation\TranslatorInterface;

class Mailer
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    private $translator;

    public function __construct(\Swift_Mailer $mailer, TranslatorInterface $translator)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
    }
    public function sendNumberViews(Article $article)
    {
        $nbViews = $article->getNbViews();
        $message = new \Swift_Message("Nombre de vues","Le nombre de vue de l'article est de : $nbViews");
        $message->addTo('urbainaxel.au@gmail.com')
            ->addFrom('urbainaxel.au@gmail.com');
        $this->mailer->send($message);
    }
}