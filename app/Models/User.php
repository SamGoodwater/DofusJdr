<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;


    const ROLES = [
        'guest' => 'guest',
        'user' => 'user',
        'player' => 'player',
        "game_master" => "game_master",
        'contributor' => 'contributor',
        'moderator' => 'moderator',
        'admin' => 'admin',
        'super_admin' => 'super_admin'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'uniqid'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'deleted_at'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function verifyRole(string $role): bool
    {
        if (!in_array($role, self::ROLES) || !in_array($this->role, self::ROLES)) {
            return false;
        }
        if ($this->role === self::ROLES['super_admin']) {
            return true;
        }

        switch ($role) {
            case self::ROLES['guest']:
                return in_array($this->role, [
                    self::ROLES['guest'],
                    self::ROLES['user'],
                    self::ROLES['player'],
                    self::ROLES['game_master'],
                    self::ROLES['contributor'],
                    self::ROLES['moderator'],
                    self::ROLES['admin'],
                    self::ROLES['super_admin']
                ]);

            case self::ROLES['user']:
                return in_array($this->role, [
                    self::ROLES['user'],
                    self::ROLES['player'],
                    self::ROLES['game_master'],
                    self::ROLES['contributor'],
                    self::ROLES['moderator'],
                    self::ROLES['admin'],
                    self::ROLES['super_admin']
                ]);

            case self::ROLES['player']:
                return in_array($this->role, [
                    self::ROLES['player'],
                    self::ROLES['game_master'],
                    self::ROLES['contributor'],
                    self::ROLES['moderator'],
                    self::ROLES['admin'],
                    self::ROLES['super_admin']
                ]);

            case self::ROLES['game_master']:
                return in_array($this->role, [
                    self::ROLES['game_master'],
                    self::ROLES['contributor'],
                    self::ROLES['moderator'],
                    self::ROLES['admin'],
                    self::ROLES['super_admin']
                ]);

            case self::ROLES['contributor']:
                return in_array($this->role, [
                    self::ROLES['contributor'],
                    self::ROLES['moderator'],
                    self::ROLES['admin'],
                    self::ROLES['super_admin']
                ]);

            case self::ROLES['moderator']:
                return in_array($this->role, [
                    self::ROLES['moderator'],
                    self::ROLES['admin'],
                    self::ROLES['super_admin']
                ]);

            case self::ROLES['admin']:
                return in_array($this->role, [
                    self::ROLES['admin'],
                    self::ROLES['super_admin']
                ]);

            case self::ROLES['super_admin']:
                return in_array($this->role, [
                    self::ROLES['super_admin']
                ]);

            default:
                return false;
        }
    }
}
