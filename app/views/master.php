<!DOCTYPE html>
<!--[if lt IE 7]>      <html lang="en" ng-app="timeManagement" class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html lang="en" ng-app="timeManagement" class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html lang="en" ng-app="timeManagement" class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en" ng-app="timeManagement" class="no-js"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Time Management</title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="/css/main.css">

  <script src="bower_components/html5-boilerplate/js/vendor/modernizr-2.6.2.min.js"></script>
</head>
<body>
  <!--[if lt IE 7]>
      <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
  <![endif]-->

  <div flash-alert="error" active-class="in" class="alert fade">
      <strong class="alert-heading">Oh snap!</strong>
      <span class="alert-message">{{flash.message}}</span>
  </div>

  <div flash-alert="success" active-class="in" class="alert fade">
      <strong class="alert-heading">Well done!</strong>
      <span class="alert-message">{{flash.message}}</span>
  </div>

  <div class="container" ng-view></div>

  <script src="bower_components/angular/angular.js"></script>
  <script src="bower_components/angular-route/angular-route.js"></script>
  <script src="bower_components/lodash/dist/lodash.min.js"></script>
  <script src="bower_components/restangular/dist/restangular.js"></script>
  <script src="bower_components/angular-flash/dist/angular-flash.js"></script>
  <script src="js/app.js"></script>
</body>
</html>
