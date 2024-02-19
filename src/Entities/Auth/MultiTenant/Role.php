<?php

namespace EMedia\Oxygen\Entities\Auth\MultiTenant;

use EMedia\MultiTenant\Scoping\Traits\TenantScopedModelTrait;
use Silber\Bouncer\Database\Role as BouncerRole;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Role extends BouncerRole
{

	use TenantScopedModelTrait;
	use HasSlug;

	protected $fillable = ['title', 'description'];

	public function getSlugOptions(): SlugOptions
	{
		return SlugOptions::create()->generateSlugsFrom('title')->saveSlugsTo('name');
	}

}
