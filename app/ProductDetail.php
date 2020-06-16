<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model  
{

    

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'product_detail';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['mark_id', 'model_id', 'category_id', 'product_id', 'created_at', 'updated_at'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    public function model(){
        return $this->hasOne('App\ModelItem','id','model_id');
    }

    public function mark(){
        return $this->hasOne('App\Mark','id','mark_id');
    }

    public function category(){
        return $this->hasOne('App\Category','id','category_id');
    }

    public function product()
    {
        return $this->belongsTo('Product','id','product_id');
    }

}
