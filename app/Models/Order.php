<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'invitation_template_id', 'price', 'status',
        'payment_method', 'payment_proof_path', 'paid_at', 'confirmed_by', 'invitation_page_id', 'created_by', 'notes',
    ];

    protected function casts(): array
    {
        return ['price' => 'integer', 'paid_at' => 'datetime'];
    }

    public const STATUS_PENDING = 'pending';
    public const STATUS_AWAITING = 'awaiting_confirmation';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUSES = [
        self::STATUS_PENDING, self::STATUS_AWAITING, self::STATUS_PAID, self::STATUS_CANCELLED,
    ];

    public function statusLabel(): string
    {
        return [
            self::STATUS_PENDING => 'Menunggu pembayaran',
            self::STATUS_AWAITING => 'Menunggu konfirmasi',
            self::STATUS_PAID => 'Lunas',
            self::STATUS_CANCELLED => 'Dibatalkan',
        ][$this->status] ?? $this->status;
    }

    public function priceLabel(): string
    {
        return 'Rp' . number_format($this->price, 0, ',', '.');
    }

    /** Customer boleh (ganti) unggah bukti selama belum dikonfirmasi/dibatalkan. */
    public function canUploadProof(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_AWAITING], true);
    }

    /** ORD-YYYYMMDD-XXXXX; kolom unik menjaga tabrakan, loop retry kalau bentrok. */
    public static function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(5));
        } while (self::where('order_number', $number)->exists());

        return $number;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function template()
    {
        return $this->belongsTo(InvitationTemplate::class, 'invitation_template_id');
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function invitationPage()
    {
        return $this->belongsTo(InvitationPage::class);
    }
}
