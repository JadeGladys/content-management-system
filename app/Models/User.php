<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    
    use HasFactory, Notifiable, HasUlids;

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $fillable = [
        'name',
        'email',
        'password',
        'must_set_password',
        'role',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'must_set_password' => 'boolean',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function createdUsers(): HasMany
    {
        return $this->hasMany(User::class, 'created_by');
    }

    public function updatedUsers(): HasMany
    {
        return $this->hasMany(User::class, 'updated_by');
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(UserToken::class);
    }

    public function createdTokens(): HasMany
    {
        return $this->hasMany(UserToken::class, 'created_by');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    public function createdCareers(): HasMany
    {
        return $this->hasMany(Career::class, 'created_by');
    }

    public function updatedCareers(): HasMany
    {
        return $this->hasMany(Career::class, 'updated_by');
    }

    public function createdArticles(): HasMany
    {
        return $this->hasMany(Article::class, 'created_by');
    }

    public function updatedArticles(): HasMany
    {
        return $this->hasMany(Article::class, 'updated_by');
    }

    public function uploadedMedia(): HasMany
    {
        return $this->hasMany(Media::class, 'uploaded_by');
    }

    public function updatedMedia(): HasMany
    {
        return $this->hasMany(Media::class, 'updated_by');
    }

}
