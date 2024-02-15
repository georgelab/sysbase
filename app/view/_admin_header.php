<?php
/**
 * Admin View Header
 *
 * @category    core
 * @package     core/appSetup/view
 * @author      George Azevedo <george@fenix.rio.br>
 * @copyright   Copyright (c) 2023 Fênix Comunicação (https://fenix.rio.br)
 */

$controller->Render->protectFromDirectRender();

?><!DOCTYPE html>
<html lang="<?php echo $appSetup->seo->language; ?>">
<head>
    <!-- Meta -->
    <meta charset="utf-8">
    <title><?php echo $appSetup->seo->title; ?></title>
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=5.0, user-scalable=5.0" />
    <meta name="robots" content="<?php echo $appSetup->seo->robots; ?>" />

    <!-- SEO -->
    <meta name="description" content="<?php echo $appSetup->seo->description; ?>">
    <meta property="og:title" content="<?php echo $appSetup->seo->title; ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?php echo $controller->Render->Router->getURL(true); ?>" />
    <meta property="og:site_name" content="<?php echo $appSetup->info->project; ?>" />
    <meta property="og:description" content="<?php echo $appSetup->seo->description; ?>">
    <meta property="og:image" content="<?php echo $controller->Render->Router->getURL() . $appSetup->seo->image; ?>" />
    <meta property="og:image:alt" content="<?php echo $appSetup->seo->title; ?>" />
    <meta property="og:locale" content="<?php echo $appSetup->seo->locale; ?>" />
    <link rel="canonical" href="<?php echo $controller->Render->Router->getURL(true); ?>" />
    <?php
        #prefetch css data
        $controller->Render->loadStyles('prefetch');
        #prefetch fonts
        $controller->Render->loadFonts('preload');
        #preload js data
        $controller->Render->loadScripts('preload');
    ?>

    <link rel="DNS-prefetch" href="https://maps.googleapis.com" />
    <link rel="preconnect" href="https://maps.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://maps.googleapis.com" crossorigin>

    <!-- favicon -->
    <link rel="shortcut icon" href="/assets/favicon/favicon.ico" />
</head>

<body>
    <h3>Header</h3>