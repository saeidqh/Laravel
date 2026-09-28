<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Person extends Model
{
    protected $table = 'obs_table_person';

    protected $primaryKey = 'PersonID';

    public $timestamps = false;

    protected $fillable = [
        'PersonTitle',
        'personType',
        'PersonCategoryIDRef',
        'firstName',
        'lastName',
        'email',
        'mobile',
        'companyPhone',
        'companyFax',
        'companyNameEn',
        'companyName',
        'anonymous',
        'companyNationalCode',
        'directorManager',
        'boardChief',
        'boardMember',
        'contactPerson',
        'nationalCode',
        'economicCode',
        'registrationNumber',
        'registrationDate',
        'countryIDRef',
        'address',
        'companyAddress',
        'postalCode',
        'registrationPlace',
        'parentId',
        'activityDescription',
    ];

    protected function casts(): array
    {
        return [
            'PersonID' => 'integer',
            'PersonCategoryIDRef' => 'integer',
            'countryIDRef' => 'integer',
            'userIdRef1' => 'integer',
            'userIdRef2' => 'integer',
            'hasChange' => 'boolean',
            'isDeleted' => 'boolean',
            'personStatus' => 'integer',
            'parentId' => 'integer',
            'verified' => 'integer',
        ];
    }

    public function personCategory(): BelongsTo
    {
        return $this->belongsTo(
            PersonCategory::class,
            'PersonCategoryIDRef',
            'id'
        );
    }

    public function company(): HasOne
    {
        return $this->hasOne(
            Company::class,
            'companyIdRef',
            'PersonID'
        );
    }
}