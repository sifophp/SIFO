<h2 id="benchmarks">{t}Benchmarks{/t}</h2>
<h3 id="bench"><a class="debug_toggle_view" rel="benchmarks_content{$execution_key}" href="#">Times of execution</a></h3>
<div id="benchmarks_content{$execution_key}" class="debug_contents">
	<table class="benchmark_contents">
{ if isset($debug.benchmarks)}
{		foreach name=bench from=$debug.benchmarks item=bench key=label}
		<tr {if $bench>0.1} class="slow"{/if}><td>{$label}</td><td>{$bench|number_format:4} secs.</td></tr>
{		/foreach}
{ /if}
	</table>
	<div class="benchmarks_legend">
		<p>Order of execution:
		<ul>
		<li><strong>Parent dispatch:</strong>
				<ul>
					<li>Parent preDispatch</li>
					<li>executeNestedModules [<strong>foreach module</strong>]
						<ul>
							<li>preDispatch</li>
							<li>execute</li>
							<li>postDispatch</li>
						</ul>
					</li>
					<li>Parent execute
						<ul>
							<li>preDispatch</li>
							<li>execute</li>
							<li>postDispatch</li>
						</ul>
					</li>
					<li>Grab HTML</li>
					<li>Realtimereplacement [<strong>foreach RTR found</strong>]
						<ul>
							<li>preDispatch</li>
							<li>execute</li>
							<li>postDispatch</li>
						</ul>
					</li>
				</ul>
			</li>
		</ul>
	</div>

</div>
	<h2 id="controllers">{t}Controllers{/t}</h2>
{foreach name=controllers from=$debug.controllers item=controller key=controller_name}
	<h3 id="cont_{$smarty.foreach.controllers.index}"><a class="debug_toggle_view" rel="cont_content_{$smarty.foreach.controllers.index}{$execution_key}" href="#">{$smarty.foreach.controllers.index+1}. {$controller_name}</a></h3>
	<div id="cont_content_{$smarty.foreach.controllers.index}{$execution_key}" class="debug_contents">
{	foreach from=$controller item=content key=key name=controllerparams}
{		if $key == "CONTROLLER"}
	<h4><a class="debug_toggle_view" rel="params_cont_content_{$smarty.foreach.controllers.index}_{$smarty.foreach.controllerparams.index}{$execution_key}" href="#">Controller parameters</a></h4>
	<div id="params_cont_content_{$smarty.foreach.controllers.index}_{$smarty.foreach.controllerparams.index}{$execution_key}" class="debug_contents">
	<ul>
{		foreach from=$content.parameters item=content1 key=key1}
{			if is_array($content1)}
			<li class="array"><strong>{$key1}: Array</strong>
{				foreach from=$content1 item=arr key=k}
				<ul>
					<li><strong>{$k}</strong>: {if is_array($arr)}<pre>{$arr|debug_print_var}</pre>{else}<code>{$arr|escape}</code>{/if}</li>
				</ul>
{				/foreach}
			</li>
{			else}
			<li><strong>{$key1}</strong>: "{$content1|escape}"</li>
{			/if}
{		/foreach}
	</ul>
	</div>
{		else}

{*	TEMPLATES AND OTHER FUTURE ELEMENTS *}
	<h4><a class="debug_toggle_view" rel="assigns_cont_content_{$smarty.foreach.controllers.index}_{$smarty.foreach.controllerparams.index}{$execution_key}" href="#">
{			if $key == "assigns"}Template assigns{else}{$key}{/if}
		</a></h4>
	<div  id="assigns_cont_content_{$smarty.foreach.controllers.index}_{$smarty.foreach.controllerparams.index}{$execution_key}" class="debug_contents">
	<ul>
{		foreach from=$content item=content1 key=key1}
{			if is_array($content1)}
			<li class="array"><strong>{$key1}: Array</strong>
{				foreach from=$content1 item=arr key=k}
				<ul>
					<li><strong>{$k}</strong>: {if is_array($arr)}<pre>{$arr|debug_print_var}</pre>{else}<code>{$arr|escape}</code>{/if}</li>
				</ul>
{				/foreach}
			</li>
{			else}
			<li><strong>{$key1}</strong>: "{$content1|escape}"</li>
{			/if}
{		/foreach}
	</ul>
	</div>
{		/if}
{	/foreach}
	</div>
{/foreach}