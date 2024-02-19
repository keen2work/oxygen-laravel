<?php


namespace EMedia\Oxygen\Entities\Traits;

use ElegantMedia\SimpleRepository\Search\Filterable;
use Illuminate\Http\Request;

trait FiltersByLatLngTrait
{


	/**
	 *
	 * Filter a query by latitude, longitude and distance
	 * Use this method on a repository to extend an existing query
	 * Example:
	 *
	 *
	 * @param Request $request
	 * @param Filterable $query
	 * @param int $defaultDistance
	 * @param string $unit
	 */
	protected function filterByLatLng(
		Request $request,
		Filterable $query,
		int $defaultDistance = 10000,
		string $unit = 'km'
	) {
		if ($unit === 'km') {
			$circleRadius = 6371; // kilometers
		} else {
			$circleRadius = 3959; // miles
		}

		if ($request->filled('latitude') && $request->filled('longitude')) {
			$latitude = $request->latitude;
			$longitude = $request->longitude;

			$distance = $defaultDistance;
			if ($request->filled('distance')) {
				$distance = $request->distance;
			}

			$query->selectRaw('*, (? * acos(cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude)))
                    ) AS distance', [$circleRadius, $latitude, $longitude, $latitude]);

			$query->whereRaw('(? * acos(cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude)))
                    ) < ?', [$circleRadius, $latitude, $longitude, $latitude, $distance]);

			if ($request->filled('sort') && $request->sort === 'distance') {
				$query->orderBy('distance');
			}
		}
	}

}
