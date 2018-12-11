{if $href}
  <li {if $selected}class="active"{/if}>
    <a href="{eval var=$href}" title="{$title|strip_tags}" class="top_section_tab{if $selected}_selected{/if}{if $class} {$class}{/if}" {if $onclick}onclick="{$onclick}" {/if}>
      {$title}
    </a>
  </li>
{else}
  <li class="top_section_tab {if $selected}active{/if}{if $class} {$class}{/if}">
    <a>{$title}</a>
  </li>
{/if}
