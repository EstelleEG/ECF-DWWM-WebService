<?php

namespace App\Dao;

use PDO;
use Core\AbstractDao;
use App\Model\User;

class UserDao extends AbstractDao
{
    /**
     * Récupère de la base de données tous les utilisateurs
     *
     * @return User[] 
     */
    public function getAll(): array 
    {
        $sth = $this->dbh->prepare("SELECT * FROM `user`");
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        for ($i = 0; $i < count($result); $i++) {   
            $a = new User();
            $result[$i] = $a->setIdUser($result[$i]['id_user'])
                ->setPseudo($result[$i]['pseudo'])
                ->setPwd($result[$i]['pwd'])
                ->setEmail($result[$i]['email'])
                ->setCreatedAt($result[$i]['created_at']);
        }

        return $result;
    }

    /**
     * Récupère un utilisateur par son email si l'email existe dans la base de données,
     * sinon on récupèrera NULL
     *
     * @param string $email L'email de l'utilisateur
     * @return User|null Renvoie un utilisateur ou null
     */
    public function getByEmail(string $email): ?User
    {
        $sth = $this->dbh->prepare('SELECT * FROM user WHERE email = :email');
        $sth->execute([':email' => $email]);
        $result = $sth->fetch(PDO::FETCH_ASSOC);

        if (empty($result)) {
            return null;
        }

        $u = new User();
        return $u->setIdUser($result['id_user'])
            ->setPseudo($result['pseudo'])
            ->setPwd($result['pwd'])
            ->setEmail($result['email'])
            ->setCreatedAt($result['created_at']);
    }

    /**
     * Récupère de la base de données un utilisateur en fonction de son id ou null si l'utilisateur n'existe pas
     *
     * @param int $id Identifiant de l'utilisateur qu'on doit récupérer de la bdd
     */
    public function getById(int $id): User
    {
        $sth = $this->dbh->prepare("SELECT * FROM `user` WHERE id_user = :id_user");
        $sth->execute([":id_user" => $id]);
        $result = $sth->fetch(PDO::FETCH_ASSOC);

        $a = new User(); 
        return $a->setIdUser($result['id_user'])
        ->setPseudo($result['pseudo'])
        ->setPwd($result['pwd'])
        ->setEmail($result['email'])
        ->setCreatedAt($result['created_at']);
    }

    /**
     * Crée un nouvel utilisateur 
     * 
     * $user est le nouvel utilisateur
     */
    public function new(User $user) : void
    {
        $sth = $this->dbh->prepare('INSERT INTO user (pseudo, email, pwd) VALUES (:pseudo,:email,:pwd)');
        $sth->execute([
            ':pseudo' => $user->getPseudo(),
            ':email' => $user->getEmail(),
            ':pwd' => $user->getPwd()
        ]);
        $user->setIdUser($this->dbh->lastInsertId());
    }

      /**
     * Edite un compte utilisateur de la base de données
     *
     * @param User $user est l'objet de l'utilisateur à éditer
     */
    public function edit(User $user)
    {
        $sth = $this->dbh->prepare("UPDATE `user` SET pseudo = :pseudo, pwd = :pwd, email = :email WHERE id_user = :id_user");
        $sth->execute([
            ':id_user' => $user->getIdUser(),
            ':pseudo' => $user->getPseudo(),
            ':pwd' => $user->getPwd(),
            ':email' => $user->getEmail(),
            ':created_at' => $user->getCreatedAt()
        ]);
    }

    /**
     * Supprime un utilisateur de la base de données
     *
     * @param int $id est l'identifiant de l'utilisateur à supprimer
     */
    public function delete(int $id)
    {
        $sth = $this->dbh->prepare("DELETE FROM `user` WHERE id_user = :id_user");
        $sth->execute([":id_user" => $id]);
    }
}

