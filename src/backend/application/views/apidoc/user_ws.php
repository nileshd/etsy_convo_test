<?php


function format_json($json, $html = false, $tabspaces = null)
{
	$tabcount = 0;
	$result = '';
	$inquote = false;
	$ignorenext = false;

	if ($html)
	{
		$tab = str_repeat ( "&nbsp;", ($tabspaces == null ? 4 : $tabspaces) );
		$newline = "<br/>";
	}
	else
	{
		$tab = ($tabspaces == null ? "\t" : str_repeat ( " ", $tabspaces ));
		$newline = "\n";
	}

	for($i = 0; $i < strlen ( $json ); $i ++)
	{
		$char = $json [$i];

		if ($ignorenext)
		{
			$result .= $char;
			$ignorenext = false;
		}
		else
		{
			switch ($char)
			{
				case ':' :
					$result .= $char . (! $inquote ? " " : "");
					break;
				case '{' :
					if (! $inquote)
					{
						$tabcount ++;
						$result .= $char . $newline . str_repeat ( $tab, $tabcount );
					}
					else
					{
						$result .= $char;
					}
					break;
				case '}' :
					if (! $inquote)
					{
						$tabcount --;
						$result = trim ( $result ) . $newline . str_repeat ( $tab, $tabcount ) . $char;
					}
					else
					{
						$result .= $char;
					}
					break;
				case ',' :
					if (! $inquote)
					{
						$result .= $char . $newline . str_repeat ( $tab, $tabcount );
					}
					else
					{
						$result .= $char;
					}
					break;
				case '"' :
					$inquote = ! $inquote;
					$result .= $char;
					break;
				case '\\' :
					if ($inquote)
						$ignorenext = true;
					$result .= $char;
					break;
				default :
					$result .= $char;
			}
		}
	}

	return $result;
}
?>
<script
	src="https://google-code-prettify.googlecode.com/svn/loader/run_prettify.js?skin=sunburst"></script>


<div class="apidoc">


	<a name="top"></a>
	<h2>Get Conversation Threads for a Recipient</h2>


	<a name="url_endpoint"></a>
	<div class="url_endpoint">

		<div class="btn btn-info btn-large">GET</div>

		/users/{user_id}/convos

	</div>



	<div id="description" class="info_box">
		<h3>Description</h3>
		This API gets the conversations threads for a user, such user denoted
		by the {user_id} param in the url. It gets only the top
		level conversations and show the thread count as well (number of
		replies). One can optionally want to get only the sent items by passing in optional query parameters. One can also get only the read vs unread messages too.
	</div>



	<a name="parameters"></a>
	<div id="parameters" class="info_box">
		<h3>Optional Parameters</h3>
These parameters can be added as get String Params to filter the output. E.g users/12/convos?start_row=100&num_items=20
		<table class="table table-bordered table-striped  ">
			<thead>
				<tr class="params_table_row_head" valign="middle">
					<td>Name</td>
					<td>Desc</td>
					<td>Required</td>
					<td>Type</td>

				</tr>
			</thead>
			<tbody>




				<tr class="param_row">
					<td class="param_name">start_row</td>
					<td>Starting Row for Convos. Default is 0.</td>
					<td>N</td>
					<td>int</td>
				</tr>

				<tr class="param_row">
					<td class="param_name">num_items</td>
					<td>Number of Items to show in resultset. Defaults to 10.</td>
					<td>N</td>
					<td>int</td>
				</tr>

	<tr class="param_row">
					<td class="param_name">get_sent</td>
					<td>If set to 1, this flag will get only the sent convos by a particular user rather than only received. Defaults to false.</td>
					<td>N</td>
					<td>int</td>
				</tr>


					<tr class="param_row">
					<td class="param_name">read_status</td>
					<td>This parameter filters only by read/unread status of the message.</td>
					<td>N</td>
					<td>'read'/'unread'</td>
				</tr>



			</tbody>
		</table>
	</div>






	<div id="example_construct" class="info_box">
		<h3>Example URL constructs</h3>
http://localhost:28001/api/convos/20/thread



<?php

$success_code = <<<EOT

{"success":1,"data":[{"id":"20","sender_id":"3","recipient_id":"2","subject":"User 3 to User 2 - Top Message","thread_count":"2"},{"id":"26","sender_id":"1","recipient_id":"2","subject":"nilesh changed","thread_count":"0"}]}

EOT;

?>
				<div id="example_success" class="info_box">
			<h3>Success Example</h3>



			<pre class="prettyprint">
<?php echo format_json($success_code,true); ?>
</pre>
		</div>



<?php

$failure_code = <<<EOT
{"success":0,"data":{"error":{"message":"Http Method Not supported yet"}}}
EOT;

?>

				<div id="example_failure" class="info_box">
			<h3>Failure Example</h3>

			<pre class="prettyprint">
<?php echo format_json($failure_code,true); ?>
</pre>

		</div>



	</div>



</div>



<div class="apidoc">


	<a name="top"></a>
	<h2>Get User By Id</h2>


	<a name="url_endpoint"></a>
	<div class="url_endpoint">

		<div class="btn btn-info btn-large">GET</div>

		/users/{user_id}

	</div>



	<div id="description" class="info_box">
		<h3>Description</h3>
		This API gets a user by Id denoted by {user_id}.
	</div>


	<div id="example_construct" class="info_box">
		<h3>Example URL constructs</h3>
http://localhost:28001/api/users/2



<?php

$success_code = <<<EOT

{"success":1,"data":{"user":{"id":"2","name":"John"}}}
EOT;

?>
				<div id="example_success" class="info_box">
			<h3>Success Example</h3>



			<pre class="prettyprint">
<?php echo format_json($success_code,true); ?>
</pre>
		</div>



	</div>



</div>






