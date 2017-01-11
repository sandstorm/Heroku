# Introduction
We at sandstorm|media use Dokku to deploy applications quickly and easily to our server. Nevertheless, it is often time-consuming to manually make applications ready for the deployment with Dokku or Heroku. Thus, we created this package to minimize your effort of making TYPO3 Flow and Neos based projects ready for deployment in a few seconds.

# Prerequisites
A Flow version greater or equal to 3.0 is mandatory.

# Compatibility and Maintenance
This package is currently being maintained for Neos 2.3 LTS and Neos 3.x.

| Neos / Flow Version        | Sandstorm.UserManagement Version | Maintained |
|----------------------------|----------------------------------|------------|
| Neos 3.x, Flow 4.x         | 0.5.x                            | Yes        |
| Neos 2.3 LTS, Flow 3.3 LTS | 0.4.x                            | Yes        |

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
1. add domain to App
1. set the baseUri
1. set flow context
1. set database config
1. add dokku as git remote
1. push your project to Dokku
1. (optional) access your project with ssh to configure your Flow instance

``` 
dokku create your-app
dokku storage:mount your-app /home/dokku/your-app/DATA/app/Data/Persistent:/app/Data/Persistent
dokku mariadb:create your-app
dokku mariadb:link your-app your-app
dokku domains:add your-app your-domain-to-the-app.de
dokku config:set your-app BASE_URI=http://your-domain-to-the-app.de/
dokku config:set your-app FLOW_CONTEXT=Production/Heroku
dokku config:set your-app DB_NAME=your-app-db DB_USER=mariadb DB_PASSWORD=super-secret DB_HOST=dokku-mariadb-your-app-db DB_PORT=3306
git remote add dokku dokku@your-dokku-domain.de:your-app
git push dokku master
dokku enter your-app
```

# Prune and Import Site-Package on every Deploy

Careful: This deletes all content on every redeploy. Don't use in staging environments where customers work.

```
dokku config:set your-app PRUNE_AND_IMPORT_SITE=Package.Key
```

# Debugging and Trouble-Shooting

## check status of App

```
dokku ls
```

## Access database

If you want to access the database for debugging run:

```
dokku mariadb:expose your-app
dokku mariadb:info your-app
```

### Access with SequelPro
* Connection Type SSH
* MySQL Host: 127.0.0.1
* Username: username from `mariadb:info`
* Password: password from `mariadb:info`
* Port: port from `mariadb:expose`
* SSH Host: dokku.your-domain.de
* SSH User: you@dokku.your-domain.de

---

# TODOs

* support for gerrit_update.php and gerrit.json
