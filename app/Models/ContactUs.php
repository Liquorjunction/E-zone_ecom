<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class ContactUs extends Model
{
    use SoftDeletes, HasFactory, Notifiable;

    protected $table = 'contact_us';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'mobile',
        'subject',
        'order_number',
        'product_sku',
        'message',
        'images',
        'attachments',
        'ticket_number',
        'status',
        'admin_id',
        'admin_notes',
    ];

    protected $casts = [
        'images' => 'array',
        'attachments' => 'array',
    ];

    public function getSubjectLabelAttribute(): string
    {
        return match($this->subject) {
            'product_inquiry'      => 'Product Inquiry',
            'order_status'         => 'Order Status',
            'return_replacement'   => 'Return / Replacement',
            'warranty_claim'       => 'Warranty Claim',
            'installation_support' => 'Installation Support',
            'technical_issue'      => 'Technical Issue',
            'bulk_purchase'        => 'Bulk Purchase Inquiry',
            'complaint'            => 'Complaint',
            'other'                => 'Other',
            default                => ucfirst($this->subject),
        };
    }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'open'        => ['label' => 'Open',        'color' => 'blue'],
            'in_progress' => ['label' => 'In Progress', 'color' => 'yellow'],
            'resolved'    => ['label' => 'Resolved',    'color' => 'green'],
            'closed'      => ['label' => 'Closed',      'color' => 'gray'],
            default       => ['label' => 'Unknown',     'color' => 'gray'],
        };
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeBySubject($query, string $subject)
    {
        return $query->where('subject', $subject);
    }
}
