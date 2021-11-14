<?php

namespace Tests\Feature;

use App\Models\Budget;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Support\Str;
use Tests\TestCase;

class BudgetTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations, WithFaker;

    /**
     * @test
     */
    public function unauthorized_user_cannot_see_budgets()
    {
        $response = $this->json("GET", "/api/budgets");

        $response->assertUnauthorized();
    }

    /**
     * @test
     */
    public function show_404_if_budget_does_not_exist()
    {
        $user = $this->newUser();
        $response = $this->actingAs($user, "sanctum")->json("GET", "/api/budgets/-1");

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function can_return_a_collection_of_budgets()
    {
        $count = 50;

        $user = $this->create("User", [], false);
        $icon = $this->create("Icon", [], false);
        $transaction_type = $this->create("TransactionType", ["icon_id" => $icon->id], false);

        Budget::insert(
            Budget::factory()
                ->count($count)
                ->make(["user_id" => $user->id, "transaction_type_id" => $transaction_type->id])
                ->toArray()
        );

        $response = $this->actingAs($user, "sanctum")->json("GET", "/api/budgets");

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                "data" => [
                    "*" => [
                        "id",
                        "title",
                        "description",
                        "ammount",
                        "start_date",
                        "end_date",
                        "created_at",
                        "transaction_type" => [
                            "id",
                            "title",
                            "color",
                            "outflow",
                            "icon" => ["id", "title", "value", "group", "enabled"],
                        ],
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

        $this->assertDatabaseCount("budgets", $count);
    }

    /**
     * @test
     */
    public function can_create_a_budget()
    {
        $user = $this->newUser();
        $icon = $this->create("Icon", [], false);
        $transaction_type = $this->create("TransactionType", ["icon_id" => $icon->id], false);

        $post_data = [
            "title" => ($title = $this->faker->word()),
            "description" => ($description = $this->faker->sentence(10)),
            "ammount" => ($ammount = $this->randomPrice(0, 500)),
            "start_date" => ($start_date = $this->mockDate($this->faker->dateTimeBetween("+0 days", "+1 month"))),
            "end_date" => ($end_date = $this->mockDate($this->faker->dateTimeBetween("+1 month", "+2 month"))),
            "transaction_type" => $transaction_type->id,
        ];

        $response = $this->actingAs($user, "sanctum")->json("POST", "/api/budgets", $post_data);

        $response
            ->assertCreated()
            ->assertJsonStructure([
                "data" => [
                    "id",
                    "title",
                    "description",
                    "ammount",
                    "start_date",
                    "end_date",
                    "created_at",
                    "transaction_type",
                ],
            ])
            ->assertJson(function (AssertableJson $json) use (
                $title,
                $description,
                $ammount,
                $start_date,
                $end_date,
                $transaction_type
            ) {
                $json->has("data", function ($json) use (
                    $title,
                    $description,
                    $ammount,
                    $start_date,
                    $end_date,
                    $transaction_type
                ) {
                    $json
                        ->where("title", Str::title($title))
                        ->where("description", $description)
                        ->where("ammount", $ammount)
                        ->where("start_date", $start_date)
                        ->where("end_date", $end_date)
                        ->where("transaction_type.id", $transaction_type->id)
                        ->etc();
                });
            });

        $this->assertDatabaseHas("budgets", [
            "user_id" => $user->id,
            "title" => $title,
            "description" => $description,
            "ammount" => $ammount,
            "start_date" => (string) Carbon::parse($start_date),
            "end_date" => (string) Carbon::parse($end_date),
        ]);
    }

    /**
     * @test
     */
    public function can_update_budget()
    {
        $user = $this->newUser();
        $icon = $this->create("Icon", [], false);
        $transaction_type = $this->create("TransactionType", ["icon_id" => $icon->id], false);
        $budget = $this->create("Budget", ["user_id" => $user->id, "transaction_type_id" => $transaction_type->id]);

        $updated_data = [
            "title" => ($title = $this->faker->word()."_updated"),
            "description" => ($description = $this->faker->sentence(10)),
            "ammount" => ($ammount = $this->randomPrice(0, 500)),
            "start_date" => ($start_date = $this->mockDate($this->faker->dateTimeBetween("+0 days", "+1 month"))),
            "end_date" => ($end_date = $this->mockDate($this->faker->dateTimeBetween("+1 month", "+2 month"))),
            "transaction_type" => $transaction_type->id,
        ];

        $response = $this->actingAs($user, "sanctum")->json("PUT", "/api/budgets/$budget->id", $updated_data);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                "data" => [
                    "id",
                    "title",
                    "description",
                    "ammount",
                    "start_date",
                    "end_date",
                    "created_at",
                    "transaction_type",
                ],
            ])
            ->assertJson(function (AssertableJson $json) use (
                $title,
                $description,
                $ammount,
                $start_date,
                $end_date,
                $transaction_type
            ) {
                $json->has("data", function ($json) use (
                    $title,
                    $description,
                    $ammount,
                    $start_date,
                    $end_date,
                    $transaction_type
                ) {
                    $json
                        ->where("title", Str::title($title))
                        ->where("description", $description)
                        ->where("ammount", $ammount)
                        ->where("start_date", $start_date)
                        ->where("end_date", $end_date)
                        ->where("transaction_type.id", $transaction_type->id)
                        ->etc();
                });
            });

        $this->assertDatabaseHas("budgets", [
            "user_id" => $user->id,
            "title" => $title,
            "description" => $description,
            "ammount" => $ammount,
            "start_date" => (string) Carbon::parse($start_date),
            "end_date" => (string) Carbon::parse($end_date),
        ]);
    }

    /**
     * @test
     */
    public function show_a_budget()
    {
        $user = $this->newUser();
        $icon = $this->create("Icon", [], false);
        $transaction_type = $this->create("TransactionType", ["icon_id" => $icon->id], false);
        $budget = $this->create("Budget", ["user_id" => $user->id, "transaction_type_id" => $transaction_type->id]);

        $response = $this->actingAs($user, "sanctum")->json("GET", "/api/budgets/$budget->id");

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                "data" => [
                    "id",
                    "title",
                    "description",
                    "ammount",
                    "start_date",
                    "end_date",
                    "created_at",
                    "transaction_type",
                ],
            ])
            ->assertJson(function (AssertableJson $json) use ($budget) {
                $json->has("data", function ($json) use ($budget) {
                    $json
                        ->where("title", Str::title($budget->title))
                        ->where("description", $budget->description)
                        ->where("ammount", $budget->ammount)
                        ->where("start_date", $this->mockDate($budget->start_date))
                        ->where("end_date", $this->mockDate($budget->end_date))
                        ->where("transaction_type.id", $budget->transaction_type->id)
                        ->etc();
                });
            });

        $this->assertDatabaseHas("budgets", [
            "user_id" => $user->id,
            "title" => $budget->title,
            "description" => $budget->description,
            "ammount" => $budget->ammount,
            "start_date" => $budget->start_date,
            "end_date" => $budget->end_date,
        ]);
    }

    /**
     * @test
     */
    public function can_delete_a_budget()
    {
        $user = $this->newUser();
        $icon = $this->create("Icon", [], false);
        $transaction_type = $this->create("TransactionType", ["icon_id" => $icon->id], false);
        $budget = $this->create("Budget", ["user_id" => $user->id, "transaction_type_id" => $transaction_type->id]);

        $response = $this->actingAs($user, "sanctum")->json("DELETE", "/api/budgets/$budget->id");

        $response->assertStatus(200);

        $this->assertDatabaseMissing("budgets", [
            "id" => $budget->id,
        ]);
    }

    /**
     * @test
     */
    public function will_fail_if_start_date_is_higher_or_equal_to_end_date()
    {
        $user = $this->newUser();
        $icon = $this->create("Icon", [], false);
        $transaction_type = $this->create("TransactionType", ["icon_id" => $icon->id], false);

        $new_budget = [
            "title" => $this->faker->word(),
            "description" => $this->faker->sentence(10),
            "ammount" => $this->randomPrice(0, 500),
            "start_date" => ($start_date = $this->mockDate(Carbon::tomorrow())),
            "end_date" => ($end_date = $this->mockDate(Carbon::now())),
            "transaction_type" => $transaction_type->id,
        ];

        $response = $this->actingAs($user, "sanctum")->json("POST", "/api/budgets", $new_budget);

        $response->assertUnprocessable();
    }

    /**
     * @test
     */
    public function user_can_only_see_its_own_budgets()
    {
        $count1 = 53;
        $count2 = 37;

        $user1 = $this->create("User", [], false);
        $user2 = $this->create("User", [], false);
        $icon = $this->create("Icon", [], false);
        $transaction_type = $this->create("TransactionType", ["icon_id" => $icon->id], false);

        Budget::insert(
            Budget::factory()
                ->count($count1)
                ->make(["user_id" => $user1->id, "transaction_type_id" => $transaction_type->id])
                ->toArray()
        );

        Budget::insert(
            Budget::factory()
                ->count($count2)
                ->make(["user_id" => $user2->id, "transaction_type_id" => $transaction_type->id])
                ->toArray()
        );

        $this->actingAs($user2, "sanctum")
            ->json("GET", "/api/budgets")
            ->assertStatus(200)
            ->assertJsonPath("meta.total", $count2);

        $this->actingAs($user1, "sanctum")
            ->json("GET", "/api/budgets")
            ->assertStatus(200)
            ->assertJsonPath("meta.total", $count1);
    }
}
