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
            <style>
            .navbar {
              background-color: #cc0000 !important;
              background-image: none !important;
            }
            .navbar-brand {
              color: #fff !important;
            }
            </style>

           <!-- Bootstrap Stylesheet -->
            <link href="./css/bootstrap.min.css" rel="stylesheet">

            <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
            <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
            <!--[if lt IE 9]>
              <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
              <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
            <![endif]-->

            <link href="./css/bootstrap.min.css" rel="stylesheet"/>
            <link href="./css/bootstrap-theme.min.css" rel="stylesheet"/>
            <link href="./css/styles.css" rel="stylesheet"/>

            <?php if (isset($title)): ?>
                <title>Gleann Abhann Heraldry Database: <?= htmlspecialchars($title) ?></title>
            <?php else: ?>
                <title>Gleann Abhann Heraldry Database</title>
            <?php endif ?>

            <script src="./js/jquery-1.10.2.min.js"></script>
            <script src="./js/bootstrap.min.js"></script>
            <script src="./js/scripts.js"></script>

        <!-- END OF BOOTSTRAP SECTION -->

    </head>

    <body>

<div class="container">
  <!-- begin page -->
  <header class="page-header">

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
            <a class="navbar-brand" href="../">Gleann Abhann Heraldry Database</a>
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
            <form class="navbar-form navbar-right" role="search" action="search.php" method="get">
              <div class="form-group">
                <input type="text" class="form-control" placeholder="Search for Name or Award" name="name">
              </div>
              <button type="submit" class="btn btn-default">Submit</button>
            </form>
        <!--

          <li class="disabled"><a href="#">About Us</a></li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">More <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li class="disabled"><a href="#">Contact Us</a></li>
              <li><a href="#">Disclaimers</a></li>
              <li><a href="http://gleannabhann.net" rel="external">GleannAbhann.net</a></li>
              <li role="separator" class="divider"></li>
              <li><a href="#">Separated link</a></li>
            </ul>
          </li>
          -->
        </ul>
      </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
  </nav>

</header>
            </div>
<!-- end header -->

<!-- Begin middle -->
            <div id="middle" class="container">
