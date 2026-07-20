<?php

namespace App\Policies;

use App\Models\InvitationPage;
use App\Models\User;

class InvitationPagePolicy
{
    /** Super admin bypass semua cek kepemilikan. */
    public function before(User $user): ?bool
    {
        return $user->division === 'super_admin' ? true : null;
    }

    public function view(User $user, InvitationPage $page): bool
    {
        return $this->owns($user, $page);
    }

    public function update(User $user, InvitationPage $page): bool
    {
        return $this->owns($user, $page);
    }

    /**
     * Boleh: customer PEMILIK (owner_id), atau mitra PEMBUAT (created_by).
     * owner_id null (undangan lama) tak cocok dengan siapa pun kecuali super admin (before()).
     */
    protected function owns(User $user, InvitationPage $page): bool
    {
        if ($user->isCustomer()) {
            return $page->owner_id === $user->id;
        }

        if ($user->division === 'designer') {
            return $page->created_by === $user->id;
        }

        return false;
    }
}
