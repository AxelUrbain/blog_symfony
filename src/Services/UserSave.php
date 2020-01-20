<?php
namespace App\Services;

use App\Entity\Article;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Security;

class UserSave implements EventSubscriber
{
    private $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getSubscribedEvents()
    {
        return ['prePersist'];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Article) {
            return;
        }
        if (null === $this->security || !is_object($user = $this->security->getUser())) {
            return;
        }
        $entity->setUser($user);
    }
}