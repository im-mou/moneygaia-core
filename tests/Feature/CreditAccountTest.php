<?php

namespace Tests\Feature;

use App\Models\CreditAccount;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Support\Str;

use Tests\TestCase;

class CreditAccountTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations, WithFaker;

    /**
     * @test
     */
    public function unauthorized_user_cannot_see_credit_accounts()
    {
        $response = $this->json("GET", "/api/credit-accounts");

        $response->assertUnauthorized();
    }

    /**
     * @test
     */
    public function show_404_if_credit_account_does_not_exist()
    {
        $user = $this->newUser();
        $response = $this->actingAs($user, "sanctum")->json("GET", "/api/credit-accounts/-1");

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function can_return_a_collection_of_budgets()
    {
        $count = 50;

        $user = $this->create("User", [], false);
        $credit_account_type = $this->create("CreditAccountType");

        CreditAccount::insert(
            CreditAccount::factory()
                ->count($count)
                ->make(["user_id" => $user->id, "credit_account_type_id" => $credit_account_type->id])
                ->toArray()
        );

        $response = $this->actingAs($user, "sanctum")->json("GET", "/api/credit-accounts");

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                "data" => [
                    "*" => [
                        "id",
                        "title",
                        "description",
                        "balance",
                        "active",
                        "created_at",
                        "credit_account_type" => ["id", "title", "type", "enabled"],
                    ],
                ],
            ])
            ->assertJsonPath("meta.total", $count)
            ->assertJsonCount(
                $count >= Config::get("constants.pagination.per_page")
                    ? Config::get("constants.pagination.per_page")
                    : $count,
                "data"
            );

        $this->assertDatabaseCount("credit_accounts", $count);
    }

    /**
     * @test
     */
    public function can_create_a_credit_account()
    {
        $user = $this->create("User", [], false);
        $credit_account_type = $this->create("CreditAccountType");

        $post_data = [
            "title" => ($title = $this->faker->word()),
            "description" => ($description = $this->faker->sentence(10)),
            "balance" => ($balance = $this->randomPrice(-500, 500)),
            "credit_account_type" => $credit_account_type->id,
        ];

        $response = $this->actingAs($user, "sanctum")->json("POST", "/api/credit-accounts", $post_data);

        $response
            ->assertCreated()
            ->assertJsonStructure([
                "data" => ["id", "title", "description", "balance", "active", "created_at", "credit_account_type"],
            ])
            ->assertJson(function (AssertableJson $json) use ($title, $description, $balance, $credit_account_type) {
                $json->has("data", function ($json) use ($title, $description, $balance, $credit_account_type) {
                    $json
                        ->where("title", Str::title($title))
                        ->where("description", $description)
                        ->where("balance", $balance)
                        ->where("credit_account_type.id", $credit_account_type->id)
                        ->etc();
                });
            });

        $this->assertDatabaseHas("credit_accounts", [
            "user_id" => $user->id,
            "title" => $title,
            "description" => $description,
            "balance" => $balance,
            "credit_account_type_id" => $credit_account_type->id,
        ]);
    }

    /**
     * @test
     */
    public function can_update_credit_account()
    {
        $user = $this->create("User", [], false);
        $credit_account_type = $this->create("CreditAccountType");
        $credit_account = $this->create("CreditAccount", [
            "user_id" => $user->id,
            "credit_account_type_id" => $credit_account_type->id,
        ]);

        $post_data = [
            "title" => ($title = $this->faker->word() . "_updated"),
            "description" => ($description = $this->faker->sentence(10)),
            "credit_account_type" => $credit_account_type->id,
        ];

        $response = $this->actingAs($user, "sanctum")->json(
            "PUT",
            "/api/credit-accounts/$credit_account->id",
            $post_data
        );

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                "data" => ["id", "title", "description", "balance", "active", "created_at", "credit_account_type"],
            ])
            ->assertJson(function (AssertableJson $json) use ($title, $description, $credit_account_type) {
                $json->has("data", function ($json) use ($title, $description, $credit_account_type) {
                    $json
                        ->where("title", Str::title($title))
                        ->where("description", $description)
                        ->where("credit_account_type.id", $credit_account_type->id)
                        ->etc();
                });
            });

        $this->assertDatabaseHas("credit_accounts", [
            "user_id" => $user->id,
            "title" => $title,
            "description" => $description,
            "balance" => $credit_account->balance,
            "credit_account_type_id" => $credit_account_type->id,
        ]);
    }

    /**
     * @test
     */
    public function show_a_credit_account()
    {
        $user = $this->create("User", [], false);
        $credit_account_type = $this->create("CreditAccountType");
        $credit_account = $this->create("CreditAccount", [
            "user_id" => $user->id,
            "credit_account_type_id" => $credit_account_type->id,
        ]);

        $response = $this->actingAs($user, "sanctum")->json("GET", "/api/credit-accounts/$credit_account->id");

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                "data" => ["id", "title", "description", "balance", "active", "created_at", "credit_account_type"],
            ])
            ->assertJson(function (AssertableJson $json) use ($credit_account) {
                $json->has("data", function ($json) use ($credit_account) {
                    $json
                        ->where("title", Str::title($credit_account->title))
                        ->where("description", $credit_account->description)
                        ->where("balance", $credit_account->balance)
                        ->where("credit_account_type.id", $credit_account->credit_account_type->id)
                        ->etc();
                });
            });

        $this->assertDatabaseHas("credit_accounts", [
            "user_id" => $user->id,
            "title" => $credit_account->title,
            "description" => $credit_account->description,
            "balance" => $credit_account->balance,
            "credit_account_type_id" => $credit_account->credit_account_type->id,
        ]);
    }

    /**
     * @test
     */
    public function can_delete_a_credit_account()
    {
        $user = $this->create("User", [], false);
        $credit_account_type = $this->create("CreditAccountType");
        $credit_account = $this->create("CreditAccount", [
            "user_id" => $user->id,
            "credit_account_type_id" => $credit_account_type->id,
        ]);

        $response = $this->actingAs($user, "sanctum")->json("DELETE", "/api/credit-accounts/$credit_account->id");

        $response->assertStatus(200);

        $this->assertDatabaseMissing("credit_accounts", [
            "id" => $credit_account->id,
        ]);
    }
}
