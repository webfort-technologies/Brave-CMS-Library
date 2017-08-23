<?php include("connection.php"); ?>
<html>
	<head></head>
	<body>
	<table border="1">
		<thead>
			<tr>
				<th>ID</th>
				<th>Name</th>
			</tr>
		</thead>
		
			<?php 
			 $user_sql_query = "select * from user";
			 $user_step_result = mysqli_query($con,$user_sql_query) or die(mysqli_error($con));
			 while($user_row = mysqli_fetch_array($user_step_result))
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

	</body>
</html>