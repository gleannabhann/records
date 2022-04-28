<!DOCTYPE html>
<?php if (isset($_SESSION['initiated'])) validate_session();?>

<html>

    <head>
        <!-- START OF BOOTSTRAP SECTION -->
        <!-- DO NOT MODIFY -->
           <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
           <!-- Bootstrap Stylesheet -->
            <link href="/css/bootstrap.min.css" rel="stylesheet">
            <link href="/css/bootstrap-theme.min.css" rel="stylesheet"/>

            <!-- Bootstrap Overrides -->
            <link href="/css/cards.css" rel="stylesheet"/>
            <link href="/css/forms.css" rel="stylesheet"/>

            <!-- local style settings -->
            <link href="/css/styles.css" rel="stylesheet"/>

            <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
            <!-- WARNING: Respond.js does not work if you view the page via file:// -->
            <!--[if lt IE 9]>
              <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
              <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
            <![endif]-->



            <style>
            html {
              position: relative;
              min-height: 100%;
            }
            body {
              /* Margin bottom by footer height */
              margin-bottom: 60px;
              min-height: 100%;
            }

            .footer {
              position: absolute;
              bottom: 0;
              width: 95%;
              /* Set the fixed height of the footer here */
              height: 60px;

            }

            .navbar {
              background-color: #cc0000 !important;
              background-image: none !important;
            }
            .navbar-brand {
              color: #fff !important;
            }
            input {
               width: 100%;
               box-sizing: border-box;
               height: 28px; }
            textarea {
              width: 100%;
              box-sizing: border-box;
              height: 84px; }

            #map {
              height: 500px;
            }
            </style>

            <?php if (isset($title)): ?>
                <title>Gleann Abhann Hall of Records: <?= htmlspecialchars($title) ?></title>
            <?php else: ?>
                <title>Gleann Abhann Hall of Records</title>
            <?php endif ?>

            <script src="/js/jquery-1.10.2.min.js"></script>
            <script src="/js/bootstrap.min.js"></script>
            <script src="/js/scripts.js"></script>
            <script src="/js/sorttable.js"></script>
        <!-- END OF BOOTSTRAP SECTION -->

    </head>

    <body>

<div class="container-fluid">
  <!-- begin page -->
  <header class="header">
<img class="banner" src="/img/banner.png" alt="Banner for Kingdom of Gleann Abhann" width="100%">
    <div id="top" role="navigation">
      <nav class="navbar navbar-expand-md navbar-default">
        <div class="container-fluid">
      <!-- Brand and toggle get grouped for better mobile display -->
            <a class="navbar-brand" href="../">Home</a>
            <button type="button" class="navbar-toggler collapsed" data-bs-toggle="collapse" data-bs-target="#main-nav" aria-expanded="false">
            <span class="navbar-toggler-icon"></span>
            </button>

      <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="main-nav">
      <ul class="nav navbar-nav">
      <li class='nav-item'><a class="nav-link"  href="/public/awards.php">Awards</a></li>
      <li class='nav-item'><a class="nav-link"  href="/public/combat.php">Combat</a></li>
      <!-- <li><a class="navbar-brand"  href="/public/auth.php">Authorizations</a></li> -->
      <li class='nav-item'><a class="nav-link"  href="/public/list_site.php">Campgrounds</a></li>
        <?php
        if (isset($_SESSION["id"]))
        {
           echo '<li class="nav-item"><a class="nav-link" href="/public/reports.php">Reports</a></li>';
        }
        ?>           <!--
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">More <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li><a href="#">Recent Additions</a></li>
              <li><a href="#">Another Link</a></li>
              <li><a href="#">Something else here</a></li>
              <li role="separator" class="divider"></li>
              <li><a href="#">Advanced Search</a></li>
              <li role="separator" class="divider"></li>
              <li><a href="#">One more separated link</a></li>
            </ul>
          </li>
-->

        </ul>


          <ul class="nav navbar-nav navbar-right">
            <form class="navbar-form navbar-right" role="search" action="search.php" method="get">
              <div class="form-group">
                <input type="text" class="form-control" placeholder="Search for Name or Award" name="name">
              </div>
              <button type="submit" class="btn btn-default">Submit</button>
            </form>


          <?php
          if (isset($_SESSION["id"]))
          {
            echo '<li class="nav-item" >Logged in as '.$_SESSION["webuser_name"].'</li>';
            echo '<li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>';
          }
          ?>
          <li class="nav-item dropdown">
            <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">More <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="http://gleannabhann.net/award-recommendation-form/" rel="external">Award Recommendation Form</a></li>
              <!--<li class="disabled">
                 <a href="legal.php">Disclaimers</a></li>-->
              <li><a class="dropdown-item" href="http://docs.gleannabhann.net/ws-ga-library/College%20of%20Heralds/Amethyst%20Herald" rel="external">Award Definitions</a></li>
              <li role="separator" class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="http://gleannabhann.net" rel="external">GleannAbhann.net</a></li>
            </ul>
          </li>

        </ul>
      </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
  </nav>
</div>
</header>

<!-- end header -->

<!-- Begin middle -->
            <div id="middle">
