<?php

declare(strict_types=1);

namespace App;


class Authorization
{
    /**
     * @var DataBase
     */
    private DataBase $dataBase;

    /**
     * Authorization constructor.
     * @param DataBase $dataBase
     */
    public function __construct(DataBase $dataBase)
    {
        $this->dataBase = $dataBase;
    }

    /**
     * @param array $data
     * @return bool
     * @throws AuthorizationException
     */
    public function siginup(array $data): bool
    {
        if (empty($data['username'])) {
            throw new AuthorizationException('The Username should not be empty');
        }
        if (empty($data['email'])) {
            throw new AuthorizationException('The Email should not be empty');
        }
        if (empty($data['password'])) {
            throw new AuthorizationException('The Password should not be empty');
        }
        if ($data['password'] != $data['confirm_password']) {
            throw new AuthorizationException('The Password and Confirm Password should match');
        }
        
        $statement = $this->dataBase->getConnection()->prepare(
            'INSERT INTO user (email, username, password) VALUES (:email, :username, :password)'
        );

        $statement->execute([
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => password_hash($data['password'],  PASSWORD_BCRYPT),
        ]);

        return true;
    }

   

}