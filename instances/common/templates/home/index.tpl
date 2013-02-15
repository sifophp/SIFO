<!DOCTYPE html>
<html lang="en">
{ $modules.head nofilter}
  <body>

    <div class="topbar">
      <div class="fill">
        <div class="container">
          <a class="brand" href="#">Sifo</a>
          <ul class="nav">
			<li class="active"><a href="">Home</a></li>
			<li><a href="">Section 1</a></li>
			<li><a href="">Section 2</a></li>
			<li><a href="">Section 3</a></li>
          </ul>
        </div>
      </div>
    </div>

    <div class="container">

      <!-- Main hero unit for a primary marketing message or call to action -->
      <div class="hero-unit">
        <h1>Hello, world!</h1>
        <p>{t 1=$mood}This is how i feel... %1{/t}</p>
        <p><a class="btn primary large" href="http://sifo.me">Learn more &raquo;</a></p>
      </div>

      <!-- Example row of columns -->
      <div class="row">
        <div class="span-one-third">
          <h2>This is a sample home</h2>
          <p>Etiam porta sem malesuada magna mollis euismod. Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit.</p>
          <p><a class="btn" href="#">View details &raquo;</a></p>
        </div>
        <div class="span-one-third">
          <h2>Create your instance</h2>
           <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
          <p><a class="btn" href="#">View details &raquo;</a></p>
       </div>
        <div class="span-one-third">
			<h2>See <em>/scripts</em></h2>
          <p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
          <p><a class="btn" href="#">View details &raquo;</a></p>
        </div>
      </div>

      <footer>
        <p>Powered by Sifo, 2009-{$smarty.now|date_format:"%Y"}</p>
      </footer>

    </div> <!-- /container -->

  </body>
</html>