<?php

namespace timvermaercke\whitepapers\controllers;

use Craft;
use craft\web\Controller;
use craft\web\Response;
use craft\web\View;
use timvermaercke\whitepapers\models\Whitepaper;
use timvermaercke\whitepapers\Whitepapers;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

class AdminController extends Controller
{
    public function actionEdit(?int $whitepaperId = null, ?Whitepaper $whitepaper = null)
    {
        if (!Craft::$app->getUser()->getIsAdmin()) {
            throw new ForbiddenHttpException('User is not permitted to perform this action.');
        }

        if (!$whitepaper) {
            if ($whitepaperId) {
                $whitepaper = Whitepapers::$instance->whitepapers->find($whitepaperId);
                if (!$whitepaper) {
                    throw new BadRequestHttpException(\Craft::t('whitepapers', 'whitepaper_not_found', ['id' => $whitepaperId]));
                }
            } else {
                $whitepaper = new Whitepaper();
            }
        }

        return $this->renderTemplate(
            'whitepapers/_edit',
            ['whitepaper' => $whitepaper],
            View::TEMPLATE_MODE_CP
        );
    }

    public function actionSave(): ?Response
    {
        if (!Craft::$app->getUser()->getIsAdmin()) {
            throw new ForbiddenHttpException('User is not permitted to perform this action.');
        }

        $whitepaperId = $this->request->getBodyParam('whitepaperId');

        if ($whitepaperId) {
            $whitepaper = Whitepapers::$instance->whitepapers->find($whitepaperId);
            if (!$whitepaper) {
                throw new BadRequestHttpException(\Craft::t('whitepapers', 'whitepaper_not_found', ['id' => $whitepaperId]));
            }
        } else {
            $whitepaper = new Whitepaper();
        }

        $whitepaper->title = $this->request->getBodyParam(('title'));
        $whitepaper->assetId = $this->request->getBodyParam(('assetId'));

        $saved = Whitepapers::$instance->whitepapers->save($whitepaper);
        if (!$saved) {
            $this->setFailFlash(\Craft::t('whitepapers', 'something_went_wrong'));
            Craft::$app->urlManager->setRouteParams([
                'whitepaper' => $whitepaper,
            ]);
            return null;
        }

        $this->setSuccessFlash(\Craft::t('whitepapers', 'whitepaper_saved'));

        return $this->redirectToPostedUrl();
    }

    public function actionDelete()
    {
        if (!Craft::$app->getUser()->getIsAdmin()) {
            throw new ForbiddenHttpException('User is not permitted to perform this action.');
        }

        $whitepaperId = $this->request->getBodyParam('whitepaperId');
        $whitepaper = Whitepapers::$instance->whitepapers->find($whitepaperId);
        if (!$whitepaper) {
            throw new BadRequestHttpException(\Craft::t('whitepapers', 'whitepaper_not_found', ['id' => $whitepaperId]));
        }

        Whitepapers::$instance->whitepapers->remove($whitepaper);

        $this->setSuccessFlash(\Craft::t('whitepapers', 'whitepaper_deleted'));

        return $this->redirectToPostedUrl();
    }

    public function actionExport(int $whitepaperId)
    {
        if (!Craft::$app->getUser()->getIsAdmin()) {
            throw new ForbiddenHttpException('User is not permitted to perform this action.');
        }

        $whitepaper = Whitepapers::$instance->whitepapers->find($whitepaperId);
        if (!$whitepaper) {
            throw new BadRequestHttpException(\Craft::t('whitepapers', 'whitepaper_not_found', ['id' => $whitepaperId]));
        }

        $downloads = Whitepapers::$instance->whitepapers->getDownloadsByWhitepaperId($whitepaper->id);
        $filename = "emailaddresses $whitepaper->title.csv";
        $array = [];
        foreach ($downloads as $item) {
            $array[] = [$item->email];
        }

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');
        $f = fopen('php://output', 'w');
        $delimiter = ';';
        foreach ($array as $line) {
            fputcsv($f, $line, $delimiter);
        }

        die;
    }
}
