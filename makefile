up:
	php -S localhost:1234
	
clear:
 	php bin/console orm orm:clear-cache:metadata

orm-update:
	php bin/console orm orm:schema-tool:update --force^C