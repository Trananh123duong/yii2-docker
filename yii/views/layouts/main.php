<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\widgets\Menu;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="header">
    <?php
    $items = [];

    if (Yii::$app->user->isGuest) {
        $items[] = ['label' => 'Login', 'url' => ['/site/login']];
    } else {
        $role = Yii::$app->user->identity->role;
    
        if ($role == 1) {
            $items[] = ['label' => 'User', 'url' => ['/user']];
            $items[] = ['label' => 'Project', 'url' => ['/project']];
            $items[] = ['label' => 'Chart', 'url' => ['/chart']];
            $items[] = ['label' => 'Account', 'url' => ['/user/view', 'id' => Yii::$app->user->id]];
            $items[] = [
                'label' => 'Logout (' . Yii::$app->user->identity->username . ')',
                'url' => ['/site/logout'],
                'linkOptions' => ['data-method' => 'post']
            ];
        } elseif ($role == 2) {
            $items[] = ['label' => 'Project', 'url' => ['/project']];
            $items[] = ['label' => 'Account', 'url' => ['/user/view', 'id' => Yii::$app->user->id]];
            $items[] = [
                'label' => 'Logout (' . Yii::$app->user->identity->username . ')',
                'url' => ['/site/logout'],
                'linkOptions' => ['data-method' => 'post']
            ];
        } elseif ($role == 3) {
            $items[] = ['label' => 'Account', 'url' => ['/user/view', 'id' => Yii::$app->user->id]];
            $items[] = [
                'label' => 'Logout (' . Yii::$app->user->identity->username . ')',
                'url' => ['/site/logout'],
                'linkOptions' => ['data-method' => 'post']
            ];
        }
    }

    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top']
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => $items,
    ]);
    // echo Menu::widget([
    //     'items' => [
    //         // Important: you need to specify url as 'controller/action',
    //         // not just as 'controller' even if default action is used.
    //         ['label' => 'Home', 'url' => ['site/index']],
    //         // 'Products' menu item will be selected as long as the route is 'product/index'
    //         ['label' => 'Products', 'url' => ['product/index'], 'items' => [
    //             ['label' => 'New Arrivals', 'url' => ['product/index', 'tag' => 'new']],
    //             ['label' => 'Most Popular', 'url' => ['product/index', 'tag' => 'popular']],
    //         ]],
    //         ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
    //     ],
    // ]);
    NavBar::end();
    ?>
</header>

<main id="main" class="flex-shrink-0" role="main">
    <div class="container">
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
        <?php endif ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer id="footer" class="mt-auto py-3 bg-light">
    <div class="container">
        <div class="row text-muted">
            <div class="col-md-6 text-center text-md-start">&copy; My Company <?= date('Y') ?></div>
            <div class="col-md-6 text-center text-md-end"><?= Yii::powered() ?></div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
