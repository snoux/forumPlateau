<?php

namespace Controller;

use App\Session;
use App\AbstractController;
use App\ControllerInterface;
use Model\Managers\UserManager;

class SecurityController extends AbstractController implements ControllerInterface
{
    public function index()
    {
        $this->redirectTo("security", "login");
    }

    public function register() 
    {
        if (isset($_POST['submitSignUp'])) {
            // Filtre
            $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL, FILTER_VALIDATE_EMAIL);
            $nickname = filter_input(INPUT_POST, "nickname", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $confirmPassword = filter_input(INPUT_POST, "confirmPassword", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            // Si les filtres passent
            if ($email !== false && $nickname !== false && $password !==false) {
                $userManager = new UserManager();

                // si le mail n'existe pas
                if (!$userManager->findOneByEmail($email)) {
                    // Si le pseudo n'existe pas
                    if (!$userManager->findOneByUser($nickname)) {
                        // si les 2 mots de passe concordent et que le mot de passe a une longueur minimale de 8
                        if (($password == $confirmPassword) && strlen($password) >= 8) {
                            // Hachage du mot de passe
                            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                            // Ajout de l'user à la db avec la method add() du Manager
                            $data = ['nickname' => $nickname, 'email' => $email, 'password' => $hashedPassword];
                            $userManager->add($data);
                            // Redirection vers le login
                            SESSION::addFlash('success', "<div class='message'>Inscription réussie ! Vous pouvez maintenant vous connecter</div>");
                            $this->redirectTo("login");
                        // Si les mots de passe ne correspondent pas
                        } else {
                            SESSION::addFlash('error', "<div class='message'>Les deux mots de passe ne correspondent pas ou le mot de passe fait moins de 8 caractères</div>");
                            $this->redirectTo("security", "register");
                        }
                    // Si le nickname existe déjà
                    } else {
                        SESSION::addFlash('error', "<div class='message'>Ce pseudo est déjà utilisé</div>");
                        $this->redirectTo("security", "register");
                    }
                // Si l'adresse mail est déjà utilisé
                } else {
                    SESSION::addFlash('error', "<div class='message'>Cette adresse mail est déjà utilisée</div>");
                    $this->redirectTo("security", "register");
                }
            }  else {
                SESSION::addFlash('error', "<div class='message'>Erreur filtre</div>");
                $this->redirectTo("security", "register");
            }
        }

        return [
            "view" => VIEW_DIR . 'security\register.php'
        ];
    }

    public function login() {

        if (isset($_POST['submitLogin'])) {
            // Filtre
            $user = filter_input(INPUT_POST, "user", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            // On vérifie si c'est le nickname ou un email
            // Patern email
            $emailPattern = "/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix"; // Validation d'email par expression régulière
            // preg_match() est utilisée pour rechercher un motif dans une chaîne de caractères
            if (preg_match($emailPattern, $user)) {
                $user = filter_input(INPUT_POST, "user", FILTER_SANITIZE_EMAIL, FILTER_VALIDATE_EMAIL);
                $email = 1;
            }
            
            if ($user !== false && $password !== false) {
                $userManager = new UserManager();
                // retrouver le mot de passe de l'utilisateur
                $dbPass = $userManager->retrievePassword($user);
                // si le mot de passe est retrouvé
                if ($dbPass) {
                    // récupération du mot de passe
                    $hash = $dbPass->getPassword();
                    // retrouver l'utilisateur
                    if (isset($email)) {
                        $user = $userManager->findOneByEmail($user);
                    } else {
                        $user = $userManager->findOneByUser($user);
                    }
                    // comparaison du hash de la base de données et le mot de passe renseigné
                    if (password_verify($password, $hash)) {
                        // si l'user n'est pas banni
                        if (!$user->getStatut()) {
                            // placer l'utilisateur en Session
                            Session::setUser($user);
                            SESSION::addFlash('success', "<div class='message'>Connexion réussie</div>");
                            $this->redirectTo("index.php");
                        } else {
                            SESSION::addFlash('error', "<div class='message'>Ce compte est banni</div>");
                            $this->redirectTo("security", "login");
                        }
                    } else {
                        SESSION::addFlash('error', "<div class='message'>Mot de passe incorrect</div>");
                        $this->redirectTo("security", "login");
                    }
                }  else {
                    SESSION::addFlash('error', "<div class='message'>Erreur filtre</div>");
                    $this->redirectTo("security", "register");
                }
            }
        }

        return [
            "view" => VIEW_DIR . 'security\login.php'
        ];
    }
}