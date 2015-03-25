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

<a name="getthread"></a>
<div class="apidoc">


	<a name="top"></a>
	<h2>Get a Conversation Thread by Root Parent Id</h2>


	<a name="url_endpoint"></a>
	<div class="url_endpoint">

		<div class="btn btn-info btn-large">GET</div>

		/users/{id}/convos

	</div>



	<div id="description" class="info_box">
		<h3>Description</h3>
		This API lets you get an entire conversation thread based on the Root
		Parent Id (top level parent).
	</div>



	<a name="parameters"></a>
	<div id="parameters" class="info_box">
		<h3>Optional Parameters</h3>

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


			</tbody>
		</table>
	</div>






	<div id="example_construct" class="info_box">
		<h3>Example URL constructs</h3>
http://localhost:28001/api/convos/20/thread


<?php

$success_code = <<<EOT

{"success":1,"data":{"convo_thread":[{"id":"20","sender_id":"3","recipient_id":"2","subject":"User 3 to User 2 - Top Message","body":"User 3 to User 2 - Top Message","parent_id":null,"root_parent_id":null},{"id":"21","sender_id":"2","recipient_id":"3","subject":"User 2 to User 3 Reply - Level 1","body":"User 2 to User 3 - Reply Level 1","parent_id":"20","root_parent_id":"20"},{"id":"42","sender_id":"1","recipient_id":"2","subject":"","body":"xxxx","parent_id":"21","root_parent_id":"20"}]}}
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

<a name="getbyid"></a>
<div class="apidoc">


	<a name="top"></a>
	<h2>Get Convo By Id</h2>


	<a name="url_endpoint"></a>
	<div class="url_endpoint">

		<div class="btn btn-info btn-large">GET</div>

		/convos/{id}

	</div>



	<div id="description" class="info_box">
		<h3>Description</h3>
		This method can get just one convo by Id.
	</div>


	<div id="example_construct" class="info_box">
		<h3>Example URL constructs</h3>
http://localhost:28001/api/convos/25



<?php

$success_code = <<<EOT

{"success":1,"data":{"convo":{"id":"25","sender_id":"1","recipient_id":"2","parent_id":"21","root_parent_id":"21","subject":"nilesh changed","body":"xxxx","status":"unread","date_created":"2015-03-25 00:57:51"}}}
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





<a name="postconvo"></a>
<div class="apidoc">


	<a name="top"></a>
	<h2>Send/Post a new Convo</h2>


	<a name="url_endpoint"></a>
	<div class="url_endpoint">

		<div class="btn btn-info btn-large">POST</div>

		/convos

	</div>



	<div id="description" class="info_box">
		<h3>Description</h3>
		This API allows you to send a convo to another user. The paramters
		need to be passed in in the POST request.
	</div>



	<a name="parameters"></a>
	<div id="parameters" class="info_box">
		<h3>Parameters Needed</h3>

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
					<td class="param_name">sender_id</td>
					<td>User Id of the Sender</td>
					<td>Y</td>
					<td>int</td>
				</tr>

				<tr class="param_row">
					<td class="param_name">recipient_id</td>
					<td>User Id of the recipient.</td>
					<td>y</td>
					<td>int</td>
				</tr>

				<tr class="param_row">
					<td class="param_name">subject</td>
					<td>The subject of your convo. Note that if you are doing a reply,
						the subject will be set to blank.</td>
					<td>N</td>
					<td>text</td>
				</tr>


				<tr class="param_row">
					<td class="param_name">body</td>
					<td>The text body of your convo. Can be up to 64k characters.</td>
					<td>N</td>
					<td>text</td>
				</tr>


				<tr class="param_row">
					<td class="param_name">reply_convo_id</td>
					<td>If you are replying to a convo, this will denote the convo id
						you are replying to.</td>
					<td>N</td>
					<td>text</td>
				</tr>

				<tr class="param_row">
					<td class="param_name">root_parent_id</td>
					<td>The Root parent Id of your message thread. This should be
						available to you, when you get the convos. Persist it to use it
						here.</td>
					<td>N</td>
					<td>text</td>
				</tr>




			</tbody>
		</table>
	</div>






	<div id="example_construct" class="info_box">
		<h3>Example URL constructs</h3>
http://localhost:28001/api/convos



<?php

$success_code = <<<EOT

{"success":1,"data":{"convo_id":48}}
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
{"success":0,"data":{"error":{"message":"You need to also pass in the Root Parent Id if you are adding a child"}}}
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







<a name="deleteconvo"></a>
<div class="apidoc">


	<a name="top"></a>
	<h2>Delete a Convo and children</h2>


	<a name="url_endpoint"></a>
	<div class="url_endpoint">

		<div class="btn btn-info btn-large">DELETE</div>

		/convos/{id}

	</div>



	<div id="description" class="info_box">
		<h3>Description</h3>
		This API allows you to delete a convo. It's children also will be
		deleted if you delete a parent convo. {id} denotes the id of the convo
		or convo thread to be deleted.
	</div>






	<div id="example_construct" class="info_box">
		<h3>Example URL constructs</h3>
http://localhost:28001/api/convos



<?php

$success_code = <<<EOT
{"success":1,"data":{"deleted":1}}
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
{"success":0,"data":{"error":{"message":"Could not Find this Convo Thread."}}}
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






<a name="updateconvo"></a>
<div class="apidoc">


	<a name="top"></a>
	<h2>Update Convo</h2>


	<a name="url_endpoint"></a>
	<div class="url_endpoint">

		<div class="btn btn-info btn-large">PUT</div>

		/convos/{id}

	</div>



	<div id="description" class="info_box">
		<h3>Description</h3>
		This API allows you to update data in a convo that you wrote.
	</div>



	<a name="parameters"></a>
	<div id="parameters" class="info_box">
		<h3>Parameters Needed</h3>

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
					<td class="param_name">sender_id</td>
					<td>User Id of the Sender</td>
					<td>Y</td>
					<td>int</td>
				</tr>

				<tr class="param_row">
					<td class="param_name">recipient_id</td>
					<td>User Id of the recipient.</td>
					<td>y</td>
					<td>int</td>
				</tr>

				<tr class="param_row">
					<td class="param_name">subject</td>
					<td>The subject of your convo. Note that if you are doing a reply,
						the subject will be set to blank.</td>
					<td>N</td>
					<td>text</td>
				</tr>


				<tr class="param_row">
					<td class="param_name">body</td>
					<td>The text body of your convo. Can be up to 64k characters.</td>
					<td>N</td>
					<td>text</td>
				</tr>


				<tr class="param_row">
					<td class="param_name">reply_convo_id</td>
					<td>If you are replying to a convo, this will denote the convo id
						you are replying to.</td>
					<td>N</td>
					<td>text</td>
				</tr>

				<tr class="param_row">
					<td class="param_name">root_parent_id</td>
					<td>The Root parent Id of your message thread. This should be
						available to you, when you get the convos. Persist it to use it
						here.</td>
					<td>N</td>
					<td>text</td>
				</tr>




			</tbody>
		</table>
	</div>


	<div id="example_construct" class="info_box">
		<h3>Example URL constructs</h3>
http://localhost:28001/api/convos



<?php

$success_code = <<<EOT
{"success":1,"data":{"updated":1}}
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
{"success":0,"data":{"error":{"message":"you need to have written the message to be able to edit it."}}}
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




