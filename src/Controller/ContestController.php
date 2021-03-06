<?php

namespace App\Controller;

use App\Entity\Contest;
use App\Entity\Contestants;
use App\Entity\Organizer;
use App\Entity\Photo;
use App\Entity\VoteLog;
use App\Form\ContestType;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ContestController extends AbstractController
{
    /**
     * @Route("/new_contest", name="new_contest")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function new_contest(request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $username= $this->getUser()->getUsername();
        $user = $this->getDoctrine()->getRepository("App:UserAccounts")->findOneBySomeField($username);
        $userId = $user->getId();
        $sql = " 
                    select count(user_id_id) as id
                    from organizer join contest c on organizer.contest_id = c.id
                    where vote_end_time >= CURRENT_TIMESTAMP and user_id_id = $userId;   
                            ";
        $em = $this->getDoctrine()->getManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        $amount=  $stmt->fetch();
        var_dump($amount);
        if($amount['id'] >= 3){
            $this->addFlash(
                'error',
                'Zbyt wiele aktywnych konkursów'
            );
            return $this->redirect('/contests');
        }
        if (isset($_POST['save'])) {
            if(!$this->getDoctrine()->getRepository('App:Contest')->findOneBy(array("name" => $_POST['name']))){
                if(!empty($_POST['name']) and  !empty($_POST['theme']) and !empty($_POST['user_limit']) and !empty($_POST['photo_limit']) and !empty($_POST['deadline']) and !empty($_POST['voteEnd']) and !empty($_POST['voteStart'])){
                    if(strtotime($_POST['deadline']) and strtotime($_POST['voteStart']) and strtotime($_POST['voteEnd']) and strtotime($_POST['deadline']) <= strtotime($_POST['voteStart']) and strtotime($_POST['voteStart']) <= strtotime($_POST['voteEnd'])) {
                        $contest = new Contest();
                        $contest->setName($_POST['name']);
                        $contest->setTheme($_POST['theme']);
                        $contest->setUserLimit($_POST['user_limit']);
                        $contest->setPhotoLimit($_POST['photo_limit']);
                        $deadline = new \DateTime('@'.strtotime($_POST['deadline']));
                        $start = new \DateTime('@'.strtotime($_POST['voteStart']));
                        $end = new \DateTime('@'.strtotime($_POST['voteEnd']));
                        $contest->setApplicationsDeadline($deadline);
                        $contest->setVoteStartTime($start);
                        $contest->setVoteEndTime($end);
                        if(!empty($_POST['form'])){
                            $form =true;
                        }else{
                            $form =false;
                        }
                        $contest->setUseForm($form);
                        $entityManager->persist($contest);
                        $entityManager->flush();

                        $c = $this->getDoctrine()->getRepository('App:Contest')->findOneBy(array("name" => $_POST['name']));
                        $organizer = new Organizer();
                        $organizer->setUserId($user);
                        $organizer->setContest($c);
                        $entityManager->persist($organizer);
                        $entityManager->flush();
                        return $this->redirect('/');
                    }else {
                        $this->addFlash(
                            'error',
                            'Jedna z dat jest nieprawidłowa'
                        );
                        return $this->render('contest/new_contest.html.twig', [
                        ]);
                    }
                }else {
                    $this->addFlash(
                        'error',
                        'Uzupełnij wszystkie pola formularza'
                    );
                    return $this->render('contest/new_contest.html.twig', [
                ]);
                }
            } else{
                return $this->render('contest/new_contest.html.twig', [
                ]);
            }
        }
        return $this->render('contest/new_contest.html.twig', [
        ]);
    }

    /**
     * @Route("/contests", name="contests")
     */
    public function constests()
    {
        $contest = $this->getDoctrine()->getRepository('App:Contest')->findAll();
        return $this->render('contest/contests.html.twig', [
            'contests' => $contest,
        ]);
    }

    /**
     * @Route("/contest/{id}", name="contest_by_id")
     * @param $id
     * @return Response
     */
    public function contest($id){
        if (!$this->getDoctrine()->getRepository('App:Contest')->findOneBy(array('id' => $id))) {
            throw $this->createNotFoundException("Brak konkursu");
        }
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $contest = $this->getDoctrine()->getRepository('App:Contest')->findOneBy(array('id' => $id));
        $contestants_list= $contest->getMyContestants();
        $free_places= $contestants_list->count();
        $free_places = $contest->getUserLimit()-$free_places;
        $user = $this->getUser();
        $photos = $this->getDoctrine()->getRepository("App:Photo")->findBy(array("author" =>$user, "contest" =>$contest));
        $is_contestant = $this->getDoctrine()->getRepository("App:Contestants")->findOneBy(array("user_id" =>$user, "contest" =>$contest));
        $photos_count = count($photos);

        return $this->render('contest/contest.html.twig', [
            'contest' => $contest,
            "photos" => $photos,
            "free_places" => $free_places,
            "photos_count" => $photos_count,
            "is_contestant" => $is_contestant,

        ]);
    }

    /**
     * @Route("/contest/{id_c}/sign", name="sing_for_contest")
     * @param $id_c
     * @param EntityManagerInterface $entityManager
     */
    public function signToContest($id_c, EntityManagerInterface $entityManager){
        if (!$this->getDoctrine()->getRepository('App:Contest')->findOneBy(array('id' => $id_c))) {
            throw $this->createNotFoundException('Brak konkursu');
        }
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $username= $this->getUser()->getUsername();
        $user = $this->getDoctrine()->getRepository("App:UserAccounts")->findOneBySomeField($username);
        $userId = $user->getId();
        $for_c = $this->getDoctrine()->getRepository("App:Contest")->findOneBy(array("id" => $id_c));
        $deadline= $for_c->getApplicationsDeadline();
        $contestants_list= $for_c->getMyContestants();
        $contestant_count= $contestants_list->count();
        $user_limit=$for_c->getUserLimit();
        if($contestant_count <$user_limit ){
            if($deadline >= new DateTime()){
                if(!$this->getDoctrine()->getRepository("App:Contestants")->findOneBy(array("contest" => $id_c, "user_id"=>$userId))){
                    $contestant = new Contestants();
                    $contestant->setUserId($user);
                    $contestant->setContest($for_c);
                    $contestant->setPhotoCount(0);
                    $entityManager->persist($contestant);
                    $entityManager->flush();
                    $this->addFlash(
                        'notice',
                        'Zapisano do konkursu!'
                    );
                    return $this->redirect("/contest/$id_c");
                }else{
                    $this->addFlash(
                        'error',
                        'już zapisany'
                    );
                    return $this->redirect("/contest/$id_c");
                }
            } else{
                $this->addFlash(
                    'error',
                    'po zapisach!'
                );
                return $this->redirect("/contest/$id_c");
            }
        }else{
            $this->addFlash(
                'error',
                'brak miejsc!'
            );
            return $this->redirect("/contest/$id_c");
        }
    }

    /**
     * @Route("/contest/{id_c}/photo", name="add_photo")
     * @param $id_c
     * @param EntityManagerInterface $entityManager
     */
    public function PhotoToContest($id_c, EntityManagerInterface $entityManager){

        if (!$this->getDoctrine()->getRepository('App:Contest')->findOneBy(array('id' => $id_c))) {
            throw $this->createNotFoundException('Brak konkursu');
        }

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user= $this->getUser();
        $username= $this->getUser()->getUsername();

        $contest = $this->getDoctrine()->getRepository('App:Contest')->findOneBy(array('id' => $id_c));
        $photo_limit = $contest->getPhotoLimit();
        $user_photos = $this->getDoctrine()->getRepository('App:Photo')->findBy(array('author' =>$user,'contest' => $contest));
        $user_photos_count = count($user_photos);

        if($user_photos_count < $photo_limit){
            $target_dir = "Dokumenty/$id_c/";
            $array = explode(".", $_FILES["fileToUpload"]["name"]);
            $newfilename = time() . '_' . rand(100, 999) . '.' . end($array);
            $target_file =$target_dir . $newfilename;
            $uploadOk = 1;
            if (file_exists($target_file)) {
                $uploadOk = 0;
            }
            if ($_FILES["fileToUpload"]["size"] > 32000000) {
                $uploadOk = 0;
            }
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0700, true);
            }
            if ($uploadOk == 0) {
                $this->addFlash(
                    'error',
                    'Wystąpił błąd!'
                );
                return $this->redirect("/contest/$id_c");
            } else {
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                    $contest = $this->getDoctrine()->getRepository('App:Contest')->findOneBy(array('id' => $id_c));
                    # Database stuff
                    $photo = new Photo();
                    $photo->setAuthor($user);
                    $photo->setContest($contest);
                    $photo->setFilepath($target_file);
                    $photo->setScore(0);
                    $entityManager->persist($photo);
                    $temp = $this->getDoctrine()->getRepository("App:UserAccounts")->findOneBySomeField($username);
                    $idUser= $temp->getId();
                    $contestant = $this->getDoctrine()->getRepository('App:Contestants')->findOneBy(array('user_id'=> $idUser, 'contest'=>$id_c));
                    $contestant->setPhotoCount($contestant->getPhotoCount()+1);
                    $entityManager->persist($contestant);
                    $entityManager->flush();
                    $this->addFlash(
                        'notice',
                        'Dodano zdjęie!'
                    );
                    return $this->redirect("/contest/$id_c");
                }
                $this->addFlash(
                    'error',
                    'Wystąpił błąd!'
                );
                return $this->redirect("/contest/$id_c");
            }
        }else {
            $this->addFlash(
                'error',
                'Osiągnięto limit zdjęć!'
            );
            return $this->redirect("/contest/$id_c");
        }
    }

    /**
     * @Route("/contest/{id_c}/photos", name="view_photos")
     * @param $id_c
     * @return Response
     */
    public function contestPhotos($id_c){
        if (!$this->getDoctrine()->getRepository('App:Contest')->findOneBy(array('id' => $id_c))) {
            throw $this->createNotFoundException('Brak konkursu');
        }

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $contest = $this->getDoctrine()->getRepository('App:Contest')->findOneBy(array('id' => $id_c));
        $photos = $contest->getAppliedPhotos();

        return $this->render('contest/photos.html.twig', [
            "photos" => $photos,
            "contest"=> $contest,
        ]);
    }

    /**
     * @Route("/contest/{id_c}/vote", name="vote_in_contest")
     * @param $id_c
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @throws Exception
     */
    public function voteInContest($id_c, EntityManagerInterface $entityManager){
        if (!$this->getDoctrine()->getRepository('App:Contest')->findOneBy(array('id' => $id_c))) {
            throw $this->createNotFoundException('Brak konkursu');
        }

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $contest = $this->getDoctrine()->getRepository("App:Contest")->findOneBy(array("id" => $id_c));
        $startTime= $contest->getVoteStartTime();
        $endTime= $contest->getVoteEndTime();

        #czas głosowania
        if($startTime <= new \DateTime('@'.strtotime('now' . ' +1 hour'), new \DateTimeZone('Europe/Warsaw')) and $endTime >= new \DateTime('@'.strtotime('now' . ' +1 hour'), new \DateTimeZone('Europe/Warsaw'))){
            $username= $this->getUser()->getUsername();
            $user = $this->getDoctrine()->getRepository('App:UserAccounts')->findOneBy(array('email' => $username));
            $userId= $user->getId();
            #jeśli sędzia
            if($this->getDoctrine()->getRepository("App:Contestants")->findOneBy(array("contest" => $id_c, "user_id"=>$userId))) {
            ##vote stuff
            $sql = " 
                    select p.id, p.filepath, vl.grade
                    from photo p left  join vote_log vl
                        on
                         (p.id = vl.photo_id and vl.author_id= $userId)
                        or  (p.id = vl.photo_id and vl.author_id IS NULL)
                         where contest_id =$id_c and p.author_id != $userId;
        ";
            $em = $this->getDoctrine()->getManager();
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute();
            $photos=  $stmt->fetchAll();
            #zapisz głosy
            if($contest->getUseForm()){
                if (isset($_POST["save"])) {
                    foreach ($photos as $p) {
                        $photo = $this->getDoctrine()->getRepository('App:Photo')->findOneBy(array('id' => $p['id']));
                        if (!$this->getDoctrine()->getRepository("App:VoteLog")->findOneBy(array("photo" => $photo, "author" => $user))
                            and $_POST['comp' . $p['id']] !=null  and $_POST['tec' . $p['id']] !=null and $_POST['the' . $p['id']] !=null and $_POST['sub' . $p['id']] !=null) {
                            $score = $_POST['comp' . $p['id']] + $_POST['tec' . $p['id']] + $_POST['the' . $p['id']] + $_POST['sub' . $p['id']];
                            $new_grade = new voteLog();
                            $new_grade->setAuthor($user);
                            $new_grade->setPhoto($photo);
                            $new_grade->setGrade($score);
                            $date = new \DateTime('@' . strtotime('now'));
                            $new_grade->setDate($date);
                            $entityManager->persist($new_grade);
                            $entityManager->flush();
                            $this->addFlash(
                                'notice',
                                'Zapisano oceny!'
                            );
                        }elseif($_POST['comp' . $p['id']] !=null  and $_POST['tec' . $p['id']] !=null and $_POST['the' . $p['id']] !=null and $_POST['sub' . $p['id']] !=null){
                            $new_grade= $this->getDoctrine()->getRepository("App:VoteLog")->findOneBy(array("photo" => $photo, "author" => $user));
                            $score = $_POST['comp' . $p['id']] + $_POST['tec' . $p['id']] + $_POST['the' . $p['id']] + $_POST['sub' . $p['id']];
                            $new_grade->setGrade($score);
                            $date = new \DateTime('@' . strtotime('now'));
                            $new_grade->setDate($date);
                            $entityManager->persist($new_grade);
                            $entityManager->flush();
                            $this->addFlash(
                                'notice',
                                'Zapisano oceny!'
                            );
                        }

                    }
                    $sql = " 
                        select p.id, p.filepath, vl.grade
                        from photo p left  join vote_log vl
                            on
                             (p.id = vl.photo_id and vl.author_id= $userId)
                            or  (p.id = vl.photo_id and vl.author_id IS NULL)
                             where contest_id =$id_c and p.author_id != $userId;
            ";
                    $em = $this->getDoctrine()->getManager();
                    $stmt = $em->getConnection()->prepare($sql);
                    $stmt->execute();
                    $photos = $stmt->fetchAll();

                }
            }else {
                if (isset($_POST["save"])) {
                    foreach ($photos as $p) {
                        $photo = $this->getDoctrine()->getRepository('App:Photo')->findOneBy(array('id' => $p['id']));
                        if (!$this->getDoctrine()->getRepository("App:VoteLog")->findOneBy(array("photo" => $photo, "author" => $user)) and $_POST[$p['id']] != null) {
                            $p['grade'] = $_POST[$p['id']];
                            $new_grade = new voteLog();
                            $new_grade->setAuthor($user);
                            $new_grade->setPhoto($photo);
                            $new_grade->setGrade($p['grade']);
                            $date = new \DateTime('@' . strtotime('now'));
                            $new_grade->setDate($date);
                            $entityManager->persist($new_grade);
                            $entityManager->flush();

                        } elseif ($p['grade'] != $_POST[$p['id']] and $_POST[$p['id']] != null) {
                            $new_grade = $this->getDoctrine()->getRepository("App:VoteLog")->findOneBy(array("photo" => $photo, "author" => $user));
                            $new_grade->setGrade($_POST[$p['id']]);
                            $date = new \DateTime('@' . strtotime('now'));
                            $new_grade->setDate($date);
                            $entityManager->persist($new_grade);
                            $entityManager->flush();
                        }
                    }
                    $sql = " 
                        select p.id, p.filepath, vl.grade
                        from photo p left  join vote_log vl
                            on
                             (p.id = vl.photo_id and vl.author_id= $userId)
                            or  (p.id = vl.photo_id and vl.author_id IS NULL)
                             where contest_id =$id_c and p.author_id != $userId;
            ";
                    $em = $this->getDoctrine()->getManager();
                    $stmt = $em->getConnection()->prepare($sql);
                    $stmt->execute();
                    $photos = $stmt->fetchAll();
                    $this->addFlash(
                        'notice',
                        'Zapisano oddanie głosy!'
                    );
                }
            }
            return $this->render('contest/vote.html.twig', [
                "photos" => $photos,
                "contest" => $contest,
                ]);
        }else{
                $this->addFlash(
                    'error',
                    'Brak praw do udziału w głosowaniu!'
                );
                return $this->redirect("/contest/$id_c");
            }

        } else {
            $this->addFlash(
                'error',
                'Głosowanie aktualnie niedostępne!'
            );
            return $this->redirect("/contest/$id_c");
        }
    }

    /**
     * @Route("/contest/{id_c}/organizer", name="organizer")
     * @param $id_c
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @throws Exception
     */
    public function organizer($id_c, EntityManagerInterface $entityManager){
        if (!$this->getDoctrine()->getRepository('App:Contest')->findOneBy(array('id' => $id_c))) {
            throw $this->createNotFoundException('Brak konkursu');
        }

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $username= $this->getUser()->getUsername();
        $user = $this->getDoctrine()->getRepository("App:UserAccounts")->findOneBySomeField($username);
        $contest = $this->getDoctrine()->getRepository("App:Contest")->findOneBy(array("id" => $id_c));

        #organizator
        if($this->getDoctrine()->getRepository("App:Organizer")->findOneBy(array("contest" => $contest, "user_id"=>$user))) {
           #edycja konkursu
            if(isset($_POST['save'])) {
                if (!empty($_POST['theme']) and !empty($_POST['user_limit']) and !empty($_POST['photo_limit']) and
                    !empty($_POST['deadline']) and !empty($_POST['voteEnd']) and !empty($_POST['voteStart'])) {
                    if (strtotime($_POST['deadline']) and strtotime($_POST['voteStart']) and strtotime($_POST['voteEnd'])
                        and strtotime($_POST['deadline']) <= strtotime($_POST['voteStart']) and strtotime($_POST['voteStart'])
                        <= strtotime($_POST['voteEnd'])) {
                        $contest->setTheme($_POST['theme']);
                        $contest->setUserLimit($_POST['user_limit']);
                        $contest->setPhotoLimit($_POST['photo_limit']);
                        $deadline = new \DateTime('@' . strtotime($_POST['deadline']));
                        $start = new \DateTime('@' . strtotime($_POST['voteStart']));
                        $end = new \DateTime('@' . strtotime($_POST['voteEnd']));
                        $contest->setApplicationsDeadline($deadline);
                        $contest->setVoteStartTime($start);
                        $contest->setVoteEndTime($end);
                        $entityManager->persist($contest);
                        $entityManager->flush();
                        $this->addFlash(
                            'notice',
                            'zapisano zmiany!'
                        );
                    }else{
                        $this->addFlash(
                            'error',
                            'Jedna z dat jest nieprawidłowa'
                        );
                    }
                }else{
                    $this->addFlash(
                        'error',
                        'Uzupełnij wszystkie pola formularza'
                    );
                }
            }
            # Macierz sędzia/ oceny
            $my_contestants= $contest->getMyContestants();
            $my_photos =$contest->getAppliedPhotos();
            $user_photos= [];
            $grades = [];
            foreach($my_contestants as $contestant){
                $c =$contestant->getUserId();
                $contestantId= $c->getId();
                $contestantName=$c->getEmail();
                $sql = " 
                    select p.id,vl.grade
                    from photo p left  join vote_log vl
                        on
                         (p.id = vl.photo_id and vl.author_id= $contestantId)
                        or  (p.id = vl.photo_id and vl.author_id IS NULL)
                         where contest_id =$id_c /*and p.author_id != $contestantId*/
                         order by p.id ASC;
        ";
                $em = $this->getDoctrine()->getManager();
                $stmt = $em->getConnection()->prepare($sql);
                $stmt->execute();
                $grades +=  ["$contestantName" =>$stmt->fetchAll()];
                $user = $this->getDoctrine()->getRepository("App:UserAccounts")->findOneBy(array("id" => $contestantId));
                $up = $user->getMyPhotos();
                foreach($up as $key => $field){
                    $up[$key]= $field->getId();
                }
                $user_photos += ["$contestantName"=>$up ];
            }

            return $this->render('contest/organizer.html.twig', [
                "contest" => $contest,
                "grades" => $grades,
                "photos" =>$my_photos,
                "userPhotos" =>$user_photos,
            ]);

        }else{
            $this->addFlash(
                'error',
                'Brak praw dostępu!'
            );
            return $this->redirect("/contest/$id_c");
        }
    }

    /**
     * @Route("/contest/{id_c}/gen_res", name="generuj_wyniki")
     * @param $id_c
     * @param EntityManagerInterface $entityManager
     */
    public function genRes($id_c,EntityManagerInterface $entityManager){

        if (!$this->getDoctrine()->getRepository('App:Contest')->findOneBy(array('id' => $id_c))) {
            throw $this->createNotFoundException('Brak konkursu');
        }

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $username= $this->getUser()->getUsername();
        $user = $this->getDoctrine()->getRepository("App:UserAccounts")->findOneBySomeField($username);
        $userId= $user->getId();

        if($this->getDoctrine()->getRepository("App:Organizer")->findOneBy(array("contest" => $id_c, "user_id"=>$userId))) {
            $contest = $this->getDoctrine()->getRepository("App:Contest")->findOneBy(array('id' =>$id_c));
            $photos =$contest->getAppliedPhotos();
            foreach($photos as $p){
                $pId = $p->getId();
                $sql = " 
                    select avg(grade) as score
                    from vote_log
                    where photo_id=$pId;
        ";
                $em = $this->getDoctrine()->getManager();
                $stmt = $em->getConnection()->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetch();
                $photo_score = $result['score'];
                if($photo_score) {
                    $p->setScore($photo_score);
                    $entityManager->persist($p);
                    $entityManager->flush();
                }
            }
            return $this->redirect("/contest/$id_c/results");
        } else{
            $this->addFlash(
                'error',
                'Brak praw dostępu!'
            );
            return $this->redirect("/contest/$id_c");
        }
    }

    /**
     * @Route("/contest/{id_c}/results", name="wyniki")
     * @param $id_c
     * @return Response
     */
     public function result($id_c){

         if (!$this->getDoctrine()->getRepository('App:Contest')->findOneBy(array('id' => $id_c))) {
             throw $this->createNotFoundException('Brak konkursu');
         }

         $sql = " 
                select email, filepath, score
                from photo join contest c on photo.contest_id = c.id join user_accounts ua on photo.author_id = ua.id
                where photo.contest_id=$id_c
                order by score DESC;
        ";
         $em = $this->getDoctrine()->getManager();
         $stmt = $em->getConnection()->prepare($sql);
         $stmt->execute();
         $result = $stmt->fetchAll();
         return $this->render('contest/result.html.twig', [
             "result" => $result,
         ]);
     }
}
