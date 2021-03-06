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

        if(isset($_POST['save'])) {
            if (!empty($_POST['username']) and !empty($_POST['password']) and !empty($_POST['password2'])) {
                if ($_POST['password'] == $_POST['password2']) {
                    $userAccount = new UserAccounts();
                    $userAccount->setEmail($_POST['username']);
                    $password = $passwordEncoder->encodePassword($userAccount, $_POST["password"]);
                    $userAccount->setPassword($password);
                    $userAccount->setRoles($userAccount->getRoles());

                    $email = $this->getDoctrine()->getRepository('App:UserAccounts')->findOneBySomeField($userAccount->getEmail());
                    if ($email) {
                        $this->addFlash(
                            'error',
                            'podany email posiada już konto!'
                        );
                        return $this->render('registration/registration.html.twig', [
                        ]);
                    }

                    $entityManager->persist($userAccount);
                    $entityManager->flush();
                    $this->addFlash(
                        'notice',
                        'Założono konto!'
                    );
                    return $this->redirect("/");
                } else {
                    $this->addFlash(
                        'error',
                        'podano różne hasła!'
                    );
                    return $this->render('registration/registration.html.twig', [
                    ]);
                }
            } else {
                $this->addFlash(
                    'error',
                    'uzupełnij wszystkie pola'
                );
                return $this->render('registration/registration.html.twig', [
                ]);
            }
        }else{
            return $this->render('registration/registration.html.twig', [
            ]);
        }
    }
}
