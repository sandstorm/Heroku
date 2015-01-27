#!/bin/bash

# This file is started from the Procfile; and is executed from the FLOW_PATH_ROOT.
touch Data/Logs/System.log
touch Data/Logs/Security.log
touch Data/Logs/Query.log


compile_neos_js_css_if_needed() {
   cd Packages/Application/TYPO3.Neos/Scripts
   echo "installing NPM dependencies"
   npm install
   echo "installing ruby dependencies"
   bundle install
   npm install -g grunt

   grunt compile
   echo "All Compiled!"
}

if [[ "$COMPILE_NEOS_JS_CSS" != "" ]]; then
  echo "Asynchronously compiling Neos CSS and JS"
  compile_neos_js_css_if_needed &
fi


# TODO explain why we do this here and not on build
FLOW_CONTEXT=Production/Heroku ./flow resource:publish
FLOW_CONTEXT=Production/Heroku ./flow doctrine:migrate

if [[ "$PRUNE_AND_IMPORT_SITE" != "" ]]; then

	echo "Pruning site"
	FLOW_CONTEXT=Production/Heroku ./flow site:prune
	echo "Importing $PRUNE_AND_IMPORT_SITE"
	FLOW_CONTEXT=Production/Heroku ./flow site:import --package-key $PRUNE_AND_IMPORT_SITE

fi

bin/heroku-php-nginx \
	-F Packages/Application/Sandstorm.Heroku/Resources/Private/fpm_custom.conf \
	-C Packages/Application/Sandstorm.Heroku/Resources/Private/nginx.inc.conf \
	-l Data/Logs/System.log \
	-l Data/Logs/Security.log \
	-l Data/Logs/Query.log \
	Web/