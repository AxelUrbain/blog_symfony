<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Services\AntiSpam\AntiSpam;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/blog")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route({
     *  "fr": "/list/{page}",
     *  "en": "/list/{page}"
     * },
     * defaults={"page": "1"},
     * name="listAction",
     * methods={"GET"})
     *
     * @IsGranted("ROLE_USER")
     */
    public function index($page, EntityManagerInterface $em): Response
    {
       if($page < 1){
           throw new NotFoundHttpException("La page $page n'existe pas !");
       }

       $currentPath = 'listAction';

        $nbPerPage = $this->getParameter('nbPerPage');
        $articlesPaginator = $em->getRepository('App:Article')->myFindAllWithPaging($page, $nbPerPage);

        $nbTotalPages = intval(ceil(count($articlesPaginator) / $nbPerPage ));


       return $this->render('article/list.html.twig',[
            'articles' => $articlesPaginator,
            'nbPerPage' => $nbTotalPages,
            'page' => $page,
           'currentPath' => $currentPath]);
    }


    /**
     * @Route("/article/add", name="addAction", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param AntiSpam $spam
     * @return Response
     */
    public function new(Request $request, EntityManagerInterface $em, AntiSpam $spam, TranslatorInterface $translate): Response
    {
            $article = new Article();
            $form = $this->createForm(ArticleType::class, $article);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                if($spam->isSpam($article->getContent())){
                    $messageAdd = "Des spams sont détectés dans votre article !";
                    $translate = $translate->trans($messageAdd);
                    $this->addFlash('danger', $translate);
                    return $this->render('article/add.html.twig', array('form' => $form->createView()));
                }

                $em->persist($article);
                $em->flush();
                //Validation du formulaire
                $messageAdd = "Votre article est bien ajouté !";
                $translate = $translate->trans($messageAdd);
                $this->addFlash('success', $translate);
                return $this->redirectToRoute('listAction');
            }

            return $this->render('article/add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/article/{id}",
     *     name="viewAction",
     *     requirements={
                "id" : "\d+"
     *     }
     *     )
     *
     * @IsGranted("ROLE_USER")
     */
    public function show($id ,EntityManagerInterface $em, Security $security): Response
    {
        $article = $em->getRepository('App:Article')->findWithCategories($id);

        dump($article);
        if($article){

            if (!$security->isGranted('view', $article)) {
                throw new NotFoundHttpException('L\'article est inconnu');
            }

            $newView = 1;
            $views = $article->getNbViews();
            $views = $views+$newView;
            $article->setNbViews($views);
            $em->flush();

            return $this->render('article/view.html.twig',[
                'article' => $article
            ]);
        }
        else{
            throw new NotFoundHttpException("L'article n'existe pas !");
        }
    }

    /**
     * @Route("/article/edit/{id}",
     * name="editAction",
     *  requirements={
     *     "id" : "\d+"
     *  },
     *  methods={"GET","POST"}
     * )
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Article $article, TranslatorInterface $translator, Security $security): Response
    {
        if (!$security->isGranted('edit', $article)) {
            throw new NotFoundHttpException('Vous n\'avez pas les droits de modification sur cet article');
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article->setUpdatedAt(new \DateTime('NOW', new \DateTimeZone('Europe/Paris')));
            $this->getDoctrine()->getManager()->flush();

            $messageAdd = "Votre article est bien modifié !";
            $translator = $translator->trans($messageAdd);
            $this->addFlash('success',$translator);
            return $this->redirectToRoute('listAction');
        }
        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/article/delete/{id}", name="deleteAction")
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete($id, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {
        $article = $em->getRepository('App:Article')->find($id);

        if(! $article)
        {
            $messageAdd = "L'article n'existe pas !";
            $translator = $translator->trans($messageAdd);
            throw new NotFoundHttpException($translator);
        }

        $em->remove($article);
        $em->flush();

        //Suppression d'un article
        $messageAdd="Votre aticle est supprimé !";
        $translator = $translator->trans($messageAdd);
        $this->addFlash('info',$translator);
        return $this ->redirectToRoute("listAction");
    }

    /**
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function recentArticlesAction(EntityManagerInterface $em): Response
    {
        $article = $em->getRepository('App:Article')->findBy(
            array('published' => true),
            array('created_at' => 'desc'),
            3
        );

        $categories = $em->getRepository('App:Category')->findAll();

        dump($article);

        return $this->render('last_articles.html.twig', [
           'articles' => $article,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/category/{id}/{page}",
     *     defaults={"page": "1"},
     *     name="viewCategory",
     *     requirements={
    "id" : "\d+"
     *     },
     *     methods={"GET"}
     *     )
     * @IsGranted("ROLE_USER")
     *
     * @param $id
     * @param $page
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function showArticleWithCategory($id, $page,EntityManagerInterface $em) :Response
    {
        if($page < 1){
            throw new NotFoundHttpException("La page $page n'existe pas !");
        }

        $currentPath = 'viewCategory';

        $nbPerPage = $this->getParameter('nbPerPage');
        $articles = $em->getRepository('App:Article')->findAllArticleWithCategory($id,$page,$nbPerPage);
        $nbTotalPages = intval(ceil(count($articles) / $nbPerPage ));

        return $this->render('article/list.html.twig',[
            'articles' => $articles,
            'nbPerPage' => $nbTotalPages,
            'page' => $page,
            'id' => $id,
            'currentPath' => $currentPath]);
    }

    /**
     * @param AntiSpam $spam
     * @return Response
     * @Route("/test/antispam", name="antispam")
     */
    public function testAntiSpam(AntiSpam $spam):Response
    {
        return new Response('Coucou : '.$spam->isSpam('aaaaaa'));
    }

}
