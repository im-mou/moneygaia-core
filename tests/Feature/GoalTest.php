<?php

namespace Tests\Feature;

use App\Models\Goal;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Support\Str;
use Tests\TestCase;

class GoalTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations, WithFaker;

    /**
     * @test
     */
    public function unauthorized_user_cannot_see_goals()
    {
        $response = $this->json("GET", "/api/goals");

        $response->assertUnauthorized();
    }

    /**
     * @test
     */
    public function show_404_if_goal_does_not_exist()
    {
        $user = $this->newUser();
        $response = $this->actingAs($user, "sanctum")->json("GET", "/api/goals/-1");

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function can_return_a_collection_of_goals()
    {
        $count = 50;

        $user = $this->create("User", [], false);
        $icon = $this->create("Icon", [], false);

        Goal::insert(
            Goal::factory()
                ->count($count)
                ->make(["user_id" => $user->id, "icon_id" => $icon->id])
                ->toArray()
        );

        $response = $this->actingAs($user, "sanctum")->json("GET", "/api/goals");

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                "data" => [
                    "*" => [
                        "id",
                        "title",
                        "description",
                        "ammount",
                        "due_date",
                        "achived",
                        "created_at",
                        "icon" => ["id", "title", "value", "group", "enabled"],
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

        $this->assertDatabaseCount("goals", $count);
    }

    /**
     * @test
     */
    public function can_create_a_goal()
    {
        $user = $this->newUser();
        $icon = $this->create("Icon", [], false);

        $post_data = [
            "title" => ($title = $this->faker->word()),
            "description" => ($description = $this->faker->sentence(10)),
            "ammount" => ($ammount = $this->randomPrice(0, 500)),
            "due_date" => ($due_date = $this->mockDate($this->faker->dateTimeBetween("+0 days", "+1 month"))),
            "icon" => $icon->id,
        ];

        $response = $this->actingAs($user, "sanctum")->json("POST", "/api/goals", $post_data);

        $response
            ->assertCreated()
            ->assertJsonStructure([
                "data" => ["id", "title", "description", "ammount", "due_date", "achived", "created_at", "icon"],
            ])
            ->assertJson(function (AssertableJson $json) use ($title, $description, $ammount, $due_date, $icon) {
                $json->has("data", function ($json) use ($title, $description, $ammount, $due_date, $icon) {
                    $json
                        ->where("title", Str::title($title))
                        ->where("description", $description)
                        ->where("ammount", $ammount)
                        ->where("due_date", $due_date)
                        ->where("achived", false)
                        ->where("icon.id", $icon->id)
                        ->etc();
                });
            });

        $this->assertDatabaseHas("goals", [
            "user_id" => $user->id,
            "title" => $title,
            "description" => $description,
            "ammount" => $ammount,
            "due_date" => (string) Carbon::parse($due_date),
            "achived" => 0,
        ]);
    }

    /**
     * @test
     */
    public function can_update_goal()
    {
        $user = $this->newUser();
        $icon = $this->create("Icon", [], false);
        $goal = $this->create("Goal", ["user_id" => $user->id, "icon_id" => $icon->id]);

        $updated_data = [
            "title" => ($title = $this->faker->word() . "_updated"),
            "description" => ($description = $this->faker->sentence(10)),
            "ammount" => ($ammount = $this->randomPrice(0, 500)),
            "due_date" => ($due_date = $this->mockDate($this->faker->dateTimeBetween("+0 days", "+1 month"))),
            "icon" => $icon->id,
        ];

        $response = $this->actingAs($user, "sanctum")->json("PUT", "/api/goals/$goal->id", $updated_data);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                "data" => ["id", "title", "description", "ammount", "due_date", "created_at", "icon"],
            ])
            ->assertJson(function (AssertableJson $json) use ($title, $description, $ammount, $due_date, $icon) {
                $json->has("data", function ($json) use ($title, $description, $ammount, $due_date, $icon) {
                    $json
                        ->where("title", Str::title($title))
                        ->where("description", $description)
                        ->where("ammount", $ammount)
                        ->where("due_date", $due_date)
                        ->where("icon.id", $icon->id)
                        ->etc();
                });
            });

        $this->assertDatabaseHas("goals", [
            "user_id" => $user->id,
            "title" => $title,
            "description" => $description,
            "ammount" => $ammount,
            "due_date" => (string) Carbon::parse($due_date),
        ]);
    }

    /**
     * @test
     */
    public function show_a_goal()
    {
        $user = $this->newUser();
        $icon = $this->create("Icon", [], false);
        $goal = $this->create("Goal", ["user_id" => $user->id, "icon_id" => $icon->id]);

        $response = $this->actingAs($user, "sanctum")->json("GET", "/api/goals/$goal->id");

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                "data" => ["id", "title", "description", "ammount", "due_date", "achived", "created_at", "icon"],
            ])
            ->assertJson(function (AssertableJson $json) use ($goal) {
                $json->has("data", function ($json) use ($goal) {
                    $json
                        ->where("title", Str::title($goal->title))
                        ->where("description", $goal->description)
                        ->where("ammount", $goal->ammount)
                        ->where("due_date", $this->mockDate($goal->due_date))
                        ->where("icon.id", $goal->icon->id)
                        ->etc();
                });
            });

        $this->assertDatabaseHas("goals", [
            "user_id" => $user->id,
            "title" => $goal->title,
            "description" => $goal->description,
            "ammount" => $goal->ammount,
            "due_date" => $goal->due_date,
        ]);
    }

    /**
     * @test
     */
    public function can_delete_a_goal()
    {
        $user = $this->newUser();
        $icon = $this->create("Icon", [], false);
        $goal = $this->create("Goal", ["user_id" => $user->id, "icon_id" => $icon->id]);

        $response = $this->actingAs($user, "sanctum")->json("DELETE", "/api/goals/$goal->id");

        $response->assertStatus(200);

        $this->assertDatabaseMissing("goals", [
            "id" => $goal->id,
        ]);
    }

    /**
     * @test
     */
    public function user_can_only_see_its_own_goals()
    {
        $count1 = 53;
        $count2 = 37;

        $user1 = $this->create("User", [], false);
        $user2 = $this->create("User", [], false);
        $icon = $this->create("Icon", [], false);

        Goal::insert(
            Goal::factory()
                ->count($count1)
                ->make(["user_id" => $user1->id, "icon_id" => $icon->id])
                ->toArray()
        );

        Goal::insert(
            Goal::factory()
                ->count($count2)
                ->make(["user_id" => $user2->id, "icon_id" => $icon->id])
                ->toArray()
        );

        $this->actingAs($user2, "sanctum")
            ->json("GET", "/api/goals")
            ->assertStatus(200)
            ->assertJsonPath("meta.total", $count2);

        $this->actingAs($user1, "sanctum")
            ->json("GET", "/api/goals")
            ->assertStatus(200)
            ->assertJsonPath("meta.total", $count1);
    }

    /**
     * @test
     */
    public function can_mark_goal_as_achived()
    {
        $user = $this->newUser();
        $icon = $this->create("Icon", [], false);
        $goal = $this->create("Goal", ["user_id" => $user->id, "icon_id" => $icon->id]);

        $response = $this->actingAs($user, "sanctum")->json("POST", "/api/goals/$goal->id/achived", [
            "achived" => true,
        ]);

        $response->assertStatus(200)->assertJson(["achived" => true]);

        $this->assertDatabaseHas("goals", [
            "user_id" => $user->id,
            "id" => $goal->id,
            "achived" => true,
        ]);
    }
}
