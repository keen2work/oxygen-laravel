<?php

namespace App\Models;

use EMedia\Oxygen\Entities\Traits\OxygenUserTrait;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
	// use HasApiTokens;
	use HasFactory, Notifiable;
	use OxygenUserTrait;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'email',
		'password',
		'name',
		'first_name',
		'last_name',
		'email',
		'password',
		'timezone',
		'phone'
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
		'remember_token',
		'name',
		'password',
		'remember_token',
		'avatar_path',
		'avatar_disk'
	];

	/**
	 *
	 * The attributes that should be visible for arrays.
	 *
	 * @var string[]
	 */
	protected $visible  = [
		'uuid',
		'first_name',
		'last_name',
		'full_name',
		'email',
		'avatar_url',
		'avatar_path',
		'avatar_disk',
		'timezone'
	];

	/**
	 *
	 * The attributes that should be automatically appended.
	 *
	 * @var string[]
	 */
	protected $appends  = [
		'first_name',
		'full_name'
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'id' => 'string',
		'email_verified_at' => 'datetime',
		'password' => 'hashed',
	];

	/**
	 *
	 * Columns which are searchable by default.
	 *
	 * @var string[]
	 */
	protected $searchable = [
		'name',
		'last_name',
		'email'
	];

	/**
	 *
	 * Fields that may be added to the API responses.
	 *
	 * @return string[]
	 */
	public function getExtraApiFields(): array
	{
		return [
			'access_token',
		];
	}

}
