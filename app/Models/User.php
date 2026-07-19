<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'division',
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
    /**
     * Boleh membuka Studio dan mengelola template undangan.
     *
     * Setara super admin DI DALAM Studio, termasuk komponen HTML mentah. Artinya akun
     * designer yang jebol bisa menanam script ke tiap undangan yang dibuat dari template
     * yang dia sentuh — perlakukan pemberian division ini seperti memberi akses admin,
     * bukan seperti menambah editor konten biasa.
     *
     * Sengaja TIDAK melebar ke data pelanggan: booking, invoice, user, dan
     * InvitationController tetap super_admin-only.
     */
    public function canDesignTemplates(): bool
    {
        return in_array($this->division, ['super_admin', 'designer'], true);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
