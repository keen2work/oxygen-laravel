# Formation

Formation is a library for creating and managing Forms. It's installed by default.

Please read the [Formation documentation](https://bitbucket.org/elegantmedia/formation/src/master/) for more information.

Here are some examples of using Formation:

## Auto-Generate Image Fields

To allow form generation, you must include `GeneratesFields` trait in your model.

```php
// Migration
return new class extends Migration
{
    public function up()
    {
        Schema::create('business', function (Blueprint $table) {
	        $table->id();
	        $table->string('name')->nullable();
	        $table->string('email')->nullable();
	        $table->timestamps();
        });
    }
    
    //...
};
```

```php
// Model
use EMedia\Formation\Entities\GeneratesFields;

class Business extends Model
{
	use GeneratesFields;
	
	// fillable will update the model fields
	protected $fillable = [
		'name',
		'email',
	];
	
	// editable will show the form fields
	protected $editable = [
		'name',
		'email',
	];
}
```

With the above example, your fields in migration, must match both `fillable` and `editable` fields in your model.

## Image Fields

```php
// Migration
return new class extends Migration
{
    public function up()
    {
        Schema::create('business', function (Blueprint $table) {
	        $table->id();
	        $table->string('name')->nullable();
	        $table->string('email')->nullable();
	        
	        // add an image upload field called `logo`
	        // this will also create a field called `logo_file_url`
	        $table->file('logo')->nullable();
	        
	        // add another image upload field called `cover_photo`
	        // this will also create a field called `cover_photo_file_url`
	        $table->file('cover_photo')->nullable();
	           
	        $table->timestamps();
        });
    }
    
    //...
};
```

```php
// Model
use EMedia\Formation\Entities\GeneratesFields;

class Business extends Model
{
	use GeneratesFields;
	
	// fillable will update the model fields
	protected $fillable = [
		'name',
		'email',
	];
	
	// editable will show the form fields
	protected $editable = [
		'name',
		'email',
		
		// allow the user to upload an image called `logo`
		[
			'name' => 'logo_file_url',      // this should match with the name of URL column in database
			'display_name' => 'Logo',       // the file name to display in the form
			'type' => 'file',
			'options' => [
				'disk' => 'images',     // required. This must match with a disk name in `config/filesystems.php`
				'use_db_prefix' => 'logo',	// required. This is the prefix used to create database column.
				// 'folder' => 'logos',             // optional. If given, it will be used to create a folder in the disk.
				// 'generate_thumb' => false,    // optional. default is false
				'is_image' => true,              // optional. default is false
				// 'delete_from_disk' => false,      // optional. default is false, if you set it to true, it will delete the file from disk when deleting the record.
			],
		],
		
		// allow the user to upload an image called `cover_photo`
		[
			'name' => 'cover_photo_file_url',      // this should match with the name of URL column in database
			'display_name' => 'Cover Photo',       // the file name to display in the form
			'type' => 'file',
			'options' => [
				'disk' => 'club_cover_photos',     // required. This must match with a disk name in `config/filesystems.php`
				'use_db_prefix' => 'cover_photo',	// required. This is the prefix used to create database column.
				// 'folder' => '',          // optional. If given, it will be used to create a folder in the disk.
				// 'generate_thumb' => false,    // optional. default is false
				'is_image' => true,              // optional. default is false
				// 'delete_from_disk' => false,      // optional. default is false, if you set it to true, it will delete the file from disk when deleting the record.
			],
		],
	];
}
```

## Geo-Location/Address Fields

For more examples of location fields, please read the [about Location fields](https://bitbucket.org/elegantmedia/lotus/wiki/Location%20Field)

```php
// Migration
return new class extends Migration
{
    public function up()
    {
        Schema::create('business', function (Blueprint $table) {
	        $table->id();
	        $table->string('name')->nullable();
	        $table->string('email')->nullable();	        
	        $table->place();         // create a `place` field. This includes geo-location and address fields.
	        $table->timestamps();
        });
    }
    
    //...
};
```

```php
// Model
use EMedia\Formation\Entities\GeneratesFields;

class Business extends Model
{
	use GeneratesFields;
	
	// fillable will update the model fields
	protected $fillable = [
		'name',
		'email',
		'address',
		'description',
		'phone',
		'email',
		'address',
		'formatted_address',
		'latitude',
		'longitude',
		'street',
		'street_2',
		'city',
		'state',
		'state_iso_code',
		'zip',
		'country',
		'country_iso_code',
	];
	
	// because location needs some customisation, we'll override the editable fields
	public function getEditableFields() {
		return [
			'name',
			'email',
			[
				'name' => 'address',
				'type' => 'location',
				'config' => lotus()->locationConfig()
					->setSearchBoxElementId('js-search-box3')
					->showAddressComponents(true)
					->setAutoCompleteOptions([
						'types' => ['establishment'],
						'componentRestrictions' => [
							'country' => 'nz',
						]
					])
			],
		];
	}
}
```

## Date Fields

For date-time selection, the frontend requries a datepicker. The recommended datepicker is the [flatpickr library](https://flatpickr.js.org/).

```php
// Migration
return new class extends Migration
{
    public function up()
    {
        Schema::create('business', function (Blueprint $table) {
	        $table->id();
	        $table->string('name')->nullable();
	        $table->dateTime('started_at')->nullable();	   // create a date field     
	        $table->timestamps();
        });
    }
    
    //...
};
```

```php
// Model
use EMedia\Formation\Entities\GeneratesFields;

class Business extends Model
{
	use GeneratesFields;
	
	// fillable will update the model fields
	protected $fillable = [
		'name',
		'email',
	];
	
	// define date fields <- this is important, so the dates will be properly cast
	protected $dates = [
		'started_at',
	];
	
	// editable will show the form fields
	protected $editable = [
		'name',
		'email',
	];
}
```
