<?php

declare(strict_types=1);


namespace App\Web\Login;

use App\Domain\User\User;
use Symfony\Contracts\Service\Attribute\Required;
use Yiisoft\FormModel\Attribute\Safe;
use Yiisoft\FormModel\FormModel;
use Yiisoft\Hydrator\Attribute\Parameter\Trim;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;

class Form extends FormModel
{
    public const ERROR_MESSAGE = 'Incorrect login or password.';

    #[Required]
    #[Trim]
    #[Callback(method: 'validateLoginAndPassword', skipOnError: true)]
    public string $login = '';

    #[Required]
    #[Callback(method: 'validateLoginAndPassword', skipOnError: true)]
    public string $password = '';

    #[Safe]
    public bool $rememberMe = false;

    private function validateLoginAndPassword(): Result
    {
        $result = new Result();
        if (mb_strlen($this->login) > User::MAX_LOGIN_LENGTH
            || mb_strlen($this->password) < User::MIN_PASSWORD_LENGTH
            || mb_strlen($this->password) > User::MAX_PASSWORD_LENGTH
        ) {
            $result->addError(self::ERROR_MESSAGE);
        }
        return $result;
    }

    public function getFormName(): string
    {
        return 'LoginForm';
    }

}
