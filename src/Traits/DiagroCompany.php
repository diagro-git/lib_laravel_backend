<?php
namespace Diagro\Backend\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Intented to be used in models.
 * When creating a record, this trait inserts the logged in user's company id
 * into the company_id column of the model.
 *
 * @package Diagro\Backend\Traits
 */
trait DiagroCompany
{


    protected static function bootDiagroCompany()
    {
        static::addGlobalScope('company', function(Builder $builder) {
            $builder->where('company_id', '=', auth()->user()?->company()->id());
        });

        static::creating(function(Model $model) {
            if(empty($model->getAttribute('company_id'))) {
                $model->setAttribute('company_id', auth()->user()?->company()->id());
            }
        });
    }


}