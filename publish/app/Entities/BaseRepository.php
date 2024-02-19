<?php

namespace App\Entities;

use ElegantMedia\OxygenFoundation\Entities\OxygenRepository;
use EMedia\MediaManager\Exceptions\FileUploadFailedException;
use EMedia\MediaManager\Uploader\FileUploader;
use Illuminate\Support\Facades\Storage;

abstract class BaseRepository extends OxygenRepository
{

	// Add common repository logic for the application here

	/**
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 * @throws \EMedia\MediaManager\Exceptions\FormFieldNotFoundException
	 * @throws FileUploadFailedException
	 */
	protected function beforeSavingModel(\Illuminate\Http\Request $request, $entity)
	{
		$this->uploadFilesFromRequest($request, $entity);
	}

	/**
	 * @param \Illuminate\Http\Request $request
	 * @param $model
	 *
	 * @return false|void
	 * @throws FileUploadFailedException
	 * @throws \EMedia\MediaManager\Exceptions\FormFieldNotFoundException
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	protected function uploadFilesFromRequest(\Illuminate\Http\Request $request, &$model)
	{
		if (!method_exists($model, 'getEditableFields')) {
			return false;
		}

		$editableFields = $model->getEditableFields();

		// get the editable file fields
		$fileFields = array_filter($editableFields, function ($item) {
			return (isset($item['type']) && $item['type'] === 'file');
		});

		foreach ($fileFields as $fileField) {
			$fileOptions = $fileField['options'];

			// without a disk, we can't upload the file
			if (empty($fileOptions['disk'])) {
				continue;
			}

			// Handle when default upload params are given
			if (!isset($fileOptions['use_db_prefix'])) {
				continue;
			}

			$generateThumb = (empty($fileOptions['thumb_path_column'])) ? false : true;

			if ($request->hasFile($fileField['name'])) {

				$data = [
					'request' => $request,
					'field_name' => $fileField['name'],     // the name of the field in the request
					'disk' => $fileOptions['disk'],         // the disk to upload to
					'generate_thumb' => $generateThumb,     // whether to generate a thumb or not
					'folder' => $fileOptions['folder'] ?? null, // the folder to upload to
					'is_image' => $fileOptions['is_image'] ?? false,
				];

				$uploadResult = $this->uploadFileFromRequest($data);

				if ($uploadResult->isSuccessful()) {
					$fillableData = [
						// These file names should match the column names in the database
						// They're defined in `\ElegantMedia\OxygenFoundation\Macros\RegisterSchemaMacros`
						$this->prefix($fileOptions['use_db_prefix'], 'name') => $uploadResult->getOriginalFilename(),
						$this->prefix($fileOptions['use_db_prefix'], 'original_filename') => $uploadResult->getOriginalFilename(),
						$this->prefix($fileOptions['use_db_prefix'], 'file_path') => $uploadResult->filePath(),
						$this->prefix($fileOptions['use_db_prefix'], 'file_disk') => $uploadResult->diskName(),
						$this->prefix($fileOptions['use_db_prefix'], 'file_url') => $uploadResult->publicUrl(),
						$this->prefix($fileOptions['use_db_prefix'], 'file_size_bytes') => $uploadResult->getFileSize(),
						$this->prefix($fileOptions['use_db_prefix'], 'uploaded_by_user_id') => auth()->id(),
					];
					foreach ($fillableData as $key => $value) {
						$model->{$key} = $value;
					}
					if ($generateThumb) {
						$model->{$fileOptions['thumb_path_column']} = $uploadResult->thumbnailPath();
					}
				} else {
					throw new FileUploadFailedException();
				}
			} elseif ($request->has($fileField['name'] . '_delete_file')) {
				// if the file should be deleted, remove it

				// deleting the file from the disk
				if (isset($fileOptions['delete_from_disk']) && $fileOptions['delete_from_disk'] === true) {
					$disk = $model->{$this->prefix($fileOptions['use_db_prefix'], 'file_disk')};
					$filePath = $model->{$this->prefix($fileOptions['use_db_prefix'], 'file_path')};
					$storageDisk = Storage::disk($disk);
					if ($storageDisk->exists($filePath)) {
						$storageDisk->delete($filePath);
					}
				}

				// removing the reference
				$fillableData = [
					// These file names should match the column names in the database
					// They're defined in `\ElegantMedia\OxygenFoundation\Macros\RegisterSchemaMacros`
					$this->prefix($fileOptions['use_db_prefix'], 'name') => null,
					$this->prefix($fileOptions['use_db_prefix'], 'original_filename') => null,
					$this->prefix($fileOptions['use_db_prefix'], 'file_path') => null,
					$this->prefix($fileOptions['use_db_prefix'], 'file_disk') => null,
					$this->prefix($fileOptions['use_db_prefix'], 'file_url') => null,
					$this->prefix($fileOptions['use_db_prefix'], 'file_size_bytes') => null,
					$this->prefix($fileOptions['use_db_prefix'], 'uploaded_by_user_id') => null,
				];
				foreach ($fillableData as $key => $value) {
					$model->{$key} = $value;
				}
			}

			// if the field is not given, ignore it
		}
	}

	/**
	 * @param $data
	 *
	 * @return \EMedia\MediaManager\Uploader\FileUploaderResponse
	 * @throws FileUploadFailedException
	 * @throws \EMedia\MediaManager\Exceptions\FormFieldNotFoundException
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	public function uploadFileFromRequest($data)
	{
		$fh = new FileUploader($data['request']);
		$fh->toDisk($data['disk'])
			->fileFieldName($data['field_name'])
			->intoSubDirectoryDateFormat('Ym');

		if ($data['is_image']) {
			$fh->resizeImageToMaxWidth(1200)->resizeImageToMaxHeight(1200);
			// $fh->convertImageToFormat('jpg', 90);
		}

		if ($data['generate_thumb']) $fh->withThumbnail();
		if ($data['folder']) $fh->saveToDir($data['folder']);

		$result = $fh->upload();

		if (!$result->isSuccessful()) {
			throw new FileUploadFailedException();
		}

		return $result;
	}

	/**
	 * @param $prefix
	 * @param $name
	 *
	 * @return string
	 */
	protected function prefix($prefix, $name): string
	{
		if ($prefix && $prefix !== true) {
			return $prefix . '_' . $name;
		}

		return $name;
	}

}
