<?php
// ini_set('error_reporting', E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);

require 'vendor/autoload.php';

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

if (!empty($_FILES['file']['tmp_name'])) {
	$html = file_get_contents($_FILES['file']['tmp_name']);
	$crawler = new Crawler($html);

	$table = $crawler->filter('table');

	$arData = array();
	$table->filter('tr')->each(function ($stroka) use (&$arData) {
		if ($stroka->filter('td')->count() > 4) {
			$status = $stroka->filter('td')->eq(2)->text();
			switch ($status) {
				case 'balance':
				case 'buy':
					$tdNum = $status == 'balance' ? 4 : 13;
					// $arData['X'][] = $stroka->filter('td')->eq($tdNum)->text();
					// $arData['Y'][] = $stroka->filter('td')->eq(1)->text();

					$arData[] = array(
						'X' => $stroka->filter('td')->eq($tdNum)->text(),
						'Y' =>  $stroka->filter('td')->eq(1)->text()
					);
					break;
				default: break;
			}
		}
	});

	$str = "['Дата', 'Баланс'],";
	$bl = 0;
	foreach ($arData as $key => $value) {
		$bl += $value['X'];
		$str .= "['" . $value['Y'] . "', $bl],";
	}
}
$title = $_POST['name'] ?: "Изменение баланса";

// echo "<pre>"; print_r($_REQUEST); echo "</pre>";
// echo "<pre>"; print_r($_FILE); echo "</pre>";
// exit();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>RoboForex (CY) Ltd.</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
  </head>

  <body>

    <nav class="navbar navbar-toggleable-md navbar-inverse fixed-top bg-inverse">
      <a class="navbar-brand" href="/">Тест</a>
    </nav>

    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron">
      <div class="container">
        <h1 class="display-4">Построить график изменения баланса</h1>
      </div>
    </div>

    <div class="container">
      <!-- Example row of columns -->
      <div class="row">
        <div class="col-md-12">
			<form action="" method="POST" enctype="multipart/form-data">
			  <div class="form-group">
			    <label for="exampleInputEmail1">Подпись на графике</label>
			    <input type="text" class="form-control" id="exampleInputEmail1" name="name" placeholder="Подпись" value="Изменение баланса">
			  </div>
			  <div class="form-group">
			    <label for="exampleInputFile" class="custom-file">Файл</label>
			    <input type="file" id="exampleInputFile" class="form-control" name="file" required="true">
			  </div>
			  <div class="form-group">
			    <a href="statement.html" target="_blank">Пример файла</a>
			  </div>
			  <button type="submit" class="btn btn-primary">Построить график</button>
			</form>
        </div>
        <div class="col-md-12">
		    <div id="curve_chart" style="width: 100%; height: 500px"></div>       	
        </div>
      </div>
    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>

	<?php if (strlen($str)): ?>
	<!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          <?=$str?>
        ]);

        var options = {
          title: '<?=$title?>',
          curveType: 'function',
          legend: { position: 'top right', text: 'dssd' }
        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

        chart.draw(data, options);
      }
    </script>
    <?php endif; ?>

  </body>
</html>
