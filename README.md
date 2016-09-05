# Introduction
We at sandstorm|media use Dokku to deploy applications quickly and easily to our server. Nevertheless, it is often time-consuming to manually make applications ready for the deployment with Dokku or Heroku. Thus, we created this package to minimize your effort of making TYPO3 Flow and Neos based projects ready for deployment in a few seconds.

# Prerequisites
A Flow version greater or equal to 3.0 is mandatory.

# Usage

1. When using composer, you can conveniently add this package to your application by typing the following command in your command line:  
    `composer require sandstorm/heroku`
2. After adding this package to your application, make your project ready for Dokku with this command:  
    `./flow heroku:addToProject`
3. Add the base URI placeholder to your *Settings.yaml*

```
TYPO3:
  Flow:
    http:
      baseUri: %env:BASE_URI%
```

# Deployment on Dokku

Execute the following steps to deploy the App to Dokku (commands below):

1. create your Dokku App
1. make *Data/Persistent* persistent over updates
1. create a database
1. link the database with the App
1. set the baseUri
1. add dokku as git remote
1. push your project to Dokku
1. (optional) access your project with ssh to configure your Flow instance

``` 
dokku create your-app
dokku storage:mount your-app /home/dokku/your-app/DATA/app/Data/Persistent:/app/Data/Persistent
dokku mariadb:create your-app
dokku mariadb:link your-app your-app
dokku config:set automon-website BASE_URI=http://your-domain-to-the-app.de/
git remote add dokku dokku@your-dokku-domain.de:your-app
git push dokku master
dokku enter your-app
```

Now, if you use any Flow commands be sure to always prefix `FLOW_CONTEXT=Production/Heroku` in order to address the correct context. Example:

`FLOW_CONTEXT=Production/Heroku ./flow import:something Packages/Application/your-app/Tests/TestData/test.tab`

---

support for gerrit_update.php and gerrit.json

PRUNE_AND_IMPORT_SITE=<SitePackageKey>