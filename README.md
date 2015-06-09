# Introduction
We at sandstorm|media use Dokku to deploy applications quickly and easily to our server. Nevertheless, it is often time-consuming to manually make applications ready for the deployment with Dokku or Heroku. Thus, we created this package to minimize your effort of making TYPO3 Flow and Neos based projects ready for deployment in a few seconds.

# Prerequisites
A Flow version greater or equal to 3.0 is mandatory.

# Usage
When using composer, you can conveniently add this package to your application by typing the following command in your command line: 

<code>composer require sandstorm/heroku</code>


After adding this package to your application, make your project ready for Dokku with this command:

<code>./flow heroku:addToProject</code>

# Deployment on Dokku

Now here are some instructions on how to deploy your application to Dokku:

<b>Create your Dokku container</b>

<code>dokku create your-project-name</code>

<b>Create a volume for your persistent data</b>

<code>dokku volume:create your-project-name /app/Data/Persistent</code>

<b>Map your volume to the container</b>

<code>dokku volume:link your-project-name your-project-name</code>

<b>Create a database for your project</b>

<code>dokku mariadb:create your-project-name</code>

<b>Map the database to your project</b>

<code>dokku mariadb:link your-project-name your-project-name</code>

<b>Define a remote branch for your Dokku instance</b>

<code>git remote add dokku dokku@your-dokku-domain.de:your-project-name</code>

<b>Push your project to Dokku</b>

<code>git push dokku master</code>

<b>Optional: Access your project with ssh to configure your Flow instance</b>

<code>dokku enter your-project-name</code>

Now, if you use any Flow commands be sure to always prefix <code>FLOW_CONTEXT=Production/Heroku</code> in order to address the correct context. Example:

<code>FLOW_CONTEXT=Production/Heroku ./flow import:something Packages/Application/your-project-name/Tests/TestData/test.tab</code>

---


support for gerrit_update.php and gerrit.json

PRUNE_AND_IMPORT_SITE=<SitePackageKey>