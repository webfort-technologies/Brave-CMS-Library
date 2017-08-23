<?php 
include("connection.php");
include("library/pageination_extended.php");

$config['query'] =  "select * from user";
$config['con'] = $con;
$config['rows_per_page'] = 50;
$config['page_no_variable']="page_no";
$pagination = new pagination($config);
$row_array  = $pagination->get_array();

?>
<html>
	<head>
		<!-- Latest compiled and minified CSS & JS -->
		<link rel="stylesheet" media="screen" href="//netdna.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
		<script src="https://code.jquery.com/jquery.js"></script>
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	</head>
	<body>
	<div class="container">

	<h1>Users</h1>

	<?php echo $pagination->perpage_selector(); ?>

<p>
	Total Records : <?php echo $pagination->show_total_records(); ?>
</p>
	
		
	<table class="table">
		<thead>
			<tr>
				<th><?php echo $pagination->sortable_label('id',"ID") ?></th>
				<th><?php echo $pagination->sortable_label('name',"Name") ?></th>
			</tr>
		</thead>
			<?php 
			foreach ($row_array as $user_row) 
			{
			 	?>
			 	<tr>
				 	<td><?php echo  $user_row['id']; ?></td>
				 	<td><?php echo  $user_row['name']; ?></td>
			 	</tr>
			 	<?php
			 }
			  ?>
	</table>


 
	<?php 
	$pagination->show_links_google_type();
	 ?>
	</div>

		
		
	</body>
</html>