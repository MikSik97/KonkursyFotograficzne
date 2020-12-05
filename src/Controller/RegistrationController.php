<?php

namespace App\Controller;

use App\Entity\UserAccounts;
use App\Form\UserAccountsType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;


class RegistrationController extends AbstractController
{

    /**
     * @Route("/registration", name="registration")
     * @param request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface $entityManager
     * @param Session $session
     * @return Response
     */
    public function registration(request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager, Session $session)
    {

        $form = $this->createForm(UserAccountsType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userAccount = $form->getData();
            $password = $passwordEncoder->encodePassword($userAccount, $form->get("password")->getData());
            $userAccount->setPassword($password);
            $userAccount->setRoles($userAccount->getRoles());

            $email = $this->getDoctrine()->getRepository('App:UserAccounts')->findOneBySomeField($userAccount->getEmail());
            if($email){
                return $this->render('registration/registration.html.twig', [
                    'form' => $form->createView(),
                    "error"=> "podany email posiada już konto"
                ]);
            }else{

            $entityManager->persist($userAccount);
            $entityManager->flush();
                return $this->redirect("/");
            }
        }

        return $this->render('registration/registration.html.twig', [
            'form' => $form->createView(),
            "error"=> null
        ]);
    }

}
