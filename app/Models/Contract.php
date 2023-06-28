<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_code',
        'contract_date',
        'list_price',
        'payment_terms',
        'downpayment',
        'maintenance_fee',
        'years_of_occupancy',
        'home_resort',
        'holiday_credits',
        'plan',
        'membership_type',
        'room_type', //x
        'status', //x
        'type', //x
        'reason_for_purchase', //x
        'remarks', //x
        'deck', //x
        'relation_manager_id', //x
        'venue_manager_id', //x
        'holiday_consultant_id', //x
        'created_by',
        'updated_by',
    ];
}
