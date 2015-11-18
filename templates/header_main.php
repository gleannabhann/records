<!DOCTYPE html>

<html>

    <head>
        <!-- START OF BOOTSTRAP SECTION -->
        <!-- DO NOT MODIFY -->
           <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
            <!-- Bootstrap Overrides -->

           <!-- Bootstrap Stylesheet -->
            <link href="/css/bootstrap.min.css" rel="stylesheet">
            <link href="/css/bootstrap-theme.min.css" rel="stylesheet"/>
            <link href="/css/styles.css" rel="stylesheet"/>
            <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
            <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
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
            </style>

            <?php if (isset($title)): ?>
                <title>Gleann Abhann Heraldry Database: <?= htmlspecialchars($title) ?></title>
            <?php else: ?>
                <title>Gleann Abhann Heraldry Database</title>
            <?php endif ?>

            <script src="/js/jquery-1.10.2.min.js"></script>
            <script src="/js/bootstrap.min.js"></script>
            <script src="/js/scripts.js"></script>

        <!-- END OF BOOTSTRAP SECTION -->

    </head>

    <body>
<?php if (isset($_SESSION['initiated'])) validate_session();?>
<div class="container-fluid">
  <!-- begin page -->
  <header class="header">
<img class="banner" src="/img/banner.png" alt="Banner for Kingdom of Gleann Abhann" width="100%">
    <div id="top" role="navigation">
      <nav class="navbar navbar-default">
        <div class="container-fluid">
      <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="../">Home</a>
          </div>


      <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <!-- (Disabled until needed)  <ul class="nav navbar-nav">
            <li><a href="#">Awards</a></li>
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


        </ul>
      -->

          <ul class="nav navbar-nav navbar-right">
            <form class="navbar-form navbar-right" role="search" action="public/search.php" method="get">
              <div class="form-group">
                <input type="text" class="form-control" placeholder="Search for Name or Award" name="name">
              </div>
              <button type="submit" class="btn btn-default">Submit</button>
            </form>

            <!-- display logout button if user is logged in -->
            <?php
            if (isset($_SESSION["id"]))
            {
              echo '<li><a href="public/logout.php" class="navbar-brand">Logout</a></li>';
            }
            ?>

          <!-- <li class="disabled">
             <a href="#" class="navbar-brand">About This Site</a></li>-->
          <li class="dropdown">
            <a href="#" class="dropdown-toggle navbar-brand" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">More <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li><a href="http://gleannabhann.net/award-recommendation-form/" rel="external">Award Recommendation Form</a></li>
              <!-- <li class="disabled">
                 <a href="legal.php">Disclaimers</a></li>-->

              <li><a href="http://docs.gleannabhann.net/ws-ga-library/College%20of%20Heralds/Amethyst%20Herald" rel="external">Award Definitions</a></li>
              <li role="separator" class="divider"></li>
              <li><a href="http://gleannabhann.net" rel="external">GleannAbhann.net</a></li>
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
