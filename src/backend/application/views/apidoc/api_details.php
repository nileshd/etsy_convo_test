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
	<h2>USER WS</h2>


	<a name="url_endpoint"></a>
	<div class="url_endpoint">

		<div class="btn btn-info btn-large">POST</div>

		/api/convos

	</div>



	<div id="description" class="info_box">
		<h3>Description</h3>
		This ws does this
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
					<td>Max Length</td>
				</tr>
			</thead>
			<tbody>


<?php

for($a = 0; $a < 10; $a ++)
{

	?>
<tr class="param_row">
					<td class="param_name">sort_order</td>
					<td>Sort Order</td>
					<td>N</td>
					<td>N</td>





				</tr>

<?php
}
?>
</tbody>
		</table>
	</div>






	<div id="example_construct" class="info_box">
		<h3>Example URL constructs</h3>
		http://www.docs.com/




		<div id="example_success" class="info_box">
			<h3>Success Example</h3>



			<pre class="prettyprint">
<?php echo format_json($doc->json_example_success,true); ?>
</pre>
		</div>





		<div id="example_failure" class="info_box">
			<h3>Failure Example</h3>

			<pre class="prettyprint">
<?php echo format_json($doc->json_example_failure,true); ?>
</pre>

		</div>


	</div>