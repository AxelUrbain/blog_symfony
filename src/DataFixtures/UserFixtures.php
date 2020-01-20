<?php
namespace App\DataFixtures;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setPassword($this->encoder->encodePassword($user, 'password'));
        $user->setRoles(['ROLE_ADMIN']);
        $this->addReference('axel', $user);

        $user2 = new User();
        $user2->setUsername('user');
        $user2->setPassword($this->encoder->encodePassword($user2, 'password'));
        $user2->setRoles(['ROLE_USER']);
        $this->addReference('steven', $user2);
        $manager->persist($user);
        $manager->persist($user2);
        $manager->flush();
    }
}