<table cellpadding="1" cellspacing="1">
	<thead>
	<tr>
            <th>DEVICE</th>
            <th>BRANCH</th>
            <th>LOCATION</th>
            <th>PLU</th>
            <th>TIME</th>
            <th>RESULT</th>
	</tr>
	</thead>

	<tbody>
            <?php foreach($Results as $field){?>
                <tr>
                    <td><?=$field['device']?></td>
                   <td><?=$field['branch_ref']?></td>
                    <td><?=$field['location_id']?></td>
                    <td><?=$field['item_number']?></td>
                    <td><?=$field['timestamp']?></td>
                    <td><?=$field['comment']?></td>
                </tr>
            <?php }?>
	</tbody>

</table>