<form action="register.php" method="post">
    <fieldset>
        <div class="form-group">
            <input autofocus class="form-control" name="username" placeholder="Username" type="text"/>
        </div>
        <div class="form-group">
            <input class="form-control" name="password" placeholder="Password" type="password"/><br/>
            <input class="form-control" name="confirmation" placeholder="Retype Password" type="password"/>
            <input class="form-control" name="key" placeholder="Your registration key" type="password"/>
        </div>
        <div class="form-group">
          <input class="form-control" name="mundaneName" placeholder="Your Mundane Name" type="text"/>
          <input class="form-control" name="email" placeholder="Your email address" type="text"/>
          <input class="form-control" name="personId" placeholder="Your Person ID" type="text"/>
        <div class="form-group">
            <button type="submit" class="btn btn-default">Register</button>
        </div>
    </fieldset>
</form>
<div>
    or <a href="login.php">log in</a>
</div>
