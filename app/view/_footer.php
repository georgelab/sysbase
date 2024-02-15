<?php
/**
 * Default View Footer
 *
 * @author      George Azevedo <george@fenix.rio.br>
 * @copyright   Copyright (c) 2023 Fênix Comunicação (https://fenix.rio.br)
 */

$controller->Render->protectFromDirectRender();
?>
    <h3>Footer</h3>

<?php
#app loader
$controller->Render->loadStyles();
$controller->Render->loadScripts();
#app footer actions
//$controller->Render->getToast();
$controller->Render->getSchemaOrg(true);
$controller->Render->developmentTools(true);
?>
</body>
</html>