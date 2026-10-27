<?php

use App\Models\Todolist;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('cannot access todolist without api key', function () {
    $response = $this->getJson('/api/v1/todolist');

    $response->assertStatus(401)
        ->assertJson([
            'message' => 'Unauthorized: Invalid or missing X-API-Key header',
        ]);
});

test('can create a new todolist', function () {
    $response = $this->withHeader('X-API-Key', 'rahasia')
        ->postJson('/api/v1/todolist', [
            'name' => 'Java',
            'priority' => 3,
            'tags' => ['Java', 'Programming'],
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'name' => 'Java',
            'priority' => 3,
            'tags' => ['Java', 'Programming'],
        ])
        ->assertJsonStructure(['id', 'name', 'priority', 'tags']);

    $this->assertDatabaseHas('todolists', [
        'name' => 'Java',
        'priority' => 3,
    ]);
});

test('can get all active todolists', function () {
    Todolist::factory()->create([
        'name' => 'Java',
        'priority' => 3,
        'tags' => ['Java'],
        'is_done' => false,
    ]);
    Todolist::factory()->create([
        'name' => 'Done Task',
        'priority' => 1,
        'tags' => ['Completed'],
        'is_done' => true,
    ]);

    $response = $this->withHeader('X-API-Key', 'rahasia')
        ->getJson('/api/v1/todolist');

    $response->assertStatus(200)
        ->assertJsonCount(1)
        ->assertJsonFragment(['name' => 'Java'])
        ->assertJsonMissing(['name' => 'Done Task']);
});

test('can include done todolists when parameter is true', function () {
    Todolist::factory()->create([
        'name' => 'Java',
        'is_done' => false,
    ]);
    Todolist::factory()->create([
        'name' => 'Done Task',
        'is_done' => true,
    ]);

    $response = $this->withHeader('X-API-Key', 'rahasia')
        ->getJson('/api/v1/todolist?include_done=true');

    $response->assertStatus(200)
        ->assertJsonCount(2);
});

test('can filter todolist by name', function () {
    Todolist::factory()->create([
        'name' => 'Learning Java',
        'is_done' => false,
    ]);
    Todolist::factory()->create([
        'name' => 'Learning Python',
        'is_done' => false,
    ]);

    $response = $this->withHeader('X-API-Key', 'rahasia')
        ->getJson('/api/v1/todolist?name=Java');

    $response->assertStatus(200)
        ->assertJsonCount(1)
        ->assertJsonFragment(['name' => 'Learning Java'])
        ->assertJsonMissing(['name' => 'Learning Python']);
});

test('can update an existing todolist', function () {
    $todo = Todolist::factory()->create([
        'name' => 'Old Name',
        'priority' => 1,
        'tags' => ['Old'],
    ]);

    $response = $this->withHeader('X-API-Key', 'rahasia')
        ->putJson("/api/v1/todolist/{$todo->id}", [
            'name' => 'PHP',
            'priority' => 5,
            'tags' => ['PHP', 'Programming'],
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'id' => (string) $todo->id,
            'name' => 'PHP',
            'priority' => 5,
            'tags' => ['PHP', 'Programming'],
        ]);

    $this->assertDatabaseHas('todolists', [
        'id' => $todo->id,
        'name' => 'PHP',
        'priority' => 5,
    ]);
});

test('returns 404 when updating non-existent todolist', function () {
    $response = $this->withHeader('X-API-Key', 'rahasia')
        ->putJson('/api/v1/todolist/99999', [
            'name' => 'PHP',
            'priority' => 5,
            'tags' => ['PHP'],
        ]);

    $response->assertStatus(404);
});

test('can delete an existing todolist', function () {
    $todo = Todolist::factory()->create();

    $response = $this->withHeader('X-API-Key', 'rahasia')
        ->deleteJson("/api/v1/todolist/{$todo->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);

    $this->assertDatabaseMissing('todolists', [
        'id' => $todo->id,
    ]);
});

test('returns 404 when deleting non-existent todolist', function () {
    $response = $this->withHeader('X-API-Key', 'rahasia')
        ->deleteJson('/api/v1/todolist/99999');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
        ]);
});
