<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'company_name',
        'is_active',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRole::class,
        'is_active' => 'boolean',
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get working papers where user is the client
     */
    public function workingPapersAsClient(): HasMany
    {
        return $this->hasMany(WorkingPaper::class, 'client_id');
    }

    /**
     * Get working papers created by this user
     */
    public function workingPapersCreated(): HasMany
    {
        return $this->hasMany(WorkingPaper::class, 'created_by');
    }

    /**
     * Get attachments uploaded by this user
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class, 'uploaded_by');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    /**
     * Check if user is internal employee
     */
    public function isInternal(): bool
    {
        return $this->role->isInternal();
    }

    /**
     * Check if user can create working papers
     */
    public function canCreateWorkingPaper(): bool
    {
        return $this->role->canCreateWorkingPaper();
    }

    /**
     * Check if user is a client
     */
    public function isClient(): bool
    {
        return $this->role === UserRole::CLIENT;
    }

    /**
     * Scope to filter active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by role
     */
    public function scopeRole($query, UserRole $role)
    {
        return $query->where('role', $role->value);
    }

    /**
     * Scope to get clients only
     */
    public function scopeClients($query)
    {
        return $query->where('role', UserRole::CLIENT->value);
    }

    /**
     * Scope to get internal users (admin + endurego_internal)
     */
    public function scopeInternal($query)
    {
        return $query->whereIn('role', [
            UserRole::ADMIN->value,
            UserRole::ENDUREGO_INTERNAL->value
        ]);
    }
}
