<?php
namespace App\Repositories\Account;

interface IAccountRepository
{
    public function createAccount($user_id, $inital_balance = 0.0);

    public function currentAccountID();

    public function updateAccountBalance($account_no, $amount);

    public function createTransaction($from_account_no, $to_account_no, $amount);

    public function getAccountBalance($account_no);

    public function getUserAccountByUserID($user_id);
}
