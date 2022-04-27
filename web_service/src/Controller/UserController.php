<?php

namespace App\Controller;

use PDOException;
use App\Model\User;
use App\Dao\UserDao;

class UserController
{
    public function index()
    {
        try {
            $userDao = new UserDao();
            $users = $userDao->getAll();

            for ($i = 0; $i < count($users); $i++) {
                $users[$i] = $users[$i]->toArray();
            }

            header("Content-Type: application/json");
            echo json_encode($users);
        } catch (PDOException $e) {
            echo "Oops ! Something went wrong";
            echo "<br>";
            echo $e->getMessage();
            die;
        }
    }

    public function new()
    {
        $user_post = json_decode(file_get_contents('php://input'), true);

        if (isset($user_post['pseudo']) && isset($user_post['email']) && isset($user_post['pwd'])) {
            if (empty(trim($user_post['pseudo']))) {
                $error_messages[] = "Pseudo inexistant";
            }
            if (empty(trim($user_post['email']))) {
                $error_messages[] = "Email inexistant";
            }
            if (empty(trim($user_post['pwd']))) {
                $error_messages[] = "Mot de passe inexistant";
            }

            if (!isset($error_messages)) {
                $user = new User();
                $user->setPseudo($user_post['pseudo'])->setEmail($user_post['email'])->setPwd($user_post['pwd']);
                $userDao = new UserDao();
                $userDao->new($user);

                header("Content-Type: application/json");
                echo json_encode([
                    'id_user' => $user->getIdUser()
                ]);
            }
        }
    }

    public function show()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $userDao = new UserDao();
        $user = $userDao->getById($data['id_user']);

        if (!is_null($user)) {
            $user = $user->toArray();
        }
        header("Content-Type: application/json");
        echo json_encode($user);
    }

    public function edit()
    {
        $user_post = json_decode(file_get_contents('php://input'), true);

        if (isset($user_post['pseudo']) && isset($user_post['email']) && isset($user_post['pwd'])) {
            if (empty(trim($user_post['pseudo']))) {
                $error_messages[] = "Pseudo inexistant";
            }
            if (empty(trim($user_post['email']))) {
                $error_messages[] = "Email inexistant";
            }
            if (empty(trim($user_post['pwd']))) {
                $error_messages[] = 'Mot de passe inexistant';
            }
            if (empty(trim($user_post['conf_pwd']))) {
                $error_messages[] = 'Confirmation mot de passe inexistant';
            }
            if ($user_post['pwd'] !== $user_post['conf_pwd']) {
                $error_messages[] = 'Les mots de passe ne sont pas identiques';
            }

            if (!isset($error_messages)) {
                $user = User::fromArray($user_post);
                $user->setIdUser($user_post['id_user']);
                $userDao = new UserDao();
                $userDao->edit($user);
            } else {
                echo json_encode([
                    "error_messages" => [
                        "danger" => $error_messages
                    ]
                ]);
            }
        }
    }

    public function delete()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $userDao = new UserDao();
        $userDao->delete($data['id_user']);
    }
}
