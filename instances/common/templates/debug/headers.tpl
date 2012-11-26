{if !empty($debug.headers)}
<h1 id="debug_headers">{t}Headers{/t}</h1>
    {foreach $debug.headers as $headers_block}
    {if !empty( $headers_block) }
    <h2 id="headers_{$headers_block@iteration}">
        <a class="debug_toggle_view" rel="headers_content_{$headers_block@iteration}" href="#">
            Headers::send()
        </a>
    </h2>
    <div id="headers_content_{$headers_block@iteration}" class="debug_contents">
    <table>
        <thead>
            <tr>
                <th>Header content</th>
                <th>Replace*</th>
            </tr>
        </thead>
        <tbody>
        {foreach $headers_block as $block}
                <tr>
                    <td>{$block.content}</td>
                    <td>{if $block.replace}Yes{else}<span class="slow">No</span> {/if}</td>
                </tr>
        {/foreach}
        </tbody>
    </table>
        * When replace is set, the header overwrites any previous header of the same type.
    </div>
    {/if}
    {/foreach}


{/if}