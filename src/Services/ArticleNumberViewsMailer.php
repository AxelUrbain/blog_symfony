<?php


namespace App\Services;

use App\Entity\Article;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class ArticleNumberViewsMailer implements EventSubscriber
{
    private $mailer;
    private const NB_VIEWS_TO_SEND_AN_EMAIL = [10, 50, 100, 200, 300, 400, 500, 1000];
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }
    public function getSubscribedEvents()
    {
        return ['preUpdate'];
    }
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Article) {
            return;
        }
        if (!in_array($entity->getNbViews(), self::NB_VIEWS_TO_SEND_AN_EMAIL) ) {
            return;
        }
        $this->mailer->sendNumberViews($entity);
    }
}