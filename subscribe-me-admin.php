<div class="subscribeme-wrapper">
<?php
global $wpdb;
$subscribed_users = $wpdb->get_results( 
	"
	SELECT * 
	FROM $wpdb->prefix"."subscribers_list
	ORDER BY time DESC
	",
	ARRAY_A
);

if ($subscribed_users){  
$i=1;
?>

<style>
table{
	border-collapse: collapse; border-spacing: 0;
}
#users-list{
	font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif; font-size: 12px;
	width: 80%;
	text-align: left;
	border-collapse: collapse;
	margin: 20px;
}
#users-list th {
	font-size: 13px; font-weight: normal;
	background: #B9C9FE url("<?php echo plugins_url('subscribe-me/images/gradhead.png');?>") repeat-x;
	border-top: 2px solid #D3DDFF; border-bottom: 1px solid white;
	color: #039;
	padding: 8px;
}
#users-list td {
	border-bottom: 1px solid white; border-top: 1px solid white;
	color: #669;
	background: #E8EDFF url("<?php echo plugins_url('subscribe-me/images/gradback.png');?>") repeat-x;
	padding: 8px;
}
</style>

<table id="users-list"> 
	<thead>
		<tr>
			<th scope="col">S.No</th>
			<th scope="col">Name</th>
			<th scope="col">Email</th>
			<th scope="col">Phone</th>
			<th scope="col">Date</th>
		</tr>
	</thead>
	<tbody>

<?php
	foreach ($subscribed_users as $user)
	{
		?>
		<tr class="sme-list">
			<td class="sno"><?php echo $i?></td>
			<td class="name"><?php echo $user['name'];?></td>
			<td class="email"><?php echo $user['email'];?></td>
			<td class="phone"><?php echo $user['country_code']." ".$user['phone'];?></td>
			<td class="date"><?php echo $user['time'];?></td>
		</tr>
		<?php $i++;
	}
echo "</tbody></table>";
	
}	
else
{
	?>
	<h2>No User(s) found.</h2>
	<?php
}

?>
</div>
