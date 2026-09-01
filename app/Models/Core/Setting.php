<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'description', 'is_public'];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    /** Ambil nilai setting (JSON-decoded), dengan cache 1 jam. */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting:{$key}", 3600, function () use ($key, $default) {
            $row = static::where('key', $key)->first();

            if (! $row) {
                return $default;
            }

            $decoded = json_decode($row->value, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : $row->value;
        });
    }

    public static function set(string $key, mixed $value, string $group = 'umum'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => json_encode($value), 'group' => $group],
        );

        Cache::forget("setting:{$key}");
    }
}
