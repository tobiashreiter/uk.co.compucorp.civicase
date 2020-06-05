<?php

/**
 * Class CRM_Civicase_Page_ActivityFiles.
 *
 * Handles downloading of attachments of activities.
 */
class CRM_Civicase_Page_ActivityFiles {

  /**
   * Download all activity files contained in a single zip file.
   */
  public static function downloadAll() {
    $activity = self::getActivityFromRequest();

    $zipName = self::getZipName($activity);
    $zipDestination = self::getDestinationPath();
    $zipFullPath = $zipDestination . '/' . $zipName;
    $files = self::getActivityFilePaths($activity['id']);
    $zipFileResource = self::createZipFile($zipFullPath, $files);

    unlink($zipFullPath);
    self::downloadZipFileResource($zipName, $zipFileResource);
  }

  /**
   * Returns the activity specified by the request.
   *
   * In case the request gives an invalid activity id it
   * throws a 404 status code.
   */
  private static function getActivityFromRequest() {
    $activityId = CRM_Utils_Array::value('activity_id', $_GET);

    self::validateActivityId($activityId);

    $activityResult = civicrm_api3('Activity', 'get', [
      'id' => $activityId,
      'return' => ['activity_type_id.label'],
    ]);

    if ($activityResult['count'] === 0) {
      return self::throwStatusCode(404);
    }

    return CRM_Utils_Array::first($activityResult['values']);
  }

  /**
   * Validates the provided activity id. If not, it returns a 404 status code.
   *
   * @param string|null $activityId
   *   Activity ID.
   */
  private static function validateActivityId($activityId) {
    if (empty($activityId)) {
      self::throwStatusCode(400);
    }
  }

  /**
   * Throws a specific status code and closes the connection.
   *
   * @param int $statusCode
   *   Status code.
   */
  private static function throwStatusCode($statusCode) {
    http_response_code($statusCode);
    CRM_Utils_System::civiExit();
  }

  /**
   * Returns the name of the zipped file for the given activity.
   *
   * Ex: Activity Open Case 123.zip.
   *
   * @param array $activity
   *   Activity.
   *
   * @return string
   *   Zip File name.
   */
  private static function getZipName(array $activity) {
    $name = 'Activity ' . $activity['activity_type_id.label'] . ' ' . $activity['id'];

    return CRM_Utils_String::munge($name, ' ') . '.zip';
  }

  /**
   * Returns the destination path for the zip file.
   *
   * @return string
   *   Destination path.
   */
  private static function getDestinationPath() {
    $config = CRM_Core_Config::singleton();

    return $config->customFileUploadDir;
  }

  /**
   * Returns a list of file paths that are part of a given activity.
   *
   * @param int|string $activityId
   *   Activity ID.
   *
   * @return array
   *   Activity file paths.
   */
  private static function getActivityFilePaths($activityId) {
    $filePaths = [];
    $activityFiles = CRM_Core_BAO_File::getEntityFile('civicrm_activity', $activityId);

    foreach ($activityFiles as $activityFile) {
      $filePaths[] = $activityFile['fullPath'];
    }

    return $filePaths;
  }

  /**
   * Creates a zip file at the given path and containing the given files.
   *
   * @param string $zipFullPath
   *   Zip file path.
   * @param array $filePaths
   *   Individual file paths.
   *
   * @return resource
   *   Resource.
   */
  private static function createZipFile($zipFullPath, array $filePaths) {
    $mode = ZipArchive::CREATE | ZipArchive::OVERWRITE;
    $zip = new ZipArchive();
    $zipName = basename($zipFullPath);
    $zipFileResource = NULL;

    $zip->open($zipFullPath, $mode);

    foreach ($filePaths as $filePath) {
      $fileName = basename($filePath);

      $zip->addFile($filePath, $fileName);
    }

    $zip->close();

    return readfile($zipFullPath, FALSE, $zipFileResource);
  }

  /**
   * Setups the given zip file resource so it can be downloaded by the browser.
   *
   * @param string $zipName
   *   Zip file name.
   * @param resource $zipFileResource
   *   Zip File Resource.
   */
  private static function downloadZipFileResource($zipName, $zipFileResource) {
    CRM_Utils_System::download($zipName, 'application/zip', $fileResource);
  }

}
