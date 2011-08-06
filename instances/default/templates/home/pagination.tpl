{if $pagination_data.num_pages > 1 }
<div class="pagination">
	<ul>
{	if isset( $pagination_data.first_page) }
		<li class="pagination_first"><a title="{t 1=$pagination_data.params.title}Go back to first page on '%1'{/t}" href="{$pagination_data.first_page.link}">{t}First{/t}</a></li>
{	/if}
{	if isset( $pagination_data.previous_page) }
		<li class="pagination_prev"><a title="{t 1=$pagination_data.params.title}Go to previous page on '%1'{/t}" href="{$pagination_data.previous_page.link}">{t}Previous{/t}</a></li>
{	/if}
{	foreach from=$pagination_data.pages item=page_data name=pagination}

		{assign var='subclass' value=''}
		{if $smarty.foreach.pagination.first && isset( $pagination_data.previous_page ) }
			{assign var='subclass' value='first'}
		{elseif $smarty.foreach.pagination.last && !isset( $pagination_data.next_page ) }
			{assign var='subclass' value='last'}
		{/if}

		{if $page_data.is_current}
			<li><span title="{t 1=$page_data.number 2=$pagination_data.params.title}Page %1 on '%2'{/t}" class="active {$subclass}">{$page_data.number}</span></li>
		{else}
			<li {if !empty($subclass)}class="{$subclass}"{/if}><a title="{t 1=$page_data.number 2=$pagination_data.params.title}Page %1 on '%2'{/t}" href="{$page_data.link}">{$page_data.number}</a></li>
		{/if}

{	/foreach}
{	if isset( $pagination_data.next_page) }
	 	<li class="pagination_next"><a title="{t 1=$pagination_data.params.title}Go to next page on '%1'{/t}" href="{$pagination_data.next_page.link}">{t}Next{/t}</a></li>
{	/if}
{	if isset( $pagination_data.last_page) }
	 	<li class="pagination_last"><a title="{t 1=$pagination_data.params.title}Go to last page on '%1'{/t}" href="{$pagination_data.last_page.link}">{t}Last{/t}</a></li>
{	/if}
	</ul>
</div>

{ if ( isset( $pagination_data.display_items_per_page.display ) && $pagination_data.display_items_per_page.display ) }
<div class="rows_page">
	{t}Show{/t}
	<select>
	{foreach from=$pagination_data.display_items_per_page.values item=item_per_page}
		<option value="{$item_per_page}">{$item_per_page}</option>
	{/foreach}
	</select>
	{t}rows per page{/t}
</div>
{/if}

{/if}