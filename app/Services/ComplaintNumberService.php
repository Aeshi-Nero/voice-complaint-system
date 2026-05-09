<?php

namespace App\Services;

use App\Models\Complaint;

class ComplaintNumberService
{
    public function generate(): string
    {
        $lastComplaint = Complaint::orderBy('id', 'desc')->first();
        
        if (!$lastComplaint) {
            return 'C001';
        }
        
        $lastNumber = intval(substr($lastComplaint->complaint_number, 1));
        $newNumber = $lastNumber + 1;
        
        return 'C' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}