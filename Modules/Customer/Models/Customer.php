<?php

namespace Modules\Customer\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;
use Modules\Address\Models\Address;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Customer extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'gender',
        'document',
        'document_secondary',
        'document_secondary_complement',
        'date_of_birth',
        'place_of_birth',
        'civil_status',
        'occupation',
        'cover',
        'income',
        'telephone',
        'cell',
        'lessor',
        'lessee',
        'type_of_communion',
        'spouse_document',
        'spouse_occupation',
        'spouse_income',
        'address_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected  $casts = [
        'name' => 'string',
        'email' => 'string',
        'email_verified_at' => 'datetime',
        'gender' => 'integer',
        'document' => 'integer',
        'document_secondary' => 'integer',
        'document_secondary_complement' => 'string',
        'date_of_birth' => 'date',
        'place_of_birth' => 'string',
        'civil_status' => 'integer',
        'cover' => 'string',
        'occupation' => 'string',
        'income' => 'integer',
        'telephone' => 'string',
        'cell' => 'string',
        'lessor' => 'boolean',
        'lessee' => 'boolean',
        'type_of_communion' => 'integer',
        'spouse_document' => 'integer',
        'spouse_occupation' => 'string',
        'spouse_income' => 'integer',
        'zipcode' => 'integer',
        'state' => 'string',
        'city' => 'string',
        'neighborhood' => 'string',
        'street' => 'string',
        'number' => 'integer',
        'complement' => 'string',
    ];

    public static array $rules = [
        'name' => 'required|min:3|max:255',
        'gender' => 'required|in:' . Gender::TYPES,
        'document' => 'required|unique:customers|min:11',
        'document_secondary' => 'nullable|unique:customers|min:7|max:12',
        'document_secondary_complement' => 'nullable',
        'date_of_birth' => 'required|date|date_format:Y-m-d',
        'place_of_birth' => 'string',
        'civil_status' => 'required|in:' . MaritalStatus::TYPES,
        'cover' => 'file|nullable',
        'income' => 'required|max:9',
        'telephone' => 'nullable|unique:customers',
        'cell' => 'required|unique:customers',
        'email' => 'nullable|unique:customers|email',

        // TODO: Criar um modelo para o tipo de cliente
        'lessor' => 'required_without:lessee|bool',
        'lessee' => 'required_without:lessor|bool',

        // TODO: Criar um modelo para cônjuge
        'type_of_communion' => 'required_if:civil_status, '. MaritalStatus::MARRIED .  '|in:' . MarriagePropertyRegimes::TYPES,
        'spouse_document' => 'required_if:civil_status, '. MaritalStatus::MARRIED,
        'spouse_occupation' => 'required_if:civil_status, '. MaritalStatus::MARRIED,
        'spouse_income' => 'required_if:civil_status, '. MaritalStatus::MARRIED,
    ];

    public static array $rulesUpdate = [
        'document' => 'required|min:11|unique:customers,document,',
        'document_secondary' => 'nullable|min:7|max:12|unique:customers,document_secondary,',
        'telephone' => 'nullable|unique:customers,telephone,',
        'cell' => 'required|unique:customers,cell,',
        'email' => 'nullable|email|unique:customers,email,',
    ];


    public static array $filters = [
        'name' => 'capitalize',
        'place_of_birth' => 'capitalize',
        'document' => 'digit',
        'document_secondary' => 'digit',
        'income' => 'digit',
        'spouse_document' => 'digit',
        'spouse_income' => 'digit'
    ];

    final public function getDocumentAttribute(string $value): string
    {
        return substr($value, 0, 3) . '.' . substr($value, 3, 3) . '.' . substr($value, 6, 3) . '-' . substr($value, -2);
    }

    final public function getDateOfBirthAttribute(string $value): string
    {
        return date('d/m/Y', strtotime($value));
    }

    final public function address(): HasOne
    {
        return $this->hasOne(Address::class, 'id', 'address_id');
    }
}
