<?php

namespace Acelle\Server\Model;

use App\Model\ApiKey;
use App\Model\Customer;
use Acelle\Server\Library\VerificationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Library\Traits\HasUid;
use Carbon\Carbon;

class EmailAddress extends Model
{
    use HasFactory;
    use HasUid;

    public static function newDefault()
    {
        $emailAddress = new self();
        $emailAddress->verification_status = VerificationStatus::NEW->value;

        return $emailAddress; 
    }

    public function apiKey()
    {
        return $this->belongsTo(ApiKey::class);
    }

    public static function verifyFromFile($email, $filePath, $customer=null)
    {
        if ($customer) {
            $emailAddress = self::verifyByCustomer($email, $customer);
        } else {
            $emailAddress = self::publicVerify($email);
        }

        $emailAddress->file_path = $filePath;
        $emailAddress->save();

        return $emailAddress;
    }

    public static function verifyByApiKey($email, $apiKey)
    {
        $emailAddress = self::verifyByCustomer($email, $apiKey->customer);
        $emailAddress->api_key_id = $apiKey->id;
        $emailAddress->save();

        return $emailAddress;
    }

    public static function publicVerify($email)
    {
        $emailAddress = self::whereNotNull('customer_id')
            ->where('email', '=', $email)->first();
        
        if (!$emailAddress) {
            $emailAddress = self::newDefault();
        }

        // set email
        $emailAddress->email = $email;

        // verify
        $emailAddress->verify();

        //
        return $emailAddress;
    }

    public static function verifyByCustomer($email, $customer)
    {
        $emailAddress = self::forCustomer($customer)
            ->where('email', '=', $email)->first();

        if (!$emailAddress) {
            $emailAddress = self::newForCustomer($customer);
        }

        $emailAddress->email = $email;

        $emailAddress->verify();

        //
        return $emailAddress;

    }

    public static function newForCustomer(Customer $customer): self
    {
        $emailAddress = self::newDefault();
        $emailAddress->customer_id = $customer->id;

        return $emailAddress;
    }

    public static function forCustomer(Customer $customer): Builder
    {
        return self::query()->where('customer_id', $customer->id);
    }

    public function verify()
    {
        $this->verification_error = null;

        $engine = new \Acelle\Server\Library\AthenaEngine();
        list($status, $raw) = $engine->verifySingle($this->email);

        $this->verification_status = $status;
        $this->last_verification_at = Carbon::now();
        $this->last_verification_by = $engine->getName();
        $this->last_verification_result = $raw;
        
        // result
        $this->save();
    }

    public static function scopeSearch($query, $keyword)
    {
    }

    public static function scopeNew($query)
    {
        $query->whereVerificationStatus(VerificationStatus::NEW->value);
    }

    public static function scopeVerified($query)
    {
        $query->where('verification_status', '!=', VerificationStatus::NEW->value);
    }

    public function getResult()
    {
        $raw = $this->last_verification_result;

        if (!$raw) {
            return null;
        }

        return json_decode($raw, true);
    }

    public function isValid()
    {
        return $this->verification_status == VerificationStatus::DELIVERABLE->value;
    }

    public function isInvalid()
    {
        return $this->verification_status == VerificationStatus::UNDELIVERABLE->value;
    }

    public function isUnknown()
    {
        return $this->verification_status == VerificationStatus::UNKNOWN->value;
    }

    public function getFileName()
    {
        $parts = explode('___', $this->file_path);
        return count($parts) > 1 ? $parts[1] : 'FileName.csv';
    }

    public static function createFromApiKey($email, $apiKey)
    {
        $record = new static();
        $record->verification_status = VerificationStatus::NEW->value;
        $record->email = $email;
        $record->api_key_id = $apiKey->id;
        $record->customer_id = $apiKey->customer_id;
        $record->save();

        return $record;
    }
}
