<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route(
     *     {
     *  "fr": "/login",
     *  "en": "/login"
     * }, name="app_login")
     * @param Request $request
     * @param AuthenticationUtils $authUtils
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(Request $request, AuthenticationUtils $authUtils)
    {
        $error = $authUtils->getLastAuthenticationError();

        $lastUsername = $authUtils->getLastUsername();


        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error,
        ));
    }

    /**
     * @Route({
     *  "fr": "/login_check",
     *  "en": "/login_check"
     * }, name="app_login_check")
     * On déclare une route mais l'action ne sera jamais exécutée car Symfony catche l'évènement
     *
     * @return void
     * @throws \Exception
     */
    public function loginCheckAction()
    {
        throw new \Exception('Unexpexted loginCheck action');
    }

    /**
     * @Route({
     *  "fr": "/logout",
     *  "en": "/logout"
     * }, name="app_logout")
     */
    public function logout()
    {
        return $this->render('base.html.twig');
    }
}