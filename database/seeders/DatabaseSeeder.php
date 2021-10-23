<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\CreditAccount;
use App\Models\CreditAccountType;
use App\Models\Goal;
use App\Models\Icon;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\User;
use App\Models\UserInfo;
use Database\Factories\UserInfoFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $icons = Icon::factory()
            ->count(10)
            ->create();

        $transactionsTypes = TransactionType::factory()
            ->count(10)
            ->for($icons->random(), 'icon')
            ->create();

        $creditAccountTypes = CreditAccountType::factory()
            ->count(5)
            ->create();

        User::factory(5)
            ->has(UserInfo::factory()->count(1), 'user_info')
            ->create()
            ->each(function ($user) use ($creditAccountTypes, $transactionsTypes, $icons) {

                $creditAccount = CreditAccount::factory()
                    ->count(rand(1, 5))
                    ->for($user, 'user')
                    ->for($creditAccountTypes->random(), 'credit_account_type')
                    ->create();

                Transaction::factory(rand(20, 100))
                    ->for($user, 'user')
                    ->for($transactionsTypes->random(), 'transaction_type')
                    ->for($creditAccount->random(), 'credit_account')
                    ->create();

                Budget::factory(rand(1, 5))
                    ->for($user, 'user')
                    ->for($transactionsTypes->random(), 'transaction_type')
                    ->create();

                Goal::factory(rand(0, 5))
                    ->for($user, 'user')
                    ->for($icons->random(), 'icon')
                    ->create();
            });
    }
}
