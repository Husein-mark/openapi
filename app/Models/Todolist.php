<?php

namespace App\Models;

use Database\Factories\TodolistFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todolist extends Model
{
    /** @use HasFactory<TodolistFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'priority',
        'tags',
        'is_done',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'priority' => 'integer',
            'tags' => 'array',
            'is_done' => 'boolean',
        ];
    }
}
