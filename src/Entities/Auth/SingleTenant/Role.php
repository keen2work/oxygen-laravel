<?php

namespace EMedia\Oxygen\Entities\Auth\SingleTenant;

use Silber\Bouncer\Database\Role as BouncerRole;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Role extends BouncerRole
{

	use HasSlug;

	protected $fillable = ['title', 'description'];

	public function getSlugOptions(): SlugOptions
	{
		return SlugOptions::create()->generateSlugsFrom('title')->saveSlugsTo('name');
	}

}
