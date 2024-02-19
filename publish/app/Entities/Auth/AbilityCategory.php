<?php

namespace App\Entities\Auth;

use Cviebrock\EloquentSluggable\Sluggable;
use ElegantMedia\OxygenFoundation\Scout\KeywordSearchable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class AbilityCategory extends Model implements KeywordSearchable
{

	use Searchable;
	use HasSlug;

	protected $fillable = [
		'name',
		'default_abilities'
	];

	public function getSlugOptions(): SlugOptions
	{
		return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
	}

	public function getSearchableFields(): array
	{
		return [
			'name',
		];
	}

	public function abilities()
	{
		return $this->hasMany(app(config('oxygen.abilityModel')));
	}
}
