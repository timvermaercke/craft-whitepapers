<?php

namespace timvermaercke\whitepapers\controllers;

use craft\web\Controller;
use DateTime;
use timvermaercke\whitepapers\models\Download;
use timvermaercke\whitepapers\Whitepapers;

class PublicController extends Controller
{
    public function actionDownloadWhitepaper()
    {
        $this->requirePostRequest();

        $whitepaperId = $this->request->getBodyParam('whitepaperId');
        $email = $this->request->getBodyParam('email');

        $download = new Download();
        $download->whitepaperId = $whitepaperId;
        $download->email = $email;
        $download->downloadedOn = new DateTime();

        $saved = Whitepapers::$instance->whitepapers->saveDownload($download);

        if (!$saved) {
            return $this->asJson(['errors' => $download->getErrors()]);
        }

        $whitepaper = Whitepapers::$instance->whitepapers->find($whitepaperId);
        $asset = \Craft::$app->assets->getAssetById($whitepaper->assetId);

        $fsPath = \Craft::getAlias($asset->getVolume()->fs->path);
        $filePath = $fsPath . DIRECTORY_SEPARATOR . $asset->getPath();
        $fileName = $asset->fileName;

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($fileName) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }
}
