<?php
#@title:FrontPage
#@description:home page
#@keywords:home, page
#@image:
?>
	<h2>FrontPage</h2>

	<form method="get" action="/">
		<input type="text" name="user" value="jose" />
		<input type="password" name="pwd" value="1q2w3e" />
		<input type="hidden" name="token" value="<?php echo $controller->Request->getCSRF(); ?>" />
		<input type="hidden" name="insertintouser" value="delete * from" />
		<button>send</button>
	</form>