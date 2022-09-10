<?php

namespace App\Repositories\Account;

use App\Account;
use App\Repositories\BaseRepository;
use App\Transaction;
use Illuminate\Support\Facades\DB;

class AccountRepository extends BaseRepository implements IAccountRepository
{
    public function createAccount($user_id)
    {
        $account = new Account();
        $account->user_id = $user_id;
        $account->account_no = ACCOUNT_PREFIX . $this->getSixDigitID($user_id) . $this->getSixDigitID($this->currentAccountID());
        $account->balance = 0.0;
        $account->save();

        return $account;
    }

    public function createTransaction($from_account_no, $to_account_no, $amount)
    {
        if ($amount <= 0) return;

        $transaction = new Transaction([
            'from_account_no' => $from_account_no,
            'to_account_no' => $to_account_no,
            'amount' => $amount
        ]);

        $transaction->save();

        $this->updateAccountBalance($from_account_no, -$amount);
        $this->updateAccountBalance($to_account_no, $amount);

        return $transaction;
    }

    public function updateAccountBalance($account_no, $amount)
    {
        $balance = $this->getAccountBalance($account_no)?->balance ?: 0.0;

        return DB::table('accounts')
            ->where('accounts.account_no', $account_no)
            ->update(
                [
                    'balance' => $amount + $balance,
                ]
            );
    }

    private function getSixDigitID($id)
    {
        return sprintf("%06d", $id);
    }

    public function currentAccountID()
    {
        return DB::table('accounts')->latest()->first()?->id ?: 1;
    }

    function getAccountBalance($account_no)
    {
        return DB::table('accounts')
            ->select('accounts.balance as balance')
            ->where('accounts.account_no', $account_no)
            ->first();
    }

    public function getUserAccountByUserID($user_id)
    {
        return DB::table('accounts')
            ->select(
                'accounts.id as id',
                'accounts.account_no as account_no',
                'accounts.balance as balance'
            )
            ->where('accounts.user_id', $user_id)
            ->first();
    }
}
